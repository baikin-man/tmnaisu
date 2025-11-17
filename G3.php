<?php session_start(); ?>
<?php
ini_set('display_errors', 1); // エラーを画面に表示する
error_reporting(E_ALL); ?>
<?php require 'db-connect.php';

// 1. GETパラメータから商品IDを取得
$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($item_id <= 0) {
    echo "商品が指定されていません。";
    exit;
}

// 2. データベースに接続
try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. 商品本体の情報を取得
    $sql_item = $pdo->prepare('SELECT * FROM items WHERE id = ?');
    $sql_item->execute([$item_id]);
    $item = $sql_item->fetch(PDO::FETCH_ASSOC);

    // 4. SKUをすべて取得 (★ 修正: color_name, color_code を取得)
    // (DB変更前の 'color' カラムを 'color_name' として取得)
    $sql_skus = $pdo->prepare('
        SELECT id, item_id, size, color AS color_name, color_code, image_url, price, stock_quantity 
        FROM item_skus 
        WHERE item_id = ? AND status = 2 
        ORDER BY size, color ASC
    ');
    $sql_skus->execute([$item_id]);
    $skus = $sql_skus->fetchAll(PDO::FETCH_ASSOC);

    // 5. 商品が見つからなければ処理を中断
    if (empty($item) || empty($skus)) {
        echo "商品が見つかりません。";
        exit;
    }

    // --- 6. 閲覧履歴を記録 (item_view_history) ---
    $history_user_id = 0; 
    if (isset($_SESSION['user_id'])) {
        $history_user_id = (int)$_SESSION['user_id'];
    }
    $sql_history = $pdo->prepare('INSERT INTO item_view_history (user_id, item_id) VALUES (?, ?)');
    try {
        $sql_history->execute([$history_user_id, $item_id]);
    } catch (PDOException $e_history) {
        // 履歴の登録失敗は無視
    }

    // 7. JavaScriptに渡すための全SKUデータ (JSON形式)
    $skus_json = json_encode($skus);

    // 8. HTMLで「サイズ」ボタンを作るためのユニークなリスト
    $unique_sizes = array_unique(array_column($skus, 'size'));

    // 9. ページ初期表示用のSKU
    $initial_sku = $skus[0];
} catch (PDOException $e) {
    echo "データベース接続エラー: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($item['name']); ?> - 商品詳細</title>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link rel="stylesheet" href="header2.css"> 
    <link rel="stylesheet" href="./css/G3.css"> 

</head>
<script>
    // PHPからの全SKUデータ (★ 'color' は 'color_name' になっています)
    const allSkus = <?php echo $skus_json; ?>;

    document.addEventListener("DOMContentLoaded", () => {

        // --- 1. 操作するHTML要素を取得 ---
        const mainImage = document.querySelector(".main-image");
        const priceElement = document.querySelector(".price");
        const sizeList = document.querySelector(".size-list");
        const colorList = document.querySelector(".color-list");
        const thumbnailList = document.querySelector(".thumbnail-list");
        const cartButton = document.querySelector(".cart-btn");
        const navLeft = document.querySelector(".nav-btn.left");
        const navRight = document.querySelector(".nav-btn.right");
        const selectedSkuIdInput = document.querySelector("#selected-sku-id");

        // --- 2. メインの「情報更新」関数群 ---

        /**
         * (A) サイズがクリックされた時の処理
         */
        function updateAvailableColors(selectedSize) {
            const availableSkus = allSkus.filter(sku => sku.size === selectedSize);

            colorList.innerHTML = '';
            thumbnailList.innerHTML = '';

            const addedColors = [];
            const addedImages = [];

            availableSkus.forEach(sku => {
                // (A-1) カラーボタンの生成 (★ 修正: color_name と color_code を使用)
                if (!addedColors.includes(sku.color_name)) {
                    const colorBtn = document.createElement('button');
                    colorBtn.type = 'button';
                    colorBtn.className = `color`; // CSSでの色指定用 (例: .black)
                    colorBtn.dataset.color = sku.color_name; // ★ 色名
                    colorBtn.style.backgroundColor = sku.color_code; // ★ カラーコード
                    
                    // ★ (オプション) 色が明るすぎる場合、枠線をつける
                    if (isLightColor(sku.color_code)) {
                        colorBtn.style.border = '1px solid #ccc';
                    }

                    colorList.appendChild(colorBtn);
                    addedColors.push(sku.color_name);
                }
                // (A-2) サムネイル画像の生成
                if (!addedImages.includes(sku.image_url)) {
                    const thumbDiv = document.createElement('div');
                    thumbDiv.className = 'thumb';
                    thumbDiv.dataset.img = sku.image_url;
                    thumbDiv.dataset.color = sku.color_name; // ★ この画像がどの色名に対応するか
                    thumbDiv.style.backgroundImage = `url('${sku.image_url}')`;
                    thumbnailList.appendChild(thumbDiv);
                    addedImages.push(sku.image_url);
                }
            });

            // (A-3) 色の初期選択（サイズに紐づく最初の色）
            const firstColorButton = colorList.querySelector(".color");

            if (firstColorButton) {
                firstColorButton.classList.add("selected");
                // 色が決定したので、価格や在庫を更新
                updateFinalSkuDetails();
            }
        }

        /**
         * (B) 色またはサムネイルがクリックされた時の最終処理
         */
        function updateFinalSkuDetails() {
            const selectedSizeBtn = document.querySelector(".size-list .size.selected");
            const selectedColorBtn = document.querySelector(".color-list .color.selected");

            if (!selectedSizeBtn || !selectedColorBtn) {
                cartButton.disabled = true;
                cartButton.textContent = 'サイズと色を選択';
                selectedSkuIdInput.value = '';
                return;
            }

            const selectedSize = selectedSizeBtn.dataset.size;
            const selectedColor = selectedColorBtn.dataset.color; // ★ 色名 (color_name)

            // 選択された「サイズ」と「色名」に一致するSKUを探す (★ 修正)
            const foundSku = allSkus.find(sku =>
                sku.size === selectedSize && sku.color_name === selectedColor
            );

            if (foundSku) {
                // (B-1) 一致するSKUが見つかった場合
                priceElement.textContent = '¥' + Number(foundSku.price).toLocaleString();
                mainImage.style.backgroundImage = `url('${foundSku.image_url}')`;
                selectedSkuIdInput.value = foundSku.id;

                // (B-2) 在庫チェック
                if (foundSku.stock_quantity > 0) {
                    cartButton.textContent = 'カートに入れる';
                    cartButton.disabled = false;
                } else {
                    cartButton.textContent = '在庫切れ';
                    cartButton.disabled = true;
                }

                // (B-3) サムネイルの選択状態を更新
                thumbnailList.querySelectorAll(".thumb").forEach(thumb => {
                    thumb.classList.toggle("selected", thumb.dataset.img === foundSku.image_url);
                });

            } else {
                // (B-4) 一致するSKUが見つからない場合 (データ不整合)
                priceElement.textContent = 'この組み合わせはありません';
                mainImage.style.backgroundImage = 'none';
                cartButton.textContent = '選択不可';
                cartButton.disabled = true;
                selectedSkuIdInput.value = '';
            }
        }

        /**
         * (C) 左右の矢印ボタンが押された時の処理
         */
        function navigateThumbs(direction) {
            const thumbs = thumbnailList.querySelectorAll(".thumb");
            if (thumbs.length === 0) return;

            let currentIndex = -1;
            thumbs.forEach((thumb, index) => {
                if (thumb.classList.contains("selected")) {
                    currentIndex = index;
                }
            });

            if (currentIndex === -1) {
                currentIndex = (direction === 1) ? -1 : 0;
            }
            let newIndex = currentIndex + direction;

            if (newIndex < 0) { newIndex = thumbs.length - 1; }
            if (newIndex >= thumbs.length) { newIndex = 0; }
            thumbs[newIndex].click(); 
        }

        // (Helper) 色が明るいか判定する関数
        function isLightColor(hexColor) {
            if (!hexColor || !hexColor.startsWith('#')) return false;
            try {
                const r = parseInt(hexColor.slice(1, 3), 16);
                const g = parseInt(hexColor.slice(3, 5), 16);
                const b = parseInt(hexColor.slice(5, 7), 16);
                // 輝度の簡易計算 (YIQ)
                const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
                return yiq >= 192; // 192以上を明るいと判定
            } catch(e) {
                return false;
            }
        }


        // --- 3. クリックイベントの設定 ---

        // (E-1) サイズボタンのクリック
        sizeList.querySelectorAll(".size").forEach(btn => {
            btn.addEventListener("click", () => {
                sizeList.querySelectorAll(".size").forEach(b => b.classList.remove("selected"));
                btn.classList.add("selected");
                updateAvailableColors(btn.dataset.size);
            });
        });

        // (E-2) 色ボタンのクリック (イベント委任)
        colorList.addEventListener("click", (e) => {
            if (e.target.classList.contains('color')) {
                const btn = e.target;
                colorList.querySelectorAll(".color").forEach(b => b.classList.remove("selected"));
                btn.classList.add("selected");
                updateFinalSkuDetails();
            }
        });

        // (E-3) サムネイルのクリック (イベント委任)
        thumbnailList.addEventListener("click", (e) => {
            if (e.target.classList.contains('thumb')) {
                const thumb = e.target;
                const color = thumb.dataset.color; // ★ 色名 (color_name)
                // 1. サムネイルに対応する「色」ボタンを選択状態にする
                colorList.querySelectorAll(".color").forEach(b => {
                    b.classList.toggle("selected", b.dataset.color === color);
                });
                // 2. 最終SKU情報を更新
                updateFinalSkuDetails();
            }
        });

        // (E-4) 左右矢印ボタンのクリック
        navLeft.addEventListener("click", () => navigateThumbs(-1));
        navRight.addEventListener("click", () => navigateThumbs(1));


        // --- 4. 初期化処理 ---
        const initialSelectedSizeBtn = document.querySelector(".size-list .size.selected");
        if (initialSelectedSizeBtn) {
            // (4-1) 初期サイズに基づいて「色」と「サムネイル」を描画
            updateAvailableColors(initialSelectedSizeBtn.dataset.size);

            // (4-2) PHPで設定された初期SKUの色名 (initial_sku.color_name) を
            //       自動で選択状態にする
            const initialColorName = '<?php echo $initial_sku['color_name']; ?>';
            const initialColorBtn = colorList.querySelector(`.color[data-color="${initialColorName}"]`);

            if (initialColorBtn) {
                colorList.querySelectorAll(".color").forEach(b => b.classList.remove("selected"));
                initialColorBtn.classList.add('selected');
                // (4-3) 最終的な価格・在庫を反映
                updateFinalSkuDetails();
            }
        }
    });
</script>

<body>
    <?php require 'header2.php'; ?>

    <div class="product-container">
        <!-- 左側：画像セクション -->
        <div class="image-section">
            <button class="nav-btn left">&#8249;</button>
            <div class="main-image" style="background-image: url('<?php echo htmlspecialchars($initial_sku['image_url']); ?>');"></div>
            <button class="nav-btn right">&#8250;</button>
            <div class="thumbnail-list">
                <!-- JSによって動的に生成されます -->
            </div>
        </div>

        <!-- 右側：情報セクション -->
        <div class="info-section">
            <h1><?php echo htmlspecialchars($item['name']); ?></h1>
            <div class="price">¥<?php echo number_format($initial_sku['price']); ?></div>

            <div class="size-list">
                <?php foreach ($unique_sizes as $size): ?>
                    <button
                        class="size <?php echo ($size === $initial_sku['size']) ? 'selected' : ''; ?>"
                        type="button"
                        data-size="<?php echo htmlspecialchars($size); ?>">
                        <?php echo htmlspecialchars($size); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="color-list">
                <!-- JSによって動的に生成されます -->
            </div>

            <form action="cart-add.php" method="POST" class="cart-form">
                <div class="quantity-selector">
                    <label for="quantity-input">数量:</label>
                    <input type="number" id="quantity-input" name="quantity" value="1" min="1" max="99">
                </div>
                <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                <input type="hidden" name="item_sku_id" id="selected-sku-id" value="<?php echo $initial_sku['id']; ?>">
                <button class="cart-btn" type="submit" disabled>カートに入れる</button>
            </form>
        </div>
    </div>
</body>

</html>
<?php require "db-connect.php"; ?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKU編集</title>

    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f4f4;
            padding-top: 20px;
        }

        /* item_list.php から持ってきたスタイル */
        .item-box {
            border: 2px solid #007bff;
            border-radius: 8px;
            margin: 20px auto;
            width: 90%;
            max-width: 600px;
            /* SKU編集フォーム用に幅を調整 */
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .item-header {
            background: #f0f8ff;
            padding: 12px 15px;
            border-bottom: 2px solid #007bff;
        }

        .item-header h1 {
            margin: 0;
            font-size: 1.2rem;
            color: #333;
        }

        /* フォーム用のスタイル */
        form {
            padding: 15px 20px;
        }

        form label {
            font-weight: bold;
            display: block;
            margin-top: 12px;
            margin-bottom: 3px;
        }

        form input[type="text"],
        form input[type="number"],
        form input[type="file"],
        form select {
            /* selectタグのスタイルも追加 */
            display: block;
            width: 95%;
            max-width: 400px;
            padding: 8px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 5px;
        }

        form img {
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        form button {
            margin-top: 15px;
            padding: 10px 15px;
            font-size: 1rem;
            font-weight: bold;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        form button:hover {
            background-color: #0056b3;
        }

        .back-links {
            text-align: center;
            margin-top: 20px;
        }
        
        /* ★ カラーピッカー用のスタイル ★ */
        .color-input-wrapper { display: flex; align-items: center; gap: 10px; margin-bottom: 5px; }
        .color-input-wrapper input[type="text"] { width: 70%; margin-bottom: 0; }
        .color-input-wrapper input[type="color"] { width: 25%; height: 38px; padding: 2px; border: 1px solid #ccc; border-radius: 5px; cursor: pointer; }

    </style>
</head>

<body>

    <?php
    if (!isset($_GET['sku_id'])) {
        die("SKU IDが指定されていません。");
    }
    $sku_id = $_GET['sku_id'];
    $pdo = new PDO($connect, USER, PASS);
    
    // ★ 修正: color_name, color_code を取得
    $sql = $pdo->prepare("SELECT * FROM item_skus WHERE id = ?");
    $sql->execute([$sku_id]);
    $sku = $sql->fetch(PDO::FETCH_ASSOC);

    if (!$sku) {
        die("該当するSKUが見つかりません。");
    }
    $item_id = $sku['item_id'];
    
    // DBに color_code が無い場合に備えてデフォルト値（白）を設定
    $color_code_val = isset($sku['color_code']) ? $sku['color_code'] : '#FFFFFF';
    ?>

    <div class="item-box">
        <div class="item-header">
            <h1>SKU ID: <?php echo htmlspecialchars($sku_id); ?> の編集</h1>
        </div>

        <!-- ★ 修正: sku_update.php に color_name と color_code を渡す -->
        <form action="sku_update.php" method="post" enctype="multipart/form-data">

            <input type="hidden" name="sku_id" value="<?php echo htmlspecialchars($sku_id); ?>">
            <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item_id); ?>">
            <input type="hidden" name="current_image_path" value="<?php echo htmlspecialchars($sku['image_url']); ?>">

            <label for="size">サイズ</label>
            <input type="text" id="size" name="size" value="<?php echo htmlspecialchars($sku['size']); ?>"><br>

            <!-- ★ 修正: 色名とカラーコードの入力 -->
            <label for="color_name">色名（例: ブラック）</label>
            <div class="color-input-wrapper">
                <input type="text" id="color_name" name="color_name" value="<?php echo htmlspecialchars($sku['color']); ?>">
                <input type="color" id="color_code" name="color_code" value="<?php echo htmlspecialchars($color_code_val); ?>">
            </div>

            <label>現在の画像:</label>
            <img src="<?php echo htmlspecialchars($sku['image_url']); ?>" width="100" alt="SKU画像"><br>

            <label for="new_image">新しい画像をアップロード (変更する場合のみ):</label>
            <input type="file" id="new_image" name="new_sku_image" accept="image/*"><br>

            <label for="price">価格</label>
            <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($sku['price']); ?>"><br>

            <label for="stock">在庫数</label>
            <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($sku['stock_quantity']); ?>"><br>

            <label for="status">販売状況</label>
            <select name="status" id="status">
                <option value="2" <?php if ($sku['status'] == 2) echo 'selected'; ?>>販売中</option>
                <option value="1" <?php if ($sku['status'] == 1) echo 'selected'; ?>>在庫切れ</option>
                <option value="0" <?php if ($sku['status'] == 0) echo 'selected'; ?>>販売停止（削除済）</option>
            </select>
            <br>
            
            <button type="submit">更新する</button>
        </form>
    </div>

    <div class="back-links">
        <a href="item_list.php">→ 商品一覧に戻る</a><br>
        <a href="item_sku_register.php?item_id=<?php echo $item_id; ?>">（SKU登録ページに戻る）</a>
    </div>

    <!-- ★ 修正: カラーピッカー連動JS -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const colorCodeInput = document.getElementById('color_code');
        
        if(colorCodeInput) {
            // ピッカーで選んだ色をリアルタイムでプレビュー（自分自身）に反映
            colorCodeInput.addEventListener('input', (e) => {
                // （特に処理は不要だが、将来的な拡張用）
            });
        }
    });
    </script>
</body>

</html>
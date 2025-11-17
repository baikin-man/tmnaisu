<?php require "db-connect.php"; ?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKU登録・管理</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f4f4;
            padding-top: 20px;
        }

        .item-box {
            border: 2px solid #007bff;
            border-radius: 8px;
            margin: 20px auto;
            width: 90%;
            max-width: 800px;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f0f8ff;
            padding: 12px 15px;
            border-bottom: 2px solid #007bff;
        }

        .item-header h2 {
            margin: 0;
            font-size: 1.2rem;
            color: #333;
        }

        .item-header a {
            font-size: 0.9rem;
            font-weight: bold;
            text-decoration: none;
            color: #007bff;
        }

        .item-tags {
            padding: 10px 15px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .tag-badge {
            background-color: #17a2b8;
            color: #fff;
            font-size: 0.85rem;
            padding: 3px 10px;
            border-radius: 15px;
        }

        .form-box {
            padding: 15px 20px;
        }

        .form-box input[type="text"],
        .form-box input[type="number"],
        .form-box input[type="file"] {
            display: block;
            width: 95%;
            max-width: 400px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 12px;
        }

        .form-box button {
            padding: 10px 15px;
            font-weight: bold;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .sku-list {
            list-style: none;
            padding: 0 15px 15px;
            margin: 0;
        }

        .sku-item {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding: 8px 5px;
        }

        .sku-item img {
            margin-right: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .item-box h4 {
            padding: 0 15px;
            margin-top: 15px;
        }

        /* ★ カラーピッカー用のスタイル ★ */
        .color-input-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .color-input-wrapper input[type="text"] {
            width: 70%;
            margin-bottom: 0;
        }

        .color-input-wrapper input[type="color"] {
            width: 25%;
            height: 38px;
            padding: 2px;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
        }

        .color-preview {
            width: 30px;
            height: 30px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #white;
            margin-left: 10px;
        }
    </style>
</head>

<body>

    <?php
    $pdo = new PDO($connect, USER, PASS);
    $item_id_to_use = null;

    // A: SKU登録から戻ってきた、または一覧から来た場合 (GET)
    if (isset($_GET["item_id"])) {
        $item_id_to_use = $_GET["item_id"];
        $stmt_item = $pdo->prepare("SELECT name FROM items WHERE id = ?");
        $stmt_item->execute([$item_id_to_use]);
        $item = $stmt_item->fetch();

        if ($item) {
            $item_name = $item['name'];

            $stmt_tags = $pdo->prepare("SELECT t.name FROM tags t JOIN item_tags it ON t.id = it.tag_id WHERE it.item_id = ?");
            $stmt_tags->execute([$item_id_to_use]);
            $tags_display = $stmt_tags->fetchAll(PDO::FETCH_COLUMN);

            echo '<div class="item-box">';
            echo '  <div class="item-header">';
            echo '    <h2>商品情報: ' . htmlspecialchars($item_name) . ' (ID: ' . $item_id_to_use . ')</h2>';
            echo '    <a href="item_list.php">→ 商品一覧に戻る</a>';
            echo '  </div>';

            if (!empty($tags_display)) {
                echo '<div class="item-tags">';
                foreach ($tags_display as $t_name) {
                    echo '<span class="tag-badge">' . htmlspecialchars($t_name) . '</span>';
                }
                echo '</div>';
            }

            echo '<h4>登録済みSKUリスト:</h4>';

            // ★ 修正: color_code も表示
            $stmt_skus = $pdo->prepare("SELECT * FROM item_skus WHERE item_id = ?");
            $stmt_skus->execute([$item_id_to_use]);

            if ($stmt_skus->rowCount() > 0) {
                echo '<ul class="sku-list">';
                foreach ($stmt_skus as $sku) {
                    echo '<li class="sku-item">';
                    if (!empty($sku['image_url'])) {
                        echo '<img src="' . htmlspecialchars($sku['image_url']) . '" width="50">';
                    }
                    echo '<span>' . htmlspecialchars($sku['size']) . " / " . htmlspecialchars($sku['color']) . " (";
                    // ★ 修正: カラーコードをプレビュー
                    echo '<span style="background-color:'.htmlspecialchars($sku['color_code']).'; border: 1px solid #ccc; width: 12px; height: 12px; display: inline-block;"></span>';
                    echo " " . htmlspecialchars($sku['color_code']) . ") / " . htmlspecialchars($sku['price']) . "円 / 在庫: " . htmlspecialchars($sku['stock_quantity']) . "</span>";
                    echo "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p style='padding: 0 15px 15px;'>まだSKUは登録されていません。</p>";
            }
            echo '</div>';
        } else {
            echo '<p style="text-align:center;">エラー: 商品が見つかりません。</p>';
            $item_id_to_use = null;
        }

    // B: 商品名を新規登録した場合 (POST)
    } elseif (isset($_POST["name"])) {
        // (ロジックは変更なし)
        $sql = $pdo->prepare('insert into items (name) values(?)');
        $sql->execute([$_POST["name"]]);
        $item_id_to_use = $pdo->lastInsertId();
        if (isset($_POST['tags']) && is_array($_POST['tags'])) {
            $sql_tag = $pdo->prepare('INSERT INTO item_tags (item_id, tag_id) VALUES (?, ?)');
            foreach ($_POST['tags'] as $tag_id) {
                $sql_tag->execute([$item_id_to_use, $tag_id]);
            }
        }
        echo '<div class="item-box">';
        echo '  <div class="item-header" style="background: #d4edda; border-color: #28a745;">';
        echo '    <h2>商品登録完了！</h2>';
        echo '  </div>';
        echo '  <p style="padding: 15px;">商品 <strong>' . htmlspecialchars($_POST["name"]) . '</strong> を登録しました。<br>';
        if (isset($_POST['tags'])) {
            echo 'タグを ' . count($_POST['tags']) . ' 個 設定しました。<br>';
        }
        echo '続けてSKUを登録してください。</p>';
        echo '</div>';
    } else {
        echo '<p style="text-align:center;"><a href="item_id_register.php">最初から登録してください</a></p>';
    }
    ?>

    <?php if ($item_id_to_use !== null) : ?>
        <div class="item-box">
            <div class="form-box">
                <h3>SKU（サイズ・色・画像）を追加登録</h3>
                
                <!-- ★ 修正: item_sku_done.php に color_name と color_code を渡す -->
                <form action="item_sku_done.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item_id_to_use); ?>">
                    
                    サイズ <input type="text" name="size" value="M">
                    
                    <!-- ★ 修正: 色名とカラーコードの入力 -->
                    色名（例: ブラック）
                    <div class="color-input-wrapper">
                        <input type="text" name="color_name" value="white" id="colorNameInput">
                        <input type="color" name="color_code" value="#FFFFFF" id="colorCodeInput">
                    </div>

                    画像 <input type="file" name="sku_image" accept="image/*" required>
                    価格 <input type="number" name="price" value="0">
                    在庫数 <input type="number" name="stock" value="0">
                    <button type="submit">SKUを登録する</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="back-link"><a href="item_list.php">→ 商品一覧に戻る</a></div>

    <!-- ★ 修正: カラーピッカー連動JS -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const colorNameInput = document.getElementById('colorNameInput');
        const colorCodeInput = document.getElementById('colorCodeInput');

        if(colorCodeInput) {
            // カラーピッカーを変更したら、テキスト入力を #xxxxxx 形式に更新
            colorCodeInput.addEventListener('input', (e) => {
                // （プレビュー用。色名入力には連動させない）
            });
            
            // 色名入力が "black" や "red" の場合、ピッカーに反映させる
            colorNameInput.addEventListener('change', (e) => {
                // 色名からカラーコードへの自動変換は複雑なため、
                // ここではピッカーとの連動は片方向（ピッカー -> プレビュー）のみとします。
                // ユーザーが「ブラック」と入力したら、ピッカーも手動で「#000000」にしてもらう運用。
            });
        }
    });
    </script>
</body>

</html>
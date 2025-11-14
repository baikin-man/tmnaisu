<?php require "db-connect.php";?>
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
        
        /* item_list.php から持ってきたスタイル */
        .item-box {
            border: 2px solid #007bff;
            border-radius: 8px;
            margin: 20px auto;
            width: 90%;
            max-width: 800px;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
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
        .item-header h2, .item-header h3 {
            margin: 0;
            font-size: 1.2rem;
            color: #333;
        }
        .item-box h4 {
            padding: 0 15px;
            margin-top: 15px;
        }
        
        /* SKU一覧リストのスタイル */
        .sku-list { 
            list-style: none; 
            padding: 0 15px 15px 15px; /* パディング調整 */
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
        .sku-item span {
            flex-grow: 1; /* スペースを埋める */
        }
        
        /* フォーム用のスタイル */
        .form-box {
            padding: 15px 20px;
        }
        .form-box h3 {
            margin-top: 0;
        }
        .form-box input[type="text"],
        .form-box input[type="number"],
        .form-box input[type="file"] {
            display: block;
            width: 95%; /* 枠線に対して少し小さく */
            max-width: 400px;
            padding: 8px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 3px;
            margin-bottom: 12px;
        }
        .form-box button {
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
        .form-box button:hover {
            background-color: #0056b3;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <?php
        $pdo=new PDO($connect, USER, PASS);
        $item_id_to_use = null; 

        // --- ロジック分岐 ---

        // A: SKU登録から戻ってきた場合 (GET)
        if (isset($_GET["item_id"])) {
            $item_id_to_use = $_GET["item_id"];
            $stmt_item = $pdo->prepare("SELECT name FROM items WHERE id = ?");
            $stmt_item->execute([$item_id_to_use]);
            $item = $stmt_item->fetch();
            
            if ($item) {
                $item_name = $item['name'];
                
                // ★ デザイン適用 (State A) ★
                echo '<div class="item-box">';
                echo '  <div class="item-header">';
                echo '    <h2>商品情報: ' . htmlspecialchars($item_name) . ' (ID: ' . $item_id_to_use . ')</h2>';
                echo '    <a href="item_list.php">→ 商品一覧に戻る</a>';
                echo '  </div>';

                echo '<h4>登録済みSKUリスト:</h4>';
                
                $stmt_skus = $pdo->prepare("SELECT * FROM item_skus WHERE item_id = ?"); 
                $stmt_skus->execute([$item_id_to_use]);
                
                if ($stmt_skus->rowCount() > 0) {
                    echo '<ul class="sku-list">';
                    foreach ($stmt_skus as $sku) {
                        // (※論理削除のスタイル判定ロジックをここに入れると、一覧も完璧になります)
                        echo '<li class="sku-item">';
                        if (!empty($sku['image_url'])) {
                             echo '<img src="' . htmlspecialchars($sku['image_url']) . '" width="50" alt="SKU画像">';
                        }
                        echo '<span>';
                        echo htmlspecialchars($sku['size']) . " / ";
                        echo htmlspecialchars($sku['color']) . " / ";
                        echo htmlspecialchars($sku['price']) . "円 / ";
                        echo "在庫: " . htmlspecialchars($sku['stock_quantity']) . "個";
                        echo '</span>';
                        // (※ここに編集/削除ボタンを追加すると、このページからも操作できます)
                        echo "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p style='padding: 0 15px 15px;'>まだSKUは登録されていません。</p>";
                }
                echo '</div>'; // .item-box 閉じ

            } else {
                // エラー表示もボックスで囲う
                echo '<div class="item-box" style="border-color: #dc3545;">';
                echo '  <div class="item-header" style="background: #f8d7da; border-color: #dc3545;">';
                echo '    <h2>エラー</h2>';
                echo '  </div>';
                echo '  <p style="padding: 15px;">該当する商品(ID: ' . htmlspecialchars($item_id_to_use) . ')が見つかりません。</p>';
                echo '</div>';
                $item_id_to_use = null; 
            }

        // B: 商品名を新規登録した場合 (POST)
        } elseif (isset($_POST["name"])) {
            $sql=$pdo->prepare('insert into items (name) values(?)');
            $sql->execute([$_POST["name"]]);
            $item_id_to_use = $pdo->lastInsertId();

            // ★ デザイン適用 (State B) ★
            echo '<div class="item-box">';
            echo '  <div class="item-header" style="background: #d4edda; border-color: #28a745;">';
            echo '    <h2>商品登録完了！</h2>';
            echo '  </div>';
            echo '  <p style="padding: 15px;">';
            echo '商品 <strong>' . htmlspecialchars($_POST["name"]) . ' (ID: ' . $item_id_to_use . ')</strong> を登録しました。<br>';
            echo '続けて、下のフォームからSKU（サイズ・色・在庫など）を登録してください。';
            echo '  </p>';
            echo '</div>';

        // C: 初期表示
        } else {
            // ★ デザイン適用 (State C) ★
            // State C は item_id_register.php に任せる
            echo '<div class="item-box">';
            echo '  <div class="item-header"><h2>操作を選択してください</h2></div>';
            echo '  <p style="padding: 15px;">';
            echo '  <a href="item_id_register.php">→ 新しい商品を登録する</a><br>';
            echo '  <a href="item_list.php">→ 登録済み商品一覧を見る</a>';
            echo '  </p>';
            echo '</div>';
        }
    ?>

    <?php
    // --- SKUフォームの表示 ---
    // (A) または (B) の場合のみ
    if ($item_id_to_use !== null) :
    ?>
    
    <div class="item-box">
        <div class="form-box">
            <h3>
                SKU（サイズ・色・画像）を
                <?php echo (isset($_GET["item_id"])) ? "追加" : "新規"; ?>
                登録
            </h3>
            
            <form action="item_sku_done.php" method="post" enctype="multipart/form-data">
                
                <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item_id_to_use); ?>">

                サイズ
                <input type="text" name="size" value="M"><br>
                色
                <input type="text" name="color" value="white"><br>
                
                画像アップロード
                <input type="file" name="sku_image" accept="image/*" required><br> 価格
                <input type="number" name="price" value="0"><br>
                在庫数
                <input type="number" name="stock" value="0"><br>
                <button type="submit">SKUを登録する</button>
            </form>
        </div>
    </div>

    <?php
    endif;
    ?>
    
    <div class="back-link">
        <a href="item_list.php">→ 商品一覧に戻る</a>
    </div>

</body>
</html>
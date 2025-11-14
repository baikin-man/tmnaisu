<?php require "db-connect.php";?>
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
            max-width: 600px; /* SKU編集フォーム用に幅を調整 */
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
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
        form input[type="file"] {
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
    </style>
</head>
<body>
    
    <?php
        // --- PHPロジック (変更なし) ---
        if (!isset($_GET['sku_id'])) {
            die("SKU IDが指定されていません。");
        }
        $sku_id = $_GET['sku_id'];
        $pdo = new PDO($connect, USER, PASS);
        $sql = $pdo->prepare("SELECT * FROM item_skus WHERE id = ?");
        $sql->execute([$sku_id]);
        $sku = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$sku) {
            die("該当するSKUが見つかりません。");
        }
        $item_id = $sku['item_id'];
    ?>
    
    <div class="item-box">
        <div class="item-header">
            <h1>SKU ID: <?php echo htmlspecialchars($sku_id); ?> の編集</h1>
        </div>

        <form action="sku_update.php" method="post" enctype="multipart/form-data">
            
            <input type="hidden" name="sku_id" value="<?php echo htmlspecialchars($sku_id); ?>">
            <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item_id); ?>">
            <input type="hidden" name="current_image_path" value="<?php echo htmlspecialchars($sku['image_url']); ?>">

            <label for="size">サイズ</label>
            <input type="text" id="size" name="size" value="<?php echo htmlspecialchars($sku['size']); ?>"><br>
            
            <label for="color">色</label>
            <input type="text" id="color" name="color" value="<?php echo htmlspecialchars($sku['color']); ?>"><br>
            
            <label>現在の画像:</label>
            <img src="<?php echo htmlspecialchars($sku['image_url']); ?>" width="100" alt="SKU画像"><br>
            
            <label for="new_image">新しい画像をアップロード (変更する場合のみ):</label>
            <input type="file" id="new_image" name="new_sku_image" accept="image/*"><br> 

            <label for="price">価格</label>
            <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($sku['price']); ?>"><br>
            
            <label for="stock">在庫数</label>
            <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($sku['stock_quantity']); ?>"><br>
            
            <button type="submit">更新する</button>
        </form>
    </div>
    
    <div class="back-links">
        <a href="item_list.php">→ 商品一覧に戻る</a><br>
        <a href="item_sku_register.php?item_id=<?php echo $item_id; ?>">（SKU登録ページに戻る）</a>
    </div>

</body>
</html>
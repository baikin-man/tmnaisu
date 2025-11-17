<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品マスタ登録</title>
    
    <style>
        body { 
            font-family: sans-serif; 
            background-color: #f4f4f4; /* 背景色を少しグレーに */
            padding-top: 20px;
        }
        
        /* item_list.php の .item-box スタイルを参考に */
        .register-box {
            border: 2px solid #007bff; /* 青い枠線 */
            border-radius: 8px;
            margin: 20px auto;
            padding: 0; /* 内側のパディングは0に */
            width: 90%;
            max-width: 500px; /* 最大幅を指定 */
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            overflow: hidden; /* ヘッダーの角を丸めるため */
        }
        
        /* item_list.php の .item-header スタイルを参考に */
        .register-header {
            background: #f0f8ff; /* 薄い青色のヘッダー */
            padding: 12px 15px;
        }
        .register-header h2 {
            margin: 0;
            font-size: 1.2rem;
            color: #333;
        }

        form {
            padding: 20px; /* フォーム自体にパディング */
            display: flex; /* Flexboxで横並び */
            gap: 10px; /* 要素間の隙間 */
        }

        input[type="text"] {
            flex-grow: 1; /* 残りのスペースをすべて使う */
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px 15px;
            font-size: 1rem;
            font-weight: bold;
            color: #fff;
            background-color: #007bff; /* メインの青色 */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        button:hover {
            background-color: #0056b3; /* ホバーで少し濃く */
        }
    </style>
</head>
<body>

    <div class="register-box">
        
        <div class="register-header">
            <h2>ステップ1： 新しい商品を登録</h2>
        </div>
        
        <form action="item_sku_register.php" method="post">
            <input type="text" name="name" placeholder="商品名を入力 (例: ロゴTシャツ)">
            <button type="submit">商品を登録</button>
        </form>
        
    </div>
    
    <div style="text-align: center; margin-top: 20px;">
        <a href="item_list.php">→ 登録済み商品一覧に戻る</a>
    </div>

</body>
</html>
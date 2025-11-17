<?php require "db-connect.php";?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品マスタ登録</title>
    
    <style>
        body { 
            font-family: sans-serif; 
            background-color: #f4f4f4; 
            padding-top: 20px;
        }
        .register-box {
            border: 2px solid #007bff; 
            border-radius: 8px;
            margin: 20px auto;
            padding: 0; 
            width: 90%;
            max-width: 500px; 
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            overflow: hidden; 
        }
        .register-header {
            background: #f0f8ff; 
            padding: 12px 15px;
        }
        .register-header h2 {
            margin: 0;
            font-size: 1.2rem;
            color: #333;
        }
        form {
            padding: 20px; 
            display: flex; 
            flex-direction: column; /* 縦並びに変更 */
            gap: 15px; 
        }
        .input-group {
            display: flex;
            gap: 10px;
        }
        input[type="text"] {
            flex-grow: 1; 
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 12px 15px;
            font-size: 1rem;
            font-weight: bold;
            color: #fff;
            background-color: #007bff; 
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        button:hover {
            background-color: #0056b3; 
        }

        /* タグ選択エリアのスタイル */
        .tag-label-title {
            font-weight: bold;
            font-size: 0.9rem;
            margin-bottom: 5px;
            color: #555;
        }
        .tag-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 10px;
            background-color: #fafafa;
            border: 1px solid #eee;
            border-radius: 5px;
        }
        .tag-label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 0.9rem;
            background: #fff;
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 20px;
            transition: 0.2s;
            user-select: none;
        }
        .tag-label:hover {
            background: #e9ecef;
        }
        /* チェックボックスは見えなくても良いが、今回は残す */
        .tag-label input {
            margin-right: 5px;
            transform: scale(1.1);
        }
        /* チェックされた時のスタイル */
        .tag-label:has(input:checked) {
            background-color: #e7f1ff;
            border-color: #007bff;
            color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="register-box">
        
        <div class="register-header">
            <h2>ステップ1： 新しい商品を登録</h2>
        </div>
        
        <form action="item_sku_register.php" method="post">
            <!-- 商品名入力 -->
            <div>
                <div class="tag-label-title">商品名</div>
                <div class="input-group">
                    <input type="text" name="name" placeholder="例: ロゴTシャツ" required>
                </div>
            </div>

            <!-- タグ選択 -->
            <div>
                <div class="tag-label-title">タグ選択 (複数選択可)</div>
                <div class="tag-container">
                    <?php
                    try {
                        $pdo = new PDO($connect, USER, PASS);
                        // タグ一覧を取得
                        $tags = $pdo->query("SELECT * FROM tags ORDER BY id ASC");
                        
                        if ($tags->rowCount() > 0) {
                            foreach ($tags as $tag) {
                                echo '<label class="tag-label">';
                                echo '<input type="checkbox" name="tags[]" value="' . $tag['id'] . '">';
                                echo htmlspecialchars($tag['name']);
                                echo '</label>';
                            }
                        } else {
                            echo '<p style="font-size:0.8rem; color:#888;">タグが登録されていません</p>';
                        }
                    } catch (PDOException $e) {
                        echo "DBエラー: " . $e->getMessage();
                    }
                    ?>
                </div>
            </div>

            <button type="submit">商品を登録して次へ</button>
        </form>
        
    </div>
    
    <div style="text-align: center; margin-top: 20px;">
        <a href="item_list.php">→ 登録済み商品一覧に戻る</a>
    </div>

</body>
</html>
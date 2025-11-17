<?php require "db-connect.php"; ?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タグ管理</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f4f4;
            padding-top: 20px;
        }

        /* 登録フォームのスタイル */
        .register-box {
            border: 2px solid #28a745;
            /* 緑色の枠線 */
            border-radius: 8px;
            margin: 20px auto;
            padding: 0;
            width: 90%;
            max-width: 500px;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .register-header {
            background: #d4edda;
            padding: 12px 15px;
            color: #155724;
        }

        .register-header h2 {
            margin: 0;
            font-size: 1.2rem;
        }

        form {
            padding: 20px;
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
            padding: 10px 15px;
            font-size: 1rem;
            font-weight: bold;
            color: #fff;
            background-color: #28a745;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
            white-space: nowrap;
        }

        button:hover {
            background-color: #218838;
        }

        /* タグ一覧のスタイル */
        .list-box {
            max-width: 500px;
            margin: 0 auto 40px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .list-header {
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
            font-weight: bold;
            color: #555;
        }

        .tag-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .tag-item:last-child {
            border-bottom: none;
        }

        .tag-name {
            font-weight: bold;
            color: #333;
        }

        .tag-id {
            color: #999;
            font-size: 0.8rem;
            margin-right: 10px;
        }

        .delete-btn {
            background-color: #dc3545;
            font-size: 0.85rem;
            padding: 6px 10px;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .message {
            max-width: 500px;
            margin: 0 auto 15px;
            padding: 10px 15px;
            border-radius: 5px;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .back-links {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 40px;
        }

        .back-links a {
            margin: 0 10px;
            color: #007bff;
            text-decoration: none;
        }

        .back-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <?php
    // エラー・成功メッセージの表示
    if (isset($_GET['error'])) {
        echo '<div class="message error">' . htmlspecialchars($_GET['error']) . '</div>';
    }
    if (isset($_GET['success'])) {
        echo '<div class="message success">' . htmlspecialchars($_GET['success']) . '</div>';
    }
    ?>

    <!-- 新規登録フォーム -->
    <div class="register-box">
        <div class="register-header">
            <h2>新規タグ登録</h2>
        </div>
        <form action="tag_process.php" method="post">
            <input type="hidden" name="action" value="add">
            <input type="text" name="name" placeholder="タグ名 (例: セール, おすすめ)" required>
            <button type="submit">追加</button>
        </form>
    </div>

    <!-- 登録済みタグ一覧 -->
    <div class="list-box">
        <div class="list-header">登録済みタグ一覧</div>

        <?php
        try {
            $pdo = new PDO($connect, USER, PASS);
            $sql = "SELECT * FROM tags ORDER BY id DESC";
            $stmt = $pdo->query($sql);

            if ($stmt->rowCount() > 0) {
                foreach ($stmt as $row) {
                    echo '<div class="tag-item">';
                    echo '  <div>';
                    echo '    <span class="tag-id">ID:' . $row['id'] . '</span>';
                    echo '    <span class="tag-name">' . htmlspecialchars($row['name']) . '</span>';
                    echo '  </div>';

                    echo '  <form action="tag_process.php" method="post" style="padding:0; margin:0;" onsubmit="return confirm(\'本当に削除しますか？\');">';
                    echo '    <input type="hidden" name="action" value="delete">';
                    echo '    <input type="hidden" name="id" value="' . $row['id'] . '">';
                    echo '    <button type="submit" class="delete-btn">削除</button>';
                    echo '  </form>';
                    echo '</div>';
                }
            } else {
                echo '<p style="color:#777;">登録されているタグはありません。</p>';
            }
        } catch (PDOException $e) {
            echo "DBエラー: " . $e->getMessage();
        }
        ?>
    </div>

    <div class="back-links">
        <a href="item_id_register.php">→ 商品登録画面へ戻る</a>
        <a href="item_list.php">→ 商品一覧へ戻る</a>
    </div>

</body>

</html>
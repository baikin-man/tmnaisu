<?php require "db-connect.php"; ?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品編集</title>
    <style>
        /* item_id_register.php と同じスタイル */
        body {
            font-family: sans-serif;
            background-color: #f4f4f4;
            padding-top: 20px;
        }

        .register-box {
            border: 2px solid #ffc107;
            border-radius: 8px;
            margin: 20px auto;
            width: 90%;
            max-width: 500px;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .register-header {
            background: #fff3cd;
            padding: 12px 15px;
        }

        .register-header h2 {
            margin: 0;
            font-size: 1.2rem;
            color: #856404;
        }

        form {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
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
        }

        .tag-label:hover {
            background: #e9ecef;
        }

        .tag-label:has(input:checked) {
            background-color: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            padding: 12px 15px;
            font-size: 1rem;
            font-weight: bold;
            color: #212529;
            background-color: #ffc107;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #e0a800;
        }
    </style>
</head>

<body>

    <?php
    if (!isset($_GET['id'])) {
        die("商品IDが指定されていません。");
    }
    $item_id = $_GET['id'];
    $pdo = new PDO($connect, USER, PASS);

    // 商品情報の取得
    $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->execute([$item_id]);
    $item = $stmt->fetch();

    if (!$item) {
        die("商品が見つかりません。");
    }

    // 現在設定されているタグIDを取得
    $stmt_tags = $pdo->prepare("SELECT tag_id FROM item_tags WHERE item_id = ?");
    $stmt_tags->execute([$item_id]);
    $current_tags = $stmt_tags->fetchAll(PDO::FETCH_COLUMN); // 配列として取得 [1, 3, 5]
    ?>

    <div class="register-box">
        <div class="register-header">
            <h2>商品編集 (ID: <?php echo $item_id; ?>)</h2>
        </div>

        <form action="item_update.php" method="post">
            <input type="hidden" name="id" value="<?php echo $item_id; ?>">

            <!-- 商品名 -->
            <div>
                <div style="font-weight:bold; margin-bottom:5px;">商品名</div>
                <input type="text" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required>
            </div>

            <!-- タグ選択 -->
            <div>
                <div style="font-weight:bold; margin-bottom:5px;">タグ選択</div>
                <div class="tag-container">
                    <?php
                    // 全タグを取得
                    $all_tags = $pdo->query("SELECT * FROM tags ORDER BY id ASC");
                    foreach ($all_tags as $tag) {
                        // 現在のタグに含まれていれば checked
                        $checked = in_array($tag['id'], $current_tags) ? 'checked' : '';
                        echo '<label class="tag-label">';
                        echo '<input type="checkbox" name="tags[]" value="' . $tag['id'] . '" ' . $checked . '>';
                        echo htmlspecialchars($tag['name']);
                        echo '</label>';
                    }
                    ?>
                </div>
            </div>

            <button type="submit">更新する</button>
        </form>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <a href="item_list.php">→ 商品一覧に戻る</a>
    </div>

</body>

</html>
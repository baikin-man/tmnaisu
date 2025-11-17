<?php require "db-connect.php"; ?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- アイコン表示用にFontAwesomeを追加 -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <title>商品・SKU一覧</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f4f4;
            padding-top: 20px;
            margin: 0 10px;
        }

        h1 {
            text-align: center;
        }

        .header-link {
            display: block;
            text-align: center;
            margin-bottom: 15px;
            font-size: 1.1rem;
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
            border-bottom: 1px solid #ddd;
        }

        .item-header h2 {
            margin: 0;
            font-size: 1.1rem;
            color: #333;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* 閲覧数バッジのスタイル */
        .view-count-badge {
            font-size: 0.85rem;
            color: #555;
            font-weight: normal;
            background: #e9ecef;
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
        }

        .view-count-badge i {
            margin-right: 4px;
            color: #007bff;
        }

        /* 編集リンク群のスタイル */
        .header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .header-actions a {
            font-size: 0.85rem;
            font-weight: bold;
            text-decoration: none;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .btn-edit-item {
            background-color: #ffc107;
            color: #333;
            border: 1px solid #e0a800;
        }

        .btn-add-sku {
            color: #007bff;
            border: 1px solid #007bff;
        }

        .btn-add-sku:hover {
            background-color: #e7f1ff;
        }

        .tag-list {
            padding: 8px 15px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .tag-badge {
            background-color: #17a2b8;
            color: #fff;
            font-size: 0.8rem;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .sku-list {
            list-style: none;
            padding: 0 15px 15px;
            margin: 0;
        }

        .sku-item {
            display: flex;
            flex-wrap: wrap;
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
            flex-grow: 1;
            min-width: 200px;
        }

        .sku-buttons {
            margin-left: auto;
            padding-left: 10px;
        }

        .sku-buttons a {
            margin-left: 5px;
            border: 1px solid #ccc;
            padding: 2px 5px;
            text-decoration: none;
            border-radius: 3px;
            font-size: 0.85rem;
        }
    </style>
</head>

<body>
    <a href="tag_register.php">タグ管理</a>
    <h1>商品・SKU一覧</h1>
    <a href="item_id_register.php" class="header-link">→ 新しい商品を登録する</a>
    <hr style="width: 90%; max-width: 800px; margin: auto;">

    <?php
    $pdo = new PDO($connect, USER, PASS);

    // 1. SKU取得
    $skus_stmt = $pdo->query("SELECT * FROM item_skus ORDER BY item_id, id");
    $skus_by_item_id = [];
    foreach ($skus_stmt as $sku) {
        $skus_by_item_id[$sku['item_id']][] = $sku;
    }

    // 2. タグ取得
    $tags_stmt = $pdo->query("SELECT it.item_id, t.name FROM item_tags it JOIN tags t ON it.tag_id = t.id ORDER BY it.item_id, t.id");
    $tags_by_item_id = [];
    foreach ($tags_stmt as $tag_row) {
        $tags_by_item_id[$tag_row['item_id']][] = $tag_row['name'];
    }

    // 3. 閲覧数取得 (新規追加)
    // item_view_historyテーブルを集計して、item_idごとの閲覧回数を取得
    $views_stmt = $pdo->query("SELECT item_id, COUNT(*) as cnt FROM item_view_history GROUP BY item_id");
    $views_by_item_id = [];
    foreach ($views_stmt as $view_row) {
        $views_by_item_id[$view_row['item_id']] = $view_row['cnt'];
    }

    // 4. 商品取得
    $items_stmt = $pdo->query("SELECT * FROM items ORDER BY id DESC");

    foreach ($items_stmt as $item) {
        $item_id = $item['id'];
        $item_name = $item['name'];

        // 閲覧数を取得 (存在しなければ0)
        $view_count = isset($views_by_item_id[$item_id]) ? $views_by_item_id[$item_id] : 0;
    ?>

        <div class="item-box">
            <div class="item-header">
                <h2>
                    <?php echo htmlspecialchars($item_name); ?> (ID: <?php echo $item_id; ?>)
                    <!-- 閲覧数表示エリア -->
                    <span class="view-count-badge">
                        <i class="far fa-eye"></i> <?php echo number_format($view_count); ?> views
                    </span>
                </h2>
                <div class="header-actions">
                    <a href="item_edit.php?id=<?php echo $item_id; ?>" class="btn-edit-item">商品名・タグ編集</a>
                    <a href="item_sku_register.php?item_id=<?php echo $item_id; ?>" class="btn-add-sku">＋SKU追加</a>
                </div>
            </div>

            <!-- タグ表示 -->
            <?php if (isset($tags_by_item_id[$item_id])): ?>
                <div class="tag-list">
                    <?php foreach ($tags_by_item_id[$item_id] as $t_name): ?>
                        <span class="tag-badge"><?php echo htmlspecialchars($t_name); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- SKUリスト -->
            <?php if (isset($skus_by_item_id[$item_id])): ?>
                <ul class="sku-list">
                    <?php foreach ($skus_by_item_id[$item_id] as $sku):
                        $style = ($sku['status'] == 0) ? 'color:#aaa;' : '';
                        $status_txt = ($sku['status'] == 0) ? '[削除済]' : ($sku['status'] == 1 ? '[在庫切れ]' : '');
                    ?>
                        <li class="sku-item" style="<?php echo $style; ?>">
                            <img src="<?php echo htmlspecialchars($sku['image_url']); ?>" width="50">
                            <span>
                                ID:<?php echo $sku['id']; ?> / <?php echo htmlspecialchars($sku['size']); ?> / <?php echo htmlspecialchars($sku['color']); ?> / ¥<?php echo number_format($sku['price']); ?> / 在庫:<?php echo $sku['stock_quantity']; ?> <?php echo $status_txt; ?>
                            </span>
                            <div class="sku-buttons">
                                <a href="sku_edit.php?sku_id=<?php echo $sku['id']; ?>">編集</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p style="padding: 10px 15px;">SKUなし</p>
            <?php endif; ?>
        </div>
    <?php } ?>

</body>

</html>
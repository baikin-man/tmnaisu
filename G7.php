<?php 
session_start();
require 'db-connect.php';

// DB接続
if (!isset($pdo)) {
    try {
        $pdo = new PDO($connect, USER, PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "DB接続エラー: " . htmlspecialchars($e->getMessage());
        exit();
    }
}

// --- 検索条件・ソート条件の取得 ---
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : ''; 
$tag = isset($_GET['tag']) ? trim($_GET['tag']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'new'; // デフォルトは新着順

// --- SQL構築 ---
// 1. item_skus から画像(image)を取得
// 2. item_skus から価格(price)を取得
// 3. item_view_history から閲覧数(view_count)を集計して取得
// これらをサブクエリで itemsテーブル(i.*) に結合して取得します。

$sql = "
    SELECT 
        i.*, 
        (
            SELECT s.image_url 
            FROM item_skus s 
            WHERE s.item_id = i.id AND s.status = 2 
            ORDER BY s.id ASC 
            LIMIT 1
        ) AS image,
        (
            SELECT s.price 
            FROM item_skus s 
            WHERE s.item_id = i.id AND s.status = 2 
            ORDER BY s.id ASC 
            LIMIT 1
        ) AS price,
        (
            SELECT COUNT(*) 
            FROM item_view_history v 
            WHERE v.item_id = i.id
        ) AS view_count
    FROM items i 
    WHERE i.status = 1 -- ★ 修正: 販売中(status=1) の商品のみ検索
";
$params = [];

// 1. キーワードがある場合
if (!empty($keyword)) {
    $sql .= " AND i.name LIKE ?";
    $params[] = "%{$keyword}%";
}

// 2. タグがある場合
if (!empty($tag)) {
    $sql .= " AND i.id IN (
                SELECT item_id 
                FROM item_tags 
                INNER JOIN tags ON item_tags.tag_id = tags.id 
                WHERE tags.name = ?
              )";
    $params[] = $tag;
}

// 3. 並び替え (ソート)
// view_count (閲覧数) で並び替える処理
switch ($sort) {
    case 'popular': // 人気順 (閲覧数が多い順)
        // 閲覧数(降順) -> 同じなら新しい順
        $sql .= " ORDER BY view_count DESC, i.id DESC";
        break;
    case 'price_asc': // 価格が安い順
        $sql .= " ORDER BY price ASC, i.id DESC";
        break;
    case 'price_desc': // 価格が高い順
        $sql .= " ORDER BY price DESC, i.id DESC";
        break;
    default: // 'new' (新着順)
        $sql .= " ORDER BY i.id DESC";
        break;
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "データ取得エラー: " . htmlspecialchars($e->getMessage());
    exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link rel="stylesheet" href="header2.css">
    
    <!-- ★ 修正: G7.css を読み込む (パスは環境に合わせてください) -->
    <link rel="stylesheet" href="./css/G7.css"> 
    
    <title>ZeZe | 商品一覧</title>
    
    <!-- ★ 修正: styleタグを削除 -->

</head>
<body>
    <?php require 'header2.php'; ?>

    <div class="container">
        <div class="search-result-header">
            <div class="header-left">
                <h1>商品一覧</h1>
                <div class="search-conditions">
                    検索条件: 
                    <?php if(empty($keyword) && empty($tag)) echo "すべて"; ?>
                    
                    <?php if(!empty($tag)): ?>
                        <span class="tag-label"><?= htmlspecialchars($tag) ?></span>
                    <?php endif; ?>
                    
                    <?php if(!empty($keyword)): ?>
                        キーワード「<?= htmlspecialchars($keyword) ?>」
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- ソート機能 -->
            <div class="sort-box">
                <form name="sortForm" method="get">
                    <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                    <input type="hidden" name="tag" value="<?= htmlspecialchars($tag) ?>">
                    
                    <select name="sort" onchange="document.sortForm.submit()" class="sort-select">
                        <option value="new" <?= $sort == 'new' ? 'selected' : '' ?>>新着順</option>
                        <option value="popular" <?= $sort == 'popular' ? 'selected' : '' ?>>人気順</option>
                        <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>価格が安い順</option>
                        <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>価格が高い順</option>
                    </select>
                </form>
            </div>
        </div>

        <?php if (count($results) > 0): ?>
            <div class="product-grid">
                <?php foreach ($results as $item): ?>
                    <!-- ★ 修正: リンク先を G3.php に -->
                    <a href="G3.php?id=<?= $item['id'] ?>" class="product-card">
                        <div class="product-img-wrapper">
                            <?php if (!empty($item['image'])): ?>
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-img">
                            <?php else: ?>
                                <span class="no-image-text">No Image</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <p class="product-name"><?= htmlspecialchars($item['name']) ?></p>
                            <p class="product-price">¥<?= number_format($item['price']) ?></p>
                            <!-- ★ 修正: 閲覧数を表示 (G7.css側で .product-views スタイルを定義してください) -->
                            <div class="product-views">
                                <i class="far fa-eye"></i> <?= number_format($item['view_count']) ?> views
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-result">
                <p>条件に一致する商品は見つかりませんでした。</p>
                <a href="G2.php" style="color:#007bff; text-decoration:underline;">ホームに戻る</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
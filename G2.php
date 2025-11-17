<?php 
session_start();
require 'db-connect.php';

// --- データ取得ロジック ---
// DB接続
try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "DB接続エラー: " . htmlspecialchars($e->getMessage());
    exit();
}

// 共通のサブクエリ定義
// item_skusテーブルから status=2 の「画像」と「価格」を1つずつ取得します
// i.* の後にこれらを指定することで、items.price (0円) を上書きします
$skuSubQueries = "
    (SELECT s.image_url FROM item_skus s WHERE s.item_id = i.id AND s.status = 2 ORDER BY s.id ASC LIMIT 1) AS image,
    (SELECT s.price FROM item_skus s WHERE s.item_id = i.id AND s.status = 2 ORDER BY s.id ASC LIMIT 1) AS price
";

// 1. 新着商品 (IDの降順で最新4件)
$sql_new = "SELECT i.*, $skuSubQueries FROM items i WHERE i.status = 1 ORDER BY i.id DESC LIMIT 4"; // ★ status=1 (販売中) を追加
$stmt_new = $pdo->query($sql_new);
$new_items = $stmt_new->fetchAll(PDO::FETCH_ASSOC);

// 2. 人気商品 (閲覧数が多い順で4件)
// ★ 修正: RAND() ではなく item_view_history を集計した人気順に変更
$sql_pop = "
    SELECT i.*, $skuSubQueries, COUNT(v.item_id) as view_count
    FROM items i
    LEFT JOIN item_view_history v ON i.id = v.item_id
    WHERE i.status = 1
    GROUP BY i.id
    ORDER BY view_count DESC, i.id DESC
    LIMIT 4
";
$stmt_pop = $pdo->query($sql_pop);
$pop_items = $stmt_pop->fetchAll(PDO::FETCH_ASSOC);

// 3. 閲覧履歴 (item_view_historyテーブルを利用)
$history_items = [];
// ログインしている場合のみDBから履歴を取得
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $sql_hist = "
        SELECT i.*, $skuSubQueries
        FROM items i
        INNER JOIN (
            SELECT item_id, MAX(viewed_at) as latest_view
            FROM item_view_history
            WHERE user_id = ?
            GROUP BY item_id
            ORDER BY latest_view DESC
            LIMIT 4
        ) h ON i.id = h.item_id
        WHERE i.status = 1 -- ★ 閲覧履歴でも販売中のもののみ表示
        ORDER BY h.latest_view DESC
    ";
    
    $stmt_hist = $pdo->prepare($sql_hist);
    $stmt_hist->execute([$user_id]);
    $history_items = $stmt_hist->fetchAll(PDO::FETCH_ASSOC);
}

// ★ 4. 全てのタグを取得
$sql_tags = "SELECT name FROM tags ORDER BY id ASC";
$stmt_tags = $pdo->query($sql_tags);
$all_tags = $stmt_tags->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <!-- 共通ヘッダーCSS -->
    <link rel="stylesheet" href="header2.css">
    
    <!-- ★ 修正: G2.css のパス確認 -->
    <!-- もし G2.css が 'css' フォルダの中にある場合は './css/G2.css' にしてください -->
    <link rel="stylesheet" href="./css/G2.css"> 
    
    <title>ZeZe | ホーム</title>
</head>

<body>

    <?php require 'header2.php'; ?>

    <div class="container">

        <!-- バナーエリア -->
        <div class="banner">
            <h2>Season Collection 2024</h2>
        </div>

        <!-- カテゴリリンク (クリックでG7.phpへタグ指定で遷移) -->
        <div class="category-nav">
            <a href="G7.php?tag=メンズ" class="cat-btn mens">
                <span>MEN'S</span>
            </a>
            <a href="G7.php?tag=レディース" class="cat-btn ladies">
                <span>LADIES'</span>
            </a>
            <a href="G7.php?tag=キッズ" class="cat-btn kids">
                <span>KIDS'</span>
            </a>
        </div>

        <!-- ★ 5. 全タグリスト表示エリア -->
        <?php if (!empty($all_tags)): ?>
        <div class="tag-list-nav">
            <h4>タグから探す</h4>
            <div class="tags">
                <?php foreach ($all_tags as $tag): ?>
                    <a href="G7.php?tag=<?= htmlspecialchars($tag['name']) ?>" class="tag-btn">
                        <?= htmlspecialchars($tag['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>


        <!-- 人気商品エリア -->
        <h3 class="section-title">Popular Items / 人気商品</h3>
        <div class="product-grid">
            <?php foreach ($pop_items as $item): ?>
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
                        <!-- item_skusから取得したpriceを表示 -->
                        <p class="product-price">¥<?= number_format($item['price']) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- 新着商品エリア -->
        <h3 class="section-title">New Arrivals / 新着商品</h3>
        <div class="product-grid">
            <?php foreach ($new_items as $item): ?>
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
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- 閲覧履歴エリア (データがある場合のみ表示) -->
        <?php if (!empty($history_items)): ?>
            <h3 class="section-title">Browsing History / 閲覧履歴</h3>
            <div class="product-grid">
                <?php foreach ($history_items as $item): ?>
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
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>
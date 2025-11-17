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
$sql_new = "SELECT i.*, $skuSubQueries FROM items i ORDER BY i.id DESC LIMIT 4";
$stmt_new = $pdo->query($sql_new);
$new_items = $stmt_new->fetchAll(PDO::FETCH_ASSOC);

// 2. 人気商品 (ランダムで4件表示)
$sql_pop = "SELECT i.*, $skuSubQueries FROM items i ORDER BY RAND() LIMIT 4";
$stmt_pop = $pdo->query($sql_pop);
$pop_items = $stmt_pop->fetchAll(PDO::FETCH_ASSOC);

// 3. 閲覧履歴 (item_view_historyテーブルを利用)
$history_items = [];
// ログインしている場合のみDBから履歴を取得
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // SQL解説:
    // サブクエリ(h)で item_view_history からユーザーの閲覧履歴を item_id ごとにグループ化し、
    // 最新の閲覧日時(MAX(viewed_at))を取得して、その降順で上位4件を絞り込みます。
    // その結果(h)と itemsテーブル(i)を結合して商品情報を取得します。
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
        ORDER BY h.latest_view DESC
    ";

    $stmt_hist = $pdo->prepare($sql_hist);
    $stmt_hist->execute([$user_id]);
    $history_items = $stmt_hist->fetchAll(PDO::FETCH_ASSOC);
}
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


            <div class='content2'>
                <div class='search'>
                    <h3>探す</h3>
                    <ul>
                        <li><a href=''></a></li>
                        <li><a href=''></a></li>
                        <li><a href=''></a></li>
                    </ul>
                </div>
                <div class='search'>
                    <h3>カテゴリー</h3>
                    <ul>
                        <li><a href=''></a></li>
                        <li><a href=''></a></li>
                        <li><a href=''></a></li>
                    </ul>
                </div>
            </div>
            
<div class="genre-box">
    <a href="#mens" class="genre">メンズ</a>
    <a href="#ladies" class="genre">レディース</a>
    <a href="#kids" class="genre">キッズ</a>
    <a href="#tops" class="genre">トップス</a>
    <a href="#bottoms" class="genre">ボトムス</a>
    <a href="#shirt" class="genre">シャツ</a>
    <a href="#denim" class="genre">デニム</a>
    <a href="#denim-pants" class="genre">デニムパンツ</a>
    <a href="#denim-jacket" class="genre">デニムジャケット</a>
</div>

=======
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
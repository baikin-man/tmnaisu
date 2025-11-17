<?php session_start(); ?>
<?php require 'db-connect.php'; ?>
<?php
// --- 1. ログインチェック ---
if (!isset($_SESSION['user_id'])) {
    header('Location: G1.php');
    exit;
}

// --- 2. 人気商品（おすすめ商品）を取得 ---
$popular_items = [];
try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql_popular = "
        SELECT
            h.item_id,
            COUNT(h.item_id) AS view_count,
            i.name AS item_name,
            (SELECT s.price FROM item_skus s WHERE s.item_id = h.item_id AND s.status = 2 ORDER BY s.id ASC LIMIT 1) AS price,
            (SELECT s.image_url FROM item_skus s WHERE s.item_id = h.item_id AND s.status = 2 ORDER BY s.id ASC LIMIT 1) AS image_url
        FROM
            item_view_history h
        JOIN
            items i ON h.item_id = i.id
        GROUP BY
            h.item_id, i.name
        HAVING
            price IS NOT NULL AND image_url IS NOT NULL
        ORDER BY
            view_count DESC
        LIMIT 4
    ";

    $stmt_popular = $pdo->prepare($sql_popular);
    $stmt_popular->execute();
    $popular_items = $stmt_popular->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // (エラーが起きてもサンキューページは表示する)
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">

    <title>購入確定画面 | ZeZe</title>

    <!-- ★ 外部CSSの読み込み ★ -->
    <link rel="stylesheet" href="header2.css"> <!-- ヘッダーCSS -->
    <link rel="stylesheet" href="./css/G9.css"> <!-- このページ専用CSS -->

    <!-- ★ 固定ヘッダー用の余白 ★ -->
    <style>
        body {
            padding-top: 115px;
        }
    </style>
</head>

<body>
    <?php require 'header2.php'; ?>

    <div class="complete-box">
        購入が確定しました
    </div>

    <a href="G2.php" class="btn-continue">買い物を続ける</a>

    <div class="recommend-title">おすすめの商品はこちら</div>

    <div class="product-list">
        <?php foreach ($popular_items as $item): ?>
            <a href="G3.php?id=<?php echo $item['item_id']; ?>" class="product">
                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                <div class="price"><?php echo htmlspecialchars($item['item_name']); ?></div>
            </a>
        <?php endforeach; ?>
    </div>
</body>

</html>
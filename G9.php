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

    // ★ 修正: JOIN 時に i.status = 1 (販売中) のみを対象にする
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
            items i ON h.item_id = i.id AND i.status = 1 
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
            /* G9.css 側で padding-top を指定するため、ここは削除してもOK */
            padding-top: 115px; 
        }
    </style>
</head>

<body>
    <?php require 'header2.php';?>

    <!-- ★ 修正: G8.css と同じ .main で囲う -->
    <div class="main">
        <div class="complete-box">
            <i class="fas fa-check-circle"></i>
            購入が確定しました
        </div>

        <a href="G2.php" class="btn-continue">買い物を続ける</a>

        <div class="recommend-title">おすすめの商品はこちら</div>

        <!-- ★ 修正: G2.php と同じ .product-grid 構造に変更 -->
        <div class="product-grid">
            <?php foreach ($popular_items as $item): ?>
                <a href="G3.php?id=<?php echo $item['item_id']; ?>" class="product-card">
                    <div class="product-img-wrapper">
                        <?php if (!empty($item['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>" class="product-img">
                        <?php else: ?>
                            <span class="no-image-text">No Image</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <p class="product-name"><?php echo htmlspecialchars($item['item_name']); ?></p>
                        <p class="product-price">¥<?php echo number_format($item['price']); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
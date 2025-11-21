<?php session_start(); ?>
<?php require 'db-connect.php'; ?>
<?php
// --- 1. ログインチェック ---
if (!isset($_SESSION['user_id'])) {
    header('Location: G1.php'); // ログインページをG1.phpに修正
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_items = [];
$total_price = 0;

// --- 2. データベースからカート情報を取得 ---
try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // carts, item_skus, items の3つのテーブルをJOINして必要な情報を取得
    $sql = "SELECT
                carts.id AS cart_id,
                carts.quantity,
                item_skus.price,
                item_skus.image_url,
                item_skus.size,
                item_skus.color,
                items.name AS item_name
            FROM
                carts
            JOIN
                item_skus ON carts.item_sku_id = item_skus.id
            JOIN
                items ON item_skus.item_id = items.id
            WHERE
                carts.user_id = ?
                AND carts.status = 0"; // ★論理削除のため status=0 を追加

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 取得した商品で合計金額を計算
    foreach ($cart_items as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }
} catch (PDOException $e) {
    echo "データベース接続エラー: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='./css/G4.css'>
    <link rel='stylesheet' href='./css/header.css'>

    <title>ZeZe - カート</title>
</head>

<body>
    <?php require 'header.php'; ?>

    <div class="cart-container">
        <h2>ショッピングカート</h2>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'stock'): ?>
            <p style="color:red; font-weight:bold; text-align:center;">
                商品の在庫が不足していたため、注文を完了できませんでした。
            </p>
        <?php endif; ?>

        <div class="cart-items-list">

            <?php if (empty($cart_items)): ?>
                <p>カートに商品がありません。</p>
            <?php else: ?>
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <div class="cart-item-image">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                        </div>
                        <div class="cart-item-details">
                            <p class="item-name"><?php echo htmlspecialchars($item['item_name']); ?></p>
                            <p class="item-sku"><?php echo htmlspecialchars($item['size']); ?> / <?php echo htmlspecialchars($item['color']); ?></p>
                            <p class="item-price">¥<?php echo number_format($item['price']); ?></p>
                        </div>
                        <div class="cart-item-quantity">
                            <p>数量: <?php echo htmlspecialchars($item['quantity']); ?></p>
                        </div>
                        <div class="cart-item-total">
                            <p>小計: ¥<?php echo number_format($item['price'] * $item['quantity']); ?></p>
                        </div>
                        <div class="cart-item-delete">
                            <form action="cart-delete.php" method="POST">
                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                <button type="submit">削除</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div id="background">
            <div class="container">

                <h2>商品合計</h2>

                <p class="price-after">¥<?php echo number_format($total_price); ?></p>
                <div class="button-area">
                    <form action="G8.php" method="POST" style="display:inline;">
                        <button type="submit" class="move-btn" <?php echo empty($cart_items) ? 'disabled' : ''; ?>>
                            レジへ移動
                        </button>
                    </form>

                    <a href="G2.php" class="continue-btn">買い物を続ける</a>
                </div>

            </div>
        </div>

    </div>
</body>

</html>
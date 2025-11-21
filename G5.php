<?php
session_start();
require 'db-connect.php';

// --- 1. ログインチェック ---
if (!isset($_SESSION['user_id'])) {
    header('Location: G1.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'ゲスト'; // セッションに名前があれば使う

try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ==========================================
    // A. 注文履歴の取得 (Order + Details)
    // ==========================================
    // 注文情報(orders) と 注文明細(order_details) を結合して取得
    $sql_orders = "
        SELECT 
            o.id AS order_id,
            o.order_date,
            o.total_price,
            od.quantity,
            od.price AS unit_price,
            i.id AS item_id,
            i.name AS item_name,
            s.size,
            s.color,
            s.image_url
        FROM orders o
        JOIN order_details od ON o.id = od.order_id
        JOIN item_skus s ON od.item_sku_id = s.id
        JOIN items i ON s.item_id = i.id
        WHERE o.user_id = ?
        ORDER BY o.order_date DESC, o.id DESC
    ";
    $stmt_orders = $pdo->prepare($sql_orders);
    $stmt_orders->execute([$user_id]);
    $order_rows = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

    // 表示しやすいように「注文ID」ごとにまとめる
    $orders = [];
    foreach ($order_rows as $row) {
        $oid = $row['order_id'];
        if (!isset($orders[$oid])) {
            $orders[$oid] = [
                'order_id'    => $row['order_id'],
                'order_date'  => $row['order_date'],
                'total_price' => $row['total_price'],
                'items'       => []
            ];
        }
        $orders[$oid]['items'][] = $row;
    }

    // ==========================================
    // B. 全閲覧履歴の取得
    // ==========================================
    // itemsテーブルをベースに、閲覧履歴(item_view_history)でソート
    // ※画像と価格は代表SKU(status=2)から取得
    $sql_history = "
        SELECT 
            i.id,
            i.name,
            MAX(h.viewed_at) as viewed_at,
            (SELECT image_url FROM item_skus s WHERE s.item_id = i.id AND s.status = 2 ORDER BY s.id ASC LIMIT 1) as image_url,
            (SELECT price FROM item_skus s WHERE s.item_id = i.id AND s.status = 2 ORDER BY s.id ASC LIMIT 1) as price
        FROM item_view_history h
        JOIN items i ON h.item_id = i.id
        WHERE h.user_id = ? AND i.status = 1
        GROUP BY i.id
        ORDER BY viewed_at DESC
        LIMIT 50 -- 大量にあると重くなるので最新50件に制限
    ";
    $stmt_hist = $pdo->prepare($sql_history);
    $stmt_hist->execute([$user_id]);
    $history_items = $stmt_hist->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "DBエラー: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/G5.css">
    <title>マイページ | ZeZe</title>
</head>

<body>
    <?php require 'header.php'; ?>

    <div class="mypage-container">
        
        <aside class="sidebar">
            <div class="profile-summary">
                <div class="avatar-circle">
                    <i class="fas fa-user"></i>
                </div>
                <p class="user-name"><?php echo htmlspecialchars($user_name); ?> 様</p>
            </div>
            <nav class="account-menu">
                <a href="#order-history" class="menu-item active"><i class="fas fa-box-open"></i> 注文履歴</a>
                <a href="#browsing-history" class="menu-item"><i class="fas fa-history"></i> 閲覧履歴</a>
                <a href="G6.php" class="menu-item"><i class="fas fa-map-marker-alt"></i> 住所・会員情報の変更</a>
                <a href="logout.php" class="menu-item logout"><i class="fas fa-sign-out-alt"></i> ログアウト</a>
            </nav>
        </aside>

        <main class="content-area">
            
            <section id="order-history" class="section-block">
                <h2 class="section-title">注文履歴</h2>
                
                <?php if (empty($orders)): ?>
                    <p class="no-data">注文履歴はありません。</p>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div>
                                    <span class="label">注文日:</span>
                                    <span class="val"><?php echo date('Y/m/d H:i', strtotime($order['order_date'])); ?></span>
                                </div>
                                <div>
                                    <span class="label">注文番号:</span>
                                    <span class="val">#<?php echo $order['order_id']; ?></span>
                                </div>
                                <div>
                                    <span class="label">合計:</span>
                                    <span class="total-price">¥<?php echo number_format($order['total_price']); ?></span>
                                </div>
                            </div>
                            <div class="order-body">
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="order-item-row">
                                        <a href="G3.php?id=<?php echo $item['item_id']; ?>" class="img-link">
                                            <?php if($item['image_url']): ?>
                                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="">
                                            <?php else: ?>
                                                <div class="no-img">No Image</div>
                                            <?php endif; ?>
                                        </a>
                                        <div class="item-details">
                                            <a href="G3.php?id=<?php echo $item['item_id']; ?>" class="item-link">
                                                <?php echo htmlspecialchars($item['item_name']); ?>
                                            </a>
                                            <div class="item-meta">
                                                <?php echo htmlspecialchars($item['size']); ?> / <?php echo htmlspecialchars($item['color']); ?>
                                            </div>
                                            <div class="item-price-qty">
                                                ¥<?php echo number_format($item['unit_price']); ?> × <?php echo $item['quantity']; ?>
                                            </div>
                                        </div>
                                        <div class="item-action">
                                            <a href="G3.php?id=<?php echo $item['item_id']; ?>" class="buy-again-btn">もう一度買う</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>

            <section id="browsing-history" class="section-block">
                <h2 class="section-title">最近チェックした商品</h2>
                
                <?php if (empty($history_items)): ?>
                    <p class="no-data">閲覧履歴はありません。</p>
                <?php else: ?>
                    <div class="history-grid">
                        <?php foreach ($history_items as $hItem): ?>
                            <a href="G3.php?id=<?php echo $hItem['id']; ?>" class="history-card">
                                <div class="history-img-wrapper">
                                    <?php if ($hItem['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($hItem['image_url']); ?>" alt="">
                                    <?php else: ?>
                                        <span>No Image</span>
                                    <?php endif; ?>
                                </div>
                                <div class="history-info">
                                    <p class="h-name"><?php echo htmlspecialchars($hItem['name']); ?></p>
                                    <p class="h-price">¥<?php echo number_format($hItem['price']); ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

        </main>
    </div>
</body>
</html>
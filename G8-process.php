<?php session_start(); ?>
<?php require 'db-connect.php'; ?>
<?php
// --- 1. ログインチェック ---
if (!isset($_SESSION['user_id'])) {
    header('Location: G1.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$pdo = null;
try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ▼▼▼ トランザクション開始 ▼▼▼
    $pdo->beginTransaction();

    // --- 3. カート情報、在庫、価格をDBから取得 (ロック) ---
    $sql_cart = "SELECT
                    c.item_sku_id,
                    c.quantity AS cart_quantity,
                    s.price,
                    s.stock_quantity
                FROM carts c
                JOIN item_skus s ON c.item_sku_id = s.id
                WHERE c.user_id = ?
                AND c.status = 0"; // (論理削除のため status=0)
    
    $stmt_cart = $pdo->prepare($sql_cart);
    $stmt_cart->execute([$user_id]);
    $cart_items = $stmt_cart->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cart_items)) {
        $pdo->rollBack();
        header('Location: G4.php');
        exit;
    }

    // --- 4. 在庫チェック と 合計金額の計算 ---
    $total_price = 0;
    foreach ($cart_items as $item) {
        if ($item['cart_quantity'] > $item['stock_quantity']) {
            $pdo->rollBack();
            header('Location: G4.php?error=stock'); 
            exit;
        }
        $total_price += $item['price'] * $item['cart_quantity'];
    }

    // --- 5. お届け先住所を取得 ---
    $sql_addr = $pdo->prepare('SELECT * FROM addresses WHERE user_id = ? ORDER BY id ASC LIMIT 1');
    $sql_addr->execute([$user_id]);
    $address = $sql_addr->fetch(PDO::FETCH_ASSOC);

    if (empty($address)) {
        $pdo->rollBack();
        header('Location: G10.php?error=no_address');
        exit;
    }
    // (郵便番号は varchar(8) なので、intに変換しない)
    $postal_code_varchar = $address['postal_code'];

    // ▼▼▼ ★★★ここを修正★★★ ▼▼▼
    // --- 6. (A) orders テーブルに注文を挿入 ---
    // (カラム名: total_price, shipping_addressee)
    // (status, order_date はDBのデフォルト値に任せる)
    $sql_order = $pdo->prepare(
        'INSERT INTO orders (user_id, total_price, shipping_postal_code, shipping_addressee) 
         VALUES (?, ?, ?, ?)'
    );
    $sql_order->execute([
        $user_id,
        $total_price,
        $postal_code_varchar,  // varchar(8) のままINSERT
        $address['address']     // shipping_addressee に住所をINSERT
    ]);
    // ▲▲▲ ★★★修正完了★★★ ▲▲▲

    // --- 7. (B) 挿入した order_id を取得 ---
    $new_order_id = $pdo->lastInsertId();

    // --- 8. (C) order_details と (D) 在庫更新 ---
    $sql_details = $pdo->prepare('INSERT INTO order_details (order_id, item_sku_id, price, quantity) VALUES (?, ?, ?, ?)');
    $sql_stock = $pdo->prepare('UPDATE item_skus SET stock_quantity = stock_quantity - ? WHERE id = ?');

    foreach ($cart_items as $item) {
        // (C) order_details に明細を挿入
        $sql_details->execute([
            $new_order_id,
            $item['item_sku_id'],
            $item['price'],
            $item['cart_quantity']
        ]);
        
        // (D) item_skus の在庫を減らす
        $sql_stock->execute([
            $item['cart_quantity'],
            $item['item_sku_id']
        ]);
    }

    // --- 9. (E) carts テーブルを「注文済み」に更新 (論理削除) ---
    $sql_update_cart = $pdo->prepare('UPDATE carts SET status = 1 WHERE user_id = ? AND status = 0');
    $sql_update_cart->execute([$user_id]);

    // --- 10. すべて成功したらコミット（DBに反映） ---
    $pdo->commit();
    // ▲▲▲ トランザクション終了 ▲▲▲

    // --- 11. サンキューページ(G9.php)にリダイレクト ---
    header('Location: G9.php');
    exit;

} catch (PDOException $e) {
    // --- 12. エラー発生時 ---
    if ($pdo) {
        $pdo->rollBack(); // トランザクションを中止（すべて元に戻す）
    }
    // (デバッグ用にエラーメッセージを出力)
    // echo $e->getMessage();
    // exit;
    header('Location: G8.php?error=db');
    exit;
}
?>
<?php session_start(); ?>
<?php require 'db-connect.php'; ?>
<?php
// --- 1. ログインチェック ---
if (!isset($_SESSION['user_id'])) {
    header('Location: G1.php');
    exit;
}

// --- 2. POSTデータのバリデーション ---
$item_id = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
$item_sku_id = filter_input(INPUT_POST, 'item_sku_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
$user_id = $_SESSION['user_id'];

// データが不正な場合は、元のG3.php（商品詳細）にエラー付きで戻す
if (!$item_id || !$item_sku_id || !$quantity || $quantity <= 0) {
    if ($item_id) {
        header('Location: G3.php?id=' . $item_id . '&error=1');
    } else {
        header('Location: G2.php'); 
    }
    exit;
}

try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- 3. A案の SELECT と IF分岐 を削除 ---

    // --- 4. INSERT ... ON DUPLICATE KEY UPDATE 構文を使用 ---
    // (user_id, item_sku_id) の組み合わせでINSERTを試みる
    $sql = $pdo->prepare(
        "INSERT INTO carts (user_id, item_sku_id, quantity, status) 
         VALUES (?, ?, ?, 0) 
         ON DUPLICATE KEY UPDATE 
            quantity = quantity + VALUES(quantity), 
            status = 0"
    );
    // 補足:
    // ON DUPLICATE KEY UPDATE ...
    //   ... もし重複エラー(Duplicate entry)が起きたら、代わりにUPDATEを実行
    //
    // quantity = quantity + VALUES(quantity)
    //   ... 既存の quantity に、INSERTしようとした quantity を「加算」する
    //
    // status = 0
    //   ... もし既存の行が status = 1 (注文済み) だったら、status = 0 (カート内) に戻す

    // TODO: このSQL文の前に「在庫チェック」を入れるのが理想です
    
    $sql->execute([$user_id, $item_sku_id, $quantity]);


    // --- 5. カート表示ページにリダイレクト ---
    header('Location: G4.php');
    exit;

} catch (PDOException $e) {
    // ▼▼▼ デバッグコードを元のリダイレクトに戻します ▼▼▼
    
    // エラー時は商品ページに戻す
    if ($item_id) {
        header('Location: G3.php?id=' . $item_id . '&error=db');
    } else {
        echo "重大なエラーが発生しました。";
    }
    
    // ▲▲▲ ここまで ▲▲▲
    exit;
}
?>
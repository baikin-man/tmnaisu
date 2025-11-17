<?php session_start(); ?>
<?php require 'db-connect.php'; ?>
<?php
// --- 1. ログインチェック ---
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// --- 2. POSTされた cart_id を取得 ---
$cart_id = filter_input(INPUT_POST, 'cart_id', FILTER_VALIDATE_INT);
$user_id = $_SESSION['user_id'];

// cart_id が正しく送られてきたか確認
if ($cart_id) {
    try {
        $pdo = new PDO($connect, USER, PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // --- 3. 削除実行 ---
        // 他人のカートを削除できないよう、user_id もWHERE句に含める
        $sql = $pdo->prepare('DELETE FROM carts WHERE id = ? AND user_id = ?');
        $sql->execute([$cart_id, $user_id]);

    } catch (PDOException $e) {
        // エラー処理 (例: エラーページにリダイレクト)
        echo "データベースエラー: " . $e->getMessage();
        exit;
    }
}

// --- 4. 処理が終わったらカートページ(G4.php)に戻る ---
header('Location: G4.php');
exit;
?>
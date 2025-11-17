<?php session_start(); ?>
<?php require 'db-connect.php'; ?>
<?php
// --- 1. POSTデータの取得 ---
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = filter_input(INPUT_POST, 'password');

// バリデーション
if (!$email || !$password) {
    // データが不正
    header('Location: G1.php?error=1');
    exit;
}

try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- 2. ユーザー検索 (emailで) ---
    $sql = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $sql->execute([$email]);
    $user = $sql->fetch(PDO::FETCH_ASSOC);

    // --- 3. パスワードの照合 ---
    if ($user && password_verify($password, $user['password'])) {
        // --- 認証成功 ---
        
        // セッションIDを再発行（セキュリティ対策）
        session_regenerate_id(true);
        
        // セッションにユーザー情報を保存
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name']; // (header2.php などで使うため)

        // トップページ（G2.php）にリダイレクト
        header('Location: G2.php');
        exit;
        
    } else {
        // --- 認証失敗 (ユーザーが存在しない or パスワードが違う) ---
        header('Location: G1.php?error=1');
        exit;
    }

} catch (PDOException $e) {
    // データベースエラー
    header('Location: G1.php?error=db');
    exit;
}
?>
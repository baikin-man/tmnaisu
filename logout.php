<?php
// 1. 必ず session_start() を実行
session_start();

// 2. セッション変数をすべて空にする
$_SESSION = array();

// 3. セッションクッキーを削除 (セッション名を取得)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. セッションを完全に破棄
session_destroy();

// 5. ログインページ (G1.php) にリダイレクト
header('Location: G1.php');
exit;
?>
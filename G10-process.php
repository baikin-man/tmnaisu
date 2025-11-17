<?php
session_start();
require 'db-connect.php';

// --- 1. POSTデータの取得 ---
$name = filter_input(INPUT_POST, 'name');
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = filter_input(INPUT_POST, 'password');
$address = filter_input(INPUT_POST, 'address');
$postal_code = filter_input(INPUT_POST, 'postal_code');
$agree = filter_input(INPUT_POST, 'agree');

// --- 2. バリデーション ---
if (empty($name) || !$email || empty($password) || empty($address) || empty($postal_code) || !$agree) {
    // データが不正
    header('Location: G10.php?error=data');
    exit;
}

// パスワードをハッシュ化
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 郵便番号からハイフンなどを除去（int型で保存する場合）
$postal_code_int = preg_replace('/[^0-9]/', '', $postal_code);

// --- 3. データベース登録処理 ---
$pdo = null; // PDO変数を外で定義
try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ▼▼▼ トランザクション開始 ▼▼▼
    $pdo->beginTransaction();

    // (A) emailが既に登録されていないか確認
    $sql_check = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
    $sql_check->execute([$email]);
    
    if ($sql_check->fetchColumn() > 0) {
        // 既にemailが存在する
        $pdo->rollBack(); // トランザクション中止
        header('Location: G10.php?error=email');
        exit;
    }

    // (B) users テーブルに INSERT
    $sql_users = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
    $sql_users->execute([$name, $email, $hashed_password]);

    // (C) 今 INSERT した user_id を取得
    $new_user_id = $pdo->lastInsertId();

    // (D) addresses テーブルに INSERT
    $sql_address = $pdo->prepare('INSERT INTO addresses (user_id, postal_code, address) VALUES (?, ?, ?)');
    $sql_address->execute([$new_user_id, $postal_code_int, $address]);

    // (E) すべて成功したらコミット（DBに反映）
    $pdo->commit();
    // ▲▲▲ トランザクション終了 ▲▲▲

    // --- 4. 成功したらG1.php（ログイン画面）にメッセージ付きでリダイレクト ---
    header('Location: G1.php?signup=success');
    exit;

} catch (PDOException $e) {
    // --- 5. エラー発生時 ---
    if ($pdo) {
        $pdo->rollBack(); // トランザクションを中止（すべて元に戻す）
    }
    // データベースエラー
    header('Location: G10.php?error=db');
    exit;
}
?>
<?php
session_start();
require 'db-connect.php';

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: G1.php');
    exit;
}

// POST送信でなければリダイレクト
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: G6.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$name = trim($_POST['name']);
$zip1 = trim($_POST['zip1']);
$zip2 = trim($_POST['zip2']);
$address = trim($_POST['address']);

// 簡易バリデーション
if ($name === '' || $zip1 === '' || $zip2 === '' || $address === '') {
    header('Location: G6.php?error=empty');
    exit;
}

// 郵便番号を結合
$postal_code = $zip1 . '-' . $zip2;

try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->beginTransaction();

    // 1. usersテーブル（名前）を更新
    $sql_user = "UPDATE users SET name = ? WHERE id = ?";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute([$name, $user_id]);

    // 2. addressesテーブル（住所）を更新
    // 住所が存在しない場合（登録時の不具合など）を考慮し、存在確認してから UPDATE or INSERT
    $stmt_check = $pdo->prepare("SELECT id FROM addresses WHERE user_id = ?");
    $stmt_check->execute([$user_id]);
    
    if ($stmt_check->fetch()) {
        // 更新
        $sql_addr = "UPDATE addresses SET postal_code = ?, address = ? WHERE user_id = ?";
        $stmt_addr = $pdo->prepare($sql_addr);
        $stmt_addr->execute([$postal_code, $address, $user_id]);
    } else {
        // 新規作成（万が一データが無かった場合）
        $sql_addr = "INSERT INTO addresses (user_id, postal_code, address, phone_number) VALUES (?, ?, ?, '')";
        $stmt_addr = $pdo->prepare($sql_addr);
        $stmt_addr->execute([$user_id, $postal_code, $address]);
    }

    $pdo->commit();

    // セッションの名前情報も更新（ヘッダー表示用）
    $_SESSION['user_name'] = $name;

    // 成功メッセージ付きでリダイレクト
    header('Location: G6.php?success=1');
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // エラーメッセージ付きでリダイレクト
    header('Location: G6.php?error=db');
    exit;
}
?>
<?php
// (itemsテーブルを論理削除)
require "db-connect.php";

if (!isset($_GET['item_id'])) {
    die("Item IDが指定されていません。");
}
$item_id = $_GET['item_id'];

try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // items テーブルの status を 0 (論理削除) に変更
    $sql = $pdo->prepare("UPDATE items SET status = 0 WHERE id = ?");
    $sql->execute([$item_id]);

} catch (PDOException $e) {
    die("DBエラー: " . $e->getMessage());
}

// 商品一覧に戻る
header("Location: item_list.php");
exit;
?>
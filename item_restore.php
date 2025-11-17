<?php
// (itemsテーブルを復元)
require "db-connect.php";

if (!isset($_GET['item_id'])) {
    die("Item IDが指定されていません。");
}
$item_id = $_GET['item_id'];

try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // items テーブルの status を 1 (販売中) に変更
    $sql = $pdo->prepare("UPDATE items SET status = 1 WHERE id = ?");
    $sql->execute([$item_id]);

} catch (PDOException $e) {
    die("DBエラー: " . $e->getMessage());
}

// 削除済み一覧を表示していた場合、そこに戻る
$redirect_url = "item_list.php?show_deleted=1";
header("Location: " . $redirect_url);
exit;
?>
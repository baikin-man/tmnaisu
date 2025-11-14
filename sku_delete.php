<?php
require "db-connect.php";

// 1. GETで送られてきた sku_id をチェック
if (!isset($_GET['sku_id'])) {
    die("SKU IDが指定されていません。");
}
$sku_id = $_GET['sku_id'];

$pdo = new PDO($connect, USER, PASS);

// 2. リダイレクト先用の item_id を取得 (必須)
$sql_select = $pdo->prepare("SELECT item_id FROM item_skus WHERE id = ?");
$sql_select->execute([$sku_id]);
$sku = $sql_select->fetch(PDO::FETCH_ASSOC);
$item_id = $sku ? $sku['item_id'] : null;

// 3. ★★★ DELETE の代わりに UPDATE を実行 ★★★
// status を 0 (論理削除) に変更する
$sql_update = $pdo->prepare(
    "UPDATE item_skus SET status = 0 WHERE id = ?"
);
$sql_update->execute([$sku_id]);

// 4. ★★★ 画像ファイル(unlink)は削除しない ★★★


$redirect_url = "item_list.php";

header("Location: " . $redirect_url);
exit;
?>
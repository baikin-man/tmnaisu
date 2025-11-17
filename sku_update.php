<?php
require "db-connect.php";

// --- 1. POSTデータを受け取る ---
if (
    !isset($_POST['sku_id']) ||
    !isset($_POST['item_id']) || // リダイレクト用
    !isset($_POST['size']) ||
    !isset($_POST['color']) ||
    !isset($_POST['price']) ||
    !isset($_POST['stock']) ||
    !isset($_POST['status']) || // ★追加: ステータスのチェック
    !isset($_POST['current_image_path'])
) {
    die("必須情報が不足しています。");
}

$sku_id = $_POST['sku_id'];
$item_id = $_POST['item_id']; // リダイレクト先で使う
$size = $_POST['size'];
$color = $_POST['color'];
$price = $_POST['price'];
$stock = $_POST['stock'];
$status = $_POST['status']; // ★追加: ステータスを変数に格納
$destination = $_POST['current_image_path']; // デフォルトは現在の画像パス

// --- 2. ★画像が「新しく」アップロードされたかチェック ---
if (isset($_FILES['new_sku_image']) && $_FILES['new_sku_image']['error'] === UPLOAD_ERR_OK) {
    
    $upload_dir = './image/skus/'; // ★保存先フォルダ
    
    // フォルダが存在しない場合は作成する（念のため追加）
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $tmp_path = $_FILES['new_sku_image']['tmp_name'];
    $extension = pathinfo($_FILES['new_sku_image']['name'], PATHINFO_EXTENSION);
    $new_filename = 'sku_' . uniqid() . '.' . $extension;
    $new_destination = $upload_dir . $new_filename;

    if (move_uploaded_file($tmp_path, $new_destination)) {
        // アップロードが成功したら、保存先パスを「新しいパス」に上書き
        $destination = $new_destination;
        
        // (★推奨: ここで古い画像 $_POST['current_image_path'] を unlink() で削除する処理)
        
    } else {
        // 画像アップロードが選ばれたのに失敗した場合はエラー
        die("ファイルの移動に失敗しました。パーミッションを確認してください。");
    }
}
// (注: 新しい画像がアップロードされなかった場合、$destination はPOSTされた古いパスのまま)

// --- 3. データベースを「UPDATE」 ---
try {
    $pdo = new PDO($connect, USER, PASS);
    // エラー発生時に例外を投げる設定（デバッグしやすくするため）
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = $pdo->prepare(
        'UPDATE item_skus 
         SET size = ?, color = ?, price = ?, stock_quantity = ?, status = ?, image_url = ?
         WHERE id = ?'
    );

    // ★追加: $status をパラメータに含める (順番に注意: size, color, price, stock, status, image_url, id)
    $sql->execute([$size, $color, $price, $stock, $status, $destination, $sku_id]);

} catch (PDOException $e) {
    // エラー時の処理
    die("DBエラー: " . $e->getMessage());
}

// --- 4. ★重要★ SKU一覧/登録ページにリダイレクト ---
// 編集が終わったら、その商品が載っている「SKU登録ページ」に戻すのが親切
$redirect_url = "item_sku_register.php?item_id=" . $item_id;
header("Location: " . $redirect_url);
exit;

?>
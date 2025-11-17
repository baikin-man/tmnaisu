<?php
// データベース接続ファイルを読み込む
require "db-connect.php"; 
// (このファイル内で $connect, USER, PASS が定義されている前提)

// --- 1. POSTデータの必須チェック ---
// sku_id, item_id, size, color_name, color_code, price, stock, status, current_image_path
// が送信されているか確認します。
if (
    !isset($_POST['sku_id']) || trim($_POST['sku_id']) === '' ||
    !isset($_POST['item_id']) || trim($_POST['item_id']) === '' || 
    !isset($_POST['size']) || trim($_POST['size']) === '' ||
    !isset($_POST['color_name']) || trim($_POST['color_name']) === '' || // ★ 'color_name' をチェック
    !isset($_POST['color_code']) || trim($_POST['color_code']) === '' || // ★ 'color_code' をチェック
    !isset($_POST['price']) || trim($_POST['price']) === '' ||
    !isset($_POST['stock']) || trim($_POST['stock']) === '' ||
    !isset($_POST['status']) || trim($_POST['status']) === '' ||
    !isset($_POST['current_image_path']) // (空でもOK)
) {
    echo "<h1>エラー</h1>";
    echo "<p>必須情報（SKU ID, Item ID, 色名, カラーコードなど）がフォームから送信されていません。</p>";
    echo '<a href="javascript:history.back()">フォームに戻る</a>';
    die();
}

// --- 2. POSTデータを変数に格納 ---
$sku_id = $_POST['sku_id'];
$item_id = $_POST['item_id']; // リダイレクト先で使う
$size = $_POST['size'];
$color_name = $_POST['color_name']; // ★ 'color' から 'color_name' に変更
$color_code = $_POST['color_code']; // ★ 'color_code' を追加
$price = $_POST['price'];
$stock = $_POST['stock'];
$status = $_POST['status']; 

// ★ 修正: DBに保存されている ./ 無しのパス
$destination_db = $_POST['current_image_path']; 

// --- 3. ★画像が「新しく」アップロードされたかチェック ---
// ( 'new_sku_image' が存在し、かつエラーが UPLOAD_ERR_OK (0) の場合のみ処理)
if (isset($_FILES['new_sku_image']) && $_FILES['new_sku_image']['error'] === UPLOAD_ERR_OK) {
    
    // ( 'item_sku_done.php' と同じパス定義とセキュリティチェック )
    $upload_dir_server = './image/skus/'; 
    $upload_dir_db = 'image/skus/';     

    if (!is_dir($upload_dir_server)) {
        mkdir($upload_dir_server, 0755, true);
    }

    $tmp_path = $_FILES['new_sku_image']['tmp_name'];
    $file_name = $_FILES['new_sku_image']['name'];
    $file_size = $_FILES['new_sku_image']['size'];
    $extension = pathinfo($file_name, PATHINFO_EXTENSION);
    $file_ext_lower = strtolower($extension);

    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_ext_lower, $allowed_exts)) {
        die("許可されていないファイル形式です。(許可: jpg, jpeg, png, gif)");
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $tmp_path);
    finfo_close($finfo);
    $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($mime_type, $allowed_mimes)) {
        die("ファイルのMIMEタイプが正しくありません。");
    }
    $max_size = 5 * 1024 * 1024; // 5MB
    if ($file_size > $max_size) {
        die("ファイルサイズが大きすぎます。5MB以下にしてください。");
    }

    $new_filename = 'sku_' . uniqid() . '.' . $file_ext_lower;
    $destination_server = $upload_dir_server . $new_filename; 
    $new_destination_db = $upload_dir_db . $new_filename;     

    if (move_uploaded_file($tmp_path, $destination_server)) {
        // アップロードが成功したら、保存先パスを「新しいパス」に上書き
        $destination_db = $new_destination_db;
        
        // 古い画像を削除
        $old_server_path = './' . $_POST['current_image_path'];
        if (file_exists($old_server_path) && is_file($old_server_path) && !empty($_POST['current_image_path'])) {
             @unlink($old_server_path); // @エラー抑制
        }
        
    } else {
        die("ファイルの移動に失敗しました。パーミッションを確認してください。");
    }
}
// (注: 画像がアップロードされなかった場合 (UPLOAD_ERR_NO_FILE など)、
//  このifブロックは実行されず、$destination_db は古いパスのまま)


// --- 3. データベースを「UPDATE」 ---
$pdo = new PDO($connect, USER, PASS);
$sql = $pdo->prepare(
    'UPDATE item_skus 
     SET size = ?, color = ?, price = ?, stock_quantity = ?, image_url = ?
     WHERE id = ?' // id を条件に更新
);

$sql->execute([$size, $color, $price, $stock, $destination, $sku_id]);

// --- 4. データベースを「UPDATE」 ---
try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ★ 修正: color = ? (色名), color_code = ? (CSSコード) に変更
    $sql = $pdo->prepare(
        'UPDATE item_skus 
         SET size = ?, color = ?, color_code = ?, price = ?, stock_quantity = ?, status = ?, image_url = ?
         WHERE id = ?'
    );

    // ★ 修正: $color_name, $color_code をバインド
    $sql->execute([
        $size, 
        $color_name, 
        $color_code, 
        $price, 
        $stock, 
        $status, 
        $destination_db, // ★ ./ が無い方のパス
        $sku_id
    ]);

} catch (PDOException $e) {
    // エラー時の処理
    die("DBエラー: " . $e->getMessage());
}

// --- 5. SKU登録ページ（詳細）にリダイレクト ---
// (item_list.php ではなく、編集していた商品が分かるように item_sku_register.php に戻す)
$redirect_url = "item_sku_register.php?item_id=" . $item_id;
header("Location: " . $redirect_url);
exit;

?>
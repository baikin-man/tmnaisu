<?php
// データベース接続ファイルを読み込む
require "db-connect.php"; 
// (このファイル内で $connect, USER, PASS が定義されている前提)

// --- 1. POSTデータとファイルの必須チェック ---
if (
    !isset($_POST['item_id']) || trim($_POST['item_id']) === '' ||
    !isset($_POST['size']) || trim($_POST['size']) === '' ||
    !isset($_POST['color']) || trim($_POST['color']) === '' ||
    !isset($_POST['price']) || trim($_POST['price']) === '' ||
    !isset($_POST['stock']) || trim($_POST['stock']) === '' ||
    !isset($_FILES['sku_image']) ||
    $_FILES['sku_image']['error'] !== UPLOAD_ERR_OK
) {
    die("必須情報が不足しているか、ファイルアップロードエラーです。");
}

// --- 2. POSTデータを変数に格納 ---
$item_id = $_POST['item_id'];
$size = $_POST['size'];
$color = $_POST['color'];
$price = $_POST['price'];
$stock = $_POST['stock'];

// --- 3. 画像ファイルの処理 ---

// ★ パスを2種類定義する
$upload_dir_server = './image/skus/'; // サーバーがファイルを保存する場所 (./ が必要)
$upload_dir_db = 'image/skus/';     // データベースに保存するパス (./ を除く)

// フォルダが存在しない場合は作成 (初回実行時など)
if (!is_dir($upload_dir_server)) {
    mkdir($upload_dir_server, 0755, true);
}

// アップロードされたファイル情報を取得
$tmp_path = $_FILES['sku_image']['tmp_name'];
$file_name = $_FILES['sku_image']['name'];
$file_size = $_FILES['sku_image']['size'];
$extension = pathinfo($file_name, PATHINFO_EXTENSION);
$file_ext_lower = strtolower($extension);

// --- 4. ★ ファイルのセキュリティチェック ---

// (4-1) 許可する拡張子かチェック
$allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array($file_ext_lower, $allowed_exts)) {
    die("許可されていないファイル形式です。(許可: jpg, jpeg, png, gif)");
}

// (4-2) MIMEタイプをチェックして、本当に画像か確認
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $tmp_path);
finfo_close($finfo);

$allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($mime_type, $allowed_mimes)) {
    die("ファイルのMIMEタイプが正しくありません。画像ファイルを選択してください。");
}

// (4-3) ファイルサイズをチェック (例: 5MBまで)
$max_size = 5 * 1024 * 1024; // 5MB
if ($file_size > $max_size) {
    die("ファイルサイズが大きすぎます。5MB以下にしてください。");
}

// --- 5. 新しいファイル名を生成し、ファイルを移動 ---

// 他のファイルと重複しないユニークなファイル名を生成
$new_filename = 'sku_' . uniqid() . '.' . $file_ext_lower;

// ★ 2種類のパスを組み立てる
$destination_server = $upload_dir_server . $new_filename; // move_uploaded_file用
$destination_db = $upload_dir_db . $new_filename;     // DB保存用

// ファイルを指定されたディレクトリに移動
if (!move_uploaded_file($tmp_path, $destination_server)) {
    // 失敗した場合、フォルダの権限（パーミッション）が原因のことが多い
    die("ファイルの移動に失敗しました。'$upload_dir_server' フォルダに書き込み権限があるか確認してください。");
}

// --- 6. データベース登録 ---

// 在庫数に応じてステータスを決定
$status_to_insert = 2; // デフォルトは 2 (販売中)
if ($stock == 0) {
    $status_to_insert = 1; // 在庫が0なら 1 (在庫切れ)
}

// ★ PDOのエラー処理を 'exception' (例外) モードにする
try {
    $pdo = new PDO($connect, USER, PASS);
    // エラーモードを例外に設定
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = $pdo->prepare(
        'INSERT INTO item_skus (item_id, size, color, price, stock_quantity, image_url, status) 
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    
    // ★ DBには ./ が無い方のパス ($destination_db) を保存
    $sql->execute([$item_id, $size, $color, $price, $stock, $destination_db, $status_to_insert]);

} catch (PDOException $e) {
    // ★ DB登録に失敗した場合の処理
    
    // 登録に失敗したので、サーバーにアップロードした画像ファイルも削除する
    if (file_exists($destination_server)) {
        unlink($destination_server);
    }
    
    // ユーザーには汎用的なエラーを、開発者は $e->getMessage() で詳細を確認
    error_log("DBエラー: " . $e->getMessage()); // エラーログに詳細を記録
    die("データベースへの登録に失敗しました。");
}

// --- 7. 処理完了後、元の登録画面（または一覧）にリダイレクト ---
// ※このファイルは処理専用なので、完了したら画面を移動させます

// ★ item_id を指定して、今登録した商品SKUの画面に戻る（前の画面で使っていたURLに合わせてください）
$redirect_url = "item_sku_register.php?item_id=" . $item_id;

// もし一覧画面に戻すなら
// $redirect_url = "item_list.php"; 

header("Location: " . $redirect_url);
exit; // リダイレクト後は必ず exit を実行する

?>
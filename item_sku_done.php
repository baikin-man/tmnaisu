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
$errors = []; // エラーメッセージ格納用配列

// (1-1) POST項目のチェック
if (!isset($_POST['item_id']) || trim($_POST['item_id']) === '') {
    $errors[] = "item_id がありません。";
}
if (!isset($_POST['size']) || trim($_POST['size']) === '') {
    $errors[] = "size がありません。";
}
if (!isset($_POST['color_name']) || trim($_POST['color_name']) === '') {
    $errors[] = "color_name (色名) がありません。";
}
if (!isset($_POST['color_code']) || trim($_POST['color_code']) === '') {
    $errors[] = "color_code (カラーコード) がありません。";
}
if (!isset($_POST['price']) || trim($_POST['price']) === '') {
    // 0 は許可されます (trim('0') === '' は false のため)
    $errors[] = "price がありません。";
}
if (!isset($_POST['stock']) || trim($_POST['stock']) === '') {
    // 0 は許可されます
    $errors[] = "stock がありません。";
}

// (1-2) ファイルアップロードのチェック
if (!isset($_FILES['sku_image'])) {
    $errors[] = "ファイル情報がありません。";
} elseif ($_FILES['sku_image']['error'] !== UPLOAD_ERR_OK) {
    // アップロードエラーの詳細を判定
    switch ($_FILES['sku_image']['error']) {
        case UPLOAD_ERR_INI_SIZE:
            // php.ini の 'upload_max_filesize' を超えた
            $errors[] = "ファイルサイズがサーバー設定の上限を超えています。(upload_max_filesize)";
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $errors[] = "ファイルサイズがフォーム設定（MAX_FILE_SIZE）を超えています。";
            break;
        case UPLOAD_ERR_PARTIAL:
            $errors[] = "ファイルが部分的にしかアップロードされませんでした。";
            break;
        case UPLOAD_ERR_NO_FILE:
            $errors[] = "画像ファイルが選択されていません。 (フォームの required 属性が機能していない可能性があります)";
            break;
        default:
            $errors[] = "不明なファイルアップロードエラーです。 (エラーコード: " . $_FILES['sku_image']['error'] . ")";
            break;
    }
}

// (1-3) エラーがあれば全て表示して終了
if (!empty($errors)) {
    echo "<h1>エラー</h1>";
    echo "<p>必須情報が不足しているか、ファイルアップロードエラーです。</p>";
    echo "<ul style='color: red; border: 1px solid red; padding: 10px; list-style-position: inside;'>";
    foreach ($errors as $err) {
        echo "<li>" . htmlspecialchars($err) . "</li>";
    }
    echo "</ul>";
    echo '<p>（特に「ファイルサイズがサーバー設定の上限を超えています」と表示された場合は、アップロードする画像を小さくするか、サーバーの`php.ini`設定を見直してください。）</p>';
    echo '<a href="javascript:history.back()">フォームに戻る</a>';
    die();
}


// --- 2. POSTデータを変数に格納 ---
// (エラーチェックを通過したので、ここは変更なし)
$item_id = $_POST['item_id'];
$size = $_POST['size'];

$color = $_POST['color'];

$color_name = $_POST['color_name']; 
$color_code = $_POST['color_code']; 
$price = $_POST['price'];
$stock = $_POST['stock'];

// --- 3. 画像ファイルの処理 ---
// (変更なし)
$upload_dir_server = './image/skus/'; 
$upload_dir_db = 'image/skus/';     


// ★ パスを2種類定義する
$upload_dir_server = './image/skus/'; // サーバーがファイルを保存する場所 (./ が必要)
$upload_dir_db = 'image/skus/';     // データベースに保存するパス (./ を除く)

// フォルダが存在しない場合は作成 (初回実行時など)

if (!is_dir($upload_dir_server)) {
    mkdir($upload_dir_server, 0755, true);
}

$tmp_path = $_FILES['sku_image']['tmp_name'];
$file_name = $_FILES['sku_image']['name'];
$file_size = $_FILES['sku_image']['size'];
$extension = pathinfo($file_name, PATHINFO_EXTENSION);
$file_ext_lower = strtolower($extension);

// --- 4. ★ ファイルのセキュリティチェック ---

$allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array($file_ext_lower, $allowed_exts)) {
    die("許可されていないファイル形式です。(許可: jpg, jpeg, png, gif)");
}
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $tmp_path);
finfo_close($finfo);
$allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($mime_type, $allowed_mimes)) {
    die("ファイルのMIMEタイプが正しくありません。画像ファイルを選択してください。");
}
$max_size = 5 * 1024 * 1024; // 5MB
if ($file_size > $max_size) {
    die("ファイルサイズが大きすぎます。5MB以下にしてください。");
}

// --- 5. 新しいファイル名を生成し、ファイルを移動 ---
// (変更なし)
$new_filename = 'sku_' . uniqid() . '.' . $file_ext_lower;
$destination_server = $upload_dir_server . $new_filename; 
$destination_db = $upload_dir_db . $new_filename;     

if (!move_uploaded_file($tmp_path, $destination_server)) {
    die("ファイルの移動に失敗しました。'$upload_dir_server' フォルダに書き込み権限があるか確認してください。");
}

// --- 6. データベース登録 ---
// (変更なし)
$status_to_insert = 2; 
if ($stock == 0) {
    $status_to_insert = 1; 
}

try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = $pdo->prepare(
        'INSERT INTO item_skus (item_id, size, color, price, stock_quantity, image_url, status) 
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    

    $sql->execute([
        $item_id, 
        $size, 
        $color_name,  
        $color_code,  
        $price, 
        $stock, 
        $destination_db, 
        $status_to_insert
    ]);


} catch (PDOException $e) {
    if (file_exists($destination_server)) {
        unlink($destination_server);
    }
    error_log("DBエラー: " . $e->getMessage()); 
    die("データベースへの登録に失敗しました。");
}

// --- 7. 処理完了後、元の登録画面（または一覧）にリダイレクト ---

// ※このファイルは処理専用なので、完了したら画面を移動させます

// ★ item_id を指定して、今登録した商品SKUの画面に戻る（前の画面で使っていたURLに合わせてください）
$redirect_url = "item_sku_register.php?item_id=" . $item_id;

// もし一覧画面に戻すなら
// $redirect_url = "item_list.php"; 

// (変更なし)
$redirect_url = "item_sku_register.php?item_id=" . $item_id;

header("Location: " . $redirect_url);
exit; 
?>
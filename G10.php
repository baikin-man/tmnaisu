<?php session_start();?>
<?php require 'db-connect.php';?>
<?php
// 既にログイン済みの場合は、G2（トップページ）にリダイレクト
if (isset($_SESSION['user_id'])) {
    header('Location: G2.php'); // ※G2.phpはトップページのパスを想定
    exit;
}

// エラーメッセージの取得
$error_message = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'email') {
        $error_message = 'このメールアドレスは既に使用されています。';
    } else if ($_GET['error'] == 'db') {
        $error_message = 'データベースエラーが発生しました。';
    } else if ($_GET['error'] == 'data') {
        $error_message = '入力内容が正しくありません。';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    
    <title>ZeZe - 新規登録</title>
    
    <!-- ★ 外部CSSの読み込み ★ -->
    <link rel="stylesheet" href="header2.css"> <!-- ヘッダーCSS -->
    <link rel="stylesheet" href="./css/G10.css">   <!-- このページ専用CSS -->

    <!-- ★ 固定ヘッダー用の余白 ★ -->
    <style>
        body {
            /* header2.css の .kotei の高さと合わせる */
            padding-top: 115px; 
            
            /* 元の<style>タグにあった body スタイル */
            /* 背景画像を使いたい場合は、以下のコメントを解除してください */
            /*
            margin: 0;
            font-family: "Hiragino Kaku Gothic ProN", "メイリオ", sans-serif;
            background: url('bg.jpg') center/cover no-repeat;
            */
        }
    </style>
</head>
<body>
    <?php require 'header2.php';?>
<div class="wrap">
    <h2>新規登録</h2>
    
    <form action="G10-process.php" method="POST" id="signup-form">

        <!-- エラーメッセージの表示 -->
        <?php if ($error_message): ?>
            <p class="message"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <!-- 名前 ① -->
        <div class="input-box">
            <label>名前</label>
            <input type="text" name="name" placeholder="例：山田　タロウ" required>
        </div>

        <!-- メール -->
        <div class="input-box">
            <label>メールアドレス</label>
            <input type="email" name="email" placeholder="例：@example.com" required>
        </div>

        <!-- パスワード ② -->
        <div class="input-box">
            <label>パスワード</label>
            <input type="password" name="password" placeholder="8文字以上" required>
        </div>

        <!-- 住所 -->
        <div class="input-box">
            <label>住所</label>
            <input type="text" name="address" placeholder="例：東京都中央区日本橋1-2-3" required>
        </div>

        <!-- 郵便番号 ③ -->
        <div class="input-box">
            <label>郵便番号</label>
            <input type="text" name="postal_code" placeholder="例：123-4567" required>
        </div>

        <!-- 利用規約チェック ④ -->
        <div class="terms">
            会員登録には、<a href="./G11.php" target="_blank">利用規約</a>への同意が必要です。
        </div>

        <div class="agree-area">
            <input type="checkbox" name="agree" id="agree-checkbox" value="1">
            <label for="agree-checkbox">同意して作成</label>
        </div>

        <button type="submit" class="create-btn" id="create-btn" disabled>同意して作成</button>
        
    </form>
    
</div>

<!-- JavaScript (同意チェックでボタンを有効化) -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const agreeCheckbox = document.getElementById('agree-checkbox');
    const createButton = document.getElementById('create-btn');

    // チェックボックスの状態が変わったときに実行
    agreeCheckbox.addEventListener('input', () => {
        // チェックされていれば disabled を解除、されていなければ disabled にする
        if (agreeCheckbox.checked) {
            createButton.disabled = false;
        } else {
            createButton.disabled = true;
        }
    });
});
</script>

</body>
</html>
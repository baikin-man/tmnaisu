<?php session_start();?>
<?php require 'db-connect.php';?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='./css/G2.css'>
    <title>ZeZe</title>
</head>
<body>
    <?php require 'heade2.php';?>
<div class="wrap">
  <h2>新規登録</h2>

  <!-- 名前 ① -->
  <div class="input-box">
    <label>名前</label>
    <input type="text" placeholder="例：山田　タロウ">
  </div>

  <!-- メール -->
  <div class="input-box">
    <label>メールアドレス</label>
    <input type="email" placeholder="例：@example.com">
  </div>

  <!-- パスワード ② -->
  <div class="input-box">
    <label>パスワード</label>
    <input type="password" placeholder="OOOOOOOO">
  </div>

  <!-- 住所 -->
  <div class="input-box">
    <label>住所</label>
    <input type="text" placeholder="例：東京都中央区日本橋1-2-3">
  </div>

  <!-- 郵便番号 ③ -->
  <div class="input-box">
    <label>郵便番号</label>
    <input type="text" placeholder="例：1234-56">
  </div>

  <!-- 利用規約チェック ④ -->
  <div class="terms">
    会員登録には、<a href="./G11.php">利用規約</a>への同意が必要です。
  </div>

  <div class="agree-area">
    <input type="checkbox" id="agree">
    <label for="agree">同意して作成</label>
  </div>

  <button class="create-btn">同意して作成</button>
</body>
</html>
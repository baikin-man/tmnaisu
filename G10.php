<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ZeZe - 新規登録</title>

<style>
  body {
    margin: 0;
    font-family: "Hiragino Kaku Gothic ProN", "メイリオ", sans-serif;
    background: url('bg.jpg') center/cover no-repeat;
  }

  /* 上部ヘッダー */
  header {
    width: 100%;
    padding: 15px 0;
    background-color: #777;
    color: #fff;
    font-size: 28px;
    text-align: center;
    letter-spacing: 2px;
    font-weight: bold;
  }

  .wrap {
    width: 420px;
    margin: 40px auto;
    padding: 35px 45px;
    background: rgba(255,255,255,0.85);
    border-radius: 15px;
    box-shadow: 0 5px 18px rgba(0,0,0,0.15);
  }

  h2 {
    text-align: center;
    font-size: 28px;
    margin-bottom: 25px;
  }

  .input-box {
    margin-bottom: 18px;
  }

  .input-box label {
    display: block;
    font-weight: bold;
    color: #555;
    margin-bottom: 6px;
  }

  .input-box input {
    width: 100%;
    padding: 14px 12px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 8px;
    outline: none;
  }

  .input-box input::placeholder {
    color: #bbb;
  }

  /* 利用規約部分 */
  .terms {
    font-size: 14px;
    color: #333;
    margin: 10px 0 18px;
  }

  .terms a {
    color: #1a73e8;
    text-decoration: underline;
  }

  /* 登録ボタン */
  .create-btn {
    width: 100%;
    padding: 12px 0;
    font-size: 18px;
    border: none;
    border-radius: 8px;
    background-color: #8ec8ff;
    cursor: pointer;
    font-weight: bold;
    transition: 0.2s;
  }

  .create-btn:hover {
    background-color: #6db6ff;
  }

  /* チェックボックス */
  .agree-area {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 15px;
  }

  .agree-area input {
    transform: scale(1.25);
  }
</style>
</head>

<body>

<header>ZeZe</header>

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
    会員登録には、<a href="G11.php">利用規約</a>への同意が必要です。
  </div>

  <div class="agree-area">
    <input type="checkbox" id="agree">
    <label for="agree">同意して作成</label>
  </div>

  <button class="create-btn">同意して作成</button>

</div>

</body>
</html>

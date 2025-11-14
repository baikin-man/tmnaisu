<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
  <title>ZeZe - 注文確認</title>
  <style>
    body {
      font-family: "Hiragino Kaku Gothic ProN", "メイリオ", sans-serif;
      background-color: #f8f8f8;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #555;
      color: white;
      text-align: center;
      padding: 15px 0;
      font-size: 24px;
      font-weight: bold;
      letter-spacing: 1px;
    }

    .main {
      max-width: 700px;
      background: white;
      margin: 30px auto;
      border-radius: 12px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
      padding: 30px;
    }

    .total {
      text-align: center;
      margin-bottom: 25px;
    }

    .total del {
      font-size: 20px;
      color: #999;
    }

    .discount {
      color: #e74c3c;
      font-size: 14px;
      display: block;
      margin-top: 5px;
    }

    .total strong {
      color: #e74c3c;
      font-size: 28px;
    }

    .container {
      border: 2px solid #ddd;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 30px;
      background: #fafafa;
    }

    .pay-option {
      margin-bottom: 20px;
    }

    .pay-option label {
      display: block;
      font-weight: bold;
      margin-bottom: 8px;
    }

    .conbini p {
      margin-bottom: 8px;
      color: #555;
      font-size: 14px;
    }

    .conbini label {
      display: block;
      margin-left: 20px;
      margin-bottom: 5px;
      color: #333;
    }

    .card-input input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }

    .buttons {
      text-align: center;
    }

    .buttons button {
      padding: 12px 25px;
      border: none;
      border-radius: 25px;
      font-size: 16px;
      cursor: pointer;
      margin: 8px;
      font-weight: bold;
      transition: all 0.2s ease;
    }

    .address-btn {
      background-color: #f0f0f0;
      color: #333;
    }

    .address-btn:hover {
      background-color: #ddd;
    }

    .order-btn {
      background-color: #ffcc33;
      color: #000;
    }

    .order-btn:hover {
      background-color: #ffb700;
    }
  </style>
</head>
<body>

<<<<<<< Updated upstream
<?php require 'header2.php';?>

=======
>>>>>>> Stashed changes
<div class="main">

  <div class="total">
    <div>商品合計</div>
    <del>¥28,190</del>
    <span class="discount">¥2,819 off!!</span>
    <strong>¥25,371</strong>
  </div>

  <div class="container">
    <div class="pay-option">
      <label><input type="radio" name="payment" checked> 現金で支払い</label>
      <div class="conbini">
        <p>コンビニを選択</p>
        <label><input type="radio" name="conbini"> ハイソン</label>
        <label><input type="radio" name="conbini"> ソロマート</label>
        <label><input type="radio" name="conbini"> イレブンセブン</label>
        <label><input type="radio" name="conbini"> ミニスタート</label>
      </div>
    </div>

    <div class="pay-option">
      <label><input type="radio" name="payment"> カードで支払い</label>
      <div class="card-input">
        <input type="text" maxlength="19" placeholder="XXXX-XXXX-XXXX-XXXX">
      </div>
    </div>
  </div>

  <div class="buttons">
    <button class="address-btn">住所を変更する</button>
    <button class="order-btn">注文を確定する</button>
  </div>

</div>

</body>
</html>

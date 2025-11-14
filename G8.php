<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/header2.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
  <title>ZeZe - 注文確認</title>
  <?php require_once 'header2.php'; ?>
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

    .card-form {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-top: 10px;
    }

    .card-form .full {
      grid-column: 1 / -1;
    }

    .card-form label {
      font-size: 14px;
      color: #333;
      margin-bottom: 6px;
      display: block;
    }

    .card-form input, .card-form select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
      box-sizing: border-box;
    }

    .small-row {
      display: flex;
      gap: 10px;
    }

    .small-row .half {
      flex: 1 1 0;
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

    /* レスポンシブ微調整 */
    @media (max-width: 520px) {
      .card-form {
        grid-template-columns: 1fr;
      }
      .small-row {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>


<?php require 'header2.php';?>


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

      <!-- カード入力フォーム -->
      <form class="card-input-form" onsubmit="return false;" autocomplete="on" aria-label="クレジットカード情報">
        <div class="card-form">
          <!-- カード番号（ハイフン自動挿入） -->
          <div class="full">
            <label for="card-number">カード番号</label>
            <input id="card-number" name="card-number" type="text" inputmode="numeric"
                   maxlength="19" placeholder="XXXX-XXXX-XXXX-XXXX"
                   autocomplete="cc-number" aria-describedby="card-number-help" />
            <small id="card-number-help" style="color:#666;display:block;margin-top:6px;font-size:13px;">
              4桁ごとにハイフンが自動で入ります。
            </small>
          </div>

          <!-- カード名義 -->
          <div class="full">
            <label for="card-name">カード名義（ローマ字）</label>
            <input id="card-name" name="card-name" type="text" autocomplete="cc-name" placeholder="TARO YAMADA" />
          </div>

          <!-- 有効期限とセキュリティコード -->
          <div class="small-row">
            <div class="half">
              <label for="exp-month">有効期限</label>
              <div style="display:flex; gap:8px;">
                <select id="exp-month" name="exp-month" autocomplete="cc-exp-month" aria-label="有効期限 月">
                  <option value="">MM</option>
                  <option value="01">01</option><option value="02">02</option><option value="03">03</option>
                  <option value="04">04</option><option value="05">05</option><option value="06">06</option>
                  <option value="07">07</option><option value="08">08</option><option value="09">09</option>
                  <option value="10">10</option><option value="11">11</option><option value="12">12</option>
                </select>

                <select id="exp-year" name="exp-year" autocomplete="cc-exp-year" aria-label="有効期限 年">
                  <option value="">YY</option>
                  <!-- 今後の年は必要に応じて増やしてください -->
                  <option value="25">25</option><option value="26">26</option><option value="27">27</option>
                  <option value="28">28</option><option value="29">29</option><option value="30">30</option>
                </select>
              </div>
            </div>

            <div class="half">
              <label for="cvc">セキュリティコード（CVC/CVV）</label>
              <input id="cvc" name="cvc" type="text" inputmode="numeric" maxlength="4" placeholder="123" autocomplete="cc-csc" />
            </div>
          </div>

        </div>
      </form>

    </div>
  </div>

  <div class="buttons">
    <button class="address-btn">住所を変更する</button>
    <button class="order-btn">注文を確定する</button>
  </div>

</div>

<script>
  (function() {
    const cardInput = document.getElementById('card-number');
    const cvcInput = document.getElementById('cvc');

    // 数字のみの正規表現
    function digitsOnly(str) {
      return str.replace(/\D/g, '');
    }

    // カード番号を4桁ごとにハイフン挿入（最大16桁まで表示）
    function formatCardNumber(value) {
      const digits = digitsOnly(value).slice(0, 16); // カード番号は最大16桁想定（必要なら調整）
      const parts = [];
      for (let i = 0; i < digits.length; i += 4) {
        parts.push(digits.substring(i, i + 4));
      }
      return parts.join('-');
    }

    // 入力イベント
    cardInput.addEventListener('input', (e) => {
      const prev = cardInput.value;
      const formatted = formatCardNumber(prev);
      cardInput.value = formatted;
    });

    // 貼り付け時の処理（非数字を除去してフォーマット）
    cardInput.addEventListener('paste', (e) => {
      e.preventDefault();
      const text = (e.clipboardData || window.clipboardData).getData('text');
      const formatted = formatCardNumber(text);
      cardInput.value = formatted;
    });

    // CVCは数字のみ
    cvcInput.addEventListener('input', () => {
      cvcInput.value = digitsOnly(cvcInput.value).slice(0, 4);
    });

    // フォーカス時に既存のハイフンを残したいが、カーソル位置簡素化のためカーソル末尾へ
    cardInput.addEventListener('focus', () => {
      // カーソルを末尾に置く
      const len = cardInput.value.length;
      cardInput.setSelectionRange(len, len);
    });

    // 補助: Enterで何かしたい場合はここに追加できます
    // 例: document.querySelector('.order-btn').addEventListener('click', submitPayment);
  })();
</script>

</body>
</html>

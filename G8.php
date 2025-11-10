<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>G8</title>
</head>
<body>

<div class="total">
  <div>商品合計</div>
  <del>¥28,190</del><br>
  <span class="discount">¥2,819 off!!</span><br>
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
<div class="cart"></div>
<div class="buttons">
  <button class="address-btn">住所を変更する</button>
  <button class="order-btn">注文を確定する</button>
</div> 

</body>
</html>
<?php session_start(); ?>
<?php require 'db-connect.php'; ?>
<?php
// --- 1. ログインチェック ---
if (!isset($_SESSION['user_id'])) {
  header('Location: G1.php');
  exit;
}

$user_id = $_SESSION['user_id'];
$cart_items = [];
$total_price = 0;
$address = null;

// --- 2. データベースからカート情報と住所を取得 ---
try {
  $pdo = new PDO($connect, USER, PASS);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // (A) カート情報を取得
  $sql_cart = "SELECT
                    carts.id AS cart_id, carts.quantity,
                    item_skus.price, item_skus.image_url, item_skus.size, item_skus.color,
                    items.name AS item_name
                FROM carts
                JOIN item_skus ON carts.item_sku_id = item_skus.id
                JOIN items ON item_skus.item_id = items.id
                WHERE carts.user_id = ?
                AND carts.status = 0"; // ★論理削除のため status=0 を追加

  $stmt_cart = $pdo->prepare($sql_cart);
  $stmt_cart->execute([$user_id]);
  $cart_items = $stmt_cart->fetchAll(PDO::FETCH_ASSOC);

  // (B) カートが空なら、G4.php（カート）に戻す
  if (empty($cart_items)) {
    header('Location: G4.php');
    exit;
  }

  // (C) 合計金額を計算
  foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
  }

  // (D) ユーザーのデフォルト住所を取得
  $sql_addr = $pdo->prepare('SELECT * FROM addresses WHERE user_id = ? ORDER BY id ASC LIMIT 1');
  $sql_addr->execute([$user_id]);
  $address = $sql_addr->fetch(PDO::FETCH_ASSOC);

  if (empty($address)) {
    // 住所が未登録の場合は登録ページへ
    header('Location: G10.php?error=no_address');
    exit;
  }
} catch (PDOException $e) {
  echo "データベース接続エラー: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">

  <title>ZeZe - 注文確認</title>

  <!-- ★ 外部CSSの読み込み ★ -->
  <link rel="stylesheet" href="header2.css"> <!-- ヘッダーCSS -->
  <link rel="stylesheet" href="./css/G8.css"> <!-- このページ専用CSS -->

  <!-- ★ 固定ヘッダー用の余白 ★ -->
  <style>
    body {
      padding-top: 115px;
    }
  </style>
</head>

<body>

  <?php require 'header2.php'; ?>

  <div class="main">

    <form action="G8-process.php" method="POST" id="order-form">

      <div class="summary-container">
        <h3>注文概要</h3>
        <?php foreach ($cart_items as $item): ?>
          <div class="summary-item">
            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="">
            <div class="summary-details">
              <div class="name"><?php echo htmlspecialchars($item['item_name']); ?></div>
              <div class="sku">
                <?php echo htmlspecialchars($item['size']); ?> / <?php echo htmlspecialchars($item['color']); ?>
              </div>
            </div>
            <div class="summary-price">
              ¥<?php echo number_format($item['price'] * $item['quantity']); ?>
              (<?php echo htmlspecialchars($item['quantity']); ?>点)
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="total">
        <div>商品合計</div>
        <strong>¥<?php echo number_format($total_price); ?></strong>
      </div>

      <div class="address-container">
        <h3>お届け先</h3>
        <?php if ($address): ?>
          <p class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'お客様'); // ユーザー名がセッションにあれば表示 
                                ?> 様</p>
          <p>〒<?php echo htmlspecialchars($address['postal_code']); ?></p>
          <p><?php echo htmlspecialchars($address['address']); ?></p>
        <?php endif; ?>

        <!-- TODO: G9.php (サンキュー) ではなく、住所変更ページ (例: G_address_edit.php) に変更してください -->
        <a href="G_address_edit.php" class="address-btn">変更する</a>
      </div>

      <div class="container"> <!-- (注: G8.css で .container スタイルが定義されています) -->
        <h3>お支払い方法</h3>

        <div class="pay-option">
          <label>
            <input type="radio" name="payment_method" value="conbini" checked>
            現金で支払い (コンビニ)
          </label>
          <div class="conbini" id="conbini-details">
            <p>コンビニを選択</p>
            <label><input type="radio" name="conbini_choice" value="hyson" checked> ハイソン</label>
            <label><input type="radio" name="conbini_choice" value="solomart"> ソロマート</label>
            <label><input type="radio" name="conbini_choice" value="elevenseven"> イレブンセブン</label>
            <label><input type="radio" name="conbini_choice" value="ministart"> ミニスタート</label>
          </div>
        </div>

        <div class="pay-option">
          <label>
            <input type="radio" name="payment_method" value="card">
            カードで支払い
          </label>
          <div class="card-input-form" id="card-details" style="display:none;">
            <div class="card-form">
              <div class="full">
                <label for="card-number">カード番号</label>
                <input id="card-number" name="card-number" type="text" inputmode="numeric"
                  maxlength="19" placeholder="XXXX-XXXX-XXXX-XXXX"
                  autocomplete="cc-number" aria-describedby="card-number-help" />
              </div>
              <div class="full">
                <label for="card-name">カード名義（ローマ字）</label>
                <input id="card-name" name="card-name" type="text" autocomplete="cc-name" placeholder="TARO YAMADA" />
              </div>
              <div class="small-row">
                <div class="half">
                  <label for="exp-month">有効期限</label>
                  <div style="display:flex; gap:8px;">
                    <select id="exp-month" name="exp-month" autocomplete="cc-exp-month" aria-label="有効期限 月">
                      <option value="">MM</option>
                      <option value="01">01</option>
                      <option value="02">02</option>
                      <option value="03">03</option>
                      <option value="04">04</option>
                      <option value="05">05</option>
                      <option value="06">06</option>
                      <option value="07">07</option>
                      <option value="08">08</option>
                      <option value="09">09</option>
                      <option value="10">10</option>
                      <option value="11">11</option>
                      <option value="12">12</option>
                    </select>
                    <select id="exp-year" name="exp-year" autocomplete="cc-exp-year" aria-label="有効期限 年">
                      <option value="">YY</option>
                      <option value="25">25</option>
                      <option value="26">26</option>
                      <option value="27">27</option>
                      <option value="28">28</option>
                      <option value="29">29</option>
                      <option value="30">30</option>
                    </select>
                  </div>
                </div>
                <div class="half">
                  <label for="cvc">セキュリティコード</label>
                  <input id="cvc" name="cvc" type="text" inputmode="numeric" maxlength="4" placeholder="123" autocomplete="cc-csc" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="buttons">
        <button type="submit" class="order-btn">注文を確定する</button>
      </div>

    </form>
  </div>

  <script>
    // --- カード番号の自動ハイフン入力 ---
    (function() {
      const cardInput = document.getElementById('card-number');
      const cvcInput = document.getElementById('cvc');

      function digitsOnly(str) {
        return str.replace(/\D/g, '');
      }

      function formatCardNumber(value) {
        const digits = digitsOnly(value).slice(0, 16);
        const parts = [];
        for (let i = 0; i < digits.length; i += 4) {
          parts.push(digits.substring(i, i + 4));
        }
        return parts.join('-');
      }
      cardInput.addEventListener('input', (e) => {
        const prev = cardInput.value;
        const formatted = formatCardNumber(prev);
        cardInput.value = formatted;
      });
      cardInput.addEventListener('paste', (e) => {
        e.preventDefault();
        const text = (e.clipboardData || window.clipboardData).getData('text');
        const formatted = formatCardNumber(text);
        cardInput.value = formatted;
      });
      cvcInput.addEventListener('input', () => {
        cvcInput.value = digitsOnly(cvcInput.value).slice(0, 4);
      });
      cardInput.addEventListener('focus', () => {
        const len = cardInput.value.length;
        cardInput.setSelectionRange(len, len);
      });
    })();
  </script>

  <script>
    // --- 支払い方法の表示切り替え ---
    document.addEventListener('DOMContentLoaded', () => {
      const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
      const conbiniDetails = document.getElementById('conbini-details');
      const cardDetails = document.getElementById('card-details');

      function togglePaymentDetails() {
        if (this.value === 'card') {
          conbiniDetails.style.display = 'none';
          cardDetails.style.display = 'block';
        } else {
          conbiniDetails.style.display = 'block';
          cardDetails.style.display = 'none';
        }
      }
      paymentRadios.forEach(radio => {
        radio.addEventListener('change', togglePaymentDetails);
      });
      // 初期状態（カードフォームは非表示）
      cardDetails.style.display = 'none';
    });
  </script>

</body>

</html>
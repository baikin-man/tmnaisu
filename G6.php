<?php
session_start();
require 'db-connect.php';

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: G1.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

// 更新完了等のメッセージ取得
if (isset($_GET['success'])) {
    $message = '<div class="success-msg">会員情報を更新しました。</div>';
} elseif (isset($_GET['error'])) {
    $message = '<div class="error-msg">更新に失敗しました。入力内容を確認してください。</div>';
}

try {
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ユーザー情報と住所を取得
    $sql = "
        SELECT u.name, u.email, a.postal_code, a.address 
        FROM users u
        LEFT JOIN addresses a ON u.id = a.user_id
        WHERE u.id = ?
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // 郵便番号を3桁と4桁に分割（表示用）
    $zip1 = '';
    $zip2 = '';
    if (!empty($user_data['postal_code'])) {
        // ハイフンがある場合とない場合に対応
        $zip_clean = str_replace('-', '', $user_data['postal_code']);
        $zip1 = substr($zip_clean, 0, 3);
        $zip2 = substr($zip_clean, 3);
    }

} catch (PDOException $e) {
    $message = '<div class="error-msg">DBエラー: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>会員情報の変更 | ZeZe</title>
  <link rel="stylesheet" href="./css/header.css">
  <link rel="stylesheet" href="./css/G6.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
  <style>
    /* G6.css の微調整 */
    body { padding-top: 80px; } /* ヘッダー高さ分 */
    .success-msg { color: #155724; background: #d4edda; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #c3e6cb; }
    .error-msg { color: #721c24; background: #f8d7da; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #f5c6cb; }
  </style>
</head>

<body>
  <?php require 'header.php'; ?>

  <div class="container">
    <h2>会員情報の変更</h2>
    
    <?php echo $message; ?>

    <form method="post" action="address-update.php">
      
      <label>お名前<br>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user_data['name'] ?? ''); ?>" required>
      </label>

      <label>郵便番号</label>
      <div class="postcode">
        <input type="text" name="zip1" id="num1" maxlength="3" value="<?php echo htmlspecialchars($zip1); ?>" placeholder="123" required> -
        <input type="text" name="zip2" id="num2" maxlength="4" value="<?php echo htmlspecialchars($zip2); ?>" placeholder="4567" required>
        <button type="button" id="searchAddress">住所検索</button>
      </div>

      <label>住所<br>
        <input type="text" name="address" id="full_address" value="<?php echo htmlspecialchars($user_data['address'] ?? ''); ?>" required placeholder="例: 東京都〇〇区... 建物名">
      </label>

      <div style="margin-top: 20px; text-align: center;">
        <button type="submit" style="padding: 10px 30px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">変更を保存する</button>
        <a href="G5.php" style="margin-left: 15px; color: #666; text-decoration: none;">マイページへ戻る</a>
      </div>
    </form>
  </div>

  <script>
    document.getElementById("searchAddress").addEventListener("click", function() {
      const num1 = document.getElementById("num1").value;
      const num2 = document.getElementById("num2").value;
      const zipcode = num1 + num2;

      if (!/^[0-9]{7}$/.test(zipcode)) {
        alert("郵便番号を正しく入力してください（半角数字）");
        return;
      }

      const url = `https://zipcloud.ibsnet.co.jp/api/search?zipcode=${zipcode}`;

      fetch(url)
        .then(response => response.json())
        .then(data => {
          if (data.results) {
            const result = data.results[0];
            const fullAddress = `${result.address1}${result.address2}${result.address3}`;
            document.getElementById("full_address").value = fullAddress;
          } else {
            alert("該当する住所が見つかりませんでした。");
          }
        })
        .catch(error => {
          alert("通信エラーが発生しました。");
          console.error(error);
        });
    });
  </script>
</body>
</html>
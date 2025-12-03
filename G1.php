<?php session_start(); ?>
<?php
// ▼▼▼ PHP処理ブロック (変更なし) ▼▼▼
if (isset($_SESSION['user_id'])) {
  header('Location: G2.php');
  exit;
}

$error_message = '';
if (isset($_GET['error'])) {
  if ($_GET['error'] == '1') {
    $error_message = 'メールアドレスまたはパスワードが違います。';
  } else if ($_GET['error'] == 'db') {
    $error_message = 'データベースエラーが発生しました。';
  } else if ($_GET['error'] == 'exists') {
    $error_message = 'そのメールアドレスは既に使用されています。';
  }
}

$success_message = '';
if (isset($_GET['signup']) && $_GET['signup'] == 'success') {
  $success_message = 'アカウント作成が完了しました。ログインしてください。';
}

?>
<!doctype html>
<html lang="ja">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>ZeZe — Login</title>

  <link rel="stylesheet" href="./css/G1.css">
  <link rel="stylesheet" href="./css/header.css">
</head>

<body>
  <?php require 'header.php'; ?>

  <div class="wrap">
    <div class="login-card" role="region" aria-label="ログイン">
      <h2>Login</h2>

      <?php if ($error_message): ?>
        <p class="message error-message"><?php echo htmlspecialchars($error_message); ?></p>
      <?php endif; ?>

      <?php if ($success_message): ?>
        <p class="message success-message"><?php echo htmlspecialchars($success_message); ?></p>
      <?php endif; ?>

      <form action="login-process.php" method="post" novalidate>
        <label class="field" for="email">
          <input id="email" name="email" type="email" placeholder="メールアドレス" autocomplete="email" required />
        </label>
        <label class="field" for="pass">
          <input id="pass" name="password" type="password" placeholder="パスワード" autocomplete="current-password" required />
        </label>
        <button class="btn-login" type="submit">ログイン</button>
        <div class="card-links" aria-hidden="false">
          <a href="G10.php" style="text-align:left;">アカウント作成</a>
          </div>
      </form>
    </div>
  </div>

</body>
</html>
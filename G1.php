<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>ZeZe — Login</title>
<style>
  /* ベース */
  :root{
    --header-bg: #777;
    --card-bg: rgba(20,20,20,0.75);
    --input-bg: rgba(255,255,255,0.85);
    --accent: #4b6be6;
    --text: #fff;
  }
  *{box-sizing:border-box}
  html,body{height:100%}
  body{
    margin:0;
    font-family: "Helvetica Neue", Arial, sans-serif;
    color:var(--text);
    -webkit-font-smoothing:antialiased;
    -moz-osx-font-smoothing:grayscale;
    /* 背景画像（ここを差し替えてください） */
    background-image: url('/image/rogin.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position:relative;
  }

  /* 背景上に薄い暗いオーバーレイを重ねる */
  body::before{
    content:"";
    position:fixed;
    inset:0;
    background-color: rgba(0, 0, 0, 0.3); /* 0.5は透明度。0〜1で調整 */
    background-size: cover;
    pointer-events:none;
    z-index: 0;
  }
  
  .site-header a{ color:inherit; text-decoration:none; }

  /* メイン：ログインカードを縦中央に配置（ヘッダーを除く） */
  .wrap{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding-top:120px; /* ヘッダー分の余白を確保 */
    position:relative;
    z-index:10;
  }

  /* カード */
  .login-card{
    width: 420px;
    max-width:90%;
    padding: 34px 30px 22px;
    background: var(--card-bg);
    border-radius: 48px;
    box-shadow: 0 18px 30px rgba(0,0,0,0.6), inset 0 1px 0 rgba(255,255,255,0.02);
    text-align:center;
    color:#fff;
    backdrop-filter: blur(6px);
  }

  .login-card h2{
    margin:0 0 22px;
    font-size:28px;
    letter-spacing:1px;
    font-weight:700;
  }

  /* inputラウンドデザイン */
  .field{
    display:block;
    width:100%;
    margin:10px 0;
  }
  .field input{
    width:100%;
    display:block;
    padding:14px 18px;
    border-radius:999px;
    border: none;
    background: var(--input-bg);
    color:#333;
    font-size:16px;
    outline:none;
    box-shadow: 0 1px 0 rgba(0,0,0,0.06);
  }
  .field input::placeholder{ color: rgba(0,0,0,0.35); }

  /* ログインボタン */
  .btn-login{
    display:inline-block;
    margin:18px auto 12px;
    padding:12px 36px;
    border-radius:28px;
    background: linear-gradient(180deg, var(--accent), #2f4bd7);
    color:white;
    font-weight:800;
    font-size:20px;
    border:none;
    cursor:pointer;
    box-shadow: 0 10px 18px rgba(75,107,230,0.28), 0 3px 6px rgba(0,0,0,0.45);
  }
  .btn-login:active{ transform: translateY(1px); }

  /* 下部リンク */
  .card-links{
    display:flex;
    justify-content:space-between;
    gap:16px;
    margin-top:8px;
    font-size:14px;
    color: rgba(255,255,255,0.9);
  }
  .card-links a{
    color: rgba(255,255,255,0.95);
    text-decoration:none;
    opacity:0.95;
  }

  /* カード周りのシャドウの外側の丸み強調（画像寄せ） */
  .login-card::after{
    content:"";
    position:absolute;
    left:50%;
    transform:translateX(-50%);
    bottom:-26px;
    width:86%;
    height:38px;
    background: rgba(0,0,0,0.35);
    filter: blur(18px);
    z-index:-1;
    border-radius:22px;
  }

  /* レスポンシブでロゴ縮小 */
  @media (max-width:480px){
    .site-header{ height:96px; padding-bottom:6px; }
    .site-header .brand{ font-size:56px; }
    .login-card{ width:92%; padding: 26px 18px; border-radius:28px;}
    .login-card h2{ font-size:20px; }
    .btn-login{ font-size:18px; padding:10px 26px; }
  }
</style>
</head>
<body>

  <!-- ヘッダー（ロゴをクリックで G2.php に飛ばす） -->
  <header class="site-header">
  </header>

  <!-- メイン -->
  <div class="wrap">
    <?php
    require './header1.php';
    ?>
    <div class="login-card" role="region" aria-label="ログイン">
      <h2>Login</h2>

      <form action="#" method="post" novalidate>
        <label class="field" for="user">
          <input id="user" name="user" type="text" placeholder="アカウント名またはメールアドレス" autocomplete="username" />
        </label>

        <label class="field" for="pass">
          <input id="pass" name="pass" type="password" placeholder="パスワード" autocomplete="current-password" />
        </label>

        <button class="btn-login" type="submit">ログイン</button>

        <div class="card-links" aria-hidden="false">
          <a href="#" style="text-align:left;">アカウント作成</a>
          <a href="#" style="text-align:right;">パスワードを忘れた場合</a>
        </div>
      </form>
    </div>
  </div>

</body>
</html>

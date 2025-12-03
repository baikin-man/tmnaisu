<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<div class="kotei">
    <header class="header">
        <h1 id="logo"><a href="G2.php">ZeZe</a></h1>

        <form action="G7.php" method="get" class="search-box">
            <input type="text" name="keyword" placeholder="キーワードを入力...">
            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i>
            </button>
        </form>

        <a href="G4.php" class="cart-link" aria-label="カートへ">
            <i class="fas fa-shopping-cart" id="cart-icon"></i>
        </a>
    </header>

    <input type="checkbox" id="menu-toggle" hidden>
    <div class="menu-wrapper">
        <label class="menu-icon" for="menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </label>
    </div>
    <div class="overlay"></div>

    <nav class="menu">
        <ul>
            <h2>人気・新着の商品</h2>
            <li><a href="G7.php?sort=popular">ランキング</a></li>
            <li><a href="G7.php?sort=new">新着商品</a></li>
            <p>────────────</p>

            <?php if (isset($_SESSION['user_id'])): ?>
                <h2>会員メニュー</h2>
                <li><a href="G5.php">マイページ (注文・閲覧履歴)</a></li>
                <li><a href="G6.php">住所・会員情報の変更</a></li>
                <li><a href="logout.php">ログアウト</a></li>
            <?php else: ?>
                <h2>アカウント</h2>
                <li><a href="G1.php">ログイン</a></li>
                <li><a href="G10.php">新規登録</a></li>
            <?php endif; ?>

            <p>────────────</p>
            </ul>
    </nav>
</div>
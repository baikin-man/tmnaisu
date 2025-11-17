<!-- header.php -->

<div class="kotei">
    <header class="header">
        <h1 id="left"><a href="G2.php">ZeZe</a></h1>

        <a href="G4.php" class="cart-link" aria-label="カートへ">
            <i class="fas fa-shopping-cart" id="cart-icon"></i>
        </a>

        <!-- 検索ボックス (変更なし：このままでG7.phpへ飛びます) -->
        <form action="G7.php" method="get" class="search-box">
            <input type="text" name="keyword" placeholder="キーワードを入力...">
            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </header>

    <!-- ハンバーガーメニュー用のチェックボックス -->
    <input type="checkbox" id="menu-toggle" hidden>

    <!-- ハンバーガーアイコン -->
    <div class="menu-wrapper">
        <label class="menu-icon" for="menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </label>
    </div>

    <!-- オーバーレイ背景 -->
    <div class="overlay"></div>

    <!-- メニュー本体 -->
    <nav class="menu">
        <ul>
            <h2>人気・新着の商品</h2>
            <!-- ▼▼▼ リンク先を修正 ▼▼▼ -->
            <li><a href="G7.php?sort=popular">ランキング</a></li>
            <li><a href="G7.php?sort=new">新着商品</a></li>
            <!-- ▲▲▲ ▲▲▲ -->
            <p>────────────</p>

            <?php 
            if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['user_id'])): 
            ?>
                <!-- ログイン時 -->
                <h2>設定とヘルプ</h2>
                <li><a href="#">アカウントサービス</a></li>
                <li><a href="#">住所を変更</a></li>
                <li><a href="#">日本語</a></li>
                <li><a href="logout.php">ログアウト</a></li>
            <?php else: ?>
                <!-- 未ログイン時 -->
                <h2>アカウント</h2>
                <li><a href="G1.php">ログイン</a></li>
                <li><a href="G10.php">新規登録</a></li>
                <li><a href="#">日本語</a></li>
            <?php endif; ?>
            
            <p>────────────</p>
            <li><a href="#">お問い合わせ</a></li>
        </ul>
    </nav>
</div>
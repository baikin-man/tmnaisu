<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZeZe - ECサイト風ヘッダー</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: sans-serif;
        }

        /* ヘッダー全体 */
        .site-header {
            background-color: #666666; /* 濃い灰色 */
            color: white; /* テキストとアイコンの色 */
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
        }

        /* ロゴのコンテナ */
        .logo {
            margin-right: 20px;
        }
        
        /* ロゴテキスト */
        .logo h1 {
            font-size: 65px;
            font-weight: bold;
            margin: 20px 0;
            letter-spacing: 2px;
            text-indent: 180px;
        }

        /* ロゴリンク */
        .logo a {
            color: white;              /* 白文字 */
            text-decoration: none;     /* 下線消す */
        }

        .logo a:hover {
            opacity: 0.8;              /* ホバーで少し薄くする */
        }

        /* ハンバーガーメニュー */
        .menu-icon {
            width: 30px;
            height: 30px;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            cursor: pointer;
            margin-right: 20px;
        }

        .menu-icon span {
            display: block;
            width: 100%;
            height: 3px;
            background-color: white;
        }

        /* 検索バー */
        .search-container {
            flex-grow: 1;
            max-width: 400px;
            margin: 0 20px;
        }

        .search-form {
            display: flex;
            border: 1px solid #ccc;
            border-radius: 5px;
            overflow: hidden;
            background-color: white;
        }

        .search-input {
            width: 100%;
            padding: 10px;
            border: none;
            outline: none;
            font-size: 16px;
        }

        .search-button {
            background-color: white;
            color: #666666;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
        }

        /* カートアイコン */
        .cart-icon {
            font-size: 30px;
            color: white;
            cursor: pointer;
            margin-left: 20px;
        }

        /* メインコンテンツ */
        .main-content {
            padding: 20px;
            background-color: white;
            min-height: 500px;
        }
    </style>
</head>
<body>

    <header class="site-header">
        
        <div style="display: flex; align-items: center;">
            <div class="menu-icon">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <div class="logo">
                <h1><a href="G2.php">ZeZe</a></h1>
            </div>
        </div>

        <div class="search-container">
            <form class="search-form" action="#" method="get">
                <input type="search" class="search-input" placeholder="検索...">
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        
        <div class="cart-icon-container">
            <i class="fas fa-shopping-cart cart-icon"></i>
        </div>
        
    </header>

    <main class="main-content"></main>

</body>
</html>

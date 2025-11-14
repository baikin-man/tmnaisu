<?php session_start();?>
<?php require 'db-connect.php';?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/header2.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <title>ZeZe</title>

    <title>購入確定画面 | ZeZe</title>
    <?php require 'header2.php';?>
    <style>
        body {
            font-family: "Yu Gothic", sans-serif;
            background: #f8f8f8;
            text-align: center;

        }
        .complete-box {
            border: 3px solid #000;
            border-radius: 15px;
            padding: 20px;
            margin: 20px auto;
            width: 300px;
            background: #fff;
            font-size: 20px;
            font-weight: bold;
        }
        .btn-continue {
            display: inline-block;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 10px 20px;
            margin-top: 20px;
            box-shadow: 2px 2px 4px rgba(0,0,0,0.1);
            cursor: pointer;
        }
        .btn-continue:hover {
            background: #f0f0f0;
        }
        .recommend-title {
            font-size: 22px;
            font-weight: bold;
            margin: 40px 0 20px;
        }
        .product-list {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .product {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            padding: 10px;
            width: 120px;
        }
        .product img {
            width: 100%;
            border-radius: 5px;
        }
        .price {
            margin-top: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- ① 購入完了表示 -->
    
    <div class="complete-box">
        購入が確定しました
    </div>

    <!-- ② 買い物を続けるボタン -->
    <button class="btn-continue" onclick="location.href='G2.php'">買い物を続ける</button>

    <!-- ③～⑤ おすすめ商品表示 -->
    <div class="recommend-title">おすすめの商品はこちら</div>
 
    </div>
</body>
</html>

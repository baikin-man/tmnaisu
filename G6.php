<?php session_start();?>
<?php require 'db-connect.php';?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='./css/G2.css'>
    <title>ZeZe</title>
</head>
<body>
    <?php require 'header.php';?>

    <div id="background">
        <div class="container">
            <h2>住所変更</h2>
            <form action=""></form>
            <label>姓<br><input type="text"name="sei"></label><br>
            <label>名<br><input type="text"name="mei"></label><br>
            <label>郵便番号<br><input type="text"name="num1">-
            <input type="text"name="num2"></label>
            <button type="submit">住所を検索</button><br>
            <label>住所・番地<br><input type="text"name="address"></label><br>
            <label>マンション名（部屋番号）<br><input type="text"name="address"></label><br>
            <button>住所を確定</button>
            </form>







        </div>
    </div>

</body>
</html>
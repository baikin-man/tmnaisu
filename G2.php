<?php 

ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();?>
<?php require 'db-connect.php';?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='./css/header2.css'>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <title>ZeZe</title>
</head>
<body>
    <?php require 'header2.php';?>

    <div id="background">
        <div class="container">

            <div class='banner'>

            </div>

            <div class='content1'>
                <div class='mans'></div>
                <div class='ladies'></div>
                <div class='kids'></div>
                <div class='sale'></div>
            </div>

            <div class='content2'>
                <div class='search'>
                    <h3>探す</h3>
                    <ul>
                        <li><a href=''></a></li>
                        <li><a href=''></a></li>
                        <li><a href=''></a></li>
                    </ul>
                </div>
                <div class='search'>
                    <h3>カテゴリー</h3>
                    <ul>
                        <li><a href=''></a></li>
                        <li><a href=''></a></li>
                        <li><a href=''></a></li>
                    </ul>
                </div>
            </div>

<div class="genre-box">
    <a href="#mens" class="genre">メンズ</a>
    <a href="#ladies" class="genre">レディース</a>
    <a href="#kids" class="genre">キッズ</a>
    <a href="#tops" class="genre">トップス</a>
    <a href="#bottoms" class="genre">ボトムス</a>
    <a href="#shirt" class="genre">シャツ</a>
    <a href="#denim" class="genre">デニム</a>
    <a href="#denim-pants" class="genre">デニムパンツ</a>
    <a href="#denim-jacket" class="genre">デニムジャケット</a>
</div>




            <div class='content3'>
                <div class='pop'>
                    
                </div>
            </div>
        </div>
    </div>

</body>
</html>
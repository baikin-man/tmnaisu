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

<form action="G8.php">
            <h2>商品合計</h2>
            <button>レジへ移動</button>
    </form>
            <button><a href="G2.php">買い物を続ける</a></button>

        </div>
    </div>

</body>
</html>
<?php session_start();?>
<?php require 'db-connect.php';?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='./css/G4.css'>
    <title>ZeZe</title>
</head>
<body>
    <?php require 'header.php';?>

    <div id="background">
        <div class="container">

            <h2>商品合計</h2>
            <p class="price-before">¥28,190</p>
            <p class="price-after">¥25,371</p>

            <div class="button-area">
                <form action="G8.php" style="display:inline;">
                    <button type="submit" class="move-btn">レジへ移動</button>
                </form>

                <a href="G2.php" class="continue-btn">買い物を続ける</a>
            </div>

        </div>
    </div>

</body>
</html>

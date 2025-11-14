<?php session_start();?>
<?php require 'db-connect.php';?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/G3.css">
    <title>Product Page</title>
</head>
<script>
document.addEventListener("DOMContentLoaded", () => {

    /* --- main-image とサムネイル --- */
    const thumbs = document.querySelectorAll(".thumbnail-list .thumb");
    const mainImage = document.querySelector(".main-image");

    let currentIndex = 0; // 現在選択中のインデックス

    // 初期 selected を確認
    thumbs.forEach((t, i) => {
        if (t.classList.contains("selected")) {
            currentIndex = i;
        }
    });

    // main-image と selected を反映する共通関数
    function selectThumb(index) {
        thumbs.forEach(t => t.classList.remove("selected"));
        const target = thumbs[index];
        target.classList.add("selected");
        const img = target.getAttribute("data-img");
        mainImage.style.backgroundImage = `url('${img}')`;
    }

    // サムネイルクリック
    thumbs.forEach((thumb, index) => {
        thumb.addEventListener("click", () => {
            currentIndex = index;
            selectThumb(currentIndex);
        });
    });

    /* --- 左右矢印 --- */
    document.querySelector(".nav-btn.left").addEventListener("click", () => {
        currentIndex--;
        if (currentIndex < 0) currentIndex = thumbs.length - 1;
        selectThumb(currentIndex);
    });

    document.querySelector(".nav-btn.right").addEventListener("click", () => {
        currentIndex++;
        if (currentIndex >= thumbs.length) currentIndex = 0;
        selectThumb(currentIndex);
    });


    /* --- サイズ選択 --- */
    const sizeButtons = document.querySelectorAll(".size-list .size");
    sizeButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            sizeButtons.forEach(b => b.classList.remove("selected"));
            btn.classList.add("selected");
        });
    });


    /* --- 色選択 --- */
    const colorButtons = document.querySelectorAll(".color-list .color");
    colorButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            colorButtons.forEach(b => b.classList.remove("selected"));
            btn.classList.add("selected");
        });
    });

});
</script>


<body>
     <?php require 'header.php';?>

    <div class="product-container">
        <div class="image-section">
            <button class="nav-btn left">&#8249;</button>
            <div class="main-image"></div>
            <button class="nav-btn right">&#8250;</button>

            <div class="thumbnail-list">
                <div class="thumb selected"></div>
                <div class="thumb"></div>
                <div class="thumb"></div>
                <div class="thumb"></div>
                <div class="thumb"></div>
                <div class="thumb"></div>
            </div>
        </div>

        <div class="info-section">
            <div class="price">¥19,800</div>

            <div class="size-list">
                <button class="size selected" type="button">S <span>155-165</span></button>
                <button class="size" type="button">M <span>165-175</span></button>
                <button class="size" type="button">L <span>175-185</span></button>
                <button class="size" type="button">XL <span>175-185</span></button>
            </div>

            <div class="color-list">
                <button class="color black" type="button"></button>
                <button class="color white" type="button"></button>
                <button class="color red" type="button"></button>
                <button class="color yellow" type="button"></button>
                <button class="color blue" type="button"></button>
                <button class="color purple" type="button"></button>
            </div>

            <button class="cart-btn">カートに入れる</button>
        </div>
    </div>
</body>
</html>
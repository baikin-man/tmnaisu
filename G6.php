<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>住所変更フォーム</title>
  <link rel="stylesheet" href="./css/G6.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
</head>
      <?php require 'header1.php';?>
<body>
  <div class="container">
    <h2>住所変更</h2>
    <form method="post" action="address-update.php">
      
      <label>姓<br>
        <input type="text" name="sei">
      </label>

      <label>名<br>
        <input type="text" name="mei">
      </label>

      <!-- 郵便番号 -->
      <label>郵便番号</label>
      <div class="postcode">
        <input type="text" id="num1" maxlength="3"> -
        <input type="text" id="num2" maxlength="4">
        <button type="button" id="searchAddress">住所を検索</button>
      </div>

      <!-- 住所欄 -->
      <label>住所・番地<br>
        <input type="text" id="address1" name="address1">
      </label>

      <label>マンション名（部屋番号）<br>
        <input type="text" name="address2">
      </label>

      <button type="submit" style="margin-top: 10px;">住所を確定</button>
    </form>
  </div>

  <!-- ✅ ここにJavaScriptを追加 -->
  <script>
    document.getElementById("searchAddress").addEventListener("click", function() {
      const num1 = document.getElementById("num1").value;
      const num2 = document.getElementById("num2").value;
      const zipcode = num1 + num2;

      if (!/^[0-9]{7}$/.test(zipcode)) {
        alert("正しい郵便番号を入力してください（例：123-4567）");
        return;
      }

      const url = `https://zipcloud.ibsnet.co.jp/api/search?zipcode=${zipcode}`;

      fetch(url)
        .then(response => response.json())
        .then(data => {
          if (data.results) {
            const result = data.results[0];
            const fullAddress = `${result.address1}${result.address2}${result.address3}`;
            document.getElementById("address1").value = fullAddress;
          } else {
            alert("住所が見つかりませんでした。");
          }
        })
        .catch(error => {
          alert("通信エラーが発生しました。");
          console.error(error);
        });
    });
  </script>
</body>
</html>

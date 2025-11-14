<?php require "db-connect.php";?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品・SKU一覧</title>

    <style>
        body { 
            font-family: sans-serif; 
            background-color: #f4f4f4;
            padding-top: 20px;
            margin: 0 10px; /* 左右に少しマージン */
        }
        h1 {
            text-align: center;
        }
        .header-link {
            display: block;
            text-align: center;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        /* item_list.php のスタイル */
        .item-box {
            border: 2px solid #007bff;
            border-radius: 8px;
            margin: 20px auto;
            width: 90%;
            max-width: 800px;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            overflow: hidden; 
        }
        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f0f8ff;
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }
        .item-header h2 {
            margin: 0;
            font-size: 1.2rem;
            color: #333;
        }
        .item-header a {
            font-size: 0.9rem;
            font-weight: bold;
            text-decoration: none;
        }
        
        /* SKU一覧リストのスタイル */
        .sku-list { 
            list-style: none; 
            padding: 0 15px 15px 15px;
            margin: 0;
        }
        .sku-item { 
            display: flex; 
            flex-wrap: wrap; /* スマホ用に折り返し */
            align-items: center; 
            border-bottom: 1px solid #eee; 
            padding: 8px 5px;
        }
        .sku-item img { 
            margin-right: 10px; 
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .sku-item span {
            flex-grow: 1; /* ボタンを右端に寄せるため */
            min-width: 200px; /* 折り返し時の最小幅 */
        }
        
        /* 編集・削除ボタンのコンテナ */
        .sku-buttons {
            margin-left: auto; /* 右寄せ */
            padding-left: 10px; /* spanとの隙間 */
        }

        /* 編集・削除ボタンのスタイル */
        .sku-item a {
            margin-left: 5px;
            border: 1px solid #ccc;
            padding: 2px 5px;
            text-decoration: none;
            border-radius: 3px;
            font-size: 0.9rem;
            white-space: nowrap; /* ボタンが改行しないように */
        }
        .sku-item a.delete-btn {
            border-color: #dc3545;
            color: #dc3545;
        }
        .sku-item a.delete-btn:hover {
            background-color: #dc3545;
            color: #fff;
        }
        .sku-item a.edit-btn:hover {
            background-color: #eee;
        }
        .sku-item a.restore-btn {
            border-color: #28a745; /* 緑色 */
            color: #28a745;
        }
        .sku-item a.restore-btn:hover {
            background-color: #28a745;
            color: #fff;
        }
    </style>
    
</head> 
<body>

    <h1>商品・SKU一覧</h1>
    <a href="item_id_register.php" class="header-link">→ 新しい商品を登録する</a>
    <hr style="width: 90%; max-width: 800px; margin: auto;">

    <?php
        $pdo = new PDO($connect, USER, PASS);
        
        // (N+1対策のロジック)
        // 1. 全SKUを item_id をキーにした配列に格納
        $skus_stmt = $pdo->query("SELECT * FROM item_skus ORDER BY item_id, id");
        $skus_by_item_id = [];
        foreach ($skus_stmt as $sku) {
            $skus_by_item_id[$sku['item_id']][] = $sku;
        }
        
        // 2. 全商品を取得
        $items_stmt = $pdo->query("SELECT * FROM items ORDER BY id DESC");
        
        // 3. 商品ループ (外側)
        foreach ($items_stmt as $item) {
            $item_id = $item['id'];
            $item_name = $item['name'];
    ?>
    
    <div class="item-box">
        <div class="item-header">
            <h2><?php echo htmlspecialchars($item_name); ?> (ID: <?php echo $item_id; ?>)</h2>
            <a href="item_sku_register.php?item_id=<?php echo $item_id; ?>">＋SKUを追加・管理</a>
        </div>
        
        <?php
            // 4. SKU配列から取得 (DBアクセスなし)
            if (isset($skus_by_item_id[$item_id]) && count($skus_by_item_id[$item_id]) > 0) {
                echo '<ul class="sku-list">';
                
                // 5. SKUループ (内側)
                foreach ($skus_by_item_id[$item_id] as $sku) {
                    
                    // ステータス判定
                    $status = $sku['status'];
                    $status_text = '';
                    $style = '';

                    if ($status == 0) {
                        $status_text = ' [削除済み]';
                        $style = 'style="color: #aaa; text-decoration: line-through; background-color: #f9f9f9;"';
                    } elseif ($status == 1) {
                        $status_text = ' [在庫切れ]';
                        $style = 'style="color: #d9534f;"';
                    } else {
                        // status == 2 (販売中)
                        $status_text = ' [販売中]';
                        $style = '';
                    }
        ?>
                    <li class="sku-item" <?php echo $style; ?>>
                        <img src="<?php echo htmlspecialchars($sku['image_url']); ?>" width="50" alt="SKU画像">
                        <span>
                            <?php 
                                echo "SKU ID: " . $sku['id'] . " / ";
                                echo htmlspecialchars($sku['size']) . " / ";
                                echo htmlspecialchars($sku['color']) . " / ";
                                echo htmlspecialchars($sku['price']) . "円 / ";
                                echo "在庫: " . htmlspecialchars($sku['stock_quantity']) . "個";
                                echo "<strong>" . htmlspecialchars($status_text) . "</strong>";
                            ?>
                        </span>
                        
                        <div class="sku-buttons">
                        <?php
                        // ★ 削除済み(0) でない場合のみ「編集」「削除」ボタンを表示
                        if ($status != 0) : 
                        ?>
                            <a href="sku_edit.php?sku_id=<?php echo $sku['id']; ?>" class="edit-btn">
                                編集
                            </a>
                            
                            <a href="sku_delete.php?sku_id=<?php echo $sku['id']; ?>" 
                               class="delete-btn"
                               onclick="return confirm('SKU ID: <?php echo $sku['id']; ?> を「削除済み」状態にしますか？ (データは消えません)');">
                                削除
                            </a>
                        <?php 
                        // ★ 削除済み(0) の場合のみ「復活」ボタンを表示
                        else: // ($status == 0) の場合
                        ?>
                            <a href="sku_restore.php?sku_id=<?php echo $sku['id']; ?>" 
                               class="restore-btn"
                               onclick="return confirm('SKU ID: <?php echo $sku['id']; ?> を「在庫切れ」状態で復元しますか？');">
                                復活
                            </a>
                        <?php 
                        endif; // End if ($status != 0)
                        ?>
                        </div>
                    </li>
        <?php
                } // SKUのforeachを閉じる
                echo '</ul>';
            } else {
                echo "<p style='padding: 10px;'>この商品にはSKUがまだ登録されていません。</p>";
            }
        ?>
        
    </div> <?php
        } // itemsのforeachを閉じる
    ?>

</body>
</html>
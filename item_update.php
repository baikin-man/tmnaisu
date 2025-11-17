<?php
require "db-connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO($connect, USER, PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $id = $_POST['id'];
        $name = $_POST['name'];
        $tags = isset($_POST['tags']) ? $_POST['tags'] : []; // チェックがない場合は空配列

        // トランザクション開始
        $pdo->beginTransaction();

        // 1. 商品名の更新
        $stmt = $pdo->prepare("UPDATE items SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);

        // 2. タグの更新 (一度全削除して、再登録する方式)
        // 2-1. 既存のタグ紐付けを削除
        $stmt_del = $pdo->prepare("DELETE FROM item_tags WHERE item_id = ?");
        $stmt_del->execute([$id]);

        // 2-2. 新しいタグを登録
        if (!empty($tags)) {
            $stmt_ins = $pdo->prepare("INSERT INTO item_tags (item_id, tag_id) VALUES (?, ?)");
            foreach ($tags as $tag_id) {
                $stmt_ins->execute([$id, $tag_id]);
            }
        }

        // コミット
        $pdo->commit();

        // 一覧へ戻る
        header("Location: item_list.php");
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "エラーが発生しました: " . $e->getMessage();
        echo '<br><a href="item_list.php">戻る</a>';
    }
} else {
    header("Location: item_list.php");
}
?>
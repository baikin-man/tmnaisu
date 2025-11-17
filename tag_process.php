<?php
require "db-connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $action = $_POST['action'];
    
    try {
        $pdo = new PDO($connect, USER, PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // --- タグ追加処理 ---
        if ($action === 'add') {
            $name = trim($_POST['name']);
            
            if ($name === '') {
                header("Location: tag_register.php?error=タグ名を入力してください");
                exit;
            }

            // 重複チェック (オプション)
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM tags WHERE name = ?");
            $stmt_check->execute([$name]);
            if ($stmt_check->fetchColumn() > 0) {
                header("Location: tag_register.php?error=そのタグ名は既に登録されています");
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO tags (name) VALUES (?)");
            $stmt->execute([$name]);
            
            header("Location: tag_register.php?success=タグを追加しました");
            exit;
        }
        
        // --- タグ削除処理 ---
        elseif ($action === 'delete') {
            $id = $_POST['id'];
            
            // タグが使用中かチェック (item_tagsテーブルが存在し、外部キー制約がない場合の手動チェック)
            // 外部キー制約がある場合は catch ブロックで捕捉されます
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM item_tags WHERE tag_id = ?");
            $stmt_check->execute([$id]);
            if ($stmt_check->fetchColumn() > 0) {
                 header("Location: tag_register.php?error=このタグは商品に使用されているため削除できません");
                 exit;
            }

            $stmt = $pdo->prepare("DELETE FROM tags WHERE id = ?");
            $stmt->execute([$id]);
            
            header("Location: tag_register.php?success=タグを削除しました");
            exit;
        }

    } catch (PDOException $e) {
        // 外部キー制約違反などのエラーハンドリング
        $msg = $e->getMessage();
        if (strpos($msg, 'Integrity constraint violation') !== false) {
            $msg = "このタグは使用されているため削除できません。";
        }
        header("Location: tag_register.php?error=" . urlencode($msg));
        exit;
    }
}

// POST以外でアクセスされた場合
header("Location: tag_register.php");
exit;
?>
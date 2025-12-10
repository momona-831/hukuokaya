<?php
session_start();
require 'db-connect.php';

// ログインチェック
if (!isset($_SESSION['member_id'])) {
    header("Location: Login.php");
    exit;
}

$member_id = (int)$_SESSION['member_id'];

// ▼ 複数商品IDを受け取る
$product_ids = $_POST['product_ids'] ?? [];

if (!is_array($product_ids) || count($product_ids) === 0) {
    exit("商品が指定されていません。");
}

try {

    // トランザクション開始
    $pdo->beginTransaction();

    foreach ($product_ids as $pid) {

        $product_id = (int)$pid;
        if ($product_id <= 0) {
            continue;
        }

        // ======================
        // ① 購入履歴登録
        // ======================
        $sql = $pdo->prepare("
            INSERT INTO purchase_history
                (member_id, product_id, purchase_date)
            VALUES
                (?, ?, NOW())
        ");
        $sql->execute([$member_id, $product_id]);

        // ======================
        // ② 商品を販売終了にする
        // ======================
        $sql = $pdo->prepare("
            UPDATE listing_product
               SET buy_flag = 1
             WHERE product_id = ?
        ");
        $sql->execute([$product_id]);

        // ======================
        // ③ カートから削除
        // ======================
        $sql = $pdo->prepare("
            DELETE FROM cart
            WHERE member_id = ?
              AND product_id = ?
        ");
        $sql->execute([$member_id, $product_id]);

    }

    $pdo->commit();

} catch (PDOException $e) {
    $pdo->rollBack();
    exit("購入処理エラー：" . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>購入完了</title>
  <link rel="stylesheet" href="css/purchase-completed.css">
</head>
<body>
  <div class="main">
    <a href="mainpage.php" class="back">←</a>
    <h1>購入完了</h1>

    <div class="message">
      ご購入ありがとうございます
    </div>

    <div class="links">
      <a href="purchase-history.php">購入履歴を見る</a>
      <a href="mainpage.php">メインページへ戻る</a>
    </div>
  </div>
</body>
</html>

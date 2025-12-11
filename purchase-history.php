<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['member_id'])) {
    header('Location: Login.php?error=not_logged_in');
    exit;
}
?>

<?php
require 'db-connect.php';
require 'ribbon.php';

$member_id = $_SESSION['member_id'];

// flag=1 の商品を購入履歴として表示
$sql = "
    SELECT 
      p.product_name,
      p.image,
      p.price,
      b.date AS purchase_date,
      m.address
    FROM buy b
    JOIN listing_product p ON b.buy_id = p.buy_id 
    JOIN member m ON b.member_id = m.member_id
    WHERE b.member_id = :member_id
    ORDER BY b.date DESC;
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);

if (!$stmt->execute()) {
    $errorInfo = $stmt->errorInfo();
    echo "<h2>SQL実行エラーが発生しました</h2>";
    echo "<p>SQLSTATE: " . htmlspecialchars($errorInfo[0]) . "</p>";
    echo "<p>DBエラーコード: " . htmlspecialchars($errorInfo[1]) . "</p>";
    echo "<p>エラーメッセージ: " . htmlspecialchars($errorInfo[2]) . "</p>";
    exit;
}

$history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>購入履歴</title>
  <link rel="stylesheet" href="css/purchase-history.css"> 
</head>
<body>

  <button class="back"><a href="./mypage.php">←</a></button>
  <h1>購入履歴</h1>

  <div class="content">
    <?php if (empty($history)): ?>
      <p>購入履歴はありません。</p>
    <?php else: ?>
      <?php foreach ($history as $item): ?>
        <div class="cart-item">
          <img src="<?= htmlspecialchars($item['image'] ?? 'img/noimage.png') ?>" alt="商品画像">
          <div class="cart-info">
            <h2><?= htmlspecialchars($item['product_name']) ?></h2>
            <p>
              購入日：<?= htmlspecialchars($item['purchase_date']) ?><br>
              お支払額：¥<?= number_format($item['price']) ?><br>
              お届け先：<?= htmlspecialchars($item['address']) ?>
            </p>
            <button><a href="./Purchace-screen.php">再び購入</a></button>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>
</html>

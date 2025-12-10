<?php
session_start();
require 'db-connect.php';

// ログイン確認
if (!isset($_SESSION['member_id'])) {
    header("Location: Login.php");
    exit;
}

$member_id = $_SESSION['member_id'];

// カート取得
$sql = $pdo->prepare("
    SELECT c.*, p.product_name, p.price, p.image, p.product_detail
    FROM cart c
    JOIN listing_product p ON c.product_id = p.product_id
    WHERE c.member_id = ?
");
$sql->execute([$member_id]);
$cart_items = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>カート</title>
<link rel="stylesheet" href="css/cart-list.css">
<link rel="stylesheet" href="css/template.css">
</head>
<body>

<!-- ナビゲーション -->
<nav class="navigation-rail">
  <div class="nav-item"><a href="mainpage.php">メインページ</a></div>
  <div class="nav-item"><a href="mypage.php">マイページ</a></div>
  <div class="nav-item"><a href="cart-list.php">カート</a></div>
  <div class="nav-item"><a href="listing.php">出品</a></div>
</nav>

<a href="mainpage.php"><button class="back">←</button></a>
<h1>カート</h1>

<div class="content">

<?php if (count($cart_items) === 0): ?>

<p>カートに商品はありません。</p>

<?php else: ?>

<!--====================================================================
   表示 + 削除ボタン（削除用フォームは個別に独立）
=====================================================================-->
<?php foreach ($cart_items as $item): ?>

<div class="cart-item">

  <img src="<?= htmlspecialchars($item['image']) ?>">

  <div class="cart-info">
    <h2><?= htmlspecialchars($item['product_name']) ?></h2>
    <p><?= htmlspecialchars($item['product_detail']) ?></p>
    <p>￥<?= number_format($item['price']) ?></p>

    <!-- 削除フォーム -->
    <form action="remove_cart.php" method="post"
          onsubmit="return confirm('削除しますか？');">
      <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
      <button type="submit">削除</button>
    </form>

  </div>

</div>

<?php endforeach; ?>

<!--====================================================================
   販売用（購入）フォーム：商品IDだけまとめて送信
=====================================================================-->
<form action="Purchase-screen.php" method="post">
  <?php foreach ($cart_items as $item): ?>
      <input type="hidden" name="product_ids[]" value="<?= $item['product_id'] ?>">
  <?php endforeach; ?>

  <button class="buy-all">購入</button>
</form>

<?php endif; ?>

</div>

</body>
</html>

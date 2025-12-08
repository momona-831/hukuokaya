<?php
require 'db-connect.php';
session_start();

// product_id が無い場合はメインページへ
if (!isset($_GET['product_id'])) {
    header("Location: mainpage.php");
    exit();
}

// SQLエラーを画面に出す
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$product_id = $_GET['product_id'];

// 未ログインの場合は member_id = 0 を使う
$member_id = $_SESSION['member_id'] ?? null;

// ▼ 閲覧ログを product_view に記録（人気タグ用）
$sql_view = $pdo->prepare("
    INSERT INTO product_view (product_id, member_id, viewed_at)
    VALUES (?, ?, NOW())
");
$sql_view->execute([$product_id, $member_id]);

// ▼ 商品情報取得
$sql = $pdo->prepare("
    SELECT product_id, product_name, price, image, product_detail
    FROM listing_product
    WHERE product_id = ?
");
$sql->execute([$product_id]);
$product = $sql->fetch(PDO::FETCH_ASSOC);

// 商品が見つからない場合
if (!$product) {
    echo "商品が存在しません。";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($product['product_name']) ?> - 商品情報</title>
  <link rel="stylesheet" href="css/product-detail.css">
</head>

<body>

  <div class="detail-container">

    <!-- ← 戻るリンク -->
    <a href="javascript:history.back();" class="back-btn">←</a>

    <h1 class="title">商品情報</h1>

    <!-- 商品画像 -->
    <div class="image-box">
      <?php if (!empty($product['image'])): ?>
        <img src="<?= htmlspecialchars($product['image']) ?>" alt="商品画像">
      <?php else: ?>
        <div class="no-image">画像なし</div>
      <?php endif; ?>
    </div>

    <!-- 商品名・価格 -->
    <h2 class="product-name"><?= htmlspecialchars($product['product_name']) ?></h2>
    <p class="product-price">￥<?= number_format($product['price']) ?></p>

    <h3 class="subtitle">アイテム情報</h3>

    <!-- 商品説明 -->
    <div class="description-box">
      <?= nl2br(htmlspecialchars($product['product_detail'])) ?>
    </div>

    <!-- カートに追加 -->
    <form action="add-cart.php" method="post">
      <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
      <button class="cart-button">カートに追加</button>
    </form>

  </div>

</body>
</html>

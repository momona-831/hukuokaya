<?php
session_start();
require 'db-connect.php';

// ▼ ログインチェック
if (!isset($_SESSION['member_id'])) {
    header('Location: Login.php');
    exit;
}

$member_id = (int)$_SESSION['member_id'];

/* ===================================================
   ▼ 商品IDの受け取り
   単品 : product_id
   複数 : product_ids[]
=================================================== */

// 複数指定
if (!empty($_POST['product_ids'])) {
    $product_ids = array_map('intval', $_POST['product_ids']);
}
// 単品指定
else {
    $pid = isset($_POST['product_id'])
        ? (int)$_POST['product_id']
        : (int)($_GET['product_id'] ?? 0);

    if ($pid > 0) {
        $product_ids = [$pid];
    } else {
        exit('商品が正しく指定されていません');
    }
}

/* ===================================================
   ▼ 商品取得（まとめ対応）
=================================================== */

$placeholders = implode(',', array_fill(0, count($product_ids), '?'));

$sql = "
    SELECT product_id, product_name, price, image, product_detail
    FROM listing_product
    WHERE product_id IN ($placeholders)
";

$stmt = $pdo->prepare($sql);
$stmt->execute($product_ids);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$products) {
    exit('商品が見つかりません。');
}

/* ===================================================
   ▼ 合計計算
=================================================== */

$total_price = 0;
foreach ($products as $p) {
    $total_price += $p['price'];
}

$shipping_fee = 600;
$total = $total_price + $shipping_fee;

/* ===================================================
   ▼ 会員情報取得
=================================================== */

$sql = "SELECT name, address, tel
        FROM member
        WHERE member_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$member_id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) exit('会員情報が取得できませんでした。');

$user_name    = $member['name'];
$user_address = $member['address'];
$user_phone   = $member['tel'];

/* ▼ フォームで一時変更された場合 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_name']))    $user_name    = htmlspecialchars($_POST['user_name']);
    if (isset($_POST['user_address'])) $user_address = htmlspecialchars($_POST['user_address']);
    if (isset($_POST['user_phone']))   $user_phone   = htmlspecialchars($_POST['user_phone']);
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>購入画面</title>
<link rel="stylesheet" href="css/purchase-screen.css">
</head>

<body>

<div class="main">
<a href="javascript:history.back();" class="back-btn">←</a>
<h1>購入画面</h1>

<!-- =================================
   商品情報（複数対応）
================================== -->

<?php foreach ($products as $p): ?>

<div class="product">
    <?php if (!empty($p['image'])): ?>
      <img src="<?= htmlspecialchars($p['image']) ?>">
    <?php else: ?>
      <div class="no-image">画像なし</div>
    <?php endif; ?>

    <div>
      <p>商品名：<?= htmlspecialchars($p['product_name']) ?></p>
      <p>価格：￥<?= number_format($p['price']) ?></p>
      <p><?= nl2br(htmlspecialchars($p['product_detail'])) ?></p>
    </div>
</div>

<?php endforeach; ?>


<!-- =================================
   配送先（変更なし）
================================== -->

<div class="section">
<h2>配送先</h2>
<div class="info-box">

<?= htmlspecialchars($user_name) ?><br>
<?= htmlspecialchars($user_address) ?><br>
<?= htmlspecialchars($user_phone) ?>

<form action="" method="post" class="edit-form">

<?php foreach ($product_ids as $pid): ?>
  <input type="hidden" name="product_ids[]" value="<?= $pid ?>">
<?php endforeach; ?>

<input type="text" name="user_name" value="<?= htmlspecialchars($user_name) ?>"><br>
<input type="text" name="user_address" value="<?= htmlspecialchars($user_address) ?>"><br>
<input type="text" name="user_phone" value="<?= htmlspecialchars($user_phone) ?>"><br>

<button type="submit" class="save-btn">保存</button>
</form>

</div>
</div>


<!-- =================================
  注文金額
================================== -->
<div class="section">
<h2>注文金額</h2>

<div class="info-box">

商品合計：￥<?= number_format($total_price) ?><br>
送料：￥<?= number_format($shipping_fee) ?><br>

<p class="total">合計金額：￥<?= number_format($total) ?></p>
</div>
</div>


<!-- =================================
   購入確定送信
================================== -->
<form action="purchase-completed.php" method="post">

<?php foreach ($product_ids as $pid): ?>
  <input type="hidden" name="product_ids[]" value="<?= $pid ?>">
<?php endforeach; ?>

<button type="submit" class="confirm-btn">
  購入を確定する
</button>

</form>

</div>
</body>
</html>

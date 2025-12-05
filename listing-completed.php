<?php
require 'db-connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $sql = $pdo->prepare('SELECT * FROM member WHERE email = ? AND password = ?');
  $sql->execute([$_POST['email'], $_POST['password']]);
  $member = $sql->fetch(PDO::FETCH_ASSOC);

  if ($member) {
    $_SESSION['member'] = $member;
    $_SESSION['member_id'] = $member['member_id'];
    header('Location: mainpage.php');
    exit;
  } else {
    echo 'ログイン情報が間違っています。';
  }

  if (!isset($_SESSION['member_id'])) {
    exit('ログインしてください。');
  }
}

$member_id = $_SESSION['member_id'];

// 出品情報を取得
$sql = $pdo->prepare('SELECT * FROM listing_product WHERE member_id = ? AND buy_flag = 0');
$sql->execute([$member_id]);
$listings = $sql->fetchAll(PDO::FETCH_ASSOC);

// ★★★ 追加：あなたのサイトの絶対パス ★★★
$BASE_URL = "https://aso2401004.perma.jp/2025/hukuokaya/";
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>出品一覧</title>
  <link rel="stylesheet" href="css/listing-completed.css">
</head>
<body>

<nav class="navigation-rail">
  <div class="nav-item">
    <a href="mainpage.php">
      <img src="img/click_scam.jpg" alt="メインページ">
    </a>
    <span>メインページ</span>
  </div>
  <div class="nav-item">
    <a href="mypage.php">
      <img src="img/click_scam.jpg" alt="マイページ">
    </a>
    <span>マイページ</span>
  </div>
  <div class="nav-item">
    <a href="cart-list.php">
      <img src="img/click_scam.jpg" alt="カート">
    </a>
    <span>カート</span>
  </div>
  <div class="nav-item">
    <a href="listing.php">
      <img src="img/click_scam.jpg" alt="出品">
    </a>
    <span>出品</span>
  </div>
</nav>

<a href="mypage.php"><button class="back">←</button></a>
<h1>出品一覧</h1>

<div class="content">
  <p class="count"><?= count($listings) ?>件</p>
  <h2 class="section-title">出品リスト</h2>

<?php
if (count($listings) === 0) {
    echo '<p>出品中の商品はありません。</p>';
} else {
    echo '<div class="item-list">';

    foreach ($listings as $row) {

        // ======================================
        // 画像URL補正（ここを修正した！）
        // ======================================
        $image_url = htmlspecialchars($row['image']);

        if (preg_match('/^uploads\//', $image_url)) {
            $image_url = $BASE_URL . $image_url;
        } else {
            $image_url = $BASE_URL . "uploads/" . basename($image_url);
        }

        echo '<a href="listing-edit.php?product_id=' . htmlspecialchars($row['product_id']) . '" class="item-card">';
        echo '<img src="' . $image_url . '" alt="商品画像">';
        echo '<p>' . htmlspecialchars($row['product_name']) . '<br>￥' . htmlspecialchars($row['price']) . '</p>';
        echo '</a>';
    }

    echo '</div>';
}
?>
</div>
</body>
</html>

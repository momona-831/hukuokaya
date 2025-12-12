<?php 
session_start();
require 'ribbon.php';
require 'db-connect.php';

/* admin_flg 取得 */
$admin_flg = 0;

if (isset($_SESSION['member_id'])) {
    $sql = $pdo->prepare("
        SELECT admin_flg
        FROM member
        WHERE member_id = ?
    ");
    $sql->execute([$_SESSION['member_id']]);
    $row = $sql->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $admin_flg = (int)$row['admin_flg'];
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>マイページ</title>
  <link rel="stylesheet" href="css/mypage.css">
</head>
<body>

  <!-- タイトル・ボタン -->
  <a href="mainpage.php" class="back">←</a>
  <button class="cancel">×</button>
  <h1>マイページ</h1>

  <!--------------------ここまでテンプレ------------------------>

  <div class="content">
    <div class="mypage-item">
      <a href="purchase-history.php">購入履歴</a>
    </div>
    <div class="mypage-item">
      <a href="favorite.php">お気に入り</a>
    </div>
    <div class="mypage-item">
      <a href="account-info.php">アカウント情報</a>
    </div>
    <div class="mypage-item">
      <a href="listing.php">出品</a>
    </div>
    <div class="mypage-item">
      <a href="listing-completed.php">出品一覧</a>
    </div>
    <?php if ($admin_flg == 1): ?>
    <div class="mypage-item">
      <a href="admin.php">管理者</a>
    </div>
    <?php endif; ?>

  </div>
</body>
</html>

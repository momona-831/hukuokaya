<?php
require 'db-connect.php';
session_start();

// ログインチェック（ログインしていなければログインページへ）
if ($member) {
    $_SESSION['member'] = $member;
    $_SESSION['member_id'] = $member['member_id']; // ← 必須！
    header('Location: mainpage.php');
    exit;
}

$product_id = $_GET['product_id'] ?? '';
if (!$product_id) {
    exit('商品IDが指定されていません。');
}

// 該当商品を取得
$sql = "SELECT * FROM listing_product WHERE product_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    exit('指定された商品が見つかりません。');
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>出品編集</title>
    <link rel="stylesheet" href="css/template.css">
    <link rel="stylesheet" href="css/listing.css">
</head>
<body>
      <nav class="navigation-rail">
      <div class="nav-item">
        <a href="mainpage.php">
          <img src="img/click_scam.jpg" alt="メインページ" />
        </a>
        <span>メインページ</span>
      </div>
      <div class="nav-item">
        <a href="mypage.php">
          <img src="img/click_scam.jpg" alt="マイページ" />
        </a>
        <span>マイページ</span>
      </div>
      <div class="nav-item">
        <a href="cart-list.php">
          <img src="img/click_scam.jpg" alt="カート" />
        </a>
        <span>カート</span>
      </div>
      <div class="nav-item">
        <a href="listing.php">
          <img src="img/click_scam.jpg" alt="出品" />
        </a>
        <span>出品</span>
      </div>
    </nav>
<section class="main">
    <div class="topbar">
        <a herf="listing-completed.php">
            <button onclick="history.back()" class="back-btn">←</button>
        </a>
        <h1 class="title">出品編集</h1>
    </div>

    <div class="content">
        <form action="update-listing.php" method="post" enctype="multipart/form-data">
          <!-- 画像（更新しない場合は空） -->
           <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="商品画像">
            <input type="file" name="image">

            <!-- 商品ID -->
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">

            <!-- 商品名 -->
            <input class="input" type="text" name="product_name" placeholder="商品名を入力" 
                   value="<?= htmlspecialchars($product['product_name']) ?>" required>

            <!-- カテゴリー -->
            <select name="category" required>
                <option value="">カテゴリーを選択</option>
                <?php
                $categories = ["ジャケット", "パンツ", "ビジネスバッグ", "セットアップ", "ビジネスカジュアル", "アクセサリー", "シャツ"];
                foreach ($categories as $c) {
                    $selected = ($product['category'] === $c) ? 'selected' : '';
                    echo "<option value='$c' $selected>$c</option>";
                }
                ?>
            </select>

            <!-- 値段 -->
            <input class="input" type="number" name="price" min="0" placeholder="￥ 値段を入力"
                   value="<?= htmlspecialchars($product['price']) ?>" required>

            <!-- 商品説明 -->
            <textarea name="product_detail" placeholder="商品の説明" required><?= htmlspecialchars($product['product_detail']) ?></textarea>


            <button type="submit" class="submit">更新する</button>
        </form>
    </div>
</section>
</body>
</html>

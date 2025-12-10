
<?php 
require 'db-connect.php';

// ------- 検索条件 ------- //
$keyword   = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$category  = isset($_GET['category']) ? $_GET['category'] : '';
$sort      = isset($_GET['sort']) ? $_GET['sort'] : 'new';

// ------- SQL 生成 ------- //
$sql = "SELECT * FROM listing_product WHERE 1";

// 商品名 / 商品ID / 出品者ID を同時に検索
if ($keyword !== '') {
    $sql .= " AND (
        product_name LIKE :kw
        OR product_id LIKE :kw
        OR member_id LIKE :kw
    )";
}

// カテゴリ絞り込み
if ($category !== '') {
    $sql .= " AND category = :cat";
}

// 並び替え
switch ($sort) {
    case "price_asc":
        $sql .= " ORDER BY price ASC";
        break;
    case "price_desc":
        $sql .= " ORDER BY price DESC";
        break;
    case "old":
        $sql .= " ORDER BY product_id ASC";
        break;
    default:
        $sql .= " ORDER BY product_id DESC"; // new（最新）
        break;
}

$stmt = $pdo->prepare($sql);

// バインド
if ($keyword !== '') {
    $stmt->bindValue(":kw", "%{$keyword}%", PDO::PARAM_STR);
}
if ($category !== '') {
    $stmt->bindValue(":cat", $category, PDO::PARAM_STR);
}

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 削除処理
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $del = $pdo->prepare("DELETE FROM listing_product WHERE product_id = ?");
    $del->execute([$delete_id]);

    header("Location: admin_delete.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>商品管理</title>
  <link rel="stylesheet" href="css/admin_delete.css">
</head>
<body>

  <!-- ====== サイドバー（テンプレ） ====== -->
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

  <!-- タイトル -->
  <button class="back">←</button>
  <h1>商品管理</h1>

  <!-- ====== コンテンツここから ====== -->
  <div class="content">

    <h2 class="section-title">検索</h2>

    <form method="get" class="search-area">

        <input type="text" name="keyword"
               placeholder="商品名 / 商品ID / 出品者ID"
               value="<?= htmlspecialchars($keyword) ?>">

        <select name="category">
            <option value="">カテゴリーを選択</option>
            <option value="パンツ"     <?= $category=="パンツ" ? "selected":"" ?>>パンツ</option>
            <option value="ジャケット" <?= $category=="ジャケット" ? "selected":"" ?>>ジャケット</option>
            <option value="シャツ"     <?= $category=="シャツ" ? "selected":"" ?>>シャツ</option>
            <option value="アクセサリー" <?= $category=="アクセサリー" ? "selected":"" ?>>アクセサリー</option>
        </select>

        <select name="sort">
            <option value="new"         <?= $sort=="new" ? "selected":"" ?>>追加順（新しい）</option>
            <option value="old"         <?= $sort=="old" ? "selected":"" ?>>追加順（古い）</option>
            <option value="price_asc"   <?= $sort=="price_asc" ? "selected":"" ?>>価格が安い順</option>
            <option value="price_desc"  <?= $sort=="price_desc" ? "selected":"" ?>>価格が高い順</option>
        </select>

        <button class="search-btn">検索</button>
    </form>

    <!-- 商品一覧 -->
    <?php foreach ($products as $p): ?>
        <div class="product-card">
            <img src="<?= htmlspecialchars($p['image']) ?>" alt="画像" class="product-img">

            <div class="product-info">
                <div class="product-name"><?= htmlspecialchars($p['product_name']) ?></div>

                <div>商品ID：<?= $p['product_id'] ?></div>
                <div>出品者ID：<?= $p['member_id'] ?></div>
                <div>価格：￥<?= number_format($p['price']) ?></div>
                <div>カテゴリ：<?= htmlspecialchars($p['category']) ?></div>
            </div>

            <a class="delete-btn"
               href="admin_delete.php?delete_id=<?= $p['product_id'] ?>"
               onclick="return confirm('削除しますか？');">
                🗑 削除
            </a>
        </div>
    <?php endforeach; ?>

  </div>
  <!-- ====== コンテンツここまで ====== -->

</body>
</html>

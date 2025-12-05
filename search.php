<?php
// ======= DB接続 =======
$host = 'mysql326.phy.lolipop.lan';
$dbname = 'LAA1607626-hukuokaya';
$user = 'LAA1607626';
$pass = 'seniority';

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";

try {
    $pdo = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    exit("DB接続エラー: " . $e->getMessage());
}

// ======= 検索ワード =======
$keyword = isset($_GET['search_box']) ? $_GET['search_box'] : "";

// ======= 並び替えボタン =======
$orderQuery = "";

if (isset($_GET["sort"])) {
    switch ($_GET["sort"]) {
        case "new":
            $orderQuery = " ORDER BY date DESC";
            break;

        case "price":
            $orderQuery = " ORDER BY price ASC";
            break;

        case "popular":
            $orderQuery = " ORDER BY product_id DESC";
            break;
    }
}

// ======= SQL生成 =======
$sql = "SELECT * FROM listing_product WHERE buy_flag = 0";

if ($keyword !== "") {
    $sql .= " AND (product_name LIKE :kw OR category LIKE :kw OR product_detail LIKE :kw)";
}

$sql .= $orderQuery;

$stmt = $pdo->prepare($sql);

if ($keyword !== "") {
    $stmt->bindValue(":kw", "%".$keyword."%", PDO::PARAM_STR);
}

$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>検索ページ</title>
  <link rel="stylesheet" href="css/search.css">
</head>

<body>
    <!-- アプリバー -->
    <header class="app-bar">
      <div class="headline">ふくおかや</div>
        <div class="trailing-icons">
          <a href="search.php">
            <img src="img/click_scam.jpg" alt="検索" />検索
          </a>
          <a href="Login.php">
            <img src="img/icon-7.svg" alt="ログイン" />
          </a>
        </div>
      </div>
    </header>

    <!-- ナビゲーションバー -->
    <nav class="navigation-rail">
      <div class="nav-item">
        <a href="mainpage.php"><img src="img/click_scam.jpg"></a>
        <img src="img/icon-3.svg" alt="メインページ" />
        <span>メインページ</span>
        </a>
      </div>
      <div class="nav-item">

        <a href="mypage.php"><img src="img/click_scam.jpg"></a>
        <a href="account-info.php">
        <img src="img/icon-8.svg" alt="マイページ" />
        <span>マイページ</span>
        </a>
      </div>
      <div class="nav-item">
        <a href="cart-list.php"><img src="img/click_scam.jpg"></a>
        <img src="img/icon-8.svg" alt="カート" />
        <span>カート</span>
        </a>
      </div>
      <div class="nav-item">
        <a href="listing.php"><img src="img/click_scam.jpg"></a>
        <img src="img/icon-8.svg" alt="出品" />
        <span>出品</span>
        </a>
      </div>
    </nav>

  <div class="content"> 
      <h1>商品検索</h1>

      <!-- 検索フォーム -->
      <form method="GET" action="">
          <div class="search_box">
                <input 
                  type="text" 
                  name="search_box" 
                  placeholder="商品名やカテゴリで検索できます"
                  value="<?= htmlspecialchars($keyword) ?>"
                >
                <button class="enter">検索</button>
          </div>
      </form>

      <!-- 並び替えボタン -->
      <div class="menu-buttons">
        <form method="GET" action="">
          <input type="hidden" name="search_box" value="<?= htmlspecialchars($keyword) ?>">
          <button class="category" name="sort" value="new">新着順</button>
          <button class="category" name="sort" value="popular">人気順</button>
          <button class="category" name="sort" value="price">価格順</button>
        </form>
      </div>

      <!-- 検索結果（カード表示） -->
      <?php if (count($items) === 0): ?>
          <p>該当する商品がありません。</p>
      <?php else: ?>
        <div class="card-container">
          <?php foreach($items as $item): ?>
            <div class="card">
              <div class="card-img">
                <?php if (!empty($item['image'])): ?>
                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="商品画像">
                <?php else: ?>
                    <img src="img/noimage.png" alt="画像なし">
                <?php endif; ?>
              </div>
              <div class="card-info">
                <div class="title"><?= htmlspecialchars($item['product_name']) ?></div>

                <div class="info-row">
                  <span class="label">出品日</span>
                  <span class="value"><?= htmlspecialchars($item['date']) ?></span>
                </div>

                <div class="info-row">
                  <span class="label">価格</span>
                  <span class="value">¥<?= number_format($item['price']) ?></span>
                </div>

                <div class="info-row">
                  <span class="label">カテゴリ</span>
                  <span class="value"><?= htmlspecialchars($item['category']) ?></span>
                </div>

                <div class="info-row">
                  <span class="label">説明</span>
                  <span class="value"><?= nl2br(htmlspecialchars($item['product_detail'])) ?></span>
                </div>

                <button class="buy-again">詳細を見る</button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
  </div>
  <footer class="footer">
    <ul class="footer__links">
        <li><a href="">ルール</a></li>
        <li><a href="">利用規約</a></li>
        <li><a href="">特定商取引法</a></li>
        <li><a href="">プライバシー</a></li>
        <li><a href="">著作権 (DMCA)</a></li>
        <li><a href="">サーバー状態</a></li>
        <li><a href="">免責事項</a></li>
    </ul>
  </footer>

</body>
</html>

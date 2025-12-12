<?php
 session_start();
require 'db-connect.php';
require 'ribbon.php';

// ▼ おすすめ商品（buy_flag が 0 = 未購入商品）
$sql = $pdo->query("
    SELECT product_id, product_name, price, image
    FROM listing_product
    WHERE buy_flag = 0
    ORDER BY product_id DESC
");
$recommend = $sql->fetchAll(PDO::FETCH_ASSOC);


// ▼ 過去購入商品（purchase_history が無いのでコメントアウト）
 $past_items = [];
 if (isset($_SESSION['member_id'])) {
     $member_id = $_SESSION['member_id'];

     $sql2 = $pdo->prepare("
         SELECT lp.product_id, lp.product_name, lp.price, lp.image
         FROM purchase_history ph
         JOIN listing_product lp ON ph.product_id = lp.product_id
         WHERE ph.member_id = ?
         ORDER BY ph.purchase_date DESC
     ");
     $sql2->execute([$member_id]);
     $past_items = $sql2->fetchAll(PDO::FETCH_ASSOC);
 }


// ▼ 人気カテゴリ TOP3（閲覧数集計）
$sql3 = $pdo->query("
    SELECT p.category, COUNT(*) AS views
    FROM product_view v
    JOIN listing_product p ON v.product_id = p.product_id
    WHERE v.viewed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY p.category
    ORDER BY views DESC
");
$popular_tags = $sql3->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ふくおかやメインページ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/mainpage.css" />

  </head>

  <script>
  function scrollItems(direction, btn) {
    try {
      // btn が来なければフォールバックで最初の .items を使う
      let container = null;

      if (btn && btn.closest) {
        const section = btn.closest('.section');
        if (section) container = section.querySelector('.items');
      }

      if (!container) {
        container = document.querySelector('.items'); // フォールバック
      }

      if (!container) {
        console.warn('scroll target (.items) not found');
        return;
      }

      // カード幅を実測してスクロール量を決める（gap を考慮）
      const card = container.querySelector('.item-card');
      const style = getComputedStyle(container);
      const gap = parseFloat(style.gap) || parseFloat(style.columnGap) || 16;
      const cardWidth = card ? Math.round(card.getBoundingClientRect().width + gap) : 160;

      const scrollAmount = cardWidth * 1.5 * (direction < 0 ? -1 : 1);

      container.scrollBy({
        left: scrollAmount,
        behavior: 'smooth'
      });
    } catch (e) {
      console.error('scrollItems error:', e);
    }
  }
</script>


  <body>
    <!-- アプリバー -->
    <header class="app-bar">
      <div class="headline">ふくおかやめいんぺーじ</div>
      <div class="trailing-icons buttons are-small">

  <!-- 検索 -->
  <a href="search.php" class="button is-light">
    <span class="icon">
      <i class="fa-solid fa-magnifying-glass"></i>
    </span>
    <span>検索</span>
  </a>

  <?php if (isset($_SESSION['member_id'])): ?>
    <!-- ログアウト -->
    <a href="logout.php"
       class="button is-danger is-light"
       onclick="return confirm('ログアウトしますがよろしいですか？');">
      <span class="icon">
        <i class="fa-solid fa-right-from-bracket"></i>
      </span>
      <span>ログアウト</span>
    </a>

  <?php else: ?>
    <!-- ログイン -->
    <a href="Login.php" class="button is-primary is-light">
      <span class="icon">
        <i class="fa-solid fa-user"></i>
      </span>
      <span>ログイン</span>
    </a>
  <?php endif; ?>

</div>

    </header>
    <!-- メインコンテンツ -->
    <div class="content">

      <!-- 人気のタグ一覧 -->
      <section class="section">
        <div class="section-header">
          <div class="title">タグ一覧</div>
          <div class="arrow-buttons">
            <button class="arrow-btn left" onclick="scrollItems(-1, this)">←</button>
            <button class="arrow-btn right" onclick="scrollItems(1, this)">→</button>
          </div>
        </div>
        <div class="items">
          <?php
            $category_icons = [
              "ジャケット"         => "img/jacket.png",
              "パンツ"             => "img/pantu.jpg",  
              "ビジネスバッグ"     => "img/bag.jpg",
              "シャツ"             => "img/syutu.png",
              "ビジネスカジュアル" => "img/bizikazi.png",
              "アクセサリー"       => "img/nekutai.png",
            ];

            $default_icon = "img/defalt.png"; // 未定義カテゴリはタグアイコン
          ?>

          <?php foreach ($popular_tags as $tag): ?>
          <?php 
            $category = $tag['category'];
            // カテゴリに一致する画像があるか？
            $icon = $category_icons[$category] ?? $default_icon;
          ?>
          <div class="item-card">
            <img src="<?= $icon ?>" alt="">
            <?= htmlspecialchars($category) ?>
            </div>
          <?php endforeach; ?>
        </div>


      </section>

      <!-- おすすめ商品 -->
      <section class="section">
        <div class="section-header">
          <div class="title">おすすめ商品</div>
          <div class="arrow-buttons">
            <button class="arrow-btn left" onclick="scrollItems(-1, this)">←</button>
            <button class="arrow-btn right" onclick="scrollItems(1, this)">→</button>
          </div>
        </div>
        <div class="items">
      <?php foreach ($recommend as $item): ?>
        <div class="item-card">
          <a href="product-detail.php?product_id=<?= $item['product_id'] ?>">
            <img src="<?= htmlspecialchars($item['image']) ?>" alt="商品画像" />
            <?= htmlspecialchars($item['product_name']) ?><br>
            ￥<?= number_format($item['price']) ?>
          </a>
        </div>
<?php endforeach; ?>
</div>

      </section>

      <!-- 過去に購入した商品 -->
      <section class="section">
        <div class="section-header">
          <div class="title">過去に購入した商品</div>
          <div class="arrow-buttons">
            <button class="arrow-btn left" onclick="scrollItems(-1, this)">←</button>
            <button class="arrow-btn right" onclick="scrollItems(1, this)">→</button>
          </div>
        </div>
        <div class="items">
          <?php if (count($past_items) === 0): ?>

            <p>購入履歴がありません。</p>

          <?php else: ?>
            <?php foreach ($past_items as $item): ?>
              <div class="item-card">
                <a href="product-detail.php?product_id=<?= $item['product_id'] ?>">
                  <img src="<?= htmlspecialchars($item['image']) ?>" alt="商品画像" />
                  <?= htmlspecialchars($item['product_name']) ?><br>
                  ￥<?= number_format($item['price']) ?>
                </a>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>
    </div>

    <!-- フッター -->
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
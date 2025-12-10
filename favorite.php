<?php
session_start();
require 'db-connect.php';
require 'ribbon.php';

// ★ ログインチェック（member_id がないときはログイン画面へ）
if (!isset($_SESSION['member_id'])) {
    header('Location: Login.php');
    exit;
}

$member_id = $_SESSION['member_id'];

try {
    // ★ ログイン中会員のお気に入り＋商品情報を取得
    $sql = "
        SELECT 
            f.favorite_id,
            f.product_id,
            p.product_name,
            p.image
        FROM favorite AS f
        JOIN listing_product AS p
          ON f.product_id = p.product_id
        WHERE 
          f.member_id = ?
          AND f.favorite_flag = 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$member_id]);
    $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'データベースエラー: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>お気に入り</title>
  <link rel="stylesheet" href="css/favorite.css">
</head>
<body>
    
  <main class="content">
    <!-- アプリバー -->
    <button class="back"><a href="./mypage.php">←</a></button>
    <header class="app-bar">
      <div class="headline">お気に入り</div>
      <div class="trailing-icons">
        <img src="img/icon-2.svg" alt="検索">
        <img src="img/icon-7.svg" alt="設定">
      </div>
    </header>

    <!-- お気に入り商品一覧 -->
    <section class="section">
      <div class="section-header">
        <div class="title">お気に入りアイテム</div>
      </div>

      <div class="items">
        <?php if (empty($favorites)): ?>
          <p>お気に入りはまだありません。</p>
        <?php else: ?>
          <?php foreach ($favorites as $item): ?>
            <div class="item-card">
              <!-- 商品画像 listing_product.image を使用 -->
              <?php if (!empty($item['image'])): ?>
                <img src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>"
                     alt="<?php echo htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?>">
              <?php else: ?>
                <div class="no-image">画像なし</div>
              <?php endif; ?>

              <p><?php echo htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?></p>

              <!-- 削除ボタン：確認してすぐ画面から消す＋DB更新 -->
              <button
                type="button"
                class="remove-btn"
                data-favorite-id="<?php echo (int)$item['favorite_id']; ?>"
                data-action="remove_favorite.php"
              >
                削除
              </button>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <!-- JSで削除処理 -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      document.querySelectorAll(".remove-btn").forEach(button => {
        button.addEventListener("click", async (e) => {
          const card = e.target.closest(".item-card");
          const favoriteId = button.dataset.favoriteId;
          const action = button.dataset.action;

          // 確認ダイアログ
          if (!confirm("削除しますか？")) return;

          // 画面から即削除
          card.remove();

          // サーバーにも削除リクエスト送信
          try {
            const formData = new FormData();
            formData.append("favorite_id", favoriteId);

            await fetch(action, {
              method: "POST",
              body: formData
            });
          } catch (err) {
            console.error("削除通信エラー:", err);
          }
        });
      });
    });
  </script>
</body>
</html>

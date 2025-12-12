<?php
require 'db-connect.php';
session_start();
require 'ribbon.php';

// ===== ログイン確認 =====
if (!isset($_SESSION['member_id'])) {
    // ログインしていなければリダイレクト
    header('Location: Login.php?error=not_logged_in');
    exit;
}

try {
    // ===== DB接続 =====
    $pdo = new PDO(
        'mysql:host=' . SERVER . ';dbname=' . DBNAME . ';charset=utf8mb4',
        USER,
        PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // ===== POSTデータ取得 =====
    $product_name = $_POST['product_name'] ?? '';
    $category = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? 0;
    $description = $_POST['description'] ?? '';
    $member_id = $_SESSION['member_id']; // ← ログイン中の会員IDを使用

    // ===== 画像アップロード処理 =====
    $image_path = null;
    if (!empty($_FILES['image_file']['name'])) {
        // アップロード先のパス（ロリポップ上の公開ディレクトリ内）
        $upload_dir = __DIR__ . '/uploads/'; // サーバー上の保存フォルダ
        $web_path = 'uploads/';               // DBに保存する相対パス

        // フォルダがなければ作成
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // ファイル名をユニークにして保存
        $filename = uniqid() . '_' . basename($_FILES['image_file']['name']);
        $target_path = $upload_dir . $filename;
        $image_path = $web_path . $filename; // ← DBに保存する用のパス

        // アップロード実行
        if (!move_uploaded_file($_FILES['image_file']['tmp_name'], $target_path)) {
            throw new Exception('画像のアップロードに失敗しました。');
        }
    }

    // ===== DB登録 =====
    $sql = "INSERT INTO listing_product (member_id, product_name, price, category, image, product_detail, date)
            VALUES (:member_id, :product_name, :price, :category, :image, :detail, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
    $stmt->bindValue(':product_name', $product_name, PDO::PARAM_STR);
    $stmt->bindValue(':price', $price, PDO::PARAM_INT);
    $stmt->bindValue(':category', $category, PDO::PARAM_STR);
    $stmt->bindValue(':image', $image_path, PDO::PARAM_STR);
    $stmt->bindValue(':detail', $description, PDO::PARAM_STR);
    $stmt->execute();

} catch (Exception $e) {
    exit('エラー：' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>出品完了</title>
  <link rel="stylesheet" href="css/shopping-complete.css">
  <link rel="stylesheet" href="css/template.css">
</head>
<body>

  <!-- メインコンテンツ -->
  <main class="content">
    <header class="app-bar">
      <div class="back">←</div>
      <h1 class="headline">出品完了</h1>
      <div></div>
    </header>

    <section class="complete-box">
      <h2>出品が完了しました！</h2>
      <p class="description">
        商品が公開されました<br>
        購入されるのをお待ちください
      </p>

      <div class="item-info">
        <img src="<?= htmlspecialchars($image_path ?? 'img/no-image.svg') ?>" alt="商品画像">
        <div class="item-text">
          <p><?= htmlspecialchars($product_name) ?></p>
          <p>¥<?= number_format($price) ?></p>
        </div>
      </div>

      <div class="actions">
        <a href="account-management.php">アカウント削除</a>
      </div>
    </section>
  </main>

</body>
</html>

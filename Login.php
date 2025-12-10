<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ログイン</title>
  <link rel="stylesheet" href="css/Login-style.css">
  <link rel="stylesheet" href="css/template.css">
</head>
<body>

  <div class="login-container">
    <header>
      <a href="mainpage.php">
        <span class="back">←</span>
      </a>
      <h1>ログイン</h1>
    </header>

    <!-- ログインフォーム -->
    <form action="login-check.php" method="post">
      <input type="text" name="login_id" placeholder="ログインID" required>
      <input type="password" name="password" placeholder="パスワード" required>

      <!-- エラーメッセージ表示エリア -->
      <?php if (isset($_GET['error'])): ?>
        <p class="error-message">
          <?php
            if ($_GET['error'] === 'empty') {
              echo 'ログインIDとパスワードを入力してください。';
            } elseif ($_GET['error'] === 'invalid') {
              echo 'ログインIDまたはパスワードが間違っています。';
            } elseif ($_GET['error'] === 'db') {
              echo 'データベース接続エラーが発生しました。';
            }
          ?>
        </p>
      <?php endif; ?>

      <button type="button" class="sub-btn" onclick="location.href='account-entry.php'">
        アカウントをお持ちでない方
      </button>
      <button type="submit" class="login-btn">ログイン</button>
    </form>
  </div>
</body>
</html>

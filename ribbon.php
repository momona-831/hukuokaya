<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title></title>
    <link rel="stylesheet" href="css/template.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
  </head>

  <body>
    <!-- ナビゲーションバー -->
    <?php
    echo '
    <nav class="navigation-rail">

  <div class="nav-item">
    <a href="mainpage.php">
      <span class="icon">
        <i class="fa-solid fa-house"></i>
      </span>
      <span>メインページ</span>
    </a>
  </div>

  <div class="nav-item">
    <a href="account-info.php">
      <span class="icon">
        <i class="fa-solid fa-user"></i>
      </span>
      <span>マイページ</span>
    </a>
  </div>

  <div class="nav-item">
    <a href="cart-list.php">
      <span class="icon">
        <i class="fa-solid fa-cart-shopping"></i>
      </span>
      <span>カート</span>
    </a>
  </div>

  <div class="nav-item">
    <a href="listing.php">
      <span class="icon">
        <i class="fa-solid fa-pen-to-square"></i>
      </span>
      <span>出品</span>
    </a>
  </div>

</nav>

    '
    ?>
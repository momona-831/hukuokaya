<?php
session_start();
require 'ribbon.php'; // ← ナビ + page-wrapper開始

if (!isset($_SESSION['member_id'])) {
    header('Location: Login.php?error=not_logged_in');
    exit;
}

require_once 'db-connect.php';

try {
    $member_id = $_SESSION['member_id'];

    $stmt = $pdo->prepare("
        SELECT name, mailaddress, tel, password, address
        FROM member
        WHERE member_id = :member_id
    ");
    $stmt->execute([':member_id' => $member_id]);
    $user = $stmt->fetch();

    if (!$user) {
        session_destroy();
        header('Location: Login.php?error=user_not_found');
        exit;
    }

} catch (PDOException $e) {
    echo "データベースエラー: " . $e->getMessage();
    exit;
}
?>

<!-- account-infoページ専用CSS -->
<link rel="stylesheet" href="css/account-info.css">

<div class="page-title-wrap">
    <a href="./mypage.php" class="back">←</a>
    <h1 class="page-title">アカウント情報</h1>
</div>

<div class="content">

    <div class="accountinfo-item">
        <div class="label">名前</div>
        <div class="value"><?= htmlspecialchars($user['name']) ?></div>
    </div>

    <div class="accountinfo-item">
        <div class="label">メールアドレス</div>
        <div class="value"><?= htmlspecialchars($user['mailaddress']) ?></div>
    </div>

    <div class="accountinfo-item">
        <div class="label">電話番号</div>
        <div class="value"><?= htmlspecialchars($user['tel']) ?></div>
    </div>

    <div class="accountinfo-item">
        <div class="label">パスワード</div>
        <div class="value">
            <span id="password-mask">********</span>
            <span id="password-real" class="real-password"><?= htmlspecialchars($user['password']) ?></span>
            <button id="show-pass" class="eye-btn">
                <i class="fa-solid fa-eye"></i>
            </button>
        </div>
    </div>

    <div class="accountinfo-item">
        <div class="label">住所</div>
        <div class="value"><?= htmlspecialchars($user['address']) ?></div>
    </div>

    <a href="account-edit.php" class="account-info-button">アカウント設定</a>

</div>

<script>
const showBtn = document.getElementById('show-pass');
const passMask = document.getElementById('password-mask');
const passReal = document.getElementById('password-real');

showBtn.addEventListener('mousedown', () => {
    passMask.style.display = 'none';
    passReal.style.display = 'inline';
});

showBtn.addEventListener('mouseup', () => {
    passMask.style.display = 'inline';
    passReal.style.display = 'none';
});

showBtn.addEventListener('mouseleave', () => {
    passMask.style.display = 'inline';
    passReal.style.display = 'none';
});
</script>

<?php
// ribbon.php の閉じタグを閉じる
echo '
    </div><!-- /page-content -->
</div><!-- /page-wrapper -->
</body>
</html>
';
?>

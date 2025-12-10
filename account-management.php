<?php
require 'db-connect.php';

// 会員一覧を取得
$sql = $pdo->query("
    SELECT 
        member_id AS id,
        mailaddress AS email,
        password,
        name
    FROM member
    ORDER BY member_id ASC
");
$accounts = $sql->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>会員一覧</title>
<link rel="stylesheet" href="css/account-mana.css">
</head>
<body>

<h2 style="text-align:center;">会員アカウント一覧</h2>

<table>
    <tr>
        <th>ID</th>
        <th>メールアドレス</th>
        <th>パスワード</th>
        <th>名前</th>
        <th>削除</th>
    </tr>

    <?php foreach ($accounts as $acc): ?>
    <tr>
        <td><?= htmlspecialchars($acc['id'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($acc['email'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($acc['password'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($acc['name'], ENT_QUOTES, 'UTF-8') ?></td>
        <td>
            <a href="account-management.php?delete=<?= $acc['id'] ?>"
               onclick="return confirm('削除してよろしいですか？');">
               削除
            </a>
        </td>
    </tr>
    <?php endforeach; ?>

</table>

</body>
</html>

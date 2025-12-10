<?php
const SERVER = 'mysql326.phy.lolipop.lan';
const DBNAME = 'LAA1607626-hukuokaya';
const USER = 'LAA1607626';
const PASS = 'seniority';

$connect = 'mysql:host=' . SERVER . ';dbname=' . DBNAME . ';charset=utf8mb4';

$pdo = new PDO($connect, USER, PASS);

try {
    // PDOで接続を作成
    $pdo = new PDO($connect, USER, PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // 接続エラー時の処理
    exit('データベース接続失敗: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>アカウント一覧</title>
<link rel="stylesheet" href="css/account-mana.css">
</head>
<body>

<h2 style="text-align:center;">アカウント</h2>

<table>
    <tr>
        <th>id</th>
        <th>メールアドレス</th>
        <th>パスワード</th>
        <th>名前</th>
        <th>削除</th>
    </tr>

    <?php foreach ($accounts as $acc): ?>
    <tr>
        <td><?= htmlspecialchars($acc['id']) ?></td>
        <td><?= htmlspecialchars($acc['email']) ?></td>
        <td><?= htmlspecialchars($acc['password']) ?></td>
        <td><?= htmlspecialchars($acc['name']) ?></td>
        <td>
            <a href="index.php?delete=<?= $acc['id'] ?>" 
               onclick="return confirm('削除してよろしいですか？');">
               削除
            </a>
        </td>
    </tr>
    <?php endforeach; ?>

</table>

</body>
</html>

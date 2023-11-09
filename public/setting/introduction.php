<?php
session_start();

if (empty($_SESSION['login_user_id'])) {
  header("HTTP/1.1 302 Found");
  header("Location: /login.php");
  return;
}

// DBに接続
$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');
// セッションにあるログインIDから、ログインしている対象の会員情報を引く
$select_sth = $dbh->prepare("SELECT * FROM users WHERE id = :id");
$select_sth->execute([
    ':id' => $_SESSION['login_user_id'],
]);
$user = $select_sth->fetch();

if (isset($_POST['introduction'])){
  $update_sth = $dbh->prepare('UPDATE users SET self_introduction = :introduction WHERE id = :id;');
  $update_sth->execute([
    ':id' => $user['id'],
    ':introduction' => $_POST['introduction'],
  ]);
  header('HTTP/1.1 302 Found');
  header('Location: ./introduction.php');
  return;
}
?>

<h1>自己紹介設定・変更</h1>

<?php if(empty($user['self_introduction'])): ?>
  現在未設定
<?php else: ?>
  <?= nl2br(htmlspecialchars($user['self_introduction'])) ?>
<?php endif; ?>

<form method="POST">
  <textarea name="introduction" id="introductionInput" maxlength="1000"><?= (empty($user['self_introduction']))? '' : (htmlspecialchars($user['self_introduction'])); ?></textarea>
  <button type="submit" id="btn">変更</button>
</form>
<a href="../login_finish.php">プロフィールに戻る</a>

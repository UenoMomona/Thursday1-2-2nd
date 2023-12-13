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

if (isset($_POST['birthday'])){
  $update_sth = $dbh->prepare('UPDATE users SET birthday = :birthday WHERE id = :id;');
  $update_sth->execute([
    ':id' => $user['id'],
    ':birthday' => $_POST['birthday'],
  ]);
  header('HTTP/1.1 302 Found');
  header('Location: ./birthday.php');
  return;
}
?>

<h1>誕生日設定・変更</h1>

<form method="POST">
  <input type="date" name="birthday" value="<?= htmlspecialchars($user['birthday']) ?>">
  <button type="submit" id="btn">変更</button>
</form>
<a href="./index.php">プロフィールに戻る</a>

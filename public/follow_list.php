<?php
session_start();

// ログインしてなければログイン画面に飛ばす
if (empty($_SESSION['login_user_id'])) {
  header("HTTP/1.1 302 Found");
  header("Location: /login.php");
  return;
}

// DBに接続
$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');

$sql = 'SELECT user_relationships.*, users.name AS followee_name, users.icon_filename AS followee_icon FROM user_relationships INNER JOIN users ON user_relationships.followee_user_id = users.id WHERE user_relationships.follower_user_id = :follower_id ORDER BY user_relationships.id DESC;';
$select_sth = $dbh->prepare($sql);
$select_sth->execute([
  ':follower_id' => $_SESSION['login_user_id'],
]);

$followee_users = $select_sth->fetchAll();

?>

<h1>フォロー欄</h1>

<ul>
  <?php foreach($followee_users as $followee): ?>
  <li>
    <a href="./profile.php?user_id=<?= $followee['followee_user_id'] ?>">
      <?php if(!empty($followee['followee_icon'])): // アイコン画像がある場合は表示 ?>
      <img src="/image/<?= $followee['followee_icon'] ?>"
        style="height: 2em; width: 2em; border-radius: 50%; object-fit: cover;">
      <?php endif; ?>
      <?= htmlspecialchars($followee['followee_name']) ?>
    </a>
    (<?= $followee['created_at'] ?>にフォロー)
  </li>
  <?php endforeach; ?>
</ul>


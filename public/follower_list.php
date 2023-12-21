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

$sql = 'SELECT user_relationships.*, users.name AS follower_name, users.icon_filename AS follower_icon FROM user_relationships INNER JOIN users ON user_relationships.follower_user_id = users.id WHERE user_relationships.followee_user_id = :followee_id ORDER BY user_relationships.id DESC;';
$select_sth = $dbh->prepare($sql);
$select_sth->execute([
  ':followee_id' => $_SESSION['login_user_id'],
]);

$follower_users = $select_sth->fetchAll();

?>

<h1>フォロワ―欄</h1>

<ul>
  <?php foreach($follower_users as $follower): ?>
  <li>
    <a href="./profile.php?user_id=<?= $follower['follower_user_id'] ?>">
      <?php if(!empty($follower['follower_icon'])): // アイコン画像がある場合は表示 ?>
      <img src="/image/<?= $follower['follower_icon'] ?>"
        style="height: 2em; width: 2em; border-radius: 50%; object-fit: cover;">
      <?php endif; ?>
      <?= htmlspecialchars($follower['follower_name']) ?>
    </a>
    (<?= $follower['created_at'] ?>にフォロー)
  </li>
  <?php endforeach; ?>
</ul>
<a href="./setting/index.php">戻る</a>

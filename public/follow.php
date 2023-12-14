<?php 

session_start();

if( empty($_SESSION['login_user_id']) ){
  header('HTTP/1.1 302 Found');
  header('Location: /login.php');
  return;
}

//DBに接続
$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');
// フォロー対象(フォローされる側)のデータを引く
$followee_user = null;
if (!empty($_GET['followee_user_id'])) {
  $select_sth = $dbh->prepare("SELECT * FROM users WHERE id = :followee");
  $select_sth->execute([
      ':followee' => $_GET['followee_user_id'],
  ]);
  $followee_user = $select_sth->fetch();
}
if (empty($followee_user)) {
  header("HTTP/1.1 404 Not Found");
  print("フォローするユーザーが存在しません");
  return;
}

// 現在のフォロー状態をDBから取得
$select_sth = $dbh->prepare(
  "SELECT * FROM user_relationships"
  . " WHERE follower_user_id = :follower AND followee_user_id = :followee"
);
$select_sth->execute([
    ':followee' => $followee_user['id'], 
    ':follower' => $_SESSION['login_user_id'],
]);

if (!empty($select_sth->fetch())) {
  print("すでにフォローしています。");
  ?>
  <a href="./profile.php?user_id=<?= $followee_user['id'] ?>">戻る</a>
  <?
  return;
}

$insert_result = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
  $insert_sth = $dbh->prepare(
    "INSERT INTO user_relationships (follower_user_id, followee_user_id) VALUES (:follower, :followee)"
  );
  $insert_result = $insert_sth->execute([
      ':followee' => $followee_user['id'],
      ':follower' => $_SESSION['login_user_id'],
  ]);
}
?>

<?php if($insert_result): ?>
<div>
  <?= htmlspecialchars($followee_user['name']) ?> さんをフォローしました。<br>
  <a href="./profile.php?user_id=<?= $followee_user['id'] ?>">
    <?= htmlspecialchars($followee_user['name']) ?> さんのプロフィールに戻る
  </a>
</div>
<?php else: ?>
<div>
  <?= htmlspecialchars($followee_user['name']) ?> さんをフォローしますか?
  <a href="./profile.php?user_id=<?= $followee_user['id'] ?>">戻る</a>
  <form method="POST">
    <button type="submit">
      フォローする
    </button>
  </form>
</div>
<?php endif; ?>


<?php 

session_start();

if( empty($_SESSION['login_user_id']) ){
  header('HTTP/1.1 302 Found');
  header('Location: /login.php');
  return;
}

//DBに接続
$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');
$sql = 'SELECT * FROM users WHERE id = :id;';
$select_sth = $dbh->prepare($sql);
$select_sth->execute([
  ':id' => $_SESSION['login_user_id'],
]);
$user = $select_sth->fetch();
?>

<a href="../bbs.php">掲示板に戻る</a>

<h1>設定画面</h1>

<p>
  現在の設定
</p>
<dl> <!-- 登録情報を出力する際はXSS防止のため htmlspecialchars() を必ず使いましょう -->
  <dt>ID</dt>
  <dd><?= htmlspecialchars($user['id']) ?></dd>
  <dt>メールアドレス</dt>
  <dd><?= htmlspecialchars($user['email']) ?></dd>
  <dt>名前</dt>
  <dd><?= htmlspecialchars($user['name']) ?></dd>
</dl>

<p><a href="../follow_list.php">フォロー欄</a></p>
<p><a href="../follower_list.php">フォロワ―欄</a></p>

<ul>
  <li><a href="./name.php">名前設定</a></li>
  <li><a href="./icon.php">アイコン設定</a></li>
  <li><a href="./cover.php">カバー画像設定</a></li>
  <li><a href="./birthday.php">誕生日設定</a></li>
  <li><a href="./introduction.php">自己紹介文設定</a></li>
</ul>


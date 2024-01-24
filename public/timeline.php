<?php

$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');

session_start();

// ログインしてなかったらログイン画面へ
if (empty($_SESSION['login_user_id'])) {
  header("HTTP/1.1 302 Found");
  header("Location: /login.php");
  return;
}

//現在のユーザー情報を取得
$sql = 'SELECT * FROM users WHERE id = :id;';
$user_select_sth = $dbh->prepare($sql);
$user_select_sth->execute([
  ':id' => $_SESSION['login_user_id'],
]);
$user = $user_select_sth->fetch();

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>タイムライン</title>
  <link rel="stylesheet" href="css/timeline_style.css">
</head>
<body>
  
  <input type="hidden" id="count" value="0">
  <p>
    現在<?= htmlspecialchars($user['name']) ?>(ID : <?= $user['id'] ?>)さんでログイン中 
  </p>
  <a href="./profile.php?user_id=<?= $user['id'] ?>">プロフィールへ</a>
   / 
  <a href="./users.php">会員一覧</a>
   /
  <a href="./logout.php">ログアウト</a>
  <hr>

  <div id="insert">
    <p class="error"></p>
    <form method="POST" action="exam_insert.php">
      <textarea name="body" class="body" required></textarea>
      <input type="file" accept="image/*" name="image" id="imageInput" multiple>
      <button type="submit" class="submit">投稿</button>
    </form>
    <div class="canvases"></div>
  </div>

<!-- <div style="height:2000px; border: 1px solid #000;"></div> --!>

  <div id="posts"></div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="./image_set.js"></script>
<script src="./timeline_ajax.js"></script>
<script src="display_img_resize.js"></script> 
</body>
</html>

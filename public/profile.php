<?php

if( isset($_GET['user_id']) ){
  
 $dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');
 $select_sth = $dbh->prepare('SELECT * FROM users WHERE id = :id;');
 $select_sth->execute([
  ':id' => $_GET['user_id']
]);

$user = $select_sth->fetch();
}

if( empty($user) ){
  header('HTTP/1.1 404 Not Found');
  print('そのユーザーIDの会員情報は存在しません');
  return;
}

?>
<h1><?= $user['name'] ?> さん のプロフィール</h1>


<div>
  <?php if(empty($user['icon_filename'])): ?>
  現在未設定
  <?php else: ?>
  <img src="/image/<?= $user['icon_filename'] ?>"
    style="height: 5em; width: 5em; border-radius: 50%; object-fit: cover;">
  <?php endif; ?>
</div>
<div>
  <?php if(isset($user['self_introduction'])): ?>
  <p>
    <?= nl2br(htmlspecialchars($user['self_introduction'])) ?>
  </p>
  <?php endif; ?>
</div>

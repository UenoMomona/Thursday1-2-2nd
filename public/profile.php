<?php

if( isset($_GET['user_id']) ){
  
 $dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');
 $select_sth = $dbh->prepare('SELECT * FROM users WHERE id = :id;');
 $select_sth->execute([
  ':id' => $_GET['user_id']
]);

$user = $select_sth->fetch();
//var_dump($user);
}


if( empty($user) ){
  header('HTTP/1.1 404 Not Found');
  print('そのユーザーIDの会員情報は存在しません');
  return;
}

// この会員の投稿を取得
$select_sth = $dbh->prepare('SELECT * FROM bbs_user_entries WHERE user_id = :id;');
$select_sth->execute([
  ':id' => $_GET['user_id']
]);
$entries = $select_sth->fetchAll();
//var_dump($entries);

// bodyのhtmlを出力するための関数
function bodyFilter( string $body ): string
{
  $body = htmlspecialchars($body);
  $body = nl2br($body);

  $body = preg_replace('/&gt;&gt;(\d+)/', '<a href="#entry$1">&gt;&gt;$1</a>', $body);

  return $body;
}

?>
<?php if( !empty($user['cover_filename'])): ?>
<div style="
    width: 100%;
    height: 100px;
    background: url('/image/<?= $user['cover_filename'] ?>') center;
    background-size: cover;
    ">
</div>
<?php endif; ?>
<h1><?= $user['name'] ?> さん のプロフィール</h1>


<div>
  <?php if(empty($user['icon_filename'])): ?>
  現在未設定
  <?php else: ?>
  <img src="/image/<?= $user['icon_filename'] ?>"
    style="height: 5em; width: 5em; border-radius: 50%; object-fit: cover;">
  <?php endif; ?>
</div>

<?php if(!empty($user['birthday'])): ?>
<?php
  $birthday = DateTime::createFromFormat('Y-m-d', $user['birthday']);
  $today = new DateTime('now');
?>
  <?= $today->diff($birthday)->y ?>歳
<?php endif; ?>

<div>
  <?php if(isset($user['self_introduction'])): ?>
  <p>
    <?= nl2br(htmlspecialchars($user['self_introduction'])) ?>
  </p>
  <?php else: ?>
  <p>
    自己紹介未設定
  </p>
  <?php endif; ?>
</div>

<hr>

<?php foreach($entries as $entry): ?>
  <dl style="margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px solid #ccc;">
    <dt id="entry<?= htmlspecialchars($entry['id']) ?>">
      番号
    </dt>
    <dd>
      <?= htmlspecialchars($entry['id']) ?>
    </dd>
    <dt>日時</dt>
    <dd><?= $entry['created_at'] ?></dd>
    <dt>内容</dt>
    <dd>
      <?= bodyFilter($entry['body']) ?>
      <?php if(!empty($entry['image_filename'])): ?>
      <div>
        <img src="/image/<?= $entry['image_filename'] ?>" style="max-height: 10em;">
      </div>
      <?php endif; ?>
    </dd>
  </dl>
<?php endforeach ?>


<a href="./bbs.php">掲示板に戻る</a>

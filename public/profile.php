<?php

session_start();
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

// この会員の投稿を取得
$select_sth = $dbh->prepare('SELECT * FROM exam_bbs_entries WHERE user_id = :id;');
$select_sth->execute([
  ':id' => $_GET['user_id']
]);
$entries = $select_sth->fetchAll();
//var_dump($entries);
//画像を取得
foreach( $entries as $i => $entry) {
  $select_images = $dbh->prepare('SELECT image_filename FROM exam_bbs_images WHERE entry_id = :id;');
  $select_images->execute([
    ':id' => $entry['id']
  ]);
  $entry['image_filenames'] = $select_images->fetchAll(PDO::FETCH_ASSOC);
  $entries[$i] = $entry;
}

// ログイン中のユーザーがこの人をフォローしているかを調べる
$relationship = null;

if ( !empty($_SESSION['login_user_id'])){
  $sql = 'SELECT * FROM user_relationships WHERE followee_user_id = :followee AND follower_user_id = :follower;';
  $select_sth = $dbh->prepare($sql);
  $select_sth->execute([
    ':followee' => $user['id'],
    ':follower' => $_SESSION['login_user_id'],
  ]);

  $relationship = $select_sth->fetch();
}

// ログイン中のユーザーがこの人にフォローされているかを調べる
$is_follower = null;
if (!empty($_SESSION['login_user_id'])){
  $sql = 'SELECT * FROM user_relationships WHERE followee_user_id = :followee AND follower_user_id = :follower;';
  $select_sth = $dbh->prepare($sql);
  $select_sth->execute([
    ':followee' => $_SESSION['login_user_id'],
    ':follower' => $user['id'],
  ]);

  $is_follower = $select_sth->fetch();
}

// bodyのhtmlを出力するための関数
function bodyFilter( string $body ): string
{
  $body = htmlspecialchars($body);
  $body = nl2br($body);

  $body = preg_replace('/&gt;&gt;(\d+)/', '<a href="#entry$1">&gt;&gt;$1</a>', $body);

  return $body;
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="./css/timeline_style.css">
  <link rel="stylesheet" href="./css/profile.css">
</head>
<body>
<div class="wrapper">
  <?php if( !empty($user['cover_filename'])): ?>
    <div class="cover" style="
      background: url('/image/<?= $user['cover_filename'] ?>') center;
      background-size: cover;
      ">
    </div>
  <?php else: ?>
    <div class="cover" style ="background: #333;"></div>
  <?php endif; ?>

  <?php if(empty($user['icon_filename'])): ?>
    <div class="icon"></div>
  <?php else: ?>
    <img class="icon" src="/image/<?= $user['icon_filename'] ?>">
  <?php endif; ?>

  <div class="user_info">
    <p class="name"><?= $user['name'] ?></p>
    <?php if(!empty($user['birthday'])): ?>
      <?php
        $birthday = DateTime::createFromFormat('Y-m-d', $user['birthday']);
        $today = new DateTime('now');
      ?>
      <?= $today->diff($birthday)->y ?>歳
    <?php endif; ?>

    <?php if(isset($user['self_introduction'])): ?>
      <p><?= nl2br(htmlspecialchars($user['self_introduction'])) ?></p>
    <?php else: ?>
      <p>自己紹介未設定</p>
    <?php endif; ?>
  </div>
<hr>
  <div class="follow_btn">
  <?php if( isset($_SESSION['login_user_id']) ): ?>
    <?php if( $user['id'] === $_SESSION['login_user_id']): ?>
      <a href="./setting/index.php">編集</a>
    <?php else: ?>
      <?php if(empty($relationship)): ?>
        <a href="./follow.php?followee_user_id=<?= $user['id'] ?>">フォロー</a>
      <?php else: ?>
        <p>フォロー中</p>
      <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>
  </div>

      <?php if(!empty($is_follower)): ?>
        <p>フォローされています</p>
      <?php endif; ?>

  <?php foreach($entries as $entry): ?>
    <div class='post'>
      <a href="./profile.php?user_id=<?= $entry['user_id']?>">
        <?php if( $user['icon_filename'] != "" ): ?>
          <img src="/image/<?= $user['icon_filename'] ?>" class='user_icon'>
        <?php else: ?>
          <span class='dummy_icon'></span>
        <?php endif; ?>
        <span class='user_name'><?= $user['name'] ?></span>
      </a>
      <span class='updated_at'><?= $entry['updated_at'] ?></span>
      <span class='body'><?= bodyFilter($entry['body'])?></span>
      <?php if( $entry['image_filenames'] != [] ): ?>
         <div class='images'>
          <?php foreach ($entry['image_filenames'] as $image_file): ?>
            <img src='/image/<?= $image_file['image_filename'] ?>' class='posted_image'>
          <?php endforeach; ?>
         </div>
      <?php endif; ?>
    </div>
<?php endforeach ?>
</div>
<a href="./timeline.php">タイムラインに戻る</a>

<?php

$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');

// 会員データの取得
$sql = 'SELECT * FROM users ORDER BY id DESC;';
$select_sth = $dbh->prepare($sql);
$select_sth->execute();
$users = $select_sth->fetchAll();
session_start();

// 検索
if( !empty( $_GET['search_name'] ) || !empty( $_GET['search_year_pre'] ) || !empty( $_GET['search_year_end'])){
  if( !empty($_GET['search_name'])){
    $name = '%' . $_GET['search_name'] . '%';
  }else{
    $name = '%';
  }
  var_dump($name);
  if(!empty($_GET['search_year_pre'])){
    $pre_year = 'AND birthday > ' . $_GET['search_year_pre'] . '-01-01';
  }else{
    $pre_year = '';
  }
  if(!empty($_GET['search_year_end'])){
    $end_year = 'AND birthday < ' . $_GET['search_year_end'] . '-12-31';
  }else{
    $end_year = '';
  }

  $sql = 'SELECT * FROM users'
      . ' WHERE name LIKE :name'
      . ' :pre_year'
      . ' :end_year'
      . ' ORDER BY id DESC;';
  
  var_dump($sql);
  $select_sth = $dbh->prepare($sql);
  $select_sth->execute([
    ':name' => $name,
    ':pre_year' => $pre_year,
    ':end_year' => $end_year,
  ]);

  $users = $select_sth->fetchAll();
}
?>
<body>
  <h1>会員一覧</h1>

  <form method="GET">
    名前検索：<input type="text" name="search_name"><br>
    生年月日検索：<input type="text" name="search_year_pre">年～<input type="text" name="search_year_end">年<br>
    <button type="submit">検索</button>
  </form>
  <?php foreach( $users as $user): ?>
    <div style="display: flex; justify-content: start; align-items: center; padding: 1em 2em;">
      <?php if(empty($user['icon_filename'])): ?>
        <!-- アイコン無い場合は同じ大きさの空白を表示して揃えておく -->
        <div style="height: 2em; width: 2em;"></div>
      <?php else: ?>
        <img src="/image/<?= $user['icon_filename'] ?>"
          style="height: 2em; width: 2em; border-radius: 50%; object-fit: cover;">
      <?php endif; ?>
      <a href="/profile.php?user_id=<?= $user['id'] ?>" style="margin-left: 1em;">
        <?= htmlspecialchars($user['name']) ?>
      </a>
      <?php if(!empty($_SESSION['login_user_id'])):
         $sql = 'SELECT * FROM user_relationships WHERE followee_user_id = :followee AND follower_user_id = :follower;';
         $select_sth = $dbh->prepare($sql);
         $select_sth->execute([
          ':followee' => $user['id'],
          ':follower' => $_SESSION['login_user_id'],
         ]);
         $relationship = $select_sth->fetch();

        ?>
        <?php if( $user['id'] === $_SESSION['login_user_id']): ?>
          自身をフォローすることはできません
        <?php else: ?> 
          <?php if(empty($relationship)): ?>
            <a href="./follow.php?followee_user_id=<?=$user['id'] ?>">フォロー</a>
          <?php else: ?>
            フォロー中です
          <?php endif; ?>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <hr style="border: none; border-bottom: 1px solid gray;">
  <?php endforeach; ?>
<a href="./timeline.php"?>タイムラインへ戻る</a>
</body>

<?php

$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');


session_start();
if( empty($_SESSION['login_user_id']) ){
  //ログインしていない場合
  header("HTTP/1.1 401 Unauthorized");
  header("Content-Type: application/json");
  print(json_encode(['entries' => [] ]));
  return;
}

if(isset($_POST['count'])){
  $count = intval($_POST["count"]);
}else{
  $count = 0;
}

$sql = 'SELECT exam_bbs_entries.*, users.name AS user_name, users.icon_filename AS user_icon'
    . ' FROM exam_bbs_entries'
    . ' INNER JOIN users ON exam_bbs_entries.user_id = users.id'
    . ' WHERE'
    . '   exam_bbs_entries.user_id IN'
    . '     (SELECT followee_user_id FROM user_relationships WHERE follower_user_id = :login_user_id)'
    . '   OR exam_bbs_entries.user_id = :login_user_id'
    . ' ORDER BY exam_bbs_entries.created_at DESC'
    . ' LIMIT ' . $count . ', 10';
$select_sth = $dbh->prepare($sql);
$select_sth->execute([
  ':login_user_id' => $_SESSION['login_user_id'],
]);

foreach ( $select_sth as $entry){
  
  $sql = 'SELECT * FROM exam_bbs_images WHERE entry_id = :id';
  $select_images = $dbh->prepare($sql);
  $select_images->execute([
    ':id' => $entry['id'],
  ]);
  $images = $select_images->fetchAll(PDO::FETCH_ASSOC);

  $result_entry = [
    'id' => $entry['id'],
    'user_name' => $entry['user_name'],
    'user_profile_url' => '/profile.php?user_id=' . $entry['user_id'],
    'user_icon' => empty($entry['user_icon']) ? '' : $entry['user_icon'],
    'body' => htmlspecialchars($entry['body']),
    'image_files' => $images,
    'created_at' => $entry['created_at'],
    'updated_at' => $entry['updated_at'],
  ];
  $result_entries[] = $result_entry;
}

header("Content-type: application/json; charset=UTF-8");
echo json_encode($result_entries);
exit;


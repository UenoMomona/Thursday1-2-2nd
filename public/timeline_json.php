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

//ログイン情報の取得
$sql = 'SELECT * FROM users WHERE id = :id;';
$user_select_sth = $dbh->prepare($sql);
$user_select_sth->execute([
  ':id' => $_SESSION['login_user_id']
]);
$user = $user_select_sth->fetch();


$sql = 'SELECT bbs_user_entries.*, users.name AS user_name, users.icon_filename AS user_icon'
    . ' FROM bbs_user_entries'
    . ' INNER JOIN users ON bbs_user_entries.user_id = users.id'
    . ' WHERE'
    . '   bbs_user_entries.user_id IN'
    . '     (SELECT followee_user_id FROM user_relationships WHERE follower_user_id = :login_user_id)'
    . '   OR bbs_user_entries.user_id = :login_user_id'
    . ' ORDER BY bbs_user_entries.created_at DESC;';
$select_sth = $dbh->prepare($sql);
$select_sth->execute([
  ':login_user_id' => $_SESSION['login_user_id'],
]);

// bodyのHTMLを出力するための関数を用意する
function bodyFilter (string $body): string
{
  $body = htmlspecialchars($body); // エスケープ処理
  $body = nl2br($body); // 改行文字を<br>要素に変換

  // >>1 といった文字列を該当番号の投稿へのページ内リンクとする (レスアンカー機能)
  // 「>」(半角の大なり記号)は htmlspecialchars() でエスケープされているため注意
  $body = preg_replace('/&gt;&gt;(\d+)/', '<a href="#entry$1">&gt;&gt;$1</a>', $body);

  return $body;
}


//JSON用entries

$result_entries = [];
foreach ( $select_sth as $entry){
  $result_entry = [
    'id' => $entry['id'],
    'user_name' => $entry['user_name'],
    'user_profile_url' => '/profile.php?user_id=' . $entry['user_id'],
    'user_icon' => empty($entry['user_icon']) ? '' : '/image/' . $entry['user_icon'],
    'body' => $entry['body'],
    'body_image' => empty($entry['image_filename']) ? '' : '/image/' . $entry['image_filename'],
    'created_at' => $entry['created_at'],
  ];
  $result_entries[] = $result_entry;
}


header("HTTP/1.1 200 OK");
header("Content-Type: application/json");
print(json_encode(['entries' => $result_entries]));

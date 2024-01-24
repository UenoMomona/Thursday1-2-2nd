<?php

$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');

session_start();

// ログインされていない場合はログイン画面へ
if( empty($_SESSION['login_user_id'])){
    header("HTTP/1.1 302 Found");
    header("Location: /login.php");
    return;
}

if( empty($_POST['body']) ){
  header("HTTP/1.1 302 Found");
  header("Location: /timeline.php");
  return;
}

  // 画像の投稿準備
$image_filenames = [];
$i = 0;

while(isset($_POST['image_base64_' . $i])){

  // 先頭の data:~base64, のところは削る
  $base64 = preg_replace('/^data:.+base64,/', '', $_POST['image_base64_' . $i]);

  // base64からバイナリにデコードする
  $image_binary = base64_decode($base64);
  
  // 新しいファイル名を決めてバイナリを出力する
  $image_filename = strval(time()) . bin2hex(random_bytes(25)) . '.png';
  $filepath =  '/var/www/upload/image/' . $image_filename;
  file_put_contents($filepath, $image_binary);
  $image_filenames[] = $image_filename;
  $i++;
}

try {

  $insert_sth = $dbh->prepare('INSERT INTO exam_bbs_entries (user_id, body) VALUES (:user_id, :body );');

  $insert_sth->execute([
    ':user_id' => $_SESSION['login_user_id'],
    ':body' => $_POST['body'],
  ]);
  $entry_id = $dbh->lastInsertId();

  foreach($image_filenames as $image_filename){
    $insert_sth = $dbh->prepare('INSERT INTO exam_bbs_images (user_id, entry_id, image_filename) VALUES (:user_id, :entry_id, :image_filename);');
    $insert_sth->execute([
      ':user_id' => $_SESSION['login_user_id'],
      ':entry_id' => $entry_id,
      ':image_filename' => $image_filename,
    ]);
  }
}catch(Exception $e){
  echo $e;
}

header('HTTP/1.1 302 Found');
header('Location: ./timeline.php');
return;


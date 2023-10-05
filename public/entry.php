<?php

if(isset($_GET['id'])){
  $id = intval($_GET['id']);
  if($id < 1){  
    header("Location: ./bbstest.php");
    return;
  }
}else{
  header("Location: ./bbstest.php");
  return;
}
try{
$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');

$sql = 'SELECT * FROM bbs_entries WHERE id = :id;';
$pre = $dbh->prepare($sql);
$pre->bindValue(':id', $id);
$pre->execute();
$entry = $pre->fetch(PDO::FETCH_ASSOC);
if($entry == false){
  header("Location: ./bbstest.php");
}
}catch(PDOException $e){
  echo "DB接続失敗";
}
?>

<h3>投稿</h3>
<dl>
  <dt>ID</dt>
  <dd><?= $entry['id'] ?> </dd>
  <dt>日時</dt>
  <dd><?= $entry['created_at'] ?> </dd>
  <dt>内容</dt>
  <dd><?= nl2br(htmlspecialchars($entry['body'])) ?> </dd>
</dl>
<a href="./bbstest.php">一覧へ戻る</a>

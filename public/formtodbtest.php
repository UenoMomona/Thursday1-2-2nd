<?php

$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');

$body = $_POST["body"] ?? "";

if($body != ""){
  $sql = 'INSERT INTO hoge (text) VALUES (:body);';
  $pre = $dbh->prepare($sql);
  $pre->bindValue(':body',$_POST['body']);

  $r = $pre->execute();

  if($r){
    header("HTTP/1.1 302 Found");
    header("Location: ./formtodbtest.php");
  }
  return;
}


$page = intval( $_GET['p']) ?? 1;
if($page < 1){
  print("1ページ目に遷移します");
  $page = 1;
}

$offset_num = ($page -1) * 10;

$sql = 'SELECT * FROM hoge ORDER BY created_at DESC LIMIT 11 OFFSET :num;';
$pre = $dbh->prepare($sql);
$pre->bindParam(':num', $offset_num ,PDO::PARAM_INT);
$pre->execute();
$data = $pre->fetchAll();

if(count($data) == 11){
  $limit = 10;
}else{
  $limit = count($data);
}

/*
for($i = 0; $i < $limit; $i++){
  $text =nl2br(htmlspecialchars($data[$i]['text']));
  $created_at = htmlspecialchars($data[$i]['created_at']);
  print("<hr><dl><dt>投稿日時</dt><dd>{$created_at}</dd><dt>投稿内容<dt><dd>{$text}</dd></dl>");
}
*/
?>

<!DOCTYPE html>
  <html la="ja">
  <head>
     <link rel="stylesheet" href="css/formtodb.css">
  </head>
<body>

<h2>投稿</h2>

<form method="POST" action="./formtodbtest.php">
  <textarea name="body"></textarea><br>
  <button type="submit">送信</button>
</form>

<hr>
<h2>投稿一覧</h2>
<p>今は<?= $page ?> ページ目です</p>
<div>
<?php if($page != 1): ?>
<a href="./formtodbtest.php?p=<?= $page -1 ?>" class="before">前のページへ</a>
<? else: ?>
<div class="before"></div>
<?php endif; ?>
<?php if(count($data) == 11): ?>
<a href="./formtodbtest.php?p=<?= $page +1 ?>" class="after">次のページへ</a>
<?php else: ?>
<div class="before"></div>
<?php endif; ?>
</div>

<?php for($i = 0; $i < $limit; $i++): ?>
<hr>
<dl>
  <dt>投稿日時</dt>
  <dd><?= $data[$i]["created_at"] ?></dd>
  <dt>投稿内容</dt>
  <dd><?= nl2br(htmlspecialchars($data[$i]["text"])) ?></dd>
</dl>
<?php endfor; ?>

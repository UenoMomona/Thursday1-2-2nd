<table border=1>
  <tr>
    <th>接続元IPアドレス</th>
    <th>ユーザーエージェント</th>
    <th>アクセス日時</th>
  </tr>
<?php
ob_start();

$address = $_SERVER["REMOTE_ADDR"];
$userAgent = $_SERVER['HTTP_USER_AGENT'];

$pageNum = $_GET['page'] ?? 0;
//var_dump($pageNum);
if($pageNum > 0){
  $startPosition = $pageNum * 10 -1;
}else{
  $startPosition = 0;
}
//try{
  $dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');
  
  $sql = 'SELECT COUNT(*) FROM access_users;';
  $pre = $dbh->prepare($sql);
  $pre->execute();
  $num = $pre->fetchColumn();
  echo "あなたは{$num}人目です";
  
  if($num / 10 < $pageNum){
    echo "そのページは存在しません";
    exit;
  }

  $sql = 'INSERT INTO access_users (address, userAgent) VALUES (:address, :userAgent);';
  $pre = $dbh->prepare($sql);
  $pre->bindValue(':address', $address);
  $pre->bindValue(':userAgent', $userAgent);

  $pre->execute();

  $sql = 'SELECT * FROM access_users ORDER BY accessDate DESC LIMIT 11 OFFSET :num;';
  $pre = $dbh->prepare($sql);
  $pre->bindParam(':num', $startPosition ,PDO::PARAM_INT);

  $pre->execute();

  $data = $pre->fetchAll();

  if($pageNum > 0){ 
    $a = $pageNum - 1;
    echo "<a href='./accessCounter.php?page=$a'>前のページへ</a>";
  }

  if(count($data) == 11){ 
    $b = $pageNum + 1;
    echo "<a href='./accessCounter.php?page=$b'>次のページへ</a>";
  }

  //var_dump($data);
  for($j = 0; $j < count($data) - 1; $j++){
    $d = $data[$j];
    echo "<tr>";
    for($i = 0; $i < count($d)/2; $i++){
      echo "<td>{$d[$i]}</td>";
    }
    echo "</tr>";
  }

//catch(Throwable $e){
//  $error[] = $e->getMessage();
//}


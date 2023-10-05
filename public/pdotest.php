<?php

try{
  $dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');


  $sql = 'INSERT INTO hoge (text) VALUES (:text);';
  $pre = $dbh->prepare($sql);
  $pre->bindValue(':text','Hello');

  $r = $pre->execute();

  if($r){
    print('insertできました');
  }
}catch(Throwable $e){
  echo $e->getMessage();
}

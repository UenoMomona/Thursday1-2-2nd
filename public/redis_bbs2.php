<?php

$redis = new Redis();
$redis->connect('redis',6379);

$key = 'bbs_bodys';


$bodys = [];
if($redis->exists($key)){
  $bodys = json_decode($redis->get($key));
}

if(!empty($_POST['body'])){
  //var_dump($_POST['body']);
  array_unshift($bodys, strval($_POST['body']));

  $value = json_encode($bodys);
  $redis->set($key, strval($value));
  return header('Location: ./redis_bbs.php');
}
?>

<form method="POST">
  <textarea name="body"></textarea>
  <button type="submit">投稿</button>
</form>
<?php if($bodys !== []): ?>
  <?php foreach($bodys as $body): ?>
    <p>投稿：<?= $body ?></p>
    <hr>
  <?php endforeach; ?>
<?php endif; ?>

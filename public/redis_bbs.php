<?php

$redis = new Redis();
$redis->connect('redis',6379);

$key = 'bbs_body';


if(!empty($_POST['body'])){
  //var_dump($_POST['body']);

  $redis->set($key, strval($_POST['body']));
  return header('Location: ./redis_bbs.php');
}

$body = "";
if($redis->exists($key)){
  $body = $redis->get($key);
}

?>

<form method="POST">
  <textarea name="body"></textarea>
  <button type="submit">投稿</button>
</form>
<p>投稿：<?= $body ?></p>

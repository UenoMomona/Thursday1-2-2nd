<?php

date_default_timezone_set ('Asia/Tokyo');

$session_cookie_name = 'session_id';
$session_id = $_COOKIE[$session_cookie_name] ?? base64_encode(random_bytes(64));

if( !isset( $_COOKIE[$session_cookie_name] ) ){
  setcookie($session_cookie_name, $session_id);
}

$redis = new Redis();
$redis->connect('redis', 6379);

$redis_session_key = "session-" . $session_id;

$session_values = $redis->exists($redis_session_key)
  ? json_decode($redis->get($redis_session_key), true)
  : [];

if(!isset($session_values['access_num'])){
  $session_values['access_num'] = 0;
}
$session_values['access_num'] += 1;

// 前回のアクセスした時刻を取得する
// なければ空文字とする
$last_access_time = $session_values['access_time'] ?? "";

// 現在の時刻を記録する
$session_values['access_time'] = date('Y-m-d H:i:s');

$redis->set($redis_session_key, json_encode($session_values));

$access_num = $session_values['access_num'];

?>

<p>このセッションでの<?= $access_num ?>回目のアクセスです！</p>
<?php if($last_access_time === ""): ?>
<p>前回のアクセスはありません</p>
<?php else: ?>
<p>前回のアクセス時間：<?= $last_access_time ?></p>
<?php endif; ?>

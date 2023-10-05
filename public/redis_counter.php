<?php

$redis = new Redis();

$redis->connect('redis', 6379);

$key = 'count';

if($redis->exists($key)){
  $val = intval($redis->get($key)) + 1;
}else{
  $val = 1;
}

$redis->set($key ,strval($val));

$redis->close();

echo strval($val) . "人目の訪問者です";

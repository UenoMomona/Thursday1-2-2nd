<?php

session_start();

$session_id = session_id();
var_dump($session_id);
$_SESSION['id'] = "aaa";


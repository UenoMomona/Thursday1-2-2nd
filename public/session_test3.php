<?php
session_start();

$session_id = session_id();
$a = $_SESSION['id'];

var_dump($session_id);
var_dump($a);

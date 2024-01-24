<?php

$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');

session_start();

// ログインされていない場合はログイン画面へ
if( empty($_SESSION['login_user_id'])){
    header("HTTP/1.1 302 Found");
    header("Location: /login.php");
    return;
}

$_SESSION = array();
session_destroy();

?>

<p>正常にログアウトされました</p>
<a href="login.php">ログイン画面へ</a>

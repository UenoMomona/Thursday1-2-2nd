<?php

session_start();

  // セッションIDの取得(なければ新規で作成&設定)
  $session_id = session_id();

  // セッションにログインIDが無ければ (=ログインされていない状態であれば) ログイン画面にリダイレクトさせる
  if(empty($_SESSION['login_user_id'])){  
    header("HTTP/1.1 302 Found");
    header("Location: ./login.php");
    return;
  }


  // DBに接続
  $dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');
  // セッションにあるログインIDから、ログインしている対象の会員情報を引く
  $insert_sth = $dbh->prepare("SELECT * FROM users WHERE id = :id");
  $insert_sth->execute([
      ':id' => $_SESSION['login_user_id'],
  ]);
  $user = $insert_sth->fetch();

if(!empty($_POST['name'])){
  $update_sth = $dbh->prepare('UPDATE users SET name = :name WHERE id = :id;');
  $update_sth->execute([
    ':name' => $_POST['name'],
    ':id' => $_SESSION['login_user_id'],
  ]);

  header("HTTP/1.1 302 Found");
  header("Location: ./name.php");
  return;
}
?>
<h1>ユーザー名変更</h1>
<form method="POST">
  名前：
  <input type="text" name="name" value="<?= $user['name'] ?>">
  <button type="submit">決定</button>
</form>
<a href="./index.php">設定一覧に戻る</a>

<?php

$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');

if( !empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['password'])){

  //同一なメールアドレスを登録できないようにする
  $select_sth = $dbh->prepare('SELECT * FROM users WHERE email = :email;');
  $select_sth->execute([
    ':email' => $_POST['email']
  ]);

  $result = $select_sth->fetch();
  if( !empty( $result )){
    header("HTTP/1.1 302 Found");
    header("Location: /signup.php?error=1");
    return;
  }

  // insert
  $insert_sth = $dbh->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
  $insert_sth->execute([
    ':name' => $_POST['name'],
    ':email' => $_POST['email'],
    ':password' => $_POST['password'],
  ]);

  // リダイレクト
  header("HTTP/1.1 302 Found");
  header("Location: /signup_finish.php");

  return;
}

?>

<h1>会員登録</h1>

<?php if(!empty($_GET['error'])): ?>
<div style="color: red;">
  同一のメールアドレスで登録することはできません!
</div>
<?php endif; ?>
<form method="POST">
  <!-- input要素のtype属性は全部textでも動くが、適切なものに設定すると利用者は使いやすい -->
  <label>
    名前:
    <input type="text" name="name">
  </label>
  <br>
  <label>
    メールアドレス:
    <input type="email" name="email">
  </label>
  <br>
  <label>
    パスワード:
    <input type="password" name="password" min="6" autocomplete="new-password">
  </label>
  <br>
  <button type="submit">決定</button>
</form> 

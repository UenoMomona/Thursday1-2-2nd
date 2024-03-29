<?php


$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');

if( !empty($_POST['email']) && !empty($_POST['password'] )) {
  
  session_start();

  // emailから会員情報を取ってくる
  // 同じemailで複数の会員情報が登録されている場合は一番新しいもの一つを正とする
  $select_sth = $dbh->prepare('SELECT * FROM users WHERE email = :email ORDER BY id DESC LIMIT 1;');
  $select_sth->execute([
    ':email' => $_POST['email']
  ]);

  $user = $select_sth->fetch();

  if( empty($user) ){
    // 入力に合ったユーザーが存在しない場合は処理を中断し、エラー情報をもってログイン画面にリダイレクト
    header("HTTP/1.1 302 Found");
    header("Location: ./login.php?error=1");
    return;
  }


  $correct_password = password_verify($_POST['password'], $user['password']);

  if( !$correct_password ){
    header("HTTP/1.1 302 Found");
    header("Location: ./login.php?error=22");
    return;
  }

  //セッションのIDの取得　なければ新規作成
  $session_id = session_id();

  $_SESSION['login_user_id'] = $user['id'];

  //ログイン完了画面にリダイレクト
  header("HTTP/1.1 302 Found");
  header("Location: ./login_finish.php");
  return;
}    

?>

<h1>ログイン</h1>
<p>はじめての人は<a href="./signup.php">会員登録</a>しましょう!</p>
<hr>
<!-- ログインフォーム -->
<form method="POST">
  <!-- input要素のtype属性は全部textでも動くが、適切なものに設定すると利用者は使いやすい -->
  <label>
    メールアドレス:
    <input type="email" name="email">
  </label>
  <br>
  <label>
    パスワード:
    <input type="password" name="password" min="6">
  </label>
  <br>
  <button type="submit">決定</button>
</form>

<?php if(!empty($_GET['error'])): // エラー用のクエリパラメータがある場合はエラーメッセージ表示 ?>
<div style="color: red;">
  メールアドレスかパスワードが間違っています。
</div>
<?php endif; ?>

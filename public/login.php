<?php

$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');

if( !empty($_POST['email']) && !empty($_POST['password'] )) {
  
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

  // パスワードが正しいかチェック
  // $correct_password = hash('sha256', $_POST['password']) === $user['password'];
  $password_hash = mb_substr($user['password'], 0, 64); // 0から64文字分がハッシュ化されたところ
  $salt = mb_substr($user['password'], 64, 64); //64から64文字分がsalt
  
  //ストレッチング
  $pass = $_POST['password'] . $salt;
  for($i = 0; $i < 100; $i++){
    $pass = hash('sha256', $pass);
  }

  $correct_password = $pass === $password_hash;

  if( !$correct_password ){
    header("HTTP/1.1 302 Found");
    header("Location: ./login.php?error=22");
    return;
  }

  //セッションのIDの取得　なければ新規作成
  $session_cookie_name = 'session_id';
  $session_id = $_COOKIE[$session_cookie_name] ?? base64_encode(random_bytes(64));
  if(!isset( $_COOKIE[$session_cookie_name] )) {
    setcookie($session_cookie_name, $session_id );
  }

  $redis = new Redis();
  $redis->connect('redis', 6379);

  // redisのkey
  $redis_session_key = "session-" . $session_id;

  // redisからvalueの読み込み
  $session_values = $redis->exists($redis_session_key)
    ? json_decode($redis->get($redis_session_key), true)
    : [];

  // valueに会員のidを登録
  $session_values['login_user_id'] = $user['id'];
  // redisに登録
  $redis->set($redis_session_key, json_encode($session_values));

  //ログイン完了画面にリダイレクト
  header("HTTP/1.1 302 Found");
  header("Location: ./login_finish.php");
  return;
}    

?>

<h1>ログイン</h1>

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

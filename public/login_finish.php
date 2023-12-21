<?php

session_start();

// セッションIDの取得(なければ新規で作成&設定)
$session_id = session_id();


// セッションにログインIDが無ければ (=ログインされていない状態であれば) ログイン画面にリダイレクトさせる
if (empty($_SESSION['login_user_id'])) {
  header("HTTP/1.1 302 Found");
  header("Location: ./login.php");
  return;
}

$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');

// sessionのidからuser情報を取得

$select_sth = $dbh->prepare('SELECT * FROM users WHERE id = :id;');
$select_sth->execute([
  ':id' => $_SESSION['login_user_id'],
]);

$user = $select_sth->fetch();
?>

<h1>ログイン完了</h1>

<p>
  ログイン完了しました!
  <a href="./timeline_2.php">タイムラインはこちらから</a>
</p>
<hr>
<p>
  また、あなたが現在ログインしている会員情報は以下のとおりです。
</p>
<dl> <!-- 登録情報を出力する際はXSS防止のため htmlspecialchars() を必ず使いましょう -->
  <dt>ID</dt>
  <dd><?= htmlspecialchars($user['id']) ?></dd>
  <dt>メールアドレス</dt>
  <dd><?= htmlspecialchars($user['email']) ?></dd>
  <dt>名前</dt>
  <dd><?= htmlspecialchars($user['name']) ?></dd>
  <dt>アイコン</dt>
  <dd>
    <?php if( empty($user['icon_filename']) ): ?>
    現在未設定
    <?php else: ?>
    <img src="/image/<?= $user['icon_filename'] ?>"
      style="height: 5em; width: 5em; border-radius: 50%; object-fit: cover;">
    <?php endif; ?>
  </dd>
  <dt>自己紹介</dt>
  <dd>
    <?php if( empty($user['self_introduction'])): ?>
    現在未設定
    <?php else: ?>
    <?= nl2br(htmlspecialchars($user['self_introduction'])) ?>
    <?php endif; ?>
  </dd>
</dl>

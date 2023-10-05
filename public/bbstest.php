<?php
$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');

if (isset($_POST['body'])) {
  // POSTで送られてくるフォームパラメータ body がある場合

  // insertする
  $insert_sth = $dbh->prepare("INSERT INTO bbs_entries (body) VALUES (:body)");
  $insert_sth->execute([
      ':body' => $_POST['body'],
  ]);

  // 処理が終わったらリダイレクトする
  // リダイレクトしないと，リロード時にまた同じ内容でPOSTすることになる
  header("HTTP/1.1 302 Found");
  header("Location: ./bbstest.php");
  return;
}

$serch_flag = false;
if (isset($_GET["key_word"])){
  $key_word = "%" . strval($_GET["key_word"]) . "%";
  // getで送られてくるフォームパラメータ key_wordがある場合
  $sql = 'SELECT * FROM bbs_entries WHERE body LIKE :key_word ORDER BY created_at DESC;';
  $select_sth = $dbh->prepare($sql);
  $select_sth->bindParam(':key_word',$key_word);
  $select_sth->execute();
  $serch_flag = true;
}else{
  // いままで保存してきたものを取得
  $select_sth = $dbh->prepare('SELECT * FROM bbs_entries ORDER BY created_at DESC');
  $select_sth->execute();
}

?>
<h2>投稿する</h2>
<!-- フォームのPOST先はこのファイル自身にする -->
<form method="POST" action="./bbstest.php">
  <textarea name="body"></textarea>
  <button type="submit">送信</button>
</form>
<hr>
<h2>検索する</h2>
<form method="GET" action="./bbstest.php">
  <input type="text" name="key_word" value='<?= $_GET['key_word'] ?? "" ?>'>
  <button type="submit">検索</button>
</form>

<?php if($serch_flag): ?>
  現在「<?= $_GET['key_word'] ?>」で検索中 <a href="./bbstest.php">検索解除</a>
<?php endif; ?>
<hr>
<?php foreach($select_sth as $entry): ?>
  <dl style="margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px solid #ccc;">
    <dt>ID</dt>
    <dd><a href="entry.php?id=<?= $entry['id'] ?>"><?= $entry['id'] ?></a></dd>
    <dt>日時</dt>
    <dd><?= $entry['created_at'] ?></dd>
    <dt>内容</dt>
    <dd><?= nl2br(htmlspecialchars($entry['body'])) // 必ず htmlspecialchars() すること ?></dd>
  </dl>
<?php endforeach ?>

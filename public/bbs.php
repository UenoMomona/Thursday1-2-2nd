<?php

$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');

session_start();
  
//投稿一覧を取得
$sql = 'SELECT bbs_user_entries.*, users.name AS user_name, users.icon_filename AS user_icon FROM bbs_user_entries'
    . ' INNER JOIN users ON bbs_user_entries.user_id = users.id'
    . ' ORDER BY bbs_user_entries.created_at DESC;';
$select_sth = $dbh->prepare($sql);
$select_sth->execute();


// bodyのhtmlを出力するための関数
function bodyFilter( string $body ): string
{
  $body = htmlspecialchars($body);
  $body = nl2br($body);

  $body = preg_replace('/&gt;&gt;(\d+)/', '<a href="#entry$1">&gt;&gt;$1</a>', $body);

  return $body;
}

?>

<?php if(empty($_SESSION['login_user_id'])): ?>
  <p><a href="./login.php">ログイン</a>をしてください</p>
<?php else: ?>
  <p>
    <a href="./timeline_2.php">タイムラインへ</a>
  </p>
<?php endif; ?>
<hr>
<?php foreach($select_sth as $entry): ?>
  <dl style="margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px solid #ccc;">
    <dt id="entry<?= htmlspecialchars($entry['id']) ?>">
      番号
    </dt>
    <dd>
      <?= htmlspecialchars($entry['id']) ?>
    </dd>
    <dt>
      投稿者
    </dt>
    <dd>
      <a href="profile.php?user_id=<?= $entry['user_id']; ?>">
        <?php if(!empty($entry['user_icon'])): ?>
          <img src="/image/<?= $entry['user_icon'] ?>"
            style="height: 5em; width: 5em; border-radius: 50%; object-fit: cover;">
        <?php endif; ?>
        <?= htmlspecialchars($entry['user_name']) ?>
        (ID: <?= htmlspecialchars($entry['user_id']) ?>)
      </a>
    </dd>
    <dt>日時</dt>
    <dd><?= $entry['created_at'] ?></dd>
    <dt>内容</dt>
    <dd>
      <?= bodyFilter($entry['body']) ?>
      <?php if(!empty($entry['image_filename'])): ?>
      <div>
        <img src="/image/<?= $entry['image_filename'] ?>" style="max-height: 10em;">
      </div>
      <?php endif; ?>
    </dd>
  </dl>
<?php endforeach ?>

<?php

$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');

session_start();

if(isset($_POST['body']) && !empty($_SESSION['login_user_id'])){

  // 画像の投稿準備
  $image_filename = null;
  if (!empty($_POST['image_base64'])) {
    // 先頭の data:~base64, のところは削る
    $base64 = preg_replace('/^data:.+base64,/', '', $_POST['image_base64']);

    // base64からバイナリにデコードする
    $image_binary = base64_decode($base64);

    // 新しいファイル名を決めてバイナリを出力する
    $image_filename = strval(time()) . bin2hex(random_bytes(25)) . '.png';
    $filepath =  '/var/www/upload/image/' . $image_filename;
    file_put_contents($filepath, $image_binary);
  }
  $insert_sth = $dbh->prepare('INSERT INTO bbs_user_entries (user_id, body, image_filename) VALUES (:user_id, :body, :image_filename);');
  $insert_sth->execute([
    ':user_id' => $_SESSION['login_user_id'],
    ':body' => $_POST['body'],
    'image_filename' => $image_filename,
  ]);

  header('HTTP/1.1 302 Found');
  header('Location: ./bbs.php');
  return;
}

  
//投稿一覧を取得
$sql = 'SELECT bbs_user_entries.*, users.name AS user_name, users.icon_filename AS user_icon'
    . ' FROM bbs_user_entries'
    . ' INNER JOIN users ON bbs_user_entries.user_id = users.id'
    . ' WHERE bbs_user_entries.user_id in '
    . ' ( SELECT followee_user_id FROM user_relationships WHERE follower_user_id = :login_user_id)'
    . ' OR bbs_user_entries.user_id = :login_user_id'
    . ' ORDER BY bbs_user_entries.created_at DESC;';
$select_sth = $dbh->prepare($sql);
$select_sth->execute([
  ':login_user_id' => $_SESSION['login_user_id']
  ]);

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
  <p>投稿するには<a href="./login.php">ログイン</a>が必要です</p>
<?php else: ?>
  <p>現在ログイン中 (<a href="./setting/index.php">設定画面はこちら</a>)
  <form method="POST" action"./bbs.php">
    <textarea name="body" required></textarea>

    <div style="margin: 1em 0;">
      <input type="file" accept="image/*" name="image" id="imageInput">
    </div>
    <input id="imageBase64Input" type="hidden" name="image_base64">
    <canvas id="imageCanvas" style="display: none;"></canvas>
    <button type="submit">送信</button>
  </form>
<?php endif; ?>

<hr>
<a href="./bbs.php">全投稿を表示する</a>
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

<script>
document.addEventListener("DOMContentLoaded", () => {
  const imageInput = document.getElementById("imageInput");
  imageInput.addEventListener("change", () => {
    if (imageInput.files.length < 1) {
      // 未選択の場合
      return;
    }

    const file = imageInput.files[0];
    if (!file.type.startsWith('image/')){ // 画像でなければスキップ
      return;
    }

    // 画像縮小処理
    const imageBase64Input = document.getElementById("imageBase64Input"); // base64を送るようのinput
    const canvas = document.getElementById("imageCanvas"); // 描画するcanvas
    const reader = new FileReader();
    const image = new Image();
    reader.onload = () => { // ファイルの読み込み完了したら動く処理を指定
      image.onload = () => { // 画像として読み込み完了したら動く処理を指定

        // 元の縦横比を保ったまま縮小するサイズを決めてcanvasの縦横に指定する
        const originalWidth = image.naturalWidth; // 元画像の横幅
        const originalHeight = image.naturalHeight; // 元画像の高さ
        const maxLength = 1000; // 横幅も高さも1000以下に縮小するものとする
        if (originalWidth <= maxLength && originalHeight <= maxLength) { // どちらもmaxLength以下の場合そのまま
            canvas.width = originalWidth;
            canvas.height = originalHeight;
        } else if (originalWidth > originalHeight) { // 横長画像の場合
            canvas.width = maxLength;
            canvas.height = maxLength * originalHeight / originalWidth;
        } else { // 縦長画像の場合
            canvas.width = maxLength * originalWidth / originalHeight;
            canvas.height = maxLength;
        }

        // canvasに実際に画像を描画 (canvasはdisplay:noneで隠れているためわかりにくいが...)
        const context = canvas.getContext("2d");
        context.drawImage(image, 0, 0, canvas.width, canvas.height);

        // canvasの内容をbase64に変換しinputのvalueに設定
        imageBase64Input.value = canvas.toDataURL();
      };
      image.src = reader.result;
    };
    reader.readAsDataURL(file);
  });
});
</script>

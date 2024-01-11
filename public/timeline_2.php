<?php

$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');

session_start();

// ログインされていない場合はログイン画面へ
if( empty($_SESSION['login_user_id'])){
    header("HTTP/1.1 302 Found");
    header("Location: /login.php");
    return;
}

// 現在のログイン情報を取得する
$sql = 'SELECT * FROM users WHERE id = :id;';
$user_select_sth = $dbh->prepare($sql);
$user_select_sth->execute([
  ':id' => $_SESSION['login_user_id'],
]);
$user = $user_select_sth->fetch();

// 投稿がある場合
if(isset($_POST['body'])){

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
  header('Location: ./timeline_2.php');
  return;
}


?>

<?php if(empty($_SESSION['login_user_id'])): ?>
  <p><a href="./login.php">ログイン</a>が必要です</p>
<?php else: ?>
  <p>
    現在<?= htmlspecialchars($user['name']) ?>(ID : <?= $user['id'] ?>)さんでログイン中 
  </p>
  <a href="./setting/index.php">設定画面</a>
   / 
  <a href="./users.php">会員一覧</a>
  <hr>
  <form method="POST" action"./timeline_2.php">
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
  
  <dl id="entryTemplate" style="display: none; margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px solid #ccc;">
    <dt>番号</dt>
    <dd data-role="entryIdArea"></dd>
    <dt>投稿者</dt>
    <dd>
      <a href="" data-role="entryUserAnchor"></a>
    </dd>
    <dt>日時</dt>
    <dd data-role="entryCreatedAtArea"></dd>
    <dt>内容</dt>
    <dd data-role="entryBodyArea"></dd>
    <dd data-role="entryBodyImageArea"></dd>
  </dl>

<div id="entryRenderArea"></div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const entryTemplate = document.getElementById('entryTemplate');
  const entryRenderArea = document.getElementById('entryRenderArea');

  const request = new XMLHttpRequest();
  request.onload = (event) => {
    const response = event.target.response;
    response.entries.forEach( (entry) => {

      const entryCopied = entryTemplate.cloneNode(true);

      entryCopied.style.display = 'block';

      entryCopied.id = 'entry' + entry.id.toString();

      entryCopied.querySelector('[data-role="entryIdArea"]').innerText = entry.id.toString();

      entryCopied.querySelector('[data-role="entryUserAnchor"]').href = entry.user_profile_url;
  
      entryCopied.querySelector('[data-role="entryUserAnchor"]').innerText = entry.user_name;
  
      if(entry.user_icon != ""){
        const userIcon = new Image();
        userIcon.src = entry.user_icon;
        userIcon.style.display = 'block';
        userIcon.style.height = '5em';
        userIcon.style.width = '5em';
        userIcon.style.borderRadius = '50%';
         userIcon.style.objectFit = 'cover';
        entryCopied.querySelector('[data-role="entryUserAnchor"]').appendChild(userIcon);
      }
  
      entryCopied.querySelector('[data-role="entryCreatedAtArea"]').innerText = entry.created_at;

      entryCopied.querySelector('[data-role="entryBodyArea"]').innerText = entry.body;

      if( entry.body_image != ""){
        console.log(entry.body_image);
        const imageElement = new Image();
        imageElement.src = entry.body_image;
        imageElement.style.display = 'block';
        imageElement.style.marginTop = '1em';
        imageElement.style.maxHeight = '300px';
        imageElement.style.maxWidth = '300px';
        entryCopied.querySelector('[data-role="entryBodyArea"]').appendChild(imageElement);
      }
      entryRenderArea.appendChild(entryCopied);

    });
  }
  request.open('GET', '/timeline_json.php', true);
  request.responseType = 'json';
  request.send();

  
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

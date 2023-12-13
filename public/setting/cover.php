<?php

session_start();

if (empty($_SESSION['login_user_id'])) {
   header("HTTP/1.1 302 Found");
   header("Location: ../login.php");
   return;
}

//dbに接続
$dbh = new PDO('mysql:host=mysql;dbname=techc', 'root', '');
// セッションにあるログインIDから、ログインしている対象の会員情報を引く
$select_sth = $dbh->prepare("SELECT * FROM users WHERE id = :id");
$select_sth->execute([
    ':id' => $_SESSION['login_user_id'],
]);
$user = $select_sth->fetch();

if (isset($_POST['image_base64'])){
  $image_filename = null;
  $base64 = preg_replace('/^data:.+base64,/', '', $_POST['image_base64']);
  
  //デコード
  $image_binary = base64_decode($base64);

  // filenameを決めて、fileを保存
  $image_filename = strval(time()) . bin2hex(random_bytes(25)) . '.png';
  $filepath = '/var/www/upload/image/' . $image_filename;
  file_put_contents($filepath, $image_binary);

  // DBにfilenameを登録
  $update_sth = $dbh->prepare('UPDATE users SET cover_filename = :cover_filename WHERE id = :id;');
  $update_sth->execute([
    ':cover_filename' => $image_filename,
    ':id' => $user['id'],
  ]);

  header('HTTP/1.1 302 Found');
  header('Location: ./cover.php');
  return;
}
?>


<h1>カバー画像設定</h1>

<div>
  <?php if( empty($user['cover_filename']) ): ?>
  現在未設定
  <?php else: ?>
  <img src="/image/<?= $user['cover_filename'] ?>"
      style="width: 100%; height:100px; object-fit: cover;">
  <?php endif; ?>
</div>

<form method="POST">
  <input type="file" accept="image/*" name="image" id="imageInput">
  <input type="hidden" name="image_base64" id="imageBase64Input">
  <canvas id="imageCanvas" style="display: none;"></canvas>
  <button type="submit">アップロード</button>
</form>

<hr>
<a href="./index.php">プロフィールに戻る</a>

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

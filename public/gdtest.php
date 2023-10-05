<?php

if ($_GET){  
  $red = $_GET['red'] ?? 0;
  $green = $_GET['green'] ?? 0;
  $blue = $_GET['blue'] ?? 0;

  $img = imagecreatetruecolor(500,500);
  $color = imagecolorallocate($img, $red, $green, $blue);
  imagefilltoborder($img,300,300, $color,$color); 

  header('Content-type: image/png');
  imagepng($img);


  return;
}
 ?>
 <form>
    赤：<input type="number" min="0" max="255" name="red" value=0><br>
    緑：<input type="number" min="0" max="255" name="green" value=0><br>
    青：<input type="number" min="0" max="255" name="blue" value=0><br>
    <button type="submit">決定</button>
</form>
<br><a href="index.html">indexへ</a>

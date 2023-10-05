<?php
echo '<image src="images/penguin.jpg" width="100"><br>';
echo '<p>この画像のexif情報:';

$data = "";
$datum = exif_read_data('images/penguin.jpg');
echo '<pre>';
var_dump($datum);
echo '</pre>';
echo '<br><a href="./index.html">indexへ</a>';

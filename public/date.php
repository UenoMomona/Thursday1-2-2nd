<?php
print('<p>現在の日時は</p>');
$now = new DateTime('Asia/Tokyo');
print( $now->format('Y-m-d H:i:s'));
/*
echo gettype(intval($now->format('H')));
echo $now->format('s');
echo intval($now->format('s') * 6);
echo "<br>";
echo -100 * sin(deg2rad(floatval($now->format('s'))*6.0)) + 150;
echo "<br>";
echo 100 * cos(deg2rad(floatval($now->format('s'))*6.0)) + 150;
*/
?>
<svg width="300" height="300" viewBox="0 0 300 300">
    <circle cx="150" cy="150" r="100" fill="#fff" stroke="#000" />
    <line x1="150" y1="150" x2="<?php echo 90 * sin(deg2rad((floatval($now->format('s'))* 6.0)-360.0)) + 150; ?>" y2="<?php echo -90 * cos(deg2rad((floatval($now->format('s'))* 6.0)-360.0)) + 150 ?>" stroke="#000" />
    <line x1="150" y1="150" x2="<?php echo 80 * sin(deg2rad((floatval($now->format('i'))* 6.0)-360.0)) + 150; ?>" y2="<?php echo -80 * cos(deg2rad((floatval($now->format('i'))* 6.0)-360.0)) + 150 ?>" stroke="#000" stroke_width="10" />
    <line x1="150" y1="150" x2="<?php echo 70 * sin(deg2rad((floatval($now->format('H'))* 30.0)+(floatval($now->format('i'))*(1/2))-360.0)) + 150; ?>" y2="<?php echo -70 * cos(deg2rad((floatval($now->format('H'))* 30.0)+(floatval($now->format('i'))*(1/2))-360.0)) + 150 ?>" stroke="#000" stroke_width="15" />
</svg>

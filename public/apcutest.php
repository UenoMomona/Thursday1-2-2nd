<?php
if(apcu_fetch('num')){
  $num = apcu_inc('num',1);
}else{
  apcu_store('num',1);
  $num = 1;
}
echo "あなたは" . $num . "人目の訪問者です";

echo '<a href="./index.html">indexへ</a>';

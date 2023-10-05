<?php

if (isset($_POST['body'])){

  print("入力を受け取りました<br>");

  print(htmlspecialchars($_POST["body"]));

  }

  ?>

  <form method="POST" action="./formtest.php">
    <textarea name="body"></textarea><br>
    <button type="submit">送信</button>
  </form>

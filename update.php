<?php
session_start();
require("library.php");

if (isset($_SESSION["id"]) && isset($_SESSION["name"])) {
  $id = $_SESSION["id"];
  $name = $_SESSION["name"];
  // $count = $_SESSION["count"];
} else {
  header("Location: login.php");
  exit();
}

$post_id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);
if (!$post_id) {
  header("Location: index.php");
  exit();
}

$db = dbconnect();

//メッセージの編集
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $editmessage = filter_input(INPUT_POST, "editmessage", FILTER_SANITIZE_STRING);

  $stmt = $db->prepare("update posts set message=?, edited=1 where id=? and member_id=? limit 1");
  if (!$stmt) {
    die($db->error);
  }

  $stmt->bindParam(1, $editmessage, PDO::PARAM_STR);
  $stmt->bindParam(2, $post_id, PDO::PARAM_INT);
  $stmt->bindParam(3, $id, PDO::PARAM_INT);

  $success = $stmt->execute();
  if (!$success) {
    die($db->error);
  }

  header("Location: index.php");
  exit();
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>ひとこと掲示板</title>

  <link rel="stylesheet" href="css/index.css" />
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1>ひとこと掲示板</h1>
    </div>
    <div id="content">
      <div style="text-align: right"><a href="index.php">一覧に戻る</a></div>
      <form action="" method="post">
        <dl>
          <dt>メッセージを編集してください</dt>
          <dd>
            <textarea name="editmessage" cols="50" rows="5"></textarea>
          </dd>
        </dl>
        <div>
          <p>
            <input type="submit" value="編集完了" />
          </p>
        </div>
      </form>
    </div>
  </div>
</body>

</html>
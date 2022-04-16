<?php
session_start();
require("library.php");

if (isset($_SESSION["id"]) && isset($_SESSION["name"])) {
  $id = $_SESSION["id"];
  $name = $_SESSION["name"];
} else {
  header("Location: login.php");
  exit();
}

$db = dbconnect();

//メッセージの投稿
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $message = filter_input(INPUT_POST, "message", FILTER_SANITIZE_STRING);

  $stmt = $db->prepare("insert into posts (message, member_id) values (?, ?)");
  if (!$stmt) {
    die($db->error);
  }

  $stmt->bindParam(1, $message, PDO::PARAM_STR);
  $stmt->bindParam(2, $id, PDO::PARAM_INT);

  $success = $stmt->execute();
  if (!$success) {
    die($db->error);
  }

  header("Location: index.php");
  exit();
}

$id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);
if (!$id) {
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
  <link rel="stylesheet" href="css/view.css" />
  <title>ひとこと掲示板</title>

</head>

<body>
  <div id="wrap">
    <div id="wrap">
      <div id="head">
        <h1>ひとこと掲示板</h1>
      </div>
      <div id="content">
        <div style="text-align: right"><a href="index.php">一覧に戻る</a></div>
        <form action="" method="post">

        </form>
        <?php
        $stmt = $db->prepare("select p.id, p.member_id, p.message, p.created, m.name, m.image 
                              from   posts p, members m 
                              where  p.id=?
                              And    m.id=p.member_id
                              order by id desc");

        if (!$stmt) {
          die($db->error);
        }

        $stmt->bindParam(1, $id, PDO::PARAM_INT);

        $success = $stmt->execute();
        if (!$success) {
          die($db->error);
        }

        // while($stmt->fetch()):

        if ($result = $stmt->fetch(PDO::FETCH_ASSOC)) :

        ?>

          <div class="msg">
            <?php if ($result["image"]) : ?>
              <img id="prof" src="member_picture/<?php echo $result["image"] ?>" width="150" height="150" alt="" />
            <?php else : ?>
              <img src="member_picture/freeaicon.jpg" width="150" height="150" alt="" />
            <?php endif ?>
            <p><?php echo h($result["message"]) ?><span class="name">（<?php echo h($result["name"]) ?>）</span></p>
            <p class="day"><a href="view.php?id="><?php echo h($result["created"]) ?></a>
              <?php if ($_SESSION["id"] === $result["member_id"]) : ?>
                [<a href="update.php?id=<?php echo $result["id"] ?>" style="color: #0000FF;">編集</a>]
                [<a href="delete.php?id=<?php echo $result["id"] ?>" style="color: #F33;">削除</a>]
              <?php endif ?>
            </p>
          </div>
        <?php else : ?>
          <p>その投稿は削除されたか、URLが間違えています</p>
        <?php endif ?>
      </div>
    </div>
</body>

</html>
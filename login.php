<?php
session_start();
require("library.php");

$error = [];
$email = "";
$password = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
  $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);
  // var_dump($email);
  // var_dump($password);
  if ($email === "" || $password === "") {
    $error["login"] = "blank";
  } else {
    //ログインチェック
    $db = dbconnect();

    $stmt = $db->prepare("select id, name, password from members where email=? limit 1");

    if (!$stmt) {
      die($db->error);
    }

    $stmt->bindParam(1, $email, PDO::PARAM_STR);


    $success = $stmt->execute();
    if (!$success) {
      die($db->error);
    }

    $result = $stmt->fetch();

    $id = $result["id"];
    $name = $result["name"];
    $hash = $result["password"];

    if (password_verify($password, $hash)) {
      //ログイン成功
      session_regenerate_id();
      $_SESSION["id"] = $id;
      $_SESSION["name"] = $name;
      header("Location: index.php");
      exit();
      
    } else {
      //ログイン失敗
      $error["login"] = "failed";
    }

    // $result = $stmt->fetch(PDO::FETCH_LAZY);

    // while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
    //   $id = $result["id"];
    //   $name = $result["name"];
    //   $hash = $result["hash"];
    // }

    // $pref_names = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    // $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    // $stmt->bindValue(':hash', $hash, PDO::PARAM_STR);

    // for ($i = 0; $i < 3; $i++) {
    //   $loginstatus = $stmt->fetchColumn();
    // }

    // var_dump($loginstatus);

    // $stmt->bindColumn('id', $id);
    // $stmt->bindColumn(1, $id);
    // $email = $stmt->fetchColumn(1);
    // $hash = $stmt->fetchColumn(2);

    // while ($stmt->fetch(PDO::FETCH_BOUND)) {
    // }

    // $id = $stmt->fetchColumn();
    // $name = $stmt->fetchColumn();
    // $hash = $stmt->fetchColumn();


    // var_dump($id);
    // var_dump($name);
    // var_dump($hash);
  }
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://unpkg.com/sanitize.css" rel="stylesheet">
  <link rel="stylesheet" href="css/login.css">
  <title>ログイン</title>
</head>

<body>
  <div class="form-wrapper">
    <h1>Sign In</h1>
    <form action="" method="POST">
      <div class="form-item">
        <label for="email"></label>
        <input type="email" name="email" size="35" maxlength="255" placeholder="Email Address" value="<?php echo h($email) ?>"></input>
        <?php if (isset($error["login"]) && $error["login"] === "blank") : ?>
          <p class="error">メールアドレスとパスワードを正しくご記入ください</p>
        <?php elseif (isset($error["login"]) && $error["login"] === "failed") : ?>
          <p class="error">ログインに失敗しました。メールアドレスとパスワードを正しく入力してください。</p>
        <?php endif ?>
      </div>
      <div class="form-item">
        <label for="password"></label>
        <input type="password" name="password" size="35" maxlength="255" placeholder="Password" value="<?php echo h($password) ?>"></input>
      </div>
      <div class="button-panel">
        <input type="submit" class="button" title="Sign In" value="Sign In"></input>
      </div>
    </form>
    <div class="form-footer">
      <p><a href="join/index.php">Create an account</a></p>
      <p><a href="#">Forgot password?</a></p>
    </div>
  </div>
</body>

</html>
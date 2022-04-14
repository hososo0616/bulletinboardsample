<?php
    //セッションスタート
    session_start();

    //インポート
    require("../library.php");

    //   var_dump($_SESSION["form"]);

    //フォームの内容があるかどうかの判定
    if (isset($_SESSION["form"])) {
        $form = $_SESSION["form"];
    }
    else {
        header("Location: index.php");
        exit();
    }
    // var_dump($form["image"]);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        
        $db = dbconnect();

        $stmt = $db->prepare("insert into members (name, email, password, image) VALUES (?, ?, ?, ?)");
        //stmtがうまくいかないとき
        if (!$stmt) {
            die($db->error);
        }

        //パスワードのハッシュ化
        $password = password_hash($form["password"], PASSWORD_DEFAULT);

        //値の設置
        // $stmt->bindParam($form["name"], $form["email"], $form["password"], $form["image"]);
        $stmt->bindParam(1, $form["name"], PDO::PARAM_STR); 
        $stmt->bindParam(2, $form["email"], PDO::PARAM_STR); 
        $stmt->bindParam(3, $password, PDO::PARAM_STR); 
        $stmt->bindParam(4, $form["image"], PDO::PARAM_STR); 
        $success = $stmt->execute();
        //successがうまくいかないとき
        if (!$success) {
            die($db->error);
        }

        //セッションの内容を消す
        unset($_SESSION["form"]);

        header("Location:thank.php");
    } 

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
    <title>確認画面</title>
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <link href="https://unpkg.com/sanitize.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="content">
        <form action="" method="POST">
            <input type="hidden" name="check" value="checked">
            <h1>入力情報の確認</h1>
            <p>ご入力情報に変更が必要な場合、下のボタンを押し、変更を行ってください。</p>
            <p>登録情報はあとから変更することもできます。</p>
            <!-- <?php if (!empty($error) && $error === "error"): ?>
                <p class="error">＊会員登録に失敗しました。</p>
            <?php endif ?> -->
            <hr>
 
            <div class="control">
                <p>ニックネーム</p>
                <p><span class="fas fa-angle-double-right"></span> <span class="check-info"><?php echo h($form["name"]); ?></span></p>
            </div>
 
            <div class="control">
                <p>メールアドレス</p>
                <p><span class="fas fa-angle-double-right"></span> <span class="check-info"><?php echo h($form['email']); ?></span></p>
            </div>

            <div class="control">
                <p>パスワード</p>
                <p>【表示されません】</p>
                <!-- <p><span class="fas fa-angle-double-right"></span> <span class="check-info"><?php echo h($form['password']); ?></span></p> -->
            </div>

            <div class="control">
                <p>プロフィール画像</p>
                <img src="../member_picture/<?php echo  h($form["image"]) ?>" alt="画像は指定されていません" width="100">
            </div>
            
            <br>
            <a href="index.php?action=rewrite" class="back-btn">変更する</a>
            <button type="submit" class="btn next-btn">登録する</button>
            <div class="clear"></div>
        </form>
    </div>
</body>
</html>
<?php
    //セッションスタート
    session_start();

    //インポート
    require("../library.php");

    //書き直しかどうかの判定
    if (isset($_GET["action"]) && $_GET["action"] === "rewrite" && isset($_SESSION["form"])) {
        $form = $_SESSION["form"];
    }
    else {
        //書き直しでなかった場合
        //配列の初期化
        $form = [
            "name" => "",
            "email" => "",
            "password" => ""
        ];
    }

    $error = [];
    // $image = [];
 
    //フォームが送信されているか
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        //ニックネームの取得
        $form["name"] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

        //空かどうかの判定
        if ($form['name'] === '') {
            $error["name"] = "blank";
        }

        //メールアドレスの取得
        $form["email"] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        //空かどうかの判定
        if ($form['email'] === '') {
            $error["email"] = "blank";
        }
        else {
            //重複していないかの判定
            $db = dbconnect();
            $stmt = $db->prepare("select count(*) from members where email=?");

            if (!$stmt) {
                die($db->error);
            }

            $stmt->bindParam(1, $form["email"], PDO::PARAM_STR); 

            //SQLの結果の格納
            // $stmt->bindColumn();

            $success = $stmt->execute();
            if (!$success) {
                die($db->error);
            }

            $cnt = $stmt->fetchColumn();

            // var_dump($cnt);

            if ($cnt > 0) {
                $error["email"] = "duplicate";
            }
        }

        //パスワードの取得
        $form["password"] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

        //空かどうかと文字数の判定
        if ($form['password'] === '') {
            $error["password"] = "blank";
        }
        else if (strlen($form["password"]) < 4){
            $error["password"] = "length";
        }

        //画像チェック
        $image = $_FILES["image"];
        if ($image["name"] !== "" && $image["error"] === 0) {
            $type = mime_content_type($image["tmp_name"]);
            if ($type !== "image/png" && $type !== "image/jpeg") {
                $error["image"] = "type";
            }
        }

        //エラーなしでする処理
        if (empty($error)) {
            $_SESSION["form"] = $form;

            //画像のアップロード
            if ($image["name"] !== "") {
                $filename = date("YmdHis") . "_" . $image["name"];
                if (!move_uploaded_file($image["tmp_name"], "../member_picture/" . $filename)){
                    die("ファイルのアップロードに失敗しました");
                }
                $_SESSION["form"]["image"] = $filename;
            }
            else {
                $_SESSION["form"]["image"] = "";
            }

            header("Location: check.php");
            exit();
        }
    }

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
    <title>アカウント作成</title>
    <link href="https://unpkg.com/sanitize.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="content">
        <form action="" method="POST" enctype="multipart/form-data">
            <h1>アカウント作成</h1>
            <p>当サービスをご利用するために、次のフォームに必要事項をご記入ください。</p>
            <br>
 
            <div class="control">
                <label for="name">ユーザー名<span class="required">必須</span></label>
                <input id="name" type="text" name="name" size="35" maxlength="255" value="<?php echo h($form['name'])?>">
                <?php if (isset($error["name"]) && $error["name"] === "blank"):?>
                    <p class="error">＊ニックネームを入力してください。</p>
                <?php endif?>
            </div>
 
            <div class="control">
                <label for="email">メールアドレス<span class="required">必須</span></label>
                <input id="email" type="email" name="email" size="35" maxlength="255" value="<?php echo h($form['email'])?>">
                <?php if (!empty($error["email"]) && $error['email'] === 'blank'): ?>
                    <p class="error">＊メールアドレスを入力してください</p>
                <?php elseif (!empty($error["email"]) && $error['email'] === 'duplicate'): ?>
                    <p class="error">＊このメールアドレスはすでに登録済みです</p>
                <?php endif ?>
            </div>
 
            <div class="control">
                <label for="password">パスワード<span class="required">必須</span></label>
                <input id="password" type="password" name="password" size="10" maxlength="20" value="<?php echo h($form['password'])?>">
                <?php if (!empty($error["password"]) && $error['password'] === 'blank'): ?>
                    <p class="error">＊パスワードを入力してください</p>
                <?php elseif (!empty($error["password"]) && $error['password'] === 'length'):?>
                    <p class="error">4文字以上でパスワードは入力してください</p>
                <?php endif ?>
            </div>

            <div class="control">
                <p>イメージ写真</p>
                <input id="image" type="file" name="image">
                <?php if (!empty($error["image"]) && $error['image'] === 'type'): ?>
                    <p class="error">*写真などは「.png」もしくは「.jpg」の画像を指定してください</p>
                    <p class="error">*画像を指定しなおしてください</p>
                <?php endif ?>
            </div>

            <div class="control">
                <button type="submit" class="btn">確認する</button>
            </div>
        </form>
    </div>
</body>
</html>
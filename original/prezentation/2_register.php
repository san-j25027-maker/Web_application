<?php
//データベースに登録するためのフォーム
    require_once("control.php");

    if(isset($_POST["submit"])) {

        $username = $_POST["username"];
        $password = $_POST["password"];

        // パスワードをハッシュ化
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $dbh = db_connect();

        $sql = "INSERT INTO users(username, password) VALUES(?, ?)";
        //SQLの用意
        $stmt = $dbh->prepare($sql);
        //実行準備
        $stmt->bindValue(1, $username, PDO::PARAM_STR);
        $stmt->bindValue(2, $hash, PDO::PARAM_STR);
        //値をセット
        $stmt->execute();
        //実行
        $dbh = null;

        echo "登録完了";
    }   
?>

<form method="post">
ユーザー名：<input type="text" name="username"><br>
パスワード：<input type="password" name="password"><br>
<input type="submit" name="submit" value="登録">
</form>
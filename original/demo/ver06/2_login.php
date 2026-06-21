<?php
session_start();
require_once("control.php");

$message = "";

// ===== 登録処理 =====
if(isset($_POST["register"])) {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if($username === "" || $password === "") {
        $message = "未入力があります";
    } else {

        $dbh = db_connect();

        // 重複チェック
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $username, PDO::PARAM_STR);
        $stmt->execute();

        if($stmt->fetch()) {
            $message = "このユーザー名は既に使われています";
        } else {
            // パスワードをハッシュ化
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users(username, password) VALUES(?, ?)";
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1, $username, PDO::PARAM_STR);
            $stmt->bindValue(2, $hash, PDO::PARAM_STR);
            $stmt->execute();

            $message = "登録完了（そのままログインできます）";
        }

        $dbh = null;
    }
}

// ===== ログイン処理 =====
if(isset($_POST["login"])) {

    $username = $_POST["username"];
    $password = $_POST["password"];

    $dbh = db_connect();

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(1, $username, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($password, $user["password"])) {
        $_SESSION["user"] = $user["username"];
        $_SESSION["user_id"] = $user["id"];

        header("Location: 2_board.php");
        exit;
    } else {
        $message = "ログイン失敗";
    }

    $dbh = null;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf8">
<title>ログイン / 新規登録</title>
</head>

<body>

<h2>ログイン / 新規登録</h2>

<p><?php echo $message; ?></p>

<!-- ログイン -->
<h3>ログイン</h3>
<form method="post">
    ユーザー名：<input type="text" name="username"><br>
    パスワード：<input type="password" name="password"><br>
    <input type="submit" name="login" value="ログイン">
</form>

<hr>

<!-- 新規登録 -->
<h3>新規登録</h3>
<form method="post">
    ユーザー名：<input type="text" name="username"><br>
    パスワード：<input type="password" name="password"><br>
    <input type="submit" name="register" value="登録">
</form>

</body>
</html>
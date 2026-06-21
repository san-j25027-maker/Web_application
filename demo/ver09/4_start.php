<?php
session_start();
require_once "control.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ゲスト
    if (isset($_POST["guest"])) {
        $_SESSION["user"] = "guest";
        header("Location: 4_game.php");
        exit;
    }

    // ログイン（掲示板と同じ方式）
    if (isset($_POST["login"])) {

        $username = $_POST["username"];
        $password = $_POST["password"];

        $dbh = db_connect();

        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $username, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user"] = $user["username"];
            $_SESSION["user_id"] = $user["id"];

            header("Location: 4_game.php");
            exit;
        } else {
            $message = "ログイン失敗";
        }

        $dbh = null;
    }
}
?>

<!DOCTYPE html>

<html lang="ja">
<head>
<meta charset="UTF-8">
<title>タイピングゲーム</title>
</head>

<body>

<h2>タイピングゲーム</h2>

<p style="color:red;"><?php echo $message; ?></p>

<form method="post">
    ユーザー名：<input type="text" name="username"><br>
    パスワード：<input type="password" name="password"><br><br>

```
<button name="login">ログインして開始</button>
<button name="guest">ゲストで開始</button>
```

</form>

</body>
</html>

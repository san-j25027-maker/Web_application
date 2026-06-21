<?php
    //登録受付とログイン用のフォーム
    session_start();
    //ログイン状態保持用
    require_once("control.php");
    $message = "";

    //登録処理
    if(isset($_POST["register"])) {
        $username = trim($_POST["username"]);
        $password = $_POST["password"];
        //エラーチェック１．未入力
        if($username === "" || $password === "") {
            $message = "未入力があります";
        } else {

            $dbh = db_connect();

            //エラーチェック用処理
            $sql = "SELECT id FROM users WHERE username = ?";
            //データベースに同じ名前があるか
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1, $username, PDO::PARAM_STR);
            $stmt->execute();
            //エラーチェック２．重複
            if($stmt->fetch()) {
                $message = "このユーザー名は既に使われています";
            } else {
                // エラーなし
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

    //ログイン処理
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            text-align: center;
        }

        h2 {
            margin-top: 20px;
            color: #000000;
        }

        h3 {
            margin-top: 30px;
            color: #333;
        }

        form {
            margin: 10px auto;
        }

        input[type="text"],
        input[type="password"] {
            width: 200px;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"],
        button {
            padding: 8px 15px;
            border: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover,
        button:hover {
            background-color: #45a049;
        }

        .menu {
            text-align: right;
            margin-right: 60px;
            margin-bottom: 20px;
        }

        p {
            color: red;
            font-weight: bold;
        }
    </style>
        <form action="0_home.php">
            <div class="menu">
                <button>メニューに戻る</button>
            </div>
        </form>
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
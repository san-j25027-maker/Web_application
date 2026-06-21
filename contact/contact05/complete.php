<?php
    session_start();
    if(isset($_POST["token"], $_SESSION["token"]) && ($_POST["token"] === $_SESSION["token"])){
        unset($_SESSION["token"]);
        //echo "きちんとしたアクセスです";
        //mysqlを使う
        $dsn = "mysql:dbname=contact_form;host=localhost;charset=utf8";
        $user = "root";
        $password = "";
        $dbh = new PDO($dsn,$user,$password);
        //$_SESSIONから各値を取得
        $name = $_SESSION["name"];
        $email = $_SESSION["email"];
        $subject = $_SESSION["subject"];
        $body = $_SESSION["body"];

        //PDOクラスのメソッドを呼び出す
        $dbh -> query("SET NAMES utf8");
        $dbh -> setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
        $sql = "INSERT INTO inquiries(name,email,subject,body) VALUES (?,?,?,?)";
        $stmt = $dbh -> prepare($sql);
        //?に値を入れていく
        $stmt -> bindValue(1,$name,PDO::PARAM_STR);
        $stmt -> bindValue(2,$email,PDO::PARAM_STR);
        $stmt -> bindValue(3,$subject,PDO::PARAM_STR);
        $stmt -> bindValue(4,$body,PDO::PARAM_STR);
        //実行
        $stmt -> execute();
        var_dump($dbh);

        $dbh = null;
        $_SESSION = array();
        if(ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(),"",time()-42000,$params["domain"],$params["secure"],$params["httponly"]);
        }
        session_destroy();

        }else{
            header("Location:http://localhost/contact/contact05/input.php");
            exit();
        }
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>完了画面 ‐ お問い合わせ</title>
    </head>
    <body>
        <p>お問い合わせありがとうございます。</p>
    </body>
    </html>
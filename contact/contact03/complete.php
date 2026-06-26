<?php
    session_start();
    if(isset($_POST["token"], $_SESSION["token"]) && ($_POST["token"] === $_SESSION["token"])){
        unset($_SESSION["token"]);
        echo "きちんとしたアクセスです";
        }else{
            header("Location:http://localhost/contact03/input.php");
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
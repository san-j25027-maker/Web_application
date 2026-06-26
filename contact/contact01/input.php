<?php
    //フォームの内容を受け取り、空だった場合にエラーを表示させる
    //送信後、入力内容を消えないようにする
    //セレクトボックスも同様にする
    session_start();
    echo "<pre>";
    //$_POSTはスーパーグローバル変数
    //最初から用意されており、どこからでも参照可能
    //フォームのpostで送られてきたものがこの連想配列の形式で格納される
    //var_dump($_POST);
    echo "</pre>";
   
    $errors = array();


    if(isset($_POST["submit"])){
        $name = $_POST["name"];
        $email = $_POST["email"];
        $subject = $_POST["subject"];
        $body = $_POST["body"];

        //エスケープ処理をする
        $name = htmlspecialchars($name, ENT_QUOTES);
        $email = htmlspecialchars($email, ENT_QUOTES);
        $subject = htmlspecialchars($subject, ENT_QUOTES);
        $body = htmlspecialchars($body, ENT_QUOTES);

        if($name === ""){
            $errors["name"]="お名前が入力されていません。";
        }
        if($email === ""){
            $errors["email"]="emailが入力されていません。";
        }
        if($subject === ""){
            $errors["subject"]="お問い合わせ内容が入力されていません。";
        }
        if($body === ""){
            $errors["body"]="お名前が入力されていません。";
        }
        //error taiou
        if (count($errors) === 0){
            $_SESSION["name"]=$name;
            $_SESSION["email"]=$email;
            $_SESSION["subject"]=$subject;
            $_SESSION["body"]=$body;
            header("Location:http://localhost/contact/contact01/confrimation.php");
            exit();
        }
       
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>お問い合わせ</title>
</head>
<body>
    <form action="" method="post">//ここに記述があるとTHHPが処理をする
        <table>
            <tr>
                <th>お名前</th>
                <td><input type="text" name="name" value="<?php if(isset($name)){echo $name;} ?>"></td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td><input type="text" name="email" value="<?php if(isset($email)){echo $email;} ?>"></td>
            </tr>
            <tr>
                <th>お問い合わせの種類</th>
                <td>
                    <select name="subject">
                        <option value ="お仕事に対するお問い合わせ" <?php if(isset($subject) && $subject === "お仕事に対するお問い合わせ"){echo "selected";} ?>>
                            お仕事に対するお問い合わせ
                        </option>
                        <option value="その他のお問い合わせ" <?php if(isset($subject) && $subject === "その他のお問い合わせ"){echo "selected";}?>>
                            その他のお問い合わせ
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>お問い合わせ内容</th>
                <td><textarea name="body" cols="40" rows="10"><?php if(isset($body)){echo $body;} ?></textarea></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" name="submit" value="確認画面へ"></td>
            </tr>
        </table>
    </form>
</body>
</html>
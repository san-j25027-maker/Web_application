<?php
//セッションの再開
//すーばーグローバル変数の#_SESSIONが使えるようになる
session_start();
//セッションにnameが保存されていれば$_SESSIONの中身をすべて表示する
if(isset($_SESSION["name"])){
    $name = $_SESSION["name"];
    $email = $_SESSION["email"];//０１では、改行が改行コードとして出力される
    $subject = $_SESSION["subject"];
    $body = $_SESSION["body"];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>確認画面ーお問い合わせ</title>
</head>
<body>
    <form action="form20.php" method="post">
        <table>
            <tr>
                <th>お名前</th>
                <!-- 取得した$nameを表示する -->
                <td><?php echo $name; ?></td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td><?php echo $email; ?></td>
            </tr>
            <tr>
                <th>お問い合わせの種類</th>
                <td><?php echo $subject; ?></td>
            </tr>
            <tr>
                <th>お問い合わせ内容</th>
                <td><?php echo nl2br($body); ?></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" name="submit" value="送信する"></td>
            </tr>
        </table>
    </form>
</body>
</html>

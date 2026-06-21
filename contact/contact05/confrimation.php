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

    //ランダムな文字列を生成、
    //openssl_random_pseudo_bytes : 指定したバイト数分のランダムな01(バイナリデータ)を出力
    //base64_encode　：　バイナリデータを文字列に変換
    $_SESSION["token"] = base64_encode(openssl_random_pseudo_bytes(48));
    //エスケープ処理
    $token = htmlspecialchars($_SESSION["token"], ENT_QUOTES);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>確認画面ーお問い合わせ</title>
</head>
<body>
    <form action="complete.php" method="post">
<!--PHPで草制したtokenをhidden属性でフォームに埋め込む -->
        <input type="hidden" name = "token" value="<?php echo $token ?>">
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
        <p><a href="input.php?action=edit">入力画面へ戻る</p>
    </form>
</body>
</html>

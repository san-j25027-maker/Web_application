<?php
session_start();
require_once("control.php");

// ログアウト処理
if(isset($_POST["logout"])) {

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]);
    }

    session_destroy();

    header("Location: 2_login.php");
    exit;
}

// ログインチェック
if(!isset($_SESSION["user"])) {
    echo "ログインしてください";
    exit;
}

$dbh = db_connect();

// -----------------
// 削除処理（自分のみ）
// -----------------
if(isset($_POST["delete"])) {

    $id = $_POST["delete_id"];
    $name = $_SESSION["user"];

    $sql = "DELETE FROM board WHERE id = ? AND name = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$id, $name]);
}

// -----------------
// anything追加処理
// -----------------
if(isset($_POST["add_anything"])) {

    $content = $_POST["content"];

    $sql = "INSERT INTO anything(content, flag) VALUES(?, 1)";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$content]);
}

// -----------------
// 投稿処理（常にboardへ）
// -----------------
if(isset($_POST["submit"])) {

    $name = $_SESSION["user"];
    $message = htmlspecialchars($_POST["message"], ENT_QUOTES);

    $sql = "INSERT INTO board(name, message) VALUES(?, ?)";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$name, $message]);
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf8">
    <title>掲示板</title>
    <link rel="stylesheet" href="2_style.css?v=<?php echo time(); ?>">
</head>

<body>

<!-- 上部 -->
<div class="header">
    <h2>掲示板</h2>
    <p>ユーザー：<?php echo htmlspecialchars($_SESSION["user"], ENT_QUOTES); ?></p>

    <form method="post">
        <input type="submit" name="logout" value="ログアウト">
    </form>
</div>

<!-- 投稿一覧 -->
<div class="messages">

<?php
$sql = "SELECT * FROM board ORDER BY id";
$stmt = $dbh->query($sql);

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    echo "<div class='message'>";

    echo "<b>" . htmlspecialchars($row["name"], ENT_QUOTES) . "</b><br>";
    echo nl2br(htmlspecialchars($row["message"], ENT_QUOTES));

    // ボタン群
    echo "<br>";

    // 自分の投稿だけ削除
    if($row["name"] === $_SESSION["user"]) {
        echo "
        <div class='button'>
        <form method='post' style='display:inline;'>
            <input type='hidden' name='delete_id' value='". $row["id"] ."'>
            <input type='submit' name='delete' value='削除'>
        </form>
        </div>
        ";
    }

    // anything追加ボタン（全員OKでもOK）
    echo "
    <div class='button'>
    <form method='post' style='display:inline;'>
        <input type='hidden' name='content' value='". htmlspecialchars($row["message"], ENT_QUOTES) ."'>
        <input type='submit' name='add_anything' value='anything追加'>
    </form>
    </div>
    ";

    echo "</div>";
}
?>

</div>

<!-- 入力 -->
<div class="input-area">
    <form method="post">
        <textarea name="message" required></textarea>
        <input type="submit" name="submit" value="送信">
    </form>
</div>

</body>
</html>
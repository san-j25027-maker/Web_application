<?php
session_start();
require_once("control.php");

// 🔴 ログアウト処理
if(isset($_POST["logout"])) {

    // セッション削除
    $_SESSION = [];

    // クッキー削除
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();

    // ログイン画面へ
    header("Location: 2_login.php");
    exit;
}

// ログインチェック
if(!isset($_SESSION["user"])) {
    echo "ログインしてください";
    exit;
}

// 投稿処理
if(isset($_POST["submit"])) {

    $name = $_SESSION["user"];
    $message = htmlspecialchars($_POST["message"], ENT_QUOTES);

    $dbh = db_connect();

    $sql = "INSERT INTO board(name, message) VALUES(?, ?)";
    $stmt = $dbh->prepare($sql);

    $stmt->bindValue(1, $name, PDO::PARAM_STR);
    $stmt->bindValue(2, $message, PDO::PARAM_STR);

    $stmt->execute();

    $dbh = null;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf8">
<title>掲示板</title>
</head>

<body>

<h2>掲示板</h2>

<p>ログインユーザー：<?php echo htmlspecialchars($_SESSION["user"], ENT_QUOTES); ?></p>

<!-- 🔴 ログアウトボタン -->
<form action="2_board.php" method="post">
    <input type="submit" name="logout" value="ログアウト">
</form>

<hr>

<!-- 投稿フォーム -->
<form action="2_board.php" method="post">
    内容：<br>
    <textarea name="message" rows="4" cols="50"></textarea><br>
    <input type="submit" name="submit" value="投稿">
</form>

<hr>

<!-- 投稿一覧 -->
<ul>

<?php
$dbh = db_connect();

$sql = "
SELECT b.*, ba.image 
FROM board b
LEFT JOIN users u ON b.name = u.username
LEFT JOIN user_badges ub ON u.id = ub.user_id
LEFT JOIN badges ba ON ub.badge_id = ba.id
ORDER BY b.id DESC
";

$stmt = $dbh->prepare($sql);
$stmt->execute();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print "<li>";

    print "<b>" . htmlspecialchars($row["name"], ENT_QUOTES) . "</b> ";

    if(!empty($row["image"])) {
        print "<img src='0_image/" . htmlspecialchars($row["image"], ENT_QUOTES) . "' width='50'>";
    }

    print "<br>";
    print nl2br(htmlspecialchars($row["message"], ENT_QUOTES)) . "<br>";

    if(isset($row["created_at"])) {
        print "<small>" . $row["created_at"] . "</small>";
    }

    print "</li><hr>";
}

$dbh = null;
?>

</ul>

</body>
</html>
<?php
    session_start();
    require_once("control.php");

    //  ログアウト処理
    if(isset($_POST["logout"])) {

        // セッション削除
        $_SESSION = [];

        // クッキー削除
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]);
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
        <link rel="stylesheet" href="2_style.css?v=<?php echo time(); ?>">
    </head>

    <body>
        <!--上部固定-->
        <div class="header">
            <h2>掲示板</h2>
            <p>ユーザー：<?php echo htmlspecialchars($_SESSION["user"], ENT_QUOTES); ?></p>

            <form method="post">
                <input type="submit" name="logout" value="ログアウト">
            </form>
        </div>

        <div class="messages">

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
                print "<div class='message'>";

                print "<b>" . htmlspecialchars($row["name"], ENT_QUOTES) . "</b>";

                if(!empty($row["image"])) {
                    print "<img src='0_image/" . htmlspecialchars($row["image"], ENT_QUOTES) . "' width='40'>";
                }

                print "<br>";
                print nl2br(htmlspecialchars($row["message"], ENT_QUOTES));

                print "</div>";
            }

            $dbh = null;
            ?>

        </div>

        <!-- 下部固定 -->
        <div class="input-area">
            <form method="post">
                <textarea name="message"></textarea>
                <input type="submit" name="submit" value="送信">
            </form>
        </div>

    </body>
</html>
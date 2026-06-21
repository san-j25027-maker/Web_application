<?php
    //外部のPHPファイルを呼び出す
    require_once("control.php");

if (isset($_POST["submit"])) {

    $name = htmlspecialchars($_POST["name"], ENT_QUOTES);
    $company = htmlspecialchars($_POST["company"], ENT_QUOTES);
    $bitter = (int)$_POST["bitter"];
    $sweet = (int)$_POST["sweet"];
    $info = htmlspecialchars($_POST["info"], ENT_QUOTES);

    $dbh = db_connect();

    $sql = "INSERT INTO instant(name, company, bitter, sweet, info, flag)
            VALUES(?,?,?,?,?,0)";

    $stmt = $dbh->prepare($sql);

    $stmt->bindValue(1, $name, PDO::PARAM_STR);
    $stmt->bindValue(2, $company, PDO::PARAM_STR);
    $stmt->bindValue(3, $bitter, PDO::PARAM_INT);
    $stmt->bindValue(4, $sweet, PDO::PARAM_INT);
    $stmt->bindValue(5, $info, PDO::PARAM_STR);

    $stmt->execute();

    $dbh = null;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf8">
        <title>更新ページ</title>
    </head>
    <body>
        <h3>インスタント追加</h3>

        <form action="instant.php" method="post">
            <li>名称：<input type="text" name="name"></li>
            <li>会社：<input type="text" name="company"></li>

            <li>苦み：<input type="number" name="bitter" min="1" max="5"></li>
            <li>甘み：<input type="number" name="sweet" min="1" max="5"></li>

            <li>特徴：<textarea name="info" rows="3" cols="70"></textarea></li>

            <li><input type="submit" name="submit" value="追加"></li>
        </form>
        
        <h3>インスタント一覧</h3>
        <ul>
            <?php
                $dbh = db_connect();

                $sql = "SELECT * FROM instant ORDER BY id DESC";
                $stmt = $dbh->prepare($sql);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    echo "<li>";
                    echo "名前：" . $row["name"] . "<br>";
                    echo "会社：" . $row["company"] . "<br>";
                    echo "苦み：" . $row["bitter"] . " / 甘み：" . $row["sweet"] . "<br>";
                    echo "特徴：" . $row["info"];
                    echo "</li>";
                }

                $dbh = null;
                ?>
            </ul>
    </body>
</html>
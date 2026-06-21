<?php
    require_once("control.php");
    //選択されたものを引き継ぎ
    $select = $_POST['searchText'];

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>また今度ね</title>
    </head>
    <body>
    <ul>
            <?php
                //データベースへの接続
                $dbh = db_connect();
                //中身を取得
                $sql = "SELECT bbbbbbbb FROM aaaaaa WHERE name === $select";
                $stmt = $dbh->prepare($sql);
                //SQLを実行
                $stmt->execute();
                $dbh = null;
                //連想配列の形で1行ずつ取り出す
                while($aaaaaa = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    print "<li>";
                    print $task["taskname"];
                    print "<form action='index.php' method='post'>";
                    print "<input type='hidden' name='method' value='put'>";
                    print "<input type='hidden' name='id' value='".$task["id"]."'>";
                    print "</form>";
                    print "</li>";
                }
            ?>
        </ul>
    </body>
</html>
<?php
    //外部のPHPファイルを呼び出す
    function db_connect() {
        //try-catch文
        try{
            $dsn = "mysql:dbname=coffee;host=localhost;charset=utf8";
            $user = "root";
            $password = "";
            $dbh = new PDO($dsn,$user,$password);
            $dbh -> query("SET NAMES utf8");
            $dbh -> setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
            return $dbh;
        }catch(PDOException $e) {
            print "エラー:".$e->getMessage()."<br>";
            exit();
        }
    }
    if(isset($_POST["submit"])) {
        //POSTしたデータを取得する
        $name = $_POST["beansname"];
        $bitter = $_POST["bitter"];
        $acidity = $_POST["acidity"];
        $body = $_POST["body"];
        $roast = $_POST["roast"];
        $info = $_POST["info"];

        //エスケープ処理　文字
        $name = htmlspecialchars($name, ENT_QUOTES);
        $roast = htmlspecialchars($roast, ENT_QUOTES);
        $info = htmlspecialchars($info, ENT_QUOTES);
        //数値
        $bitter = $_POST["bitter"];
        $acidity = $_POST["acidity"];
        $body = $_POST["body"];
        
        $dbh = db_connect();
        //プリペアドステートメントを使ってSQL文を構築
        $sql = "INSERT INTO beans(beansname,bitter,acidity,body,roast,info,flag)
        VALUES(?,?,?,?,?,?,0)";
        $stmt = $dbh -> prepare($sql);

        $stmt->bindValue(1, $name, PDO::PARAM_STR);
        $stmt->bindValue(2, $bitter, PDO::PARAM_INT);
        $stmt->bindValue(3, $acidity, PDO::PARAM_INT);
        $stmt->bindValue(4, $body, PDO::PARAM_INT);
        $stmt->bindValue(5, $roast, PDO::PARAM_STR);
        $stmt->bindValue(6, $info, PDO::PARAM_STR);
        //SQLの実行
        $stmt -> execute();
        //データベース接続情報の削除
        $dbh = null;
        unset($name);
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf8">
        <title>更新ページ</title>
    </head>
    <body>
        <h3>追加</h3>
        <form action="index.php" method="post">
            <li><span>名称</span><input type="text" name="beansname"></li>
            <span>苦み</span><input type="number" name="bitter" min="1" max="5">
            <span>酸味</span><input type="number" name="acidity" min="1" max="5">
            <span>ボディ</span><input type="number" name="body" min="1" max="5">
            <span>煎り具合</span><input type="text" name="roast">
            <li><span>特徴</span><textarea name="info" rows="3" cols="71"></textarea></li>
            <li><input type="submit" name="submit" value="追加"></li>
        </form>
        <ul>
            <?php
                // DB接続
                $dbh = db_connect();

                // beansテーブルを取得
                $sql = "SELECT * FROM beans ORDER BY id DESC";
                $stmt = $dbh->prepare($sql);

                $stmt->execute();

                // 1行ずつ取得
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    print "<li>";
                    print "名前：" . $row["name"] . "<br>";
                    print "苦み：" . $row["bitter"] . " / 酸味：" . $row["acidity"] . " / ボディ：" . $row["body"] . "<br>";
                    print "煎り具合：" . $row["roast"] . "<br>";
                    print "特徴：" . $row["info"];

                    print "</li>";
                }

                $dbh = null;
            ?>
        </ul>
    </body>
</html>
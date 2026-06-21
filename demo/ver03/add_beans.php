<?php
    //外部のPHPファイルを呼び出す
    require_once("control.php");
    if(isset($_POST["submit"])) {
        //POSTしたデータを取得する
        $name = $_POST["name"];
        $bitter = $_POST["bitter"];
        $acidity = $_POST["acidity"];
        $body = $_POST["body"];
        $roast = $_POST["roast"];
        $info = $_POST["info"];

        // 画像アップロード処理
        $filename = null;

        if(isset($_FILES["image"]) && $_FILES["image"]["error"] === 0){
            $upload_dir = "uploads/";
            $filename = uniqid() . "_" . $_FILES["image"]["name"];
            move_uploaded_file($_FILES["image"]["tmp_name"], $upload_dir . $filename);
        }

        //エスケープ処理　文字
        $name = htmlspecialchars($name, ENT_QUOTES);
        $roast = htmlspecialchars($roast, ENT_QUOTES);
        $info = htmlspecialchars($info, ENT_QUOTES);

        $image = htmlspecialchars($_POST["image"], ENT_QUOTES);
        //数値
        $bitter = $_POST["bitter"];
        $acidity = $_POST["acidity"];
        $body = $_POST["body"];
       
        $dbh = db_connect();
        //プリペアドステートメントを使ってSQL文を構築
        $sql = "INSERT INTO beans(name,bitter,acidity,body,roast,info,image,flag)
        VALUES(?,?,?,?,?,?,?,0)";
        $stmt = $dbh -> prepare($sql);

        $stmt->bindValue(1, $name, PDO::PARAM_STR);
        $stmt->bindValue(2, $bitter, PDO::PARAM_INT);
        $stmt->bindValue(3, $acidity, PDO::PARAM_INT);
        $stmt->bindValue(4, $body, PDO::PARAM_INT);
        $stmt->bindValue(5, $roast, PDO::PARAM_STR);
        $stmt->bindValue(6, $info, PDO::PARAM_STR);
        $stmt->bindValue(7, $image, PDO::PARAM_STR);
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
        <form action="add_beans.php" method="post" enctype="multipart/form-data">
            <li><span>名称 : </span><input type="text" name="name"></li>
            <li><span>苦み : </span><input type="number" name="bitter" min="1" max="5"></li>
            <li><span>酸味 : </span><input type="number" name="acidity" min="1" max="5"></li>
            <li><span>コク : </span><input type="number" name="body" min="1" max="5"></li>
            <li><span>煎り具合 : </span><input type="text" name="roast"></li>
            <li><span>特徴 : </span><textarea name="info" rows="3" cols="80"></textarea></li>
            <li><span>画像URL</span><input type="text" name="image"></li>
            <li><input type="submit" name="submit" value="追加"></li>
        </form>

        <h3>コーヒー豆一覧</h3>
        <ul>
            <?php
                // DB接続
                $dbh = db_connect();

                // beansテーブルを取得
                $sql = "SELECT * FROM beans ORDER BY id";
                $stmt = $dbh->prepare($sql);

                $stmt->execute();

                // 1行ずつ取得
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    print "<li>";
                     $star = ($row["flag"] == 1) ? "★  " : "☆  ";
                    print $star;
                    print "名前：" . $row["name"] . "<br>";
                    print "苦み：" . $row["bitter"] . "<br>" ;
                    print "酸味：" . $row["acidity"] . "<br>";
                    print "コク：" . $row["body"] . "<br>";
                    print "煎り具合：" . $row["roast"] . "<br>";
                    print "特徴：" . $row["info"];
                    if(!empty($row["image"])){print "<br><img src='" . $row["image"] . "' width='200'>";}
                    print "</li>";
                }

                $dbh = null;
            ?>
        </ul>
    </body>
</html>
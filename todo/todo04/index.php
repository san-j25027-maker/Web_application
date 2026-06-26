<?php
    //外部のPHPファイルを呼び出す
    require_once("tasks_dao.php");
    if(isset($_POST["submit"])) {
        //POSTしたデータを取得する
        $task = $_POST["task"];
        //エスケープ処理
        $task = htmlspecialchars($task,ENT_QUOTES);
        //tasks_daoの呼び出し
        $dbh = db_connect();
        //プリペアドステートメントを使ってSQL文を構築
        $sql = "INSERT INTO tasks (taskname,done) VALUES(?,0)";
        $stmt = $dbh -> prepare($sql);
        $stmt -> bindValue(1,$task,PDO::PARAM_STR);
        //SQLの実行
        $stmt -> execute();
        //データベース接続情報の削除
        $dbh = null;
        unset($task);
    }
    //「済んだ」ボタンを押したときの処理
    if(isset($_POST["method"]) && $_POST["method"] === "put") {
        $id = $_POST["id"];
        $id = htmlspecialchars($id,ENT_QUOTES);
        //フォームから送られてきたIDは文字列型になっているので直す
        $id = (int)$id;
        //データベースへの接続
        $dbh = db_connect();
        $sql = "UPDATE tasks SET done = 1 WHERE id = ?";
        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(1,$id,PDO::PARAM_INT);
        $stmt -> execute();
        $dbh = null;
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf8">
        <title>TODOリスト</title>
    </head>
    <body>
        <h1>TODOリスト</h1>
        <form action="index.php" method="post">
            <li><span>タスク名</span><input type="text" name="taskname"></li>
            <li><input type="submit" name="submit"></li>
        </form>
        <ul>
            <?php
                //データベースへの接続
                $dbh = db_connect();
                //中身を取得
                $sql = "SELECT id,taskname FROM tasks WHERE done = 0 ORDER BY id DESC";
                $stmt = $dbh->prepare($sql);
                //SQLを実行
                $stmt->execute();
                $dbh = null;
                //連想配列の形で1行ずつ取り出す
                while($task = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    print "<li>";
                    print $task["taskname"];
                    print "<form action='index.php' method='post'>";
                    print "<input type='hidden' name='method' value='put'>";
                    print "<input type='hidden' name='id' value='".$task["id"]."'>";
                    print "<button type='submit'>済んだ</button>";
                    print "</form>";
                    print "</li>";
                }
            ?>
        </ul>
    </body>
</html>
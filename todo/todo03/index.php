<?php
    //外部のPHPファイルを呼び出す
    require_once("tasks_dao.php");
    if(isset($_POST["submit"])) {
        //POSTしたデータを取得する
        $task = $_POST["taskname"];
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
            <ul>
                <li><span>タスク名</span><input type="text" name="taskname"></li>
                <li><input type="submit" name="submit"></li>
            </ul>
        </form>
    </body>
</html>
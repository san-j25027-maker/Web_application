<?php
    //送信ボタンが押されたら
    if(isset($_POST["submit"])) {
        //POSTしたデータを取得する
        $task = $_POST["taskname"];
        //エスケープ処理
        $task = htmlspecialchars($task,ENT_QUOTES);
        //try-catch文
        //エラーが出た時の例外処理
        try{
            //データベースの接続準備、Data,Source,Name
            $dsn = "mysql:dbname=todolist;host=localhost;charset=utf8";
            $user = "root";
            $password = "";
            //ID,PW
            $dbh = new PDO($dsn,$user,$password);
            $dbh -> query("SET NAMES utf8");
            $dbh -> setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
        }catch(PDOException $e) {
            print "エラー:".$e->getMessage()."<br>";
            //$eにエラー情報を格納し、表示させる
            exit();
            //強制終了
        }
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
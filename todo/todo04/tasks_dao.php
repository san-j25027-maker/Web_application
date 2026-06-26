<?php
    //外部のファイルを呼び出す
    function db_connect() {
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
            return $dbh;
        }catch(PDOException $e) {
            print "エラー:".$e->getMessage()."<br>";
            //$eにエラー情報を格納し、表示させる
            exit();
            //強制終了
        }
    }
?>
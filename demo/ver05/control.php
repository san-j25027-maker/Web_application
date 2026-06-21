<?php
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
?>
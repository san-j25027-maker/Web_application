<?php
//セッションの再開
//すーばーグローバル変数の#_SESSIONが使えるようになる
session_start();
//セッションにnameが保存されていれば$_SESSIONの中身をすべて表示する
if(isset($_SESSION["name"])){
    echo "<pre>";
    var_dump($_SESSION);
    echo "</pre>";
}
?>

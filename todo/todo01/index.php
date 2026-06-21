<?php
    //機能してるかどうか
    echo "<pre>";
    var_dump($_POST);
    echo "</pre>";
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
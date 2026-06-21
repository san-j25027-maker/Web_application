<?php
if(isset($_POST["name"]) || isset($_POST["subject"])) {
    header("Location:http://localhost/original/ver01/gazou.php");
     exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>オリジナルのWebアプリ</title>
</head>
<body>
    <form action="" method="post">
        <table>
            <tr>
                <th>自分で入力</th>
                <td>
                    <input type="text" name="name" id="nameInput">
                </td>
            </tr>
            <tr>
                <th>種類を選択</th>
                <td>
                    <select name="subject" id="subjectSelect">
                        <option value ="A">A</option>
                        <option value ="B">B</option>
                        <option value ="C">C</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><input type="submit" name="submit" value="検索"></td>
            </tr>
        </table>
    </form>
</body>
</html>
<?php
$name = "";
$kinds = "";
$searchText = "";


    // 決定ボタン
    if (isset($_POST["set"])) {

        $name = $_POST["name"];
        $kinds = $_POST["kinds"];
        if ($name !== "" || $kinds !=="") { 
            // name優先
            if ($name !== "") {
                $searchText = $name;
            } else {
                $searchText = $kinds;
            }
        } else {
            echo "何も入力されていません";
        }
    }

    // 検索ボタン
    if (isset($_POST["search"])) {
        $searchText = $_POST["searchText"] ?? "";

        header("Location: gazou.php?keyword=" . urlencode($searchText));
        exit();
    }

?>

<form action="" method="post">
    <table>

        <tr>
            <th>入力</th>
            <td>
                <input type="text" name="name"
                       value="<?php echo htmlspecialchars($name); ?>">
            </td>
        </tr>

        <tr>
            <th>選択</th>
            <td>
                <select name="kinds">
                    <option value="">-</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                </select>
            </td>
        </tr>

        <tr>
            <td>
                <input type="submit" name="set" value="決定"><br>
            </td>
        </tr>

        <tr>
            <th>検索欄</th>
            <td>
                <input type="text" name="searchText" value="<?php echo htmlspecialchars($searchText); ?>">
            </td>
        </tr>

        <tr>
            <td>
                <input type="submit" name="search" value="検索">
            </td>
        </tr>

    </table>
</form>
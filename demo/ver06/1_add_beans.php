<?php
    require_once("control.php");

    // 追加処理
    if(isset($_POST["submit"])) {

        $name = htmlspecialchars($_POST["name"], ENT_QUOTES);
        $bitter = (int)$_POST["bitter"];
        $acidity = (int)$_POST["acidity"];
        $body = (int)$_POST["body"];
        $roast = htmlspecialchars($_POST["roast"], ENT_QUOTES);
        $info = htmlspecialchars($_POST["info"], ENT_QUOTES);

        // 画像はURLとしてそのまま保存
        $image = isset($_POST["image"]) ? trim($_POST["image"]) : "";
        $shop_url = isset($_POST["shop_url"]) ? trim($_POST["shop_url"]) : "";

        $dbh = db_connect();

        $sql = "INSERT INTO beans(name,bitter,acidity,body,roast,info,image,shop_url,flag)
                VALUES(?,?,?,?,?,?,?,?,0)";
        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(1, $name, PDO::PARAM_STR);
        $stmt->bindValue(2, $bitter, PDO::PARAM_INT);
        $stmt->bindValue(3, $acidity, PDO::PARAM_INT);
        $stmt->bindValue(4, $body, PDO::PARAM_INT);
        $stmt->bindValue(5, $roast, PDO::PARAM_STR);
        $stmt->bindValue(6, $info, PDO::PARAM_STR);
        $stmt->bindValue(7, $image, PDO::PARAM_STR);
        $stmt->bindValue(8, $shop_url, PDO::PARAM_STR);

        $stmt->execute();
        $dbh = null;
    }

    // 削除
    if (isset($_POST["delete"])) {

        $delete_id = (int)$_POST["delete_id"];

        $dbh = db_connect();
        $sql = "DELETE FROM beans WHERE id = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $delete_id, PDO::PARAM_INT);
        $stmt->execute();
        $dbh = null;
    }

    // お気に入り切替
    if (isset($_POST["toggle_fav"])) {

        $fav_id = (int)$_POST["fav_id"];

        $dbh = db_connect();

        $sql = "SELECT flag FROM beans WHERE id = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $fav_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $new_flag = ($row["flag"] == 1) ? 0 : 1;

        $sql = "UPDATE beans SET flag = ? WHERE id = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $new_flag, PDO::PARAM_INT);
        $stmt->bindValue(2, $fav_id, PDO::PARAM_INT);
        $stmt->execute();

        $dbh = null;
        header("Location: 1_add_beans.php#bean" . $fav_id);
        exit;
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="1_style.css?v=<?php echo time(); ?>">
    <title>更新&一覧ページ</title>
</head>

<body>

<div class="form-card">
    <h3>新しい情報を追加</h3>

    <form action="1_add_beans.php" method="post">

        <div class="form-row">
            <label>名称</label>
            <input type="text" name="name">
        </div>

        <div class="form-row">
            <label>苦み</label>
            <input type="number" name="bitter" min="1" max="5">
        </div>

        <div class="form-row">
            <label>酸味</label>
            <input type="number" name="acidity" min="1" max="5">
        </div>

        <div class="form-row">
            <label>コク</label>
            <input type="number" name="body" min="1" max="5">
        </div>

        <div class="form-row">
            <label>煎り具合</label>
            <input type="text" name="roast">
        </div>

        <div class="form-row">
            <label>特徴</label>
            <textarea name="info"></textarea>
        </div>

        <div class="form-row">
            <label>画像URL</label>
            <input type="text" name="image" placeholder="https://...">
        </div>

        <div class="form-row">
            <label>ショップURL</label>
            <input type="text" name="shop_url" placeholder="https://...">
        </div>

        <input type="submit" name="submit" value="追加">

    </form>
</div>
<div>
    <h3>コーヒー豆一覧</h3>
</div>

<ul class="beans-list">

<?php
    $dbh = db_connect();

    $sql = "SELECT * FROM beans ORDER BY id";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        echo "<li class='bean-card' id='bean" . $row["id"] . "'>";

        echo "<div class='bean-header'>";
        echo "<span class='bean-name'>" . $row["name"] . "</span>";

        echo "<form method='post' style='display:inline;'>";
        echo "<input type='hidden' name='fav_id' value='" . $row["id"] . "'>";
        echo "<button type='submit' name='toggle_fav' class='star-btn'>";
        echo ($row["flag"] == 1) ? "★" : "☆";
        echo "</button>";
        echo "</form>";

        echo "</div>";

        echo "<div class='bean-body'>";
        echo "<p>苦み：" . $row["bitter"] . "</p>";
        echo "<p>酸味：" . $row["acidity"] . "</p>";
        echo "<p>コク：" . $row["body"] . "</p>";
        echo "<p>煎り具合：" . $row["roast"] . "</p>";
        echo "<p>" . $row["info"] . "</p>";
        echo "</div>";
        echo '<img src="' . $row["image"] . '">';
        echo "<p><a href='" . htmlspecialchars($row["shop_url"], ENT_QUOTES) . "' target='_blank' rel='noopener' style='padding:8px 12px; background:#333; color:#fff; text-decoration:none; border-radius:5px;'>オンラインショップへ</a></p>";
      
        echo "<form method='post' style='margin-top:10px;'>";
        echo "<input type='hidden' name='delete_id' value='" . $row["id"] . "'>";
        echo "<button type='submit' name='delete' class='delete-btn'>削除</button>";
        echo "</form>";

        echo "</li>";
    }

    $dbh = null;
?>

</ul>

</body>
</html>
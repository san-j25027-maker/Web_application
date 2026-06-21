<?php
    //外部のPHPファイルを呼び出す
    require_once("control.php");

    if(isset($_POST["submit"])) {       //登録機能
        //POSTしたデータを取得する
        $name = $_POST["name"];
        $bitter = $_POST["bitter"];
        $acidity = $_POST["acidity"];
        $body = $_POST["body"];
        $roast = $_POST["roast"];
        $info = $_POST["info"];

        // 画像初期化
        $image = "";

        if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {

            $upload_dir = "uploads/";

            // フォルダが無ければ作成
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $filename = uniqid() . "_" . basename($_FILES["image"]["name"]);
            $path = $upload_dir . $filename;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $path)) {
                $image = $path;
            }
        }

        //エスケープ処理　文字
        $name = htmlspecialchars($name, ENT_QUOTES);
        $roast = htmlspecialchars($roast, ENT_QUOTES);
        $info = htmlspecialchars($info, ENT_QUOTES);

        //数値
        $bitter = $_POST["bitter"];
        $acidity = $_POST["acidity"];
        $body = $_POST["body"];
       
        $dbh = db_connect();
        //プリペアドステートメントを使ってSQL文を構築
        $sql = "INSERT INTO beans(name,bitter,acidity,body,roast,info,image,flag)
        VALUES(?,?,?,?,?,?,?,0)";
        $stmt = $dbh -> prepare($sql);

        $stmt->bindValue(1, $name, PDO::PARAM_STR);
        $stmt->bindValue(2, $bitter, PDO::PARAM_INT);
        $stmt->bindValue(3, $acidity, PDO::PARAM_INT);
        $stmt->bindValue(4, $body, PDO::PARAM_INT);
        $stmt->bindValue(5, $roast, PDO::PARAM_STR);
        $stmt->bindValue(6, $info, PDO::PARAM_STR);
        $stmt->bindValue(7, $image, PDO::PARAM_STR);
        //SQLの実行
        $stmt -> execute();
        //データベース接続情報の削除
        $dbh = null;
        unset($name);
    }

        
    if (isset($_POST["delete"])) {      //削除機能

        $delete_id = (int)$_POST["delete_id"];
        $dbh = db_connect();
        $sql = "DELETE FROM beans WHERE id = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $delete_id, PDO::PARAM_INT);
        $stmt->execute();
        $dbh = null;

    }

    if (isset($_POST["toggle_fav"])) {      //お気に入り機能

        $fav_id = (int)$_POST["fav_id"];
        $dbh = db_connect();
        // 現在のflagを取得
        $sql = "SELECT flag FROM beans WHERE id = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $fav_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // 反転
        $new_flag = ($row["flag"] == 1) ? 0 : 1;
        // 更新
        $sql = "UPDATE beans SET flag = ? WHERE id = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $new_flag, PDO::PARAM_INT);
        $stmt->bindValue(2, $fav_id, PDO::PARAM_INT);
        $stmt->execute();
        $dbh = null;

    }
    
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf8">
        <link rel="stylesheet" href="./style.css?v=9999">
        <?php //強制的に最新CSSを取得 ?>
        <title>更新ページ</title>
    </head>
    <body>
        <div class="form-card">
            <h3>追加</h3>
            <form action="add_beans.php" method="post" enctype="multipart/form-data">

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
                </div>

                <div class="form-row">
                    <label>特徴</label>
                    <textarea name="info"></textarea>
                </div>

                <div class="form-row">
                    <label>画像URL</label>
                    <input type="file" name="image">
                </div>

                <input type="submit" name="submit" value="追加">
            </form>
        </div>

        <h3>コーヒー豆一覧</h3>
        <ul class="beans-list">
            <?php
                // DB接続
                $dbh = db_connect();

                // データ取得
                $sql = "SELECT * FROM beans ORDER BY id";
                $stmt = $dbh->prepare($sql);
                $stmt->execute();

                // 1行ずつ表示
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    echo "<li class='bean-card'>";

                    // ヘッダー（名前＋星）
                    echo "<div class='bean-header'>";

                    echo "<span class='bean-name'>" . $row["name"] . "</span>";

                    echo "<form method='post' style='display:inline;'>";
                    echo "<input type='hidden' name='fav_id' value='" . $row["id"] . "'>";
                    echo "<button type='submit' name='toggle_fav' class='star-btn'>";
                    echo ($row["flag"] == 1) ? "★" : "☆";
                    echo "</button>";
                    echo "</form>";

                    echo "</div>";

                    // 本文
                    echo "<div class='bean-body'>";
                    echo "<p>苦み：" . $row["bitter"] . "</p>";
                    echo "<p>酸味：" . $row["acidity"] . "</p>";
                    echo "<p>コク：" . $row["body"] . "</p>";
                    echo "<p>煎り具合：" . $row["roast"] . "</p>";
                    echo "<p>" . $row["info"] . "</p>";
                    echo "</div>";

                    // 画像
                    if(!empty($row["image"])) {
                        echo "<img src='" . $row["image"] . "' class='bean-img'>";
                    }

                    // 削除ボタン
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
<?php
    $name = "";
    $selected_name = "";
    $bitter = "";
    $acidity = "";
    $body = "";
    $roast = "";
    require_once("control.php");

    $dbh = db_connect();
    $results = [];

    // 検索ボタン
    if (isset($_GET["submit"])) {
        //入力内容を取得
        $name = isset($_GET["name"]) ? trim($_GET["name"]) : "";
                //入力があるかどうか    ある時は空白を除去した入力内容    ないなら空文字
        $bitter = isset($_GET["bitter"]) ? $_GET["bitter"] : "";
        $acidity = isset($_GET["acidity"]) ? $_GET["acidity"] : "";
        $body = isset($_GET["body"]) ? $_GET["body"] : "";
        $roast = isset($_GET["roast"]) ? $_GET["roast"] : "";

        // SQL組み立て
        $sql = "SELECT * FROM beans WHERE 1=1";
        $params = [];

        if ($name !== "") {//入力が空じゃない
            $sql .= " AND name LIKE ?";//条件を追加
            $params[] = "%{$name}%";//条件を記憶
        }

        if ($bitter !== "") {
            $sql .= " AND bitter = ?";
            $params[] = (int)$bitter;
        }

        if ($acidity !== "") {
            $sql .= " AND acidity = ?";
            $params[] = (int)$acidity;
        }

        if ($body !== "") {
            $sql .= " AND body = ?";
            $params[] = (int)$body;
        }

        if ($roast !== "") {
            $sql .= " AND roast = ?";
            $params[] = "$roast";
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $dbh->prepare($sql);

        foreach ($params as $i => $val) {
            $stmt->bindValue($i + 1, $val);
        }

        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
       
    }

?>

<!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <link rel="stylesheet" href="./1_style.css?v=9999">
            <title>コーヒー豆検索</title>

        </head>

        <body>

            <div class="form-card">
                <h3>検索フォーム</h3>

                <form method="get" action="3_test.php">

                    <div class="form-row">
                       <label>名称</label>
                        <input type="text" name="name" id="nameInput"
                               value="<?= htmlspecialchars($name, ENT_QUOTES) ?>">
                    </div>
            
                    <div class="form-row">
                        <label>苦み（1〜5）</label>
                        <input type="number" name="bitter" min="1" max="5"
                               value="<?= htmlspecialchars($bitter, ENT_QUOTES) ?>">
                    </div>

                    <div class="form-row">
                        <label>酸味（1〜5）</label>
                        <input type="number" name="acidity" min="1" max="5"
                               value="<?= htmlspecialchars($acidity, ENT_QUOTES) ?>">
                    </div>

                    <div class="form-row">
                        <label>コク（1〜5）</label>
                        <input type="number" name="body" min="1" max="5"
                               value="<?= htmlspecialchars($body, ENT_QUOTES) ?>">
                    </div>

                    <div class="form-row">
                        <label>煎り具合</label>
                        <select name="roast">
                                <option value="">-</option>
                                <option value="深煎り">深煎り</option>
                                <option value="中煎り">中煎り</option>
                                <option value="浅煎り">浅煎り</option>
                            </select>
                    </div>
        
                    <input type="submit" name="submit" value="検索">

                </form>
            </div>

            <h3>候補一覧</h3>

            <ul class="beans-list">

                <?php foreach ($results as $row): ?>

                <li class="bean-card">

                    <div class="bean-header">
                        <span class="bean-name">
                            <?= htmlspecialchars($row["name"], ENT_QUOTES) ?>
                        </span>
                    </div>

                    <div class="bean-body">
                        <p>苦み：<?= $row["bitter"] ?></p>
                        <p>酸味：<?= $row["acidity"] ?></p>
                        <p>コク：<?= $row["body"] ?></p>
                        <p>煎り具合：<?= $row["roast"] ?></p>                        
                        <p><?= htmlspecialchars($row["info"], ENT_QUOTES) ?></p>
                        <a href="<?= htmlspecialchars($row["shop_url"], ENT_QUOTES) ?>" target="_blank" rel="noopener">
                            <button type="button"> オンラインショップ </button>
                        </a>
                    </div>

                </li>

            <?php endforeach; ?>

        </ul>

    </body>
</html>
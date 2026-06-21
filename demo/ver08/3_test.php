<?php
    $name = "";//初期値
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

        if ($name !== "") { //部分一致検索用処理
            // 空白除去（任意）
            $name = str_replace(' ', '', $name);

            // 1文字ずつ分解
            $chars = preg_split('//u', $name, -1, PREG_SPLIT_NO_EMPTY);

            // %で連結して前後にも付ける
            $like = '%' . implode('%', $chars) . '%';

            $sql .= " AND name LIKE ?";
            $params[] = $like;
        }

        if ($bitter !== "") {   //それぞれ検索条件をあるなら追加
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
        //sql実行
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //$resultsに結果を格納
       
    } else {
    // ★ 初期表示（お気に入りのみ）
    $sql = "SELECT * FROM beans WHERE flag = 1 ORDER BY id DESC";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <link rel="stylesheet" href="./1_style.css?v=9999">
                                        <!--最新の状態を適用-->
            <title>コーヒー豆検索</title>
            <form action="0_home.php">
                <div class="menu">
                    <button>メニューに戻る</button>
                </div>
            </form>
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

                
            <?php if (empty($results)): ?>
                <p>該当するコーヒー豆は見つかりませんでした。</p>

            <?php else: ?>
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
                                <button type="button">オンラインショップ</button>
                            </a>
                        </div>
                    </li>
            
                <?php endforeach; ?>
            <?php endif; ?>


        </ul>

    </body>
</html>
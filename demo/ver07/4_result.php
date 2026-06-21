<?php
    require_once "control.php";

    $dbh = db_connect();

    // スコア受け取り
    $score = $_GET['score'] ?? 0;
    $miss = $_GET['miss'] ?? 0;
    // 雑学をランダム取得
    $stmt = $dbh->query("SELECT content FROM anything ORDER BY RAND() LIMIT 1");
    $comment = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>結果</title>

        <style>
            body {
                text-align: center;
                font-family: sans-serif;
                background: #222;
                color: white;
            }

            #triviaBox {
                margin-top: 30px;
                padding: 20px;
                background: #333;
                display: inline-block;
                border-radius: 10px;
                width: 500px;
            }
        </style>
    </head>

    <body>

        <h1>結果発表</h1>

        <h2>スコア：<?php echo $score; ?></h2>

        <div id="triviaBox">
            <h3>一言コメント</h3>
            <p><?php echo htmlspecialchars($comment, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>

        <br><br>

        <form action="4_start.php">
            <button type="submit">もう一度</button>
        </form>

        <form action="0_home.php">
            <div class="menu">
                <button>メニューに戻る</button>
            </div>
        </form>

    </body>
</html>
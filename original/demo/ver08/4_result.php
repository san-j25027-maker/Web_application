<?php
require_once "control.php";

$dbh = db_connect();

// スコア系
$score   = $_GET['score'] ?? 0;
$miss    = $_GET['miss'] ?? 0;
$total   = $_GET['total'] ?? 0;
$correct = $_GET['correct'] ?? 0;
$time    = $_GET['time'] ?? 30;

// 1秒あたりの速度
$perSec = ($time > 0) ? round($correct / $time, 2) : 0;

// 正確率
$accuracy = ($total > 0) ? round(($correct / $total) * 100, 1) : 0;

// 雑学をランダム取得
$stmt = $dbh->query("SELECT content FROM anything ORDER BY RAND() LIMIT 1");
$comment = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>結果画面</title>

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
<hr>

<h2>スコア：<?php echo $score; ?></h2>
<p>ミス: <?php echo $miss; ?></p>

<p>総打鍵数: <?php echo $total; ?></p>
<p>正解打鍵数: <?php echo $correct; ?></p>

<p>平均速度: <?php echo $perSec; ?> 文字/秒</p>
<p>正確率: <?php echo $accuracy; ?> %</p>

<div id="triviaBox">
    <h3>一言コメント</h3>
    <p><?php echo htmlspecialchars($comment, ENT_QUOTES, 'UTF-8'); ?></p>
</div>

<br><br>

<form action="4_game.php">
    <button type="submit">もう一度プレイ</button>
</form>

<form action="0_home.php">
    <button type="submit">メニューに戻る</button>
</form>

</body>
</html>
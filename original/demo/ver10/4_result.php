<?php
session_start();
require_once "control.php";

$dbh = db_connect();

// =====================
// パラメータ取得
// =====================
$score   = $_GET["score"] ?? 0;
$correct = $_GET["correct"] ?? 0;
$miss    = $_GET["miss"] ?? 0;
$total   = $_GET["total"] ?? ($correct + $miss);
$time    = $_GET["time"] ?? 30;

// =====================
// 計算系
// =====================
$cps = ($time > 0) ? round(($correct + $miss) / $time, 2) : 0;
$accuracy = ($total > 0) ? round(($correct / $total) * 100, 1) : 0;

// =====================
// バナー判定
// =====================
if ($score >= 2000) {
    $banner = "Sランク";
} elseif ($score >= 1000) {
    $banner = "Aランク";
} elseif ($score >= 500) {
    $banner = "Bランク";
} else {
    $banner = "Cランク";
}

// =====================
// バッジ保存処理
// =====================
if (isset($_SESSION["user"]) && $_SESSION["user"] !== "guest") {

    $stmt = $dbh->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$_SESSION["user"]]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $dbh->prepare("SELECT id FROM badges WHERE name = ?");
    $stmt->execute([$banner]);
    $badge = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $badge) {

        $stmt = $dbh->prepare("SELECT id FROM user_badges WHERE user_id = ? AND badge_id = ?");
        $stmt->execute([$user["id"], $badge["id"]]);

        if (!$stmt->fetch()) {
            $stmt = $dbh->prepare("INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)");
            $stmt->execute([$user["id"], $badge["id"]]);
        }
    }
}
// =====================
// 一言コメント取得
// =====================

// 低スコア
if ($score < 200) {
    // flag 0も含める
    $stmt = $dbh->query("SELECT content FROM anything ORDER BY RAND() LIMIT 1");
} else {
    // 通常はflag=1のみ
    $stmt = $dbh->query("SELECT content FROM anything WHERE flag = 1 ORDER BY RAND() LIMIT 1");
}

$comment = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>結果画面</title>

<style>
body {
    font-family: sans-serif;
    background: #222;
    color: white;
    margin: 0;
}

/* 横並びレイアウト */
.container {
    display: flex;
    justify-content: center;
    gap: 40px;
    margin-top: 40px;
}

/* 左：結果 */
.result-box {
    background: #333;
    padding: 20px;
    border-radius: 10px;
    width: 300px;
}

/* 右：コメント */
#triviaBox {
    background: #333;
    padding: 20px;
    border-radius: 10px;
    width: 300px;
    height: 200px; /* ← 高さを固定（これ重要） */

    display: flex;
    flex-direction: column;
    justify-content: center; /* 縦中央 */
    align-items: center;     /* 横中央 */

    text-align: center;
}

/* 下中央ボタン */
.button-area {
    text-align: center;
    margin-top: 40px;
}

button {
    padding: 10px 20px;
    margin: 10px;
    border: none;
    border-radius: 5px;
    background: #555;
    color: white;
    cursor: pointer;
}

button:hover {
    background: #777;
}

h1 {
    text-align: center;
    margin-top: 20px;
}
</style>
</head>

<body>

<h1>結果発表</h1>

<div class="container">

    <!-- 左：結果 -->
    <div class="result-box">
        <h2>スコア：<?php echo htmlspecialchars($score); ?></h2>
        <h3>バナー：<?php echo htmlspecialchars($banner); ?></h3>

        <p>ミス: <?php echo htmlspecialchars($miss); ?></p>
        <p>総打鍵数: <?php echo htmlspecialchars($total); ?></p>
        <p>正解打鍵数: <?php echo htmlspecialchars($correct); ?></p>

        <p>平均速度: <?php echo $cps; ?> 文字/秒</p>
        <p>正確率: <?php echo $accuracy; ?> %</p>
    </div>

    <!-- 右：コメント -->
    <div id="triviaBox">
        <h3>作者からの戯言</h3>
        <p><?php echo htmlspecialchars($comment, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>

</div>

<!-- 中央ボタン -->
<div class="button-area">
    <form action="4_game.php" style="display:inline;">
        <button type="submit">もう一度プレイ</button>
    </form>

    <form action="0_home.php" style="display:inline;">
        <button type="submit">メニューに戻る</button>
    </form>
</div>

</body>
</html>
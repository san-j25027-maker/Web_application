<?php
session_start();

require_once "control.php";
$dbh = db_connect();

// =====================
// パラメータ取得
// =====================
$score = $_GET["score"] ?? 0;
$miss = $_GET["miss"] ?? 0;
$totalChars = $_GET["chars"] ?? 0;
$time = $_GET["time"] ?? 30;

// CPS計算
$cps = $time > 0 ? round($totalChars / $time, 2) : 0;

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

    // username → user_id
    $stmt = $dbh->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$_SESSION["user"]]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // banner名 → badge_id
    $stmt = $dbh->prepare("SELECT id FROM badges WHERE name = ?");
    $stmt->execute([$banner]);
    $badge = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $badge) {

        $user_id = $user["id"];
        $badge_id = $badge["id"];

        // 重複チェック
        $stmt = $dbh->prepare("SELECT id FROM user_badges WHERE user_id = ? AND badge_id = ?");
        $stmt->execute([$user_id, $badge_id]);

        if (!$stmt->fetch()) {
            // 登録
            $stmt = $dbh->prepare("INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)");
            $stmt->execute([$user_id, $badge_id]);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>結果</title>
</head>

<body>

<h1>結果</h1>
<p>スコア: <?php echo $score; ?></p>
<p>ミス: <?php echo $miss; ?></p>
<p>入力文字数: <?php echo $totalChars; ?></p>
<p>平均速度: <?php echo $cps; ?> 文字/秒</p>

<h2>獲得バナー: <?php echo $banner; ?></h2>

<a href="4_start.php">戻る</a>

</body>
</html>
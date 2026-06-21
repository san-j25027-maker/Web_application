<?php
require_once("control.php");

$dbh = db_connect();

// ランダムで1件取得
$sql = "SELECT name FROM beans ORDER BY RAND() LIMIT 1";
$stmt = $dbh->prepare($sql);
$stmt->execute();

$row = $stmt->fetch(PDO::FETCH_ASSOC);
$dbh = null;

$word = $row["name"];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>タイピングゲーム</title>
<script src="https://unpkg.com/wanakana"></script>
<style>
body { text-align:center; font-family:sans-serif; }
#word { font-size:30px; margin:20px; }
#input { font-size:20px; padding:5px; }
</style>
</head>

<body>

<h2>コーヒー豆タイピング</h2>

<div id="word"><?= htmlspecialchars($word) ?></div>

<input type="text" id="input" autofocus>

<p id="result"></p>

<script>
const word = "<?= $word ?>";
const input = document.getElementById("input");
const result = document.getElementById("result");

input.addEventListener("input", function() {

    if (input.value === word) {
        result.textContent = "正解！";
        
        // 次の問題へリロード
        setTimeout(() => {
            location.reload();
        }, 1000);
    }
});
</script>

</body>
</html>
```php
<?php
require_once "control.php";
$dbh = db_connect();

// データ取得
$stmt = $dbh->query("SELECT name, image FROM beans ORDER BY RAND()");
$beans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>タイピングゲーム</title>

<style>
body {
    text-align: center;
    font-family: sans-serif;
}

#gameArea {
    position: relative;
    width: 800px;
    height: 200px;
    border: 1px solid #000;
    margin: 20px auto;
    overflow: hidden;
}

#word {
    position: absolute;
    top: 100px;
    left: 800px;
    font-size: 30px;
}

#beanImage {
    position: absolute;
    top: 20px;
    left: 800px;
    height: 80px;
}
</style>
</head>

<body>

<div id="gameArea">
    <img id="beanImage" src="">
    <div id="word"></div>
</div>

<input type="text" id="input" autofocus>

<div>
スコア: <span id="score">0</span>
ミス: <span id="miss">0</span>
時間: <span id="time">30</span>
</div>

<script>
const beans = <?php echo json_encode($beans, JSON_UNESCAPED_UNICODE); ?>;

let currentIndex = 0;
let posX = 800;
let speed = 2;

let score = 0;
let miss = 0;
let time = 30;

const wordEl = document.getElementById("word");
const imageEl = document.getElementById("beanImage");
const input = document.getElementById("input");

// 次の問題
function nextWord() {
    if (currentIndex >= beans.length) currentIndex = 0;

    const current = beans[currentIndex];

    wordEl.textContent = current.name;
    imageEl.src = current.image;

    posX = 800;

    wordEl.style.left = posX + "px";
    imageEl.style.left = posX + "px";

    input.value = "";
}

// 初期
nextWord();

// アニメーション
function update() {
    posX -= speed;

    wordEl.style.left = posX + "px";
    imageEl.style.left = posX + "px";

    if (posX < -200) {
        miss++;
        document.getElementById("miss").textContent = miss;

        currentIndex++;
        nextWord();
    }

    requestAnimationFrame(update);
}
update();

// 入力判定
input.addEventListener("input", () => {
    const current = beans[currentIndex];

    if (input.value === current.name) {
        score += 100;
        document.getElementById("score").textContent = score;

        currentIndex++;
        nextWord();
    }
});

// タイマー
const timer = setInterval(() => {
    time--;
    document.getElementById("time").textContent = time;

    if (time <= 0) {
    clearInterval(timer);

    // result.phpへスコアを渡して遷移
    location.href = "4_result.php?score=" + score + "&miss=" + miss;
}
}, 1000);
</script>

</body>
</html>
```

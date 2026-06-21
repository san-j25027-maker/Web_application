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
    width: 800px;
    height: 200px;
    border: 1px solid #000;
    margin: 20px auto;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

#word {
    font-size: 30px;
}

#word span {
    font-size: 32px;
    margin: 0 2px;
}

.correct {
    color: green;
}

.miss {
    color: red;
}

#beanImage {
    height: 80px;
    margin-bottom: 10px;
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
let score = 0;
let miss = 0;
let time = 30;

// ★追加
let totalType = 0;     // 総打鍵数
let correctType = 0;   // 正解打鍵数

const wordEl = document.getElementById("word");
const imageEl = document.getElementById("beanImage");
const input = document.getElementById("input");

// 表示
function renderWord() {
    const current = beans[currentIndex];
    const target = current.name;

    let html = "";

    for (let i = 0; i < target.length; i++) {
        const inputChar = input.value[i];
        const targetChar = target[i];

        if (inputChar == null) {
            html += `<span>${targetChar}</span>`;
        } else if (inputChar === targetChar) {
            html += `<span class="correct">${targetChar}</span>`;
        } else {
            html += `<span class="miss">${targetChar}</span>`;
        }
    }

    wordEl.innerHTML = html;
}

// 次の問題
function nextWord() {
    if (currentIndex >= beans.length) currentIndex = 0;

    const current = beans[currentIndex];

    imageEl.src = current.image;
    input.value = "";

    renderWord();
}

// 初期
nextWord();

// ★キー入力カウント
input.addEventListener("keydown", () => {
    totalType++;
});

// 入力判定
input.addEventListener("input", () => {
    renderWord();

    const current = beans[currentIndex];

    if (input.value === current.name) {
        score += 100;
        document.getElementById("score").textContent = score;

        correctType += current.name.length;

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

        location.href =
            "4_result.php?score=" + score +
            "&miss=" + miss +
            "&total=" + totalType +
            "&correct=" + correctType +
            "&time=30";
    }
}, 1000);
</script>

</body>
</html>
```

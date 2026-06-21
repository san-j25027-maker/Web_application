<?php
session_start();
require_once("control.php");
$dbh = db_connect();

$stmt = $dbh->query("SELECT name FROM beans ORDER BY RAND()");
$beans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>タイピングゲーム（統合版）</title>

<script src="https://unpkg.com/wanakana"></script>

<style>
body {
    text-align: center;
    font-family: sans-serif;
}

#word {
    font-size: 32px;
    margin: 20px;
}

.correct { color: blue; }
.miss { color: red; }
.remaining { color: black; }
</style>
</head>

<body>

<h2>タイピングゲーム</h2>

<div>
スコア: <span id="score">0</span>
ミス: <span id="miss">0</span>
時間: <span id="time">30</span>
</div>

<div id="word"></div>

<input type="text" id="input" autofocus>

<script>
const beans = <?= json_encode($beans, JSON_UNESCAPED_UNICODE) ?>;

let currentIndex = 0;
let score = 0;
let miss = 0;
let time = 30;

let currentWord = "";
let candidates = [];
let typed = "";

// =====================
// ローマ字候補生成（高精度版）
// =====================
function getRomajiCandidates(kana) {
    let base = wanakana.toRomaji(kana);

    let list = [base];

    // 揺れ対応
    list.push(base.replace(/shi/g, "si"));
    list.push(base.replace(/chi/g, "ti"));
    list.push(base.replace(/tsu/g, "tu"));
    list.push(base.replace(/fu/g, "hu"));

    list.push(base.replace(/sha/g, "sya"));
    list.push(base.replace(/shu/g, "syu"));
    list.push(base.replace(/sho/g, "syo"));

    list.push(base.replace(/cha/g, "tya"));
    list.push(base.replace(/chu/g, "tyu"));
    list.push(base.replace(/cho/g, "tyo"));

    list.push(base.replace(/ja/g, "jya"));
    list.push(base.replace(/ju/g, "jyu"));
    list.push(base.replace(/jo/g, "jyo"));

    // ん揺れ
    list.push(base.replace(/n/g, "nn"));
    list.push(base.replace(/nn/g, "n"));

    // 長音揺れ
    list.push(base.replace(/ou/g, "oo"));
    list.push(base.replace(/oo/g, "ou"));

    return [...new Set(list)];
}

// =====================
// 次の単語
// =====================
function nextWord() {
    currentWord = beans[currentIndex].name;
    candidates = getRomajiCandidates(currentWord);

    typed = "";
    document.getElementById("input").value = "";

    render();

    currentIndex++;
    if (currentIndex >= beans.length) {
        currentIndex = 0;
    }
}

// =====================
// 表示（現在の入力に対して色付け）
// =====================
function render() {
    const base = candidates[0]; // 基準表示
    const input = typed;

    let html = "";

    for (let i = 0; i < base.length; i++) {

        const char = base[i];

        if (i < input.length) {
            if (input[i] === base[i]) {
                html += `<span class="correct">${char}</span>`;
            } else {
                html += `<span class="miss">${char}</span>`;
            }
        } else {
            html += `<span class="remaining">${char}</span>`;
        }
    }

    document.getElementById("word").innerHTML = html;
}

// =====================
// 入力処理
// =====================
document.getElementById("input").addEventListener("input", function () {

    typed = this.value;

    // 前方一致チェック（最重要）
    const ok = candidates.some(c => c.startsWith(typed));

    if (!ok) {
        miss++;
        document.getElementById("miss").textContent = miss;
    }

    // 完全一致
    if (candidates.includes(typed)) {
        score++;
        document.getElementById("score").textContent = score;
        nextWord();
        return;
    }

    render();
});

// =====================
// タイマー
// =====================
setInterval(() => {
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

// 初期開始
nextWord();
</script>

</body>
</html>
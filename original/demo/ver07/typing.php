<?php
require_once "control.php";
$pdo = db_connect();

// 全単語取得
$stmt = $pdo->query("SELECT name FROM beans");
$words = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>寿司打風タイピング</title>

<script src="https://unpkg.com/wanakana"></script>

<style>
body { text-align:center; font-family:sans-serif; }
#word { font-size:32px; margin:20px; }
#input { font-size:24px; }
#info { margin-top:10px; }
</style>
</head>
<body>

<h1>タイピングゲーム</h1>

<div>時間: <span id="time">30</span></div>
<div>スコア: <span id="score">0</span></div>

<div id="word"></div>
<div id="romaji"></div>

<input type="text" id="input" autofocus>

<p id="result"></p>

<script>
// =====================
// ▼① 初期データ
// =====================
const words = <?= json_encode($words, JSON_UNESCAPED_UNICODE) ?>;

let currentWord = "";
let answers = [];
let inputText = "";

let score = 0;
let time = 30;
let timer;

// =====================
// ▼② ゆるいローマ字変換
// =====================
function getRomajiCandidates(kana){
    let base = wanakana.toRomaji(kana);
    let list = [base];

    // =====================
    // ▼基本揺れ
    // =====================
    list.push(base.replace(/shi/g, "si"));
    list.push(base.replace(/chi/g, "ti"));
    list.push(base.replace(/tsu/g, "tu"));
    list.push(base.replace(/fu/g, "hu"));

    // =====================
    // ▼拗音（しゃ系）
    // =====================
    list.push(base.replace(/sha/g, "sya"));
    list.push(base.replace(/shu/g, "syu"));
    list.push(base.replace(/sho/g, "syo"));

    list.push(base.replace(/cha/g, "tya"));
    list.push(base.replace(/chu/g, "tyu"));
    list.push(base.replace(/cho/g, "tyo"));

    list.push(base.replace(/ja/g, "jya"));
    list.push(base.replace(/ju/g, "jyu"));
    list.push(base.replace(/jo/g, "jyo"));

    // =====================
    // ▼濁音・特殊
    // =====================
    list.push(base.replace(/di/g, "dhi"));
    list.push(base.replace(/de/g, "dhe"));

    // =====================
    // ▼ん（超重要）
    // =====================
    list.push(base.replace(/n/g, "nn"));   // n → nn
    list.push(base.replace(/nn/g, "n"));   // nn → n

    // =====================
    // ▼長音（ー）
    // =====================
    list.push(base.replace(/oo/g, "o-"));
    list.push(base.replace(/ou/g, "o-"));
    list.push(base.replace(/aa/g, "a-"));
    list.push(base.replace(/uu/g, "u-"));
    list.push(base.replace(/ee/g, "e-"));
    list.push(base.replace(/ii/g, "i-"));

    // =====================
    // ▼母音揺れ（例：おう / おお）
    // =====================
    list.push(base.replace(/ou/g, "oo"));
    list.push(base.replace(/oo/g, "ou"));

    // =====================
    // ▼個別指定（要望対応）
    // =====================
    list.push(base.replace(/dei/g, "dhi"));
    list.push(base.replace(/dhi/g, "dei"));

    // =====================
    // ▼重複削除
    // =====================
    return [...new Set(list)];
}
// =====================
// ▼③ ランダム出題
// =====================
function nextQuestion(){
    currentWord = words[Math.floor(Math.random() * words.length)];
    answers = getRomajiCandidates(currentWord);

    inputText = "";
    document.getElementById("input").value = "";

    document.getElementById("word").textContent = currentWord;
    document.getElementById("romaji").textContent = answers[0]; // デバッグ用
}

// =====================
// ▼④ 判定関数
// =====================
function isMatch(input){
    return answers.some(ans => ans.startsWith(input));
}

function isComplete(input){
    return answers.some(ans => ans === input);
}

// =====================
// ▼⑤ 入力処理
// =====================
document.getElementById("input").addEventListener("input", function(){

    inputText = this.value;

    if(isMatch(inputText)){
        document.getElementById("result").textContent = "OK";
    }else{
        document.getElementById("result").textContent = "ミス";
    }

    if(isComplete(inputText)){
        score++;
        document.getElementById("score").textContent = score;
        nextQuestion();
    }
});

// =====================
// ▼⑥ タイマー
// =====================
function startGame(){
    nextQuestion();

    timer = setInterval(() => {
        time--;
        document.getElementById("time").textContent = time;

        if(time <= 0){
            clearInterval(timer);
            endGame();
        }
    }, 1000);
}

// =====================
// ▼⑦ 終了
// =====================
function endGame(){
    document.getElementById("word").textContent = "終了！";
    document.getElementById("result").textContent = "スコア: " + score;
    document.getElementById("input").disabled = true;
}

// =====================
// ▼開始
// =====================
startGame();
</script>

</body>
</html>
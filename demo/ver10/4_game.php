<?php
    session_start();
    require_once "control.php";
    $dbh = db_connect();
    //ランダムにデータベースの文字を取得
    $stmt = $dbh->query("SELECT name FROM beans ORDER BY RAND()");  
    $beans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>寿司打風タイピング</title>
        <script src="https://unpkg.com/wanakana"></script>
        <!-- wanakanaライブラリを読み込む -->
        <!-- CDN（インターネット上の配布サーバ）から直接読み込んでいる -->
        <link rel="stylesheet" href="4_style.css?v=<?php echo time(); ?>">
    </head>

    <body>

        <h2>寿司打風タイピング</h2>
        <div>
            
<div>
    眠気😴:
    <div style="width: 1600px;height:20px;background:#ddd;">
        <div id="sleepBar" style="height:20px;width:0%;background:blue;"></div>
    </div>
<div>
    カフェイン☕:
    <div id="caffeineCups"></div>
</div>
        <div>
            スコア: <span id="score">0</span>
            正解数: <span id="correct">0</span>
            ミス: <span id="miss">0</span>
            時間: <span id="time">30</span>
        </div>

        <div id="word"></div>
        <input type="text" id="input" autofocus>
        <div id="status"></div>
        <div id="missDisplay"></div>
        

        <script>
            // PHPの $beans 配列を JavaScriptで使える形に変換して代入
            const beans = <?= json_encode($beans, JSON_UNESCAPED_UNICODE) ?>;
            // PHPの配列をJavaScriptの配列形式に変換 　　\uXXXX 形式に変換せず、そのまま表示

            // =====================
            // ゲーム状態
            // =====================
            let currentIndex = 0;
            let score = 0;
            let correct = 0;
            let miss = 0;
            let time = 30;
            let combo = 0;          // 連続成功
let caffeine = 0;       // メーター
const CAFFEINE_MAX = 5; // 何回で発動
const BONUS_TIME = 3;   // +秒数

            let typed = "";
            let candidates = [];

            let gameActive = true;
            let timerId = null;
            let ending = false;

            // =====================
            // 音
            // =====================
            function beep(freq = 600, duration = 80) {
            // freq: 音の高さ 　　　duration: 音の長さ
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                // AudioContext（音を扱うためのAPI）を生成
                // ブラウザによっては webkitAudioContext を使う必要があるため両対応
                const osc = ctx.createOscillator();
                // 発振器（音を作る装置）を作成
                const gain = ctx.createGain();
                // 音量を調整するノードを作成
                osc.frequency.value = freq;
                // 周波数（音の高さ）を設定
                osc.connect(gain);
                gain.connect(ctx.destination);
                // 接続：発振器 → 音量 → スピーカー
                osc.start();
                // 音を鳴らし始める

                // 指定時間後に音を止める
                setTimeout(() => {
                    osc.stop();     // 音を停止
                    ctx.close();    // AudioContextを閉じてリソース解放
                }, duration);
            }

            // =====================
            // ローマ字候補生成
            // =====================
            function getRomajiCandidates(kana) {
    let base = wanakana.toRomaji(kana);

    // タイポ修正
    base = base.replace(/oo/g, "o-");
    base = base.replace(/aa/g, "a-");
    base = base.replace(/uu/g, "u-");
    base = base.replace(/ii/g, "i-");
    base = base.replace(/dei/g, "dhi");

    let list = [base];

    function expand(pattern, replacement) {
        const newList = [];
        list.forEach(str => {
            newList.push(str);
            newList.push(str.replace(pattern, replacement));
        });
        list = newList;
    }

    expand(/shi/g, "si");
    expand(/chi/g, "ti");
    expand(/tsu/g, "tu");
    expand(/fu/g, "hu");

    expand(/sha/g, "sya");
    expand(/shu/g, "syu");
    expand(/sho/g, "syo");

    expand(/cha/g, "tya");
    expand(/chu/g, "tyu");
    expand(/cho/g, "tyo");

    expand(/ja/g, "jya");
    expand(/ju/g, "jyu");
    expand(/jo/g, "jyo");

    expand(/oo/g, "o-");
    expand(/o-/g, "oo");

    expand(/ou/g, "oo");
    expand(/oo/g, "ou");

    expand(/dhi/g, "dei");
    expand(/dhi/g, "deli");

    return [...new Set(list)];
}
            // =====================
            // 表示
            // =====================
            function render() {
                const base = candidates[0]; // 表示する正解の文字列（配列の先頭）
                let html = ""; // 表示用HTMLをためる変数
                const jp = currentWord; // 日本語
                html += `<div class="jp">${jp}</div>`;

                // 正解文字列を1文字ずつループ
                for (let i = 0; i < base.length; i++) {
                    // すでに入力済みの範囲かどうか
                    if (i < typed.length) {
                        // 正解として表示（class="correct"）
                        html += `<span class="correct">${base[i]}</span>`; 
                    } else {
                        // まだ入力していない部分（残りの文字）
                        html += `<span class="remaining">${base[i]}</span>`;
                    }
                }
                document.getElementById("correct").textContent = correct;
                // 作成したHTMLを画面に反映（wordというidの要素に表示）
                document.getElementById("word").innerHTML = html;
                // 正しい入力時はMISS消す
                document.getElementById("missDisplay").textContent = "";
            }

// =====================
// 次の問題
// =====================
// 次の単語へ切り替える関数
function nextWord() {

    // 現在の単語（日本語）を取得
    // beans 配列の currentIndex 番目の name を使う
    currentWord = beans[currentIndex].name;

    // 現在の単語からローマ字入力の候補一覧を生成
    // 例：「し」→ ["shi", "si"] など
    candidates = getRomajiCandidates(currentWord);

    // 入力済み文字列をリセット（新しい単語なので空にする）
    typed = "";

    // 入力欄（input要素）の表示も空にする
    document.getElementById("input").value = "";

    // 画面表示を更新（新しい単語を表示）
    render();

    // インデックスを次へ進める
    currentIndex++;

    // 最後まで行ったら最初に戻る（ループ処理）
    if (currentIndex >= beans.length) {
        currentIndex = 0;
    }
}

// =====================
// 終了処理（リザルト遷移）
// =====================
// ゲーム終了処理を行う関数
function endGame() {

    // すでに終了処理が実行されている場合は何もしない（二重実行防止）
    if (ending) return;

    // 終了フラグを立てる
    ending = true;

    // ゲームを非アクティブ状態にする（入力などを無効化するため）
    gameActive = false;

    // タイマーを停止（setIntervalで動いているものを止める）
    clearInterval(timerId);

    // 入力欄を無効化（これ以上入力できないようにする）
    document.getElementById("input").disabled = true;

    // 結果画面へ遷移（GETパラメータでスコアなどを渡す）
    location.href =
        "4_result.php?score=" + score +     // スコア
        "&miss=" + miss +                  // ミス回数
        "&total=" + score +                // 総入力数（※ここではスコアを代用）
        "&correct=" + correct +              // 正解数（※ここではスコアを代用）
        "&time=30";                        // 制限時間（固定30秒）
}

function updateCaffeineUI() {
    const container = document.getElementById("caffeineCups");
    container.innerHTML = "";

    for (let i = 0; i < CAFFEINE_MAX; i++) {
        const img = document.createElement("img");
        img.src = "images4.jpg"; // ← あなたの画像
        img.classList.add("cup");

        if (i < caffeine) {
            img.classList.add("active");
        }

        container.appendChild(img);
    }
}

function updateSleepUI() {
    const maxTime = 30; // 初期時間と合わせる

    // 残り時間が少ないほどゲージが増える
    const percent = ((maxTime - time) / maxTime) * 100;

    const bar = document.getElementById("sleepBar");
    bar.style.width = percent + "%";

    // 色変化（眠くなる感じ）
    if (percent < 40) {
        bar.style.background = "blue";
    } else if (percent < 70) {
        bar.style.background = "orange";
    } else {
        bar.style.background = "red";
    }
}
// =====================
// 入力処理
// =====================
const input = document.getElementById("input"); // 入力欄の要素を取得
const wordEl = document.getElementById("word"); // 単語表示エリアを取得

// 入力が変わるたびに発火するイベント
input.addEventListener("input", function () {

    if (!gameActive) return; // ゲームが開始されていなければ何もしない

    const newTyped = this.value; // 現在入力されている文字列

    // =====================
    // 入力が空になったとき
    // =====================
    if (newTyped.length === 0) {
        typed = ""; // 入力状態をリセット
        render();   // 表示を更新
        return;     // ここで処理終了
    }

    // =====================
    // 入力が正しいかチェック（候補のどれかの先頭と一致するか）
    // =====================
    function normalizeInput(str) {
    return str
        .replace(/nn/g, "n"); // nn → n に統一
}

const ok = candidates.some(c => {
    return c.startsWith(normalizeInput(newTyped));
});
    // =====================
    // ミスした場合
    // =====================
    if (!ok) {

        miss++; // ミス回数を増やす
        document.getElementById("miss").textContent = miss; // 画面に反映
        combo = 0;
caffeine = 0;

        beep(200, 120); // 低い音でミス音を鳴らす

        // MISSと表示
        document.getElementById("missDisplay").innerHTML = '<span class="miss">MISS</span>';

        // ★入力を即座に元に戻す（重要）
        setTimeout(() => {
            input.value = typed;   // 正しい状態に戻す
        }, 0);

        updateCaffeineUI();

        return; // ミスしたので処理終了
    }

    if (newTyped.length > typed.length) {
        correct += (newTyped.length - typed.length);
        document.getElementById("correct").textContent = correct;
    }
    // =====================
    // 正しい入力の場合
    // =====================
    typed = newTyped; // 入力状態を更新

    beep(800, 40); // 軽い入力音

    updateCaffeineUI();

    if (candidates.some(c => c === normalizeInput(typed))) {

        score+=100; // スコア加算
        document.getElementById("score").textContent = score; // 表示更新

        combo++;
    caffeine++;

    if (caffeine >= CAFFEINE_MAX) {
        time += BONUS_TIME;
        updateSleepUI();
        caffeine = 0;

        document.getElementById("status").textContent = `☕ カフェインチャージ +${BONUS_TIME}秒`;
        beep(1200, 150);
    }

    updateCaffeineUI();

        beep(1000, 80); // 成功音

        nextWord(); // 次の単語へ
        return;
    }

    // =====================
    // まだ途中の入力の場合
    // =====================
    render(); // 文字の色分けを更新
});
// =====================
// タイマー
// =====================
timerId = setInterval(() => {

    if (!gameActive) return;

    updateSleepUI();
    time--;
    document.getElementById("time").textContent = time;

    if (time <= 0) {
        endGame();
    }

}, 1000);

// 初期開始
nextWord();
updateSleepUI();
</script>

</body>
</html>
<?php
    session_start();
    require_once "control.php";
    $dbh = db_connect();
    $mode = $_SESSION["mode"] ?? "easy";
    $challenge = $_SESSION["challenge"] ?? 0;
    //ランダムにデータベースの文字を取得
    //イージーもーどなら名称のみ問題文にする
    
    if ($mode === "easy") {
        $stmt = $dbh->query("SELECT name FROM beans ORDER BY RAND()");
        $beans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $dbh->query("SELECT name, info, ruby FROM beans ORDER BY RAND()");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $beans = [];

        //rubyの内容は「。」で区切って取得する
        foreach ($rows as $row) {
            if (!empty($row["name"])) {
                $beans[] = ["name" => $row["name"]];
            }

            if (!empty($row["info"]) && !empty($row["ruby"])) {
                $sentences = preg_split('/。/u', $row["info"]);
                $rubySentences = preg_split('/。/u', $row["ruby"]);

                foreach ($sentences as $index => $sentence) {
                    $sentence = trim($sentence);
                    // 前後の空白を削除
                    $ruby = trim($rubySentences[$index] ?? "");
                    // 対応するrubyを取得（なければ空文字）

                    if ($sentence !== "" && $ruby !== "") {
                        $beans[] = [
                            "name" => $sentence,
                            "ruby" => $ruby
                        ];
                    }
                }
            }
        }

        shuffle($beans);
    }
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
    <body><!--表示内容-->
        <audio id="hitSound" src="material/sounds.wav" preload="auto"></audio>
        <div id="forceHit"></div>
        <h2>寿司打風タイピング</h2>
        <div>           
            眠気😴:
            <div style="width: 1600px;height:20px;background:#ddd;">
                <div id="sleepBar" style="height:20px;width:0%;background:blue;"></div>
            </div>
            <div>
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
            <div id="forceHit"></div>
        </div>
        

        <script>//処理内容
            const beans = <?= json_encode($beans, JSON_UNESCAPED_UNICODE) ?>;
            // PHPの $beans 配列を JavaScriptで使える形に変換して代入
            // PHPの配列をJavaScriptの配列形式に変換 　　\uXXXX 形式に変換せず、そのまま表示
           const GAME_MODE = "<?= $mode ?>";
           //モード選択用

//関数用意ゾーン
            let currentIndex = 0;
            //ローマ字配列の添え字
            let score = 0;
            let correct = 0;
            let miss = 0;
            let time = 30;
            //基本的な情報
            let combo = 0;
            let caffeine = 0;
            const CAFFEINE_MAX = 5; // 何回で発動
            const BONUS_TIME = 3;   // +秒数
            //時間追加処理用
            let missStreak = 0;
            //強制終了用
            let typed = "";
            let candidates = [];
            let salmon = 0;
            //入力処理用
            let gameActive = true;
            let timerId = null;
            let ending = false;
            //状態把握用


//関数定義ゾーン
            let audioCtx = null;

            // 効果音用関数　beep
            function beep(freq = 600, duration = 80) {
            // freq: 音の高さ 　　　duration: 音の長さ
                if (!audioCtx) {
                    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                }
                const ctx = audioCtx;
                // AudioContext（音を扱うためのAPI）を生成
                // ブラウザによっては webkitAudioContext を使う必要があるため両対応
                const osc = ctx.createOscillator();
                // 発振器（音を作る装置）を作成
                const gain = ctx.createGain();
                // 音量を調整するノードを作成
                osc.frequency.value = freq;
                // 周波数（音の高さ）を設定
                gain.gain.value = 0.35;
                osc.connect(gain);
                gain.connect(ctx.destination);
                // 接続：発振器 → 音量 → スピーカー
                osc.start();
                // 音を鳴らし始める

                setTimeout(() => {// 指定時間後に音を止める
                    osc.stop();     // 音を停止
                }, duration);
            }


            function toRomajiForTyping(kana) {
                return kana
                    .split("ー")
                    .map(part => wanakana.toRomaji(part))
                    .join("-");
            }

            // ローマ字表示関数　getRomajiCandidates
            function getRomajiCandidates(kana) {
                let base = toRomajiForTyping(kana);
                //日本語をローマ字にして格納

                // 微調整。表示されるもの自体を直す
                base = base.replace(/dei/g, "dhi");

                let list = [base];

                //判定条件を緩くするための関数　expand
                function expand(pattern, replacement) {
                //置き換えたい文字や正規表現,置き換え後の文字列
                    const newList = [];
                    list.forEach(str => {// list配列の各文字列に対して処理
                        newList.push(str);
                        // 元の文字列をそのまま追加
                        const replaced = str.replace(pattern, replacement);
                        if (replaced !== str) {
                            newList.push(replaced);
                        }
                        // 指定したルールで置き換えた文字列を追加
                    });
                    list = [...new Set(newList)];
                }
                //関数を実行、変換を登録していく
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
                expand(/u-/g, "uu");
                expand(/a-/g, "aa");
                expand(/i-/g, "ii");
        
                expand(/ou/g, "oo");
                expand(/oo/g, "ou");

                expand(/dhi/g, "dei");
                expand(/dhi/g, "deli");

                expand(/nn/g, "n");
                expand(/n/g, "nn");

                return [...new Set(list)];
            }
        

            // 表示関数　render
            function render() {
                const base = candidates[0]; // 表示する正解の文字列
                let html = ""; // 表示用HTMLをためる変数
                // 日本語
                const jp = currentWord;
                html += `<div class="jp">${jp}</div>`;

                // 正解文字列を1文字ずつループ
                for (let i = 0; i < base.length; i++) {
                    if (i < typed.length) {// すでに入力済みの範囲かどうか
                        html += `<span class="correct">${base[i]}</span>`; 
                        // 正解として青く表示（class="correct"）
                    } else
                        html += `<span class="remaining">${base[i]}</span>`;
                        // 変化なし
                    }
                document.getElementById("correct").textContent = correct;
                document.getElementById("word").innerHTML = html;
                // 作成したHTMLを画面に反映
                document.getElementById("missDisplay").textContent = "";
                // 正しい入力時はMISS消す
            }


            // 次の問題へ移行するための関数　nextWord
            function nextWord() {
                currentWord = beans[currentIndex].name;
                // 現在の単語（日本語）を取得
                const currentRuby = beans[currentIndex].ruby ?? currentWord;
                candidates = getRomajiCandidates(currentRuby);
                // 現在の単語からローマ字入力の候補一覧を生成
                // 例：「し」→ ["shi", "si"] など                
                typed = "";
                // 入力済み文字列をリセット                
                document.getElementById("input").value = "";
                // 入力欄（input要素）の表示も空にする                
                render();
                // 画面表示を更新（新しい単語を表示）                
                currentIndex++;
                // インデックスを次へ進める
                if (currentIndex >= beans.length) {// 最後まで行ったら
                    currentIndex = 0;
                    //最初に戻る
                }
            }


            // 終了処理関数（result.phpへ）endGame
            function endGame() {                
                if (ending) return;
                // すでに終了処理が実行されている場合は何もしない（二重実行防止）                
                ending = true;
                // 終了フラグを立てる                
                gameActive = false;
                // ゲームを非アクティブ状態にする（入力などを無効化するため）                
                clearInterval(timerId);
                // タイマーを停止               
                document.getElementById("input").disabled = true;
                // 入力欄を無効化

                // 結果画面へ遷移（GETパラメータでスコアなどを渡す）
                location.href =
                    "4_result.php?score=" + score +     // スコア
                    "&miss=" + miss +                  // ミス回数
                    "&total=" + score +                // 総入力数
                    "&correct=" + correct +              // 正解数
                    "&time=30" +                   // 制限時間
                    "&missStreak=" + missStreak +
                    "&salmon=" + salmon;
            }


            //時間延長機能カフェインの画像用関数　updateCaffeinUI
            function updateCaffeineUI() {
                const container = document.getElementById("caffeineCups");
                // コップを表示するコンテナ要素を取得
                container.innerHTML = "";
                // 一度中身をリセット

                for (let i = 0; i < CAFFEINE_MAX; i++) {
                    const img = document.createElement("img");
                    // 画像要素を作成
                    img.src = "material/4_cup.jpg";
                    // 画像ファイルを指定
                    img.classList.add("cup");
                    // 共通のスタイルクラスを追加

                    //表示するかしないかを条件で管理
                    if (i < caffeine) {
                        img.classList.add("active");
                    }
                    container.appendChild(img);
                    // コンテナに追加して画面に表示
                }
            }


            //制限時間ゲージ反映関数　updateSleepUI
            function updateSleepUI() {
                const maxTime = 30; // 初期時間と合わせる
                const percent = ((maxTime - time) / maxTime) * 100;
                // 残り時間が少ないほどゲージが増える
                const bar = document.getElementById("sleepBar");
                bar.style.width = percent + "%";

                // 色変化
                if (percent < 40) {
                    bar.style.background = "blue";
                } else if (percent < 70) {
                    bar.style.background = "orange";
                } else {
                    bar.style.background = "red";
                }
            }


            //強制終了時のアクション関数　playForceHitAndEnd
            function playForceHitAndEnd() {
                const hit = document.getElementById("forceHit");
                // ヒット演出用の要素を取得
                hit.style.backgroundImage = "url('material/4_salmon.png')";
                //　画像を設定
                hit.classList.add("active");
                // アクティブ状態を付与（表示やアニメーション用）

                // 0.25秒後に揺れ＋音を再生
                setTimeout(() => {
                    document.body.classList.add("shake");
                    // 画面全体に揺れエフェクトを追加
                    const audio = document.getElementById("hitSound");
                    // 効果音の要素を取得
                    audio.currentTime = 0;
                    audio.volume = 1.0;
                    audio.play();

                    // 0.3秒後に揺れを解除
                    setTimeout(() => {
                        document.body.classList.remove("shake");
                    }, 300);

                }, 250);

                // 2秒後にゲーム終了処理を実行
                setTimeout(() => {
                    endGame();
                },2000);
            }


//実行ゾーン
            // 入力処理
            const input = document.getElementById("input"); // 入力欄の要素を取得
            const wordEl = document.getElementById("word"); // 単語表示エリアを取得


            // イベントリスナーを設定（入力が変わるたび）
            input.addEventListener("input", function () {
                if (!gameActive) return; // ゲームが開始されていなければ何もしない

                const newTyped = this.value; // 現在入力されている文字列

                if (newTyped.length === 0) {// 入力が空なら
                    typed = ""; // 入力状態をリセット
                    render();   // 表示を更新
                    return;     // 処理終了
                }

                // 入力が正しいかチェック
                function normalizeInput(str) {
                    return str
                    .replace(/nn/g, "n"); // nn → n に統一
                }

                const ok = candidates.some(c => {
                    return c.startsWith(normalizeInput(newTyped));
                });

                // ミスした場合
                if (!ok) {
                    miss++;         // カウント
                    missStreak++;
                    combo = 0;
                    caffeine = 0;
                    document.getElementById("miss").textContent = miss;
                    // 画面に反映
                    updateCaffeineUI();

                    beep(200, 120);
                    // 低い音でミス音を鳴らす                    
                    document.getElementById("missDisplay").innerHTML = '<span class="miss">MISS</span>';
                    // MISSと表示                    
                    setTimeout(() => {
                        input.value = typed;   // 前に戻す
                    }, 0);
                    // 入力を即座に元に戻す（入力を受け付けない）
                    
                    //ハードモード用処理
                    if (GAME_MODE === "hard" && missStreak >= 3) {//3回連続ミスで強制終了
                        document.getElementById("status").textContent = "💀 連続ミス3回で終了";
                        //メッセージを表示
                        salmon = 1;
                        document.body.classList.add("shake");
                        //揺れをつける
                        setTimeout(() => {
                            document.body.classList.remove("shake");
                        }, 300);
                        //300ミリ秒後に解除
                        playForceHitAndEnd();
                        return;
                    }
                    return;
                }
        
                // 正しい入力の場合                
                const prevTypedLength = typed.length;
                typed = newTyped; 
                // 入力状態を更新
                missStreak = 0;

                //正解数を記録
                if (newTyped.length > prevTypedLength) {//入力を受け付けて、字数が増えているなら
                    correct += (newTyped.length - prevTypedLength);
                    //増えた分を加算
                    document.getElementById("correct").textContent = correct;
                    //表示
                }

                if (candidates.some(c => c === normalizeInput(typed))) {
                //入力された文字列（typed）について、候補リスト（candidates）の中に一致するものがあるかを判定
                    score+=100; // スコア加算
                    combo++;
                    caffeine++;
                    document.getElementById("score").textContent = score; // 表示更新
                    updateCaffeineUI();

                    if (caffeine >= CAFFEINE_MAX) {//一定までcaffeineがたまったら
                        time += BONUS_TIME; //時間を追加
                        updateSleepUI();
                        caffeine = 0;   //リセット            
                        document.getElementById("status").textContent = `☕ カフェインチャージ +${BONUS_TIME}秒`;
                        //表示更新
                        beep(800, 150);
                    }                 

                    nextWord(); 
                    beep(1200, 40);
                     // 成功音
                    return;
                }
                beep(800, 40);
                 // 軽い入力音            
                updateCaffeineUI();
            render(); // 文字の色分けを更新
            });
            

            // タイマー
            timerId = setInterval(() => {
            //一定時間ごとに同じ処理を繰り返し実行するタイマーを設定し、そのIDを timerId に保存
            if (!gameActive) return;//ゲーム実行中なら
                //それぞれ時間経過で処理を更新
                updateSleepUI();
                time--;
                document.getElementById("time").textContent = time;
           
                if (time <= 0) {//時間切れで終了
                    endGame();
                }

            }, 1000);//1秒ごと反映

            // 初期開始
            nextWord();
            updateSleepUI();
        </script>

    </body>
</html>

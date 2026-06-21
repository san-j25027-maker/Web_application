<?php
    session_start();
    require_once "control.php";

    $message = "";
    $allowedModes = ["easy", "normal", "hard"];
    //モード受け取り
    function getSelectedMode(array $allowedModes): string
    {
        $mode = $_POST["mode"] ?? "normal";
        return in_array($mode, $allowedModes, true) ? $mode : "normal";
    }
    function getChallenge(): int
    {
        return isset($_POST["challenge"]) && $_POST["challenge"] === "1" ? 1 : 0;
    }
    //ゲストとしてゲーム開始
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST["guest"])) {
            $_SESSION["user"] = "guest";
            $_SESSION["mode"] = getSelectedMode($allowedModes);
            $_SESSION["challenge"] = getChallenge();

            header("Location: 4_game.php");
            exit;
        }
    }
    //ログインしてゲームを開始
    if (isset($_POST["login"])) {
        $username = $_POST["username"] ?? "";
        $password = $_POST["password"] ?? "";
        $dbh = db_connect();

        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $username, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user"] = $user["username"];
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["mode"] = getSelectedMode($allowedModes);
            $_SESSION["challenge"] = getChallenge();

            header("Location: 4_game.php");
            exit;
        }

        $message = "ログインに失敗しました";
        $dbh = null;
    }
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>タイピングゲーム</title>

        <style>
            body {
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
                background: #222;
                color: white;
                font-family: sans-serif;
            }

            .container {
                width: 350px;
                padding: 40px;
                border-radius: 15px;
                background: #333;
                text-align: center;
            }

            input {
                width: 90%;
                padding: 10px;
                margin: 8px 0;
                border: none;
                border-radius: 5px;
            }

            button {
                padding: 10px;
                margin: 10px 5px;
                border: none;
                border-radius: 5px;
                background: #555;
                color: white;
                cursor: pointer;
            }
            button:hover {
                background: #777;
            }

            .mode-list {
                display: flex;
                gap: 8px;
                justify-content: center;
                margin-bottom: 8px;
            }
            .mode {
                flex: 1;
                margin: 0;
                background: #444;
            }
            .mode.active {
                background: #00aaff;
            }

            .form-button {
                width: 45%;
            }
            .home-button {
                background: #0a952f;
                width: 90%;
            }

            .error {
                min-height: 1.5em;
                color: #ff6b6b;
            }

            .challenge {
                width: 90%;
                background: #444;
            }
            .challenge.on {
                background: #ff9800;
            }

            .info-box {
                position: absolute;
                top: 15px;
                left: 15px;
            }
            .info-label {
                background: #444;
                padding: 8px 20px;
                cursor: pointer;
            }
            .info-content {
                display: none;
                position: absolute;
                text-align: left;
                top: 30px;
                left: 0;
                width: 300px;
                background: #222;
                padding: 10px;
                border-radius: 5px;
                font-size: 14px;
                line-height: 1.5;
            }
            /* ホバーで表示 */
            .info-box:hover .info-content {
                display: block;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <h2>タイピングゲーム</h2>

            <p class="error"><?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?></p>

            <form method="post">
                <div class="info-box">
                    <span class="info-label">info</span>
                    <div class="info-content">
                        このゲームは寿司打のオマージュです。<br> <br>
                        easyモード  ：コーヒー豆の名前を入力<br>
                        normalモード：コーヒー豆の名前or<br>　　　　　　　 コーヒー豆の特徴を入力<br>
                        hardモード  ：さらに連続ミスで強制終了<br> <br>
                        チャレンジ：廃人のためのモード<br>　　　　　　問題文を自分で当ててください。
                    </div>
                </div>
                <div class="mode-list">
                    <button type="button" class="mode" data-mode="easy">イージー</button>
                    <button type="button" class="mode active" data-mode="normal">ノーマル</button>
                    <button type="button" class="mode" data-mode="hard">ハード</button>
                </div>
                <div class="challenge-area">
                    <button type="button" id="challengeBtn" class="challenge off">
                        チャレンジ：OFF
                    </button>
                </div>

                <input type="hidden" name="challenge" id="challenge" value="0">

                <input type="hidden" name="mode" id="mode" value="normal">

                <input type="text" name="username" placeholder="ユーザー名"><br>
                <input type="password" name="password" placeholder="パスワード"><br>

                <button type="submit" class="form-button" name="login">ログイン</button>
                <button type="submit" class="form-button" name="guest">ゲスト</button>
            </form>
            <br>
            <form action="0_home.php">
                <button type="submit" class="home-button">メニューに戻る</button>
            </form>
        </div>

        <script>
            const buttons = document.querySelectorAll(".mode");
            const modeInput = document.getElementById("mode");

            buttons.forEach(button => {
                button.addEventListener("click", () => {
                    buttons.forEach(item => item.classList.remove("active"));
                    button.classList.add("active");
                    modeInput.value = button.dataset.mode;
                });
            });
            const challengeBtn = document.getElementById("challengeBtn");
            const challengeInput = document.getElementById("challenge");

            challengeBtn.addEventListener("click", () => {
                const isOn = challengeInput.value === "1";

                if (isOn) {
                    challengeInput.value = "0";
                    challengeBtn.classList.remove("on");
                    challengeBtn.textContent = "チャレンジ：OFF";
                } else {
                    challengeInput.value = "1";
                    challengeBtn.classList.add("on");
                    challengeBtn.textContent = "チャレンジ：ON";
                }
            });
        </script>
    </body>
</html>

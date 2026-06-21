<?php
session_start();
require_once "control.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ゲスト
    if (isset($_POST["guest"])) {
        $_SESSION["user"] = "guest";

        // モード保存（追加）
        $_SESSION["mode"] = $_POST["mode"] ?? "easy";

        header("Location: 4_game.php");
        exit;
    }

    // ログイン
    if (isset($_POST["login"])) {

        $username = $_POST["username"];
        $password = $_POST["password"];

        $dbh = db_connect();

        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $username, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user"] = $user["username"];
            $_SESSION["user_id"] = $user["id"];

            // モード保存（追加）
            $_SESSION["mode"] = $_POST["mode"] ?? "easy";

            header("Location: 4_game.php");
            exit;
        } else {
            $message = "ログイン失敗";
        }

        $dbh = null;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>タイピングゲーム</title>

<style>
body {
    margin: 0;
    font-family: sans-serif;
    background: #222;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    background: #333;
    padding: 40px;
    border-radius: 15px;
    width: 350px;
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
    width: 45%;
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

/* モードボタン */
.mode {
    width: 30%;
    background: #444;
}

.mode.active {
    background: #00aaff;
}

.error {
    color: #ff6b6b;
}
</style>
</head>

<body>

<div class="container">

<h2>タイピングゲーム</h2>

<p class="error"><?php echo $message; ?></p>

<form method="post">

    <!-- モード選択 -->
    <div>
        <button type="button" class="mode active" data-mode="easy">イージー</button>
        <button type="button" class="mode" data-mode="normal">ノーマル</button>
        <button type="button" class="mode" data-mode="hard">ハード</button>
    </div>

    <!-- 選択されたモードを送信 -->
    <input type="hidden" name="mode" id="mode" value="easy">

    <input type="text" name="username" placeholder="ユーザー名"><br>
    <input type="password" name="password" placeholder="パスワード"><br>

    <button name="login">ログイン</button>
    <button name="guest">ゲスト</button>
</form>

<form action="0_home.php">
    <button>メニューに戻る</button>
</form>

</div>

<script>
// =====================
// モード選択（ラジオ風）
// =====================
const buttons = document.querySelectorAll(".mode");
const modeInput = document.getElementById("mode");

buttons.forEach(btn => {
    btn.addEventListener("click", () => {

        // 全部解除
        buttons.forEach(b => b.classList.remove("active"));

        // 押したものだけ有効
        btn.classList.add("active");

        // hiddenに保存
        modeInput.value = btn.dataset.mode;
    });
});
</script>

</body>
</html>
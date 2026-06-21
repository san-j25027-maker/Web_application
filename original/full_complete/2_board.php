<?php
    session_start();
    require_once("control.php");

    // ログアウト処理
    if(isset($_POST["logout"])) {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            //PHPでセッションにクッキーを使う設定が有効か
            $params = session_get_cookie_params();
            //現在のセッションクッキーの設定を取得

            //削除処理
            setcookie(session_name(), '', time() - 42000,
            //セッションIDのクッキー名を空に、過去の時間を指定してクッキー無効化
                $params["path"], $params["domain"],
                //取得したものを削除
                $params["secure"], 
                //true → HTTPSのときだけクッキー送信
                //false → HTTPでもHTTPSでも送る
                $params["httponly"]);
                //true → JavaScriptから見えない
                //false → document.cookie で取得できる
        }
        session_destroy();
        header("Location: 2_login.php");
        //ログイン画面へ戻る
        exit;
    }

    // ログインチェック
    if(!isset($_SESSION["user"])) {
        echo "ログインしてください";
        exit;
    }

    $dbh = db_connect();

    // 取得済み称号を読み込む
    $badgeOptions = [];
    $selectedBadgeId = "";
    $selectedBadgeImage = "";
    try {
        $stmt = $dbh->prepare("SELECT b.id AS badge_id, b.name AS badge_name, b.image AS badge_image FROM user_badges ub JOIN users u ON u.id = ub.users_id JOIN badges b ON b.id = ub.badges_id WHERE u.username = ? ORDER BY ub.id ASC");
        $stmt->execute([$_SESSION["user"]]);
        $badgeOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // 称号取得に失敗してもページ表示は継続
    }

    //投稿削除処理（自分の投稿のみ削除）
    if (isset($_POST["delete"])) {
        $id = $_POST["delete_id"];
        //削除対象の投稿IDを取得
        $name = $_SESSION["user"];
        //現在ログインしているユーザー名をセッションから取得
        $sql = "DELETE FROM board WHERE id = ? AND name = ?";
        //指定したIDがログインユーザーの投稿なら削除
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$id, $name]);
    }

    //anything追加処理（ゲームリザルトの一言コメント）
    //投稿内容をflag：1（通常コメント）で格納。
    if (isset($_POST["add_anything"])) {
        $content = $_POST["content"];
        $sql = "INSERT INTO anything(content, flag) VALUES(?, 1)";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$content]);
    }

    //投稿処理（掲示板への書き込み）
    if (isset($_POST["submit"])) {//送信ボタン
        //ユーザー名と投稿内容、選ばれてたなら称号を取得
        $name = $_SESSION["user"];
        $message = htmlspecialchars($_POST["message"], ENT_QUOTES);
        if (isset($_POST["badge_id"]) && $_POST["badge_id"] !== "") {
            $selectedBadgeId = intval($_POST["badge_id"]);
        } else {
            $selectedBadgeId = "";
        }

        //バッジが選択さえていたら、対応する画像を取得。
        if ($selectedBadgeId !== "") {
            foreach ($badgeOptions as $badge) {
                if ($badge["badge_id"] == $selectedBadgeId) {
                    $selectedBadgeImage = $badge["badge_image"];
                    break;
                }
            }
            if($badge["badge_id"] == 7) {
                echo '<script>
                    var clickSound = new Audio("material/sounds.wav");
                    clickSound.currentTime = 0;
                    clickSound.play();
                </script>';
            }
        } else {
        $selectedBadgeImage = "";
        }

        $sql = "INSERT INTO board(name, message, badge_id) VALUES(?, ?, ?)";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$name, $message, $selectedBadgeId === "" ? null : $selectedBadgeId]);
    }

    //バナー選択処理
    if (isset($_POST["set_badge"])) {
        $badge_id = $_POST["badge_id"];
        //選択されたバッジIDを取得
        $username = $_SESSION["user"];
        //ログイン中のユーザー名を取得

        // ユーザー名から users テーブルの id を取得する
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_id = $user["id"];

        // 既存のバナーを削除（1つのみ保持）
        $sql = "DELETE FROM user_badges WHERE user_id = ?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$user_id]);

        // 新しいバナーを登録
        $sql = "INSERT INTO user_badges(user_id, badge_id) VALUES(?, ?)";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$user_id, $badge_id]);
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf8">
        <title>掲示板</title>
        <link rel="stylesheet" href="2_style.css?v=<?php echo time(); ?>">
    </head>
    <body>
        <!-- 上部 -->
        <div class="header">
            <h2>掲示板</h2>
            <p>ユーザー：<?php echo htmlspecialchars($_SESSION["user"], ENT_QUOTES); ?></p>
            <form method="post">
                <input type="submit" name="logout" value="ログアウト">
            </form>
        </div>
        <!-- 投稿一覧（スクロール可能）-->
        <div class="messages">
        <?php
            $sql = "SELECT board.*, b.name AS badge_name, b.image AS badge_image FROM board LEFT JOIN badges b ON b.id = board.badge_id ORDER BY board.id";
            //投稿内容、称号バッジ情報を取得
            $stmt = $dbh->query($sql);

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='message'>";
                echo "<div class='message-header'>";
                echo "<b>" . htmlspecialchars($row["name"], ENT_QUOTES) . "</b>";
                //投稿者名を表示
                //あるならバッジ画像を表示
                if (!empty($row["badge_image"])) {
                    echo "<span class='message-badge'><img src='" . htmlspecialchars($row["badge_image"], ENT_QUOTES) . "' alt='称号'></span>";
                }
                echo "</div>";
                echo nl2br(htmlspecialchars($row["message"], ENT_QUOTES));
                //投稿内容を表示

                // ボタン群
                echo "<br>";
                if($row["name"] === $_SESSION["user"]) {
                    //投稿者がログインユーザーと一致する場合、削除ボタンを表示
                    echo "
                    <div class='button'>
                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='delete_id' value='". $row["id"] ."'>
                        <input type='submit' name='delete' value='削除'>
                    </form>
                    </div>
                    ";
                }
                // anything追加ボタン
                echo "
                <div class='button'>
                <form method='post' style='display:inline;'>
                    <input type='hidden' name='content' value='". htmlspecialchars($row["message"], ENT_QUOTES) ."'>
                    <input type='submit' name='add_anything' value='コメントを登録'>
                </form>
                </div>
                ";
                echo "</div>";
            }
        ?>

    </div>

    <!-- 入力欄 -->
    <div class="input-area">
        <form method="post">
            <div class="input-controls">
                <textarea name="message" required></textarea>
                <?php if (!empty($badgeOptions)): ?>
                    <div class="badge-picker">
                        <button type="button" class="badge-picker-button" id="badgePickerButton">＋</button>

                        <!-- 選択中の称号を表示 -->
                        <div class="selected-badge-preview" id="selectedBadgePreview" aria-hidden="<?php echo !empty($selectedBadgeImage) ? 'false' : 'true'; ?>">
                            <?php if (!empty($selectedBadgeImage)): ?>
                                <img src="<?php echo htmlspecialchars($selectedBadgeImage, ENT_QUOTES); ?>" alt="選択済み称号">
                            <?php endif; ?>
                        </div>

                        <input type="hidden" name="badge_id" id="selectedBadgeId" value="<?php echo htmlspecialchars($selectedBadgeId, ENT_QUOTES); ?>">
                        <div class="badge-picker-panel" id="badgePickerPanel" aria-hidden="true">
                            <div class="badge-picker-header">称号を選択</div>
                            <div class="badge-picker-list">
                                <button type="button" class="badge-picker-item<?php echo $selectedBadgeId === '' ? ' selected' : ''; ?>" data-badge-id="" data-badge-image="" aria-pressed="<?php echo $selectedBadgeId === '' ? 'true' : 'false'; ?>">
                                    <span class="badge-picker-none">なし</span>
                                </button>
                                <?php foreach ($badgeOptions as $badge): ?>
                                    <?php $badgeSelected = ((string)$badge['badge_id'] === (string)$selectedBadgeId); ?>
                                    <button type="button" class="badge-picker-item<?php echo $badgeSelected ? ' selected' : ''; ?>" data-badge-id="<?php echo htmlspecialchars($badge['badge_id'], ENT_QUOTES); ?>" data-badge-image="<?php echo htmlspecialchars($badge['badge_image'], ENT_QUOTES); ?>" aria-pressed="<?php echo $badgeSelected ? 'true' : 'false'; ?>">
                                        <?php if (!empty($badge['badge_image'])): ?>
                                            <img src="<?php echo htmlspecialchars($badge['badge_image'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($badge['badge_name'], ENT_QUOTES); ?>">
                                        <?php endif; ?>
                                        <span><?php echo htmlspecialchars($badge['badge_name'], ENT_QUOTES); ?></span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <input type="submit" name="submit" value="送信">
            </div>
        </form>
    </div>

    <script>
        (function(){
            var pickerButton = document.getElementById('badgePickerButton');
            //バッジ選択ボタン
            var pickerPanel = document.getElementById('badgePickerPanel');
            //バッジ一覧パネル
            var selectedBadgeId = document.getElementById('selectedBadgeId');
            //選択されたバッジのIDを保持する
            var selectedBadgePreview = document.getElementById('selectedBadgePreview');
            //洗濯中のバッジを表示する
            if (!pickerButton || !pickerPanel || !selectedBadgeId || !selectedBadgePreview) return;

            // バッジ選択パネルの開閉
            pickerButton.addEventListener('click', function(){
                var isOpen = pickerPanel.classList.toggle('open');
                pickerPanel.setAttribute('aria-hidden', String(!isOpen));
            });

           // バッジをクリックしたときの処理
            pickerPanel.addEventListener('click', function(event){
                var item = event.target.closest('.badge-picker-item');
                //クリックからバッジを取得
                if (!item) return;
                //バッジ以外のクリックでは何もしない。
                var badgeId = item.getAttribute('data-badge-id');
                //バッジIDを取得
                var badgeImage = item.getAttribute('data-badge-image');
                //バッジ画像のアドレスを取得
                selectedBadgeId.value = badgeId;
                //送信用にIDをセット

                pickerPanel.querySelectorAll('.badge-picker-item').forEach(function(button){
                //選択状態の更新
                    var isSelected = button === item;
                    //今クリックされたものか
                    button.classList.toggle('selected', isSelected);
                    //選択中のバッジにだけselectedクラスを付与
                    button.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
                    //反映
                });
                //バッジ選択なし状態の表示
                if (badgeId === '') {
                    pickerButton.textContent = '＋';
                    selectedBadgePreview.innerHTML = '';
                    selectedBadgePreview.setAttribute('aria-hidden', 'true');
                } else {//バッジ選択あり状態
                    if (badgeImage) {//画像があるとき
                        selectedBadgePreview.innerHTML = '<img src="' + badgeImage + '" alt="選択済み称号">';
                        selectedBadgePreview.setAttribute('aria-hidden', 'false');
                    } else {//念のためのエスケープ処理
                        selectedBadgePreview.innerHTML = '';
                        selectedBadgePreview.setAttribute('aria-hidden', 'true');
                    }
                }
                pickerPanel.classList.remove('open');
                //選択後はパネルを閉じる
                pickerPanel.setAttribute('aria-hidden', 'true');
                //非表示状態にする。
            });
        })();
        </script>
    </body>
</html>
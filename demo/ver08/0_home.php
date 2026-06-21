<!DOCTYPE html>

<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>メニュー</title>

    <style>
      body {
        text-align: center;
        font-family: sans-serif;
        background:
        linear-gradient(rgba(255,255,255,0.3), rgba(255,255,255,0.3)),
        url("images2.jpg");
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
      }

      .menu-btn {
        display: block;
        width: 200px;
        margin: 15px auto;
        padding: 10px;
        font-size: 18px;
      }
    </style>

  </head>

  <body>

    <h1>メニュー</h1>
    <!-- 各フォームのURLと名前をセット -->

    <form action="1_add_beans.php">
      <button class="menu-btn">更新フォームへ</button>
    </form>

    <form action="3_test.php">
      <button class="menu-btn">検索フォームへ</button>
    </form>

    <form action="2_login.php">
      <button class="menu-btn">掲示板へ</button>
    </form>

    <form action="4_start.php">
      <button class="menu-btn">タイピングゲームへ</button>
    </form>

  </body>
</html>

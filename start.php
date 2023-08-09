<?php
session_start(); // 新セッション開始（各ページで再開するためにsession_start();が必要）
$_SESSION = array(); // セッション変数を全て解除する
// print('$_SESSION = ');print_r($_SESSION);print('<br/>'); // $_SESSIONを表示
require('./init.php'); // 設定ファイル読込
// print('<br /> $_SESSION = '); print_r($_SESSION); // $_SESSIONを表示
?>

<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>&nbsp;参加者情報の入力</title>
    <script type="text/javascript">
     history.forward(); // ブラウザバックを無効に
    </script>
    <script type="text/javascript" src="js/input.js"></script>
    <link rel="stylesheet" href="mystyle.css" type="text/css">
  </head>
  <body>
    <?php
    echo $parti_consent; // 参加の承諾文 in init.php
    ?>
    <h2>&nbsp;参加者情報</h2>
    <p>
      あなたに関する情報を入力して下さい．<br>
      入力がすんだら，間違いがないか確認して，[次ページ]ボタンをクリックして下さい．</p>
    <form id="form" action="instruction.php" method="POST">
      <input type="hidden" name = "log_file_name" id="log_file_name">
      <input type="hidden" name = "time_stamp" id="time_stamp">
      <p>
        ● ID：
        <input type="text" name="sid" required>
      </p>
      <p>
        ● 性別：
        <input type="radio" name="gender" value="male" id="gender_label1" required><label for="gender_label1">男</label>
        <input type="radio" name="gender" value="female" id="gender_label2" required><label for="gender_label2">女</label>
        <!-- <input type="radio" name="gender" value="non" id="gender_label3" required> <label for="gender_label3">無回答</label> -->
      </p>
      <p>
        ● 年齢（半角数字．例：25）：
        <input type="number" name="age" min="10" max="100" required>
      </p>
      <p>
        ● 職業：
	<select name="job" required>
	  <option value="放射線科専門医">放射線科専門医</option>
	  <option value="他科専門医">他科専門医</option>
	  <option value="放射線科後期研修医">放射線科後期研修医</option>
	  <option value="他科後期研修医">他科後期研修医</option>
	  <option value="初期研修医">初期研修医</option>
	  <option value="診療放射線技師">診療放射線技師</option>
	  <option value="学生">学生</option>
	</select>
      </p>
      <p>
        <button type="button" name="send" onClick="reload_win()">
          次ページ</button>
      </p>
    </form>
  </body>
</html>

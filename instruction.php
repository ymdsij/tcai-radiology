<?php
ini_set('display_errors', 1); // エラー出力する場合
require('./init.php'); // 設定ファイル読込
session_start(); // セッションの開始
// セッションのキー設定
$_SESSION["r_count"] = 0; // r_count 実験の何ページ目（何問目）かを 0 に初期化
// 総刺激数を初期化(一対比較ではあとでペア数で更新)
$_SESSION["stimuli_no"] = count($stimuli_file_names) - $execise_no; //stimuli_no 本実験の刺激（ページ）数
$_SESSION["log_file_name"] = $_POST["log_file_name"]; // ログファイル名 log_file_name 代入
$_SESSION["subject_info"] = // 参加者情報 subject_info 代入
  $_POST["time_stamp"] . ',' . $_POST["sid"] . ',' . $_POST["age"] . ',' . $_POST["gender"] . ',' . $_POST["job"];
$_SESSION["cue_flag"] = []; // 較正キュー
$_SESSION["rlog"] = []; // 実験結果データ 配列変数
$file_name = "./log/" . $_SESSION["log_file_name"]; //ログファイル名を$file_nameに設定
file_put_contents($file_name, $_SESSION["subject_info"]); // ファイル開閉して書き込み
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <title>実験の説明</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta http-equiv="Content-Script-Type" content="text/javascript">
    <link rel="stylesheet" href="mystyle.css" type="text/css">
    <script type="text/javascript">
     history.forward(); // ブラウズバック禁止
    </script>
  </head>
  <body>
    <div id="header">
      &nbsp;実験の説明
    </div>
    <div id="body">
      <?php
      echo $parti_instruction; // init.php からの読込み
      ?>
      <p><form method="POST" action="questionnaire.php">
        <input type="submit" value="練習開始"></form>
      </p>
    </div>
  </body>
</html>

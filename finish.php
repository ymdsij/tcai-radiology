<?php
//ini_set('display_errors', 1); // エラー出力する場合
require('./init.php'); // 設定ファイル読込
$_SESSION = array(); //空の配列作成でセッション変数を全削除
if (isset($_COOKIE[session_name()])) {
  setcookie(session_name(), '', time()-42000, '/'); //セッションクッキーの削除
}
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Script-Type" content="text/javascript">
    <link rel="stylesheet" href="mystyle.css" type="text/css">
    <script type="text/javascript">
     history.forward(); //ブラウザの戻るボタンを無効にする
    </script>
    <title>実験終了</title>
  </head>
  <body>
    <div id="header">
      実験終了
    </div>
    <div id="body">
      <?php
      echo <<<EOT
      実験は以上です．<br/>
      ご協力ありがとうございました．<br/><br/>
EOT;
      ?>
    </div>
    <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
  </body>
  <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
</html>

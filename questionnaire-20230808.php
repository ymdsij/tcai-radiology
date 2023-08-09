<?php

ini_set('display_errors', 1); // エラー出力する場合
session_start(); // セッション再開（start.phpでsession_start()した$_SESSIONを再開）
require('./init.php'); // 設定ファイル読込
if($debug_mode) {
  print('$_SESSION = ');print_r($_SESSION);print('<br>');} // $_SESSIONを表示
// 変数定義
$cue_flag = 0; //較正キューフラグ
if (!isset($_SESSION["cflag"])) {$_SESSION["cflag"] = 10; } // セッション変数cflagの初期化（未設定なら）

if($debug_mode) {
  print('<br>$stimuli_file_names = '); print_r($stimuli_file_names);
  echo '<br>$_SESSION["stimuli_no"] = ', $_SESSION["stimuli_no"], ', $select_no = ', $select_no;
}

// 関数定義 
function save_log($data_no, $q_no) { // データのファイル保存：読影データ数$data_no, アンケート数$q_no
  //インデックス：画像ファイル名,Q1答(人-認識あり1,人-認識なし,AI),較正キューフラグ,ページ滞在時間(sec)
  $rindex = "\nimage,selection,confidence,cue-flag,time\n";
  $r = ""; // 結果リスト
  //人-AI選択：人 0, AI 1, 確信度：0-100，較正キュー提示1/非提示0，滞在時間 sec，
  for ($i = 0; $i < $data_no; $i++) { // 結果リスト作成 $i:画像番号
    $s_filename = $_SESSION["result_log"][$i]["stimuli"];
    $r = $r . $s_filename . ',' . $_SESSION["result_log"][$i]["q1"] . ',' . $_SESSION["result_log"][$i]["r1"] . ',' . $_SESSION["cue_flag"][$i] . ',' . $_SESSION["time_on_page"][$i] . "\n";
  }
  $rq = ""; // アンケート結果リストを文字列に
  for ($i = 1; $i <= $q_no; $i++) {
    $rq = $rq . $_SESSION["result_log"]["answer"][$i] . ','; //answerはアンケート回答のみ
  }
  $fd1 = $_SESSION["result_log"]["t1"];
  $fd1 = str_replace("\r\n", '', $fd1);
  $fd2 = $_SESSION["result_log"]["t2"];
  $fd2 = str_replace("\r\n", '', $fd2);
  $rq = $rq . $fd1 . ',' . $fd2 . ',' . $_SESSION["time_on_page"][$data_no] . "\n"; //最後に自由記述と時間
  $file_name = "./log/" . $_SESSION["log_file_name"]; // ファイル開閉，書き込み
  $contents = $rindex . $r . $rq; // すべての結果を文字列に連結
  file_put_contents($file_name, $contents, FILE_APPEND | LOCK_EX);
}

// **** メインプログラム **** フルHD 1920×1080

if(!isset($_SESSION["r_count"]))
  print("<br><br>スタートページから開始してください．");
// 刺激の順序リスト$_SESSION["s_seq"][n] = n個目の刺激ファイル名

if ($_POST) { // [次のページ]をクリックsubmit時に走る（各読影ページの入力データをセッション変数に格納）
  if($debug_mode) {echo '<br>** POST発火 **'; }
  if($_SESSION["r_count"] >= $execise_no + $select_no) {  //最終ラウンドならアンケートページの処理
    for ($i = 1; $i <= count($ques_list); $i++) { // 各アンケート回答を格納
      $_SESSION["result_log"]["answer"][$i] = $_REQUEST["a$i"];
    }
    $_SESSION["result_log"]["t1"] = $_REQUEST["t1"]; //自由記述1を格納
    $_SESSION["result_log"]["t2"] = $_REQUEST["t2"]; //自由記述2を格納
    // 経過時間を代入
    $start_time = $_SESSION["time_on_page"][$_SESSION["r_count"]];
    $finish_time = microtime(true);
    $_SESSION["time_on_page"][$_SESSION["r_count"]] =
      round($finish_time - $start_time, 2); // 小数点以下2桁で丸める
    // print('$_SESSION = ');print_r($_SESSION);print('<br>'); // $_SESSIONを表示 
    save_log($execise_no + $select_no, count($ques_list)); // ログをファイル保存
    header("Location:finish.php"); // 終了ページへリダイレクト
    exit; // EXIT
  } else { // 読影ページならセッション変数へ値の格納
    // q1の答えを$_SESSION["result_log"][$_SESSION["r_count"]]["q1"]へ代入
    $aq1 = $_REQUEST["q1"];
    $_SESSION["result_log"][$_SESSION["r_count"]]["q1"] = $aq1;
    // r1の答えを$_SESSION["result_log"][$_SESSION["r_count"]]["r1"]へ代入
    $_SESSION["result_log"][$_SESSION["r_count"]]["r1"] = $_REQUEST["r1"];
    // *********************************************
    $tmp = $_SESSION["cflag"];
    
    // print('<script type="text/javascript">alert("キューフラグ計算：q1="+$aq1+", cflag_session="+$tmp);</script>');
    if ($aq1 == $_SESSION["cflag"]) {$cue_flag = 1; } // 過信／不信フラグ設定
    $_SESSION["cue_flag"][$_SESSION["r_count"]] = $cue_flag; //cue_flagキューありなしの格納
    // *********************************************
    $stimuli = "";
    if ($_SESSION["r_count"] < $execise_no + $select_no) {
      $stimuli = $_SESSION["s_seq"][$_SESSION["r_count"]];
    }
    $_SESSION["result_log"][$_SESSION["r_count"]]["stimuli"] = $stimuli; //代入
    if($debug_mode) {
      echo '<br> $_SESSION["result_log"][', $_SESSION["r_count"], ']["stimuli"] = ';
      print_r($_SESSION["result_log"][$_SESSION["r_count"]]["stimuli"]);}
    //}
    // 経過時間を代入
    $start_time = $_SESSION["time_on_page"][$_SESSION["r_count"]];
    $finish_time = microtime(true);
    if($debug_mode) {
      $sarrTime = explode('.', $start_time); //microtimeを.で分割
      $farrTime = explode('.', $finish_time); //microtimeを.で分割
      echo '<br>Start_time = ', date('Y-m-d H:i:s', $sarrTime[0]), ' ', $sarrTime[1], '<br>Finish_time = ', date('Y-m-d H:i:s', $farrTime[0]), ' ', $farrTime[1], '<br>Shori_time = ', round($finish_time - $start_time, 2);
    }
    $_SESSION["time_on_page"][$_SESSION["r_count"]] =
      round($finish_time - $start_time, 2); // 小数点以下2桁で丸める
  }
  /* 最終ラウンドかチェック
   * if($_SESSION["r_count"] >= $execise_no + $select_no) {
   *   save_log($execise_no + $select_no); // ログをファイル保存
   *   header("Location:finish.php"); // 終了ページへリダイレクト
   *   exit; // EXIT
   * } */
  if(isset($_SESSION["r_count"])) {
    $_SESSION["r_count"]++;
    if($debug_mode) {
      echo '<br>$_SESSION[\"r_count\"]をインクリメント = ',$_SESSION["r_count"];}
  } //$_SESSION["r_count"]をインクリメント
}

if($_SESSION["r_count"] == 0){ // 最初の試行で初期設定
  $_SESSION["s_seq"] = $stimuli_file_names; //s_seqを刺激ファイル名リストで初期化
  if($debug_mode) {
    print('<br><br> checked_r_count = '); print_r($_SESSION["r_count"]);
    print('<br><br> $_SESSION["s_seq"] = '); print_r($_SESSION["s_seq"]);}
  // クリックログをすべて0で初期化
  $sbutton_no = 1;
  for($i = 0; $i < $_SESSION["stimuli_no"]; $i++) {
    for($j = 0; $j < $sbutton_no; $j++) {
      $_SESSION["click_log"][$i][$j] = 0;
    }
  }
}

?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Script-Type" content="text/javascript">
    <link rel="stylesheet" href="mystyle.css" type="text/css">
    <!-- <link rel="stylesheet" href="hue.css"> -->
    <title>読影</title>
    <!-- <script type="text/javascript" src="js/jquery-3.3.1.min.js"></script> -->
    <!-- <link rel="stylesheet" href="js/jquery.range.css"> -->
    <!-- <script src="js/jquery.range.js"></script> -->
    <script src="js/click-log.js"></script>
    <script type="text/javascript">
     history.forward(); //ブラウザの戻るボタンを無効に
     var number_of_q = "<?= $select_no ; ?>";
     var clicked_0 = 0; var clicked_1 = 0;

     function get_checked_value (id) {
       var qlist = document.getElementsByName(id);
       for(var i = 0 ; i < qlist.length ; i++){
         if (qlist[i].checked == true){
           return qlist[i].value;
         }
       }
       return null;
     }

     function checkForm(){
       var q_value = new Array();
       for(i=1; i<=number_of_q; i++){
         var str = "q" + i;
         q_value[i] = get_checked_value(str);
       }
       var msg = "以下の質問の入力を確認してください．\n";
       var msg_flag = 0;
       for(i=1; i <= number_of_q; i++){
         if(!q_value[i]) {
	   msg = msg + "Q" + i + ", ";
	   msg_flag = 1;
         };
       }
       msg = msg.substring(1,msg.length - 2);
       if (msg_flag) {alert(msg); return false;}
       return true;
     }
    </script>
  </head>
    
<?php
  // スタートタイム設定
  $_SESSION["time_on_page"][$_SESSION["r_count"]] = microtime(true);
  // *********************
  if ($_SESSION["r_count"] < $execise_no + $select_no) {
    $sfile0 = $_SESSION["s_seq"][$_SESSION["r_count"]];
  } else {$sfile0 = "";
  }
  if ($debug_mode) {
    print('<br> r_count = '); print_r($_SESSION["r_count"]);
    print('<br> $sfile0 = '); print_r($sfile0);
    }
  $rc = $_SESSION["r_count"] - $execise_no + 1; // 読影回数
  if ($quest_flag) {$rc = $select_no + 10; } // $quest_flagでアンケートページへ
  if ($rc <= $select_no) { //読影終了でなければ，読影ページを続ける
  print <<<EOT
    <div class="mylayout">
    <div class="topbox"></div>
    <div class="lbox">
EOT;
      // X線画像の表示
      print "<center><img id=\"xray\" src=\"image-xray/${sfile0}\" width=\"{$image_width}\"></center>";
      print <<<EOT
    </div>
    <div class="rbox1">
    <div id="header">
EOT;
    $sti_name = $_SESSION["stimuli_no"];
    if ($_SESSION["r_count"] < $execise_no) { // 練習ページの場合
      echo "&nbsp;練習&nbsp;", $_SESSION["r_count"] + 1, "/${execise_no}問";
    } elseif ($_SESSION["r_count"] < $execise_no + $select_no) { // 読影本番ページの場合
      echo "&nbsp;実験&nbsp;", $_SESSION["r_count"] - $execise_no + 1, "/${sti_name}問";
      if ($_SESSION["r_count"] - $execise_no + 1 == 1) {
	echo "&nbsp; <font color=\"#ff1900\">ここから本番です！&nbsp;</font>";
      }
    } else {
      echo "&nbsp;実験に関するアンケート";
    }
    print <<<EOT
    </div>
    </div>
    <div class="rbox2">
EOT;
    print "$toi_explanation"; // 各問の説明を表示
    print <<<EOT
    </div>
    <div class="rbox3">
EOT;
      // タコメータ表示（AIの感度・特異度）

      if (preg_match("/^pn/", $sfile0) | preg_match("/^nn/", $sfile0)) { //
      print "<br><center><img id=\"taco\" src=\"image/taco-meter90.png\" width=\"100\"></center>";
      } else {
      print "<br><center><img id=\"taco\" src=\"image/taco-meter60.png\" width=\"100\"></center>";
      }
      print <<<EOT
      <center><small>【 AIの確信度 】</small></center>
      </div>
EOT;
      }
      else { //読影終了後のアンケートページへ
      print <<<EOT
	<body>
	<div id="header">
	  &nbsp;実験に関するアンケート
	</div>
	<div id="body">
	<p>
	　以下のアンケートにお答え下さい．<br>
EOT;
       print '<form name="questions" method="post" action="" onSubmit="">'; //入力チェックに必要
       $n = 1;
       // print("ques_list = "); print_r($ques_list); print("\n");
       foreach ($ques_list as [$k, $ques]) {
       print('【問'); print_r($n); print('】'); print_r($ques); print('<br>'); // 問$n 質問を表示
       if ($k == 7) {
	  print <<<EOT
       <table border="0" align="center" width="1000" margin="100">
       <tr style="font-size:12px"; align="center">
       <tr style="font-size:14px"; align="center">
       <td width="100">まったくそう思わない</td>
       <td width="100">そうは思わない</td>
       <td width="100">あまりそうは思わない</td>
       <td width="100">どちらでもない</td>
       <td width="100">ややそう思う</td>
       <td width="100">そう思う</td>
       <td width="100">非常にそう思う</td>
       </tr>
       <tr align="center">
       <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td>
       <td>7</td>
       </tr>
       <tr align="center">
       <div class="radio">
       <td><input type="radio" name="a$n" value="1" required></td>
       <td><input type="radio" name="a$n" value="2" required></td>
       <td><input type="radio" name="a$n" value="3" required></td>
       <td><input type="radio" name="a$n" value="4" required></td>
       <td><input type="radio" name="a$n" value="5" required></td>
       <td><input type="radio" name="a$n" value="6" required></td>
       <td><input type="radio" name="a$n" value="7" required></td>
       </div>
       </tr>
       </table><br>
EOT;
       }
       if ($k == 3) {
       print <<<EOT
       <table border="0" align="center">
       <tr style="font-size:12px"; align="center">
       <tr style="font-size:14px"; align="center">
       <td width="100">まったく見なかった</td>
       <td width="100">時々見た</td>
       <td width="100">読影毎に見た．</td>
       </tr>
       <tr align="center">
       <div class="radio">
       <td><input type="radio" name="a$n" value="1" required></td>
       <td><input type="radio" name="a$n" value="2" required></td>
       <td><input type="radio" name="a$n" value="3" required></td>
       </div>
       </tr>
       </table><br>
EOT;
       }
       $n++;
       }
	if ($tcai_flag) {
       print <<<EOT
       【自由記述】途中で『この選択はよくないかも知れません』というアラートが表示されていた場合，それをどのように解釈しましたか？下記に回答下さい．（句読点をお願いします．改行OK．）<br>もし表示されなかった場合は，空欄のままで結構です．<br><br>
	 <center><textarea rows="8" cols="80" name="t1"></textarea></center> <br>
EOT;
	}
       print <<<EOT
       【自由記述】この実験についてのご意見，感想をお書き下さい．（句読点をお願いします．改行OK．）<br><br>
	 <center><textarea rows="8" cols="80" name="t2" minlength="8" required></textarea></center>
EOT;
       }
       print '<form name="questions" method="post" action="" onSubmit="">';
	 if ($_SESSION["r_count"] < $execise_no + $select_no && !$quest_flag) { // 練習or本読影でテーブル表示
	 print <<<EOT
	 <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
	 <div class="rbox4">
      <table>
	<tr>
	  <th>
	    <div class="radio">
	      <input id="radio-AI0" value="0" name="q1" type="radio" required>
	      <label for="radio-AI0" class="radio-label">自分で読影する</label>
	    </div>
	  </th>
	  <td align="right">
	    結節が存在しない
	  </td>
	  <td align="center">
          <input type="range" value="50" min="0" max="100" step="1" name="r1">
	  </td>
	  <td align="left">
	    結節が存在する
	  </td>
	</tr>
	<tr>
	  <th>
	    <div class="radio">
	      <input id="radio-AI1" value="1" name="q1" type="radio" required>
	      <label for="radio-AI1" class="radio-label">AIに読影を任せる</label>
	    </div>
	  </th>
	  <td>
	  </td>
	  <td>
	  </td>
	  <td>
	  </td>
	</tr>
      </table>
      <br>
      <table>
	<th>
	  <script src="js/jquery.range.init.js"></script>
	  <script src="js/sweetalert.min.js"></script>
EOT;
	// 較正キュー計算
	$rc = $_SESSION["r_count"] + 1 - $execise_no; // $rc 本番読影済番号1～
	$half = $select_no / 2; //前半(後半)の画像数
	
	if ($rc <= $half && $mav_win <= $rc || ($half + $mav_win) <= $rc) {
	  // 窓データ数チェック 前半/後半で窓幅-1データ有無
	  $ai_sum = 0; // $_SESSION["r_count"] = 練習＋本番終了読影数(0～)
	  for ($i = $_SESSION["r_count"] -1; $i > $_SESSION["r_count"] - $mav_win; $i--) { //AI選択回数$ai_sum計算
	    /* if ($_SESSION["result_log"][$i]["q1"] == 1) { // AIを選択 q1 == 1
	       $ai_sum++;} //人選択で1増加 直近のウインドウ幅-1 での$ai_sum */
	    $ai_sum = $ai_sum + $_SESSION["result_log"][$i]["q1"]; // AIを選択 q1 == 1
	  }
	  $half_win = floor($mav_win / 2); // 窓幅半分切り捨て整数 1
	  // if ($rc <= $select_no) {// 全画像数以下
          /* print <<<EOT
	     <script type="text/javascript">
	   * alert("ab_ba= "+$ab_ba+" rc="+$rc+" half="+$half+" ai_sum="+$ai_sum+" half_win="+$half_win);
	   * </script>
	     EOT; */
	     print <<<EOT
	    <script type="text/javascript">

function TcCue(h){ 
/*   alert("TcCue("+h+")"); */ 
              var elements = document.getElementsByName("q1") ; // 要素を取
得 
              for (var sel="", i = elements.length; i--;) { // 選択状態の値
をselに取得 
    if (elements[i].checked) { 
      var sel = elements[i].value ; 
      break;} 
              } 
/*               alert("sel= " + sel + " h= " + h); */ 
              if (sel == h) { 
                if (sel == 0) { 
                  swal("この選択はよくないかも知れません");  
                } 
                else { 
                    swal("この選択はよくないかも知れません"); 
                } // sel==1 
                elements[0].disabled = true; 
                elements[1].disabled = true; 
                } 
      } 

	    /* function TcCue(h){
/*   alert("TcCue("+h+")"); */
	      var elements = document.getElementsByName("q1") ; // 要素を取得
	      for (var sel="", i = elements.length; i--;) { // 選択状態の値をselに取得
		if (elements[i].checked) {
		  var sel = elements[i].value ;
		  break;}
	      }
/*		 alert("sel= " + sel + " h= " + h); */
	      if (sel == h) {
		if (sel == 0) {swal("この選択はよくないかも知れません"); }
		  else {swal("この選択はよくないかも知れません");} // sel==1
		 }
	    } */

	    </script>
EOT;
	  // AB & 前半 & (不信)AI選択回数 <= 窓幅半分 or BA & 後半 & (不信)AI選択 <= 窓幅半分+1)
	  if ($ab_ba == 0 && $rc <= $half && $ai_sum <= $half_win ||
	      $ab_ba == 1 && $rc > $half && $ai_sum <= $half_win +1) {
	    // 次に人間を選択する 0 と校正キュー提示 TcCue(0)
	    $cue_flag = 0; // この選択が人間(0)なら不信判定で校正キュー提示 TcCue(0)：$cue_flag=0をセッションに格納
	    $_SESSION["cflag"] = $cue_flag;
	    $cflag2 = $_SESSION["cflag"];
	    // print('<script type="text/javascript"> alert("不信か？ cue_flag="+$cue_flag+" cflag_session="+$cflag2);</script>');
	    if($tcai_flag) {
	    print('<label><input type="checkbox" name="c1" value="1" required onclick="TcCue(0)"> この選択・評定で決定</label>');
	    } else {
	      print('<label><input type="checkbox" name="c1" value="1" required> この選択・評定で決定</label>');
	    }
	  } elseif ($ab_ba == 0 && $rc > $half && $ai_sum >= $half_win +1 ||
		    $ab_ba == 1 && $rc <= $half && $ai_sum >= $half_win) {
	    // AB & 後半 & (過信)AI選択回数 >= 窓幅半分+1) or BA & (前半 & (過信)AI選択 >= 窓幅半分
	    $cue_flag = 1; // この選択がAI(1)なら過信判定で校正キュー提示 TcCue(1)：$cue_flag=1をセッションに格納
	    $_SESSION["cflag"] = $cue_flag;
	    $cflag2 = $_SESSION["cflag"];
	    print('<script type="text/javascript">alert("過信か? cue_flag="+$cue_flag+" cflag_session="+$cflag2);</script>');
	    if($tcai_flag) {
	      print('<label><input type="checkbox" name="c1" value="1" required onclick="TcCue(1)"> この選択・評定で決定</label>');
	    } else {
	      print('<label><input type="checkbox" name="c1" value="1" required> この選択・評定で決定</label>');
	    }
	    } else {
	    $cue_flag = 0; // キュー非表示 フラグを0に
	    // print('<script type="text/javascript"> alert("過信なし&不信なし"); </script>');
	    print('<label><input type="checkbox" name="c1" value="1" required> この選択・評定で決定</label>');
	    }
	    print <<<EOT
	</th>
       </tr>
      </table>
</div>
EOT;
      } else {
	$cue_flag = 0; // キュー非表示 フラグを0に
	// print('<script type="text/javascript"> alert("移動平均のデータ不足"); </script>');
	print <<<EOT
	  <label><input type="checkbox" name="c1" value="1" required> この選択・評定で決定</label>
	  </th>
	  </tr>
       </table>
</div>
EOT;
}
}

?>
  <br><center><input type="submit" value="　　次ページ　　" name="submit"></center>
<?php
  if ($_SESSION["r_count"] - $execise_no + 2 == 1) {
  echo "<br><center><font color=\"#ff1900\">【練習はこれで終わりで，次ページから本番です！】&nbsp;</font></center>";
  }
?>
	</p>
	</div>
  </body>
</html>

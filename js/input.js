/* 変数宣言*/
var log_file_name;
var gender;
var age;
var sid;
var time_stamp;

$('.single-slider').jRange({
    from: -2.0,
    to: 2.0,
    step: 0.5,
    scale: [-2.0,-1.0,0.0,1.0,2.0],
    format: '%s',
    width: 300,
    showLabels: true,
    snap: true
});

function reload_win() { //
    gender = document.getElementsByName("gender"); // このページのgenderボタンの値取得
    age = document.getElementsByName("age"); // このページのageボタンの値取得
    sid = document.getElementsByName("sid"); // このページのsidボタンの値取得
    // alert(sid[0].value);
    var msg = ""; /* メッセージ文字列初期化 */
    if (!sid[0].value)
    {var msg = msg + "[ID] が入力されていません．\n"; msg_flag = 1}
    var msg_flag = 0; /* メッセージフラグ初期化 1:アラート 0:アラートなし */
    if (!gender[0].checked && !gender[1].checked && !gender[2].checked)
    {var msg = msg + "[性別] がチェックされていません．\n"; msg_flag = 1}
    if (!age[0].value)
    {var msg = msg + "[年齢] が入力されていません．\n"; msg_flag = 1}
    if (!/^[0-9]+$/.test(age[0].value))
    {var msg = msg + "[年齢] をすべて半角数字にして下さい．あほ \n"; msg_flag = 1}
    if (msg_flag) {alert(msg); return;} // メッセージフラグをチェックしてアラート，リターン
    if (gender[0].checked) {gender = "m" // gender値の代入
			   } else if(gender[1].checked) {gender = "f"
							} else {gender = "n"}
    set_file_name(); // ファイル名，ファイルの準備
    document.getElementById("log_file_name").value = log_file_name;
    document.getElementById("time_stamp").value = time_stamp;
    document.getElementById("form").submit(); // ページ遷移か？
}

function set_file_name() {
  var nowdate = new Date();
  //  alert(nowdate);
  var year = String(nowdate.getFullYear()); // 年
  var mon  = nowdate.getMonth() + 1; // 月
  if (mon < 10) {mon = "0"+String(mon);} else {mon = String(mon);}
  var day = nowdate.getDate(); // 日
  if (day < 10) {day = "0"+String(day);} else {day = String(day);}
  var hour = nowdate.getHours(); // 時
  if (hour < 10) {hour = "0"+String(hour);} else {date = String(hour);}
  var min  = nowdate.getMinutes(); // 分
  if (min < 10) {min = "0"+String(min);} else {min = String(min);}
  var sec  = nowdate.getSeconds(); // 秒
  if (sec < 10) {sec = "0"+String(sec);} else {sec = String(sec);}
  var msec  = nowdate.getMilliseconds(); // ミリ秒
  if (msec < 10) {msec = "00"+String(msec);}
  else if (msec < 100) {msec = "0"+String(msec);} else {msec = String(msec);}
  log_file_name = sid[0].value+"-"+year+mon+day+"-"+hour+"-"+min+"-"+sec+"-"+msec+"-"+gender+age[0].value+".csv";
  time_stamp = year+"/"+mon+"/"+day+" "+hour+":"+min+":"+sec+" "+msec;
}


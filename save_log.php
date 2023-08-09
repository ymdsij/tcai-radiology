<?php
session_start(); // セッション開始
if(isset($_POST['b_no'])) {
  switch ($_POST['b_no']) {
    case '0':
      //      echo 'Click-1';
      $_SESSION["click_log"][$_SESSION["r_count"]][0] = 1; break;
    case '1':
      //      echo 'Click-2';
      $_SESSION["click_log"][$_SESSION["r_count"]][1] = 1; break;
  }
};
?>

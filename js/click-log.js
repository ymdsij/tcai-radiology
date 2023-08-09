
function ClickLog(x){
  //  alert("b_no = " + x);
  //  var b_no = x;
  $.ajax({
    url: 'save_log.php',
    type: 'POST',
    data: {
      b_no : x
    },
    dataType: 'text'
  })
   .done(function() {
     //    alert("clicked button = "+ data);
     //     alert(data);
   })
   .fail(function() {
    alert("失敗");
  });
}

<?php
require_once('Connections/koneksi.php'); 
if (!isset($_SESSION)) {
session_start();
}
$username = $_SESSION['MM_Username'];

mysql_select_db($database_koneksi, $koneksi);
$query_Recordset1 = "SELECT sisawaktu, sisawaktus from userujian where username ='$username'";
$Recordset1 = mysql_query($query_Recordset1, $koneksi) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);

if (isset($_SESSION["mins"])){
$mins = $_SESSION["mins"];
$secs = $_SESSION["secs"];
} else {
$mins = $row_Recordset1['sisawaktu'];
$secs = $row_Recordset1['sisawaktus'];
$_SESSION["mins"] = $row_Recordset1['sisawaktu'];
$_SESSION["secs"] = $row_Recordset1['sisawaktus'];
}
?>
<html>
<head>
<style>
#txt {
border:2px solid red;
font-family:verdana;
font-size:16pt;
font-weight:bold;
background: #FECFC7;
width:120px;
text-align:center;
}
</style>
</head>
<body>
<form name="cd">
<input id="txt" name="txt" type="text" readonly="true">
</form>
<script>
var xmlhttp = false;
var mins = "<?php echo $mins ?>";
var secs = "<?php echo $secs ?>";
var secs1 = secs;
if(secs < 10){secs = "0" + secs;}
document.getElementById("txt").value = mins + ":" + secs;
secs = secs1;
var cd = setInterval("count_down_timer()",1000);
function count_down_timer()
{
 if(secs == 0 && mins <= 0)
 {
  clearInterval(cd);
     window.location = "selesai.php";
  }
 else{if(secs == 0){secs = 59;mins--;}}
 if(secs < 10){secs = "0" + secs;}
 var tm = mins + ":" + secs;
 document.getElementById("txt").value = tm;
 send_request(mins,secs);
 secs--;
 }
function createRequest()
{
  if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
    try {
        xmlhttp = new XMLHttpRequest();
    } catch (e) {
        xmlhttp=false;
    }
 }
 if (!xmlhttp && window.createRequest) {
    try {
        xmlhttp = window.createRequest();
    } catch (e) {
        xmlhttp=false;
    }
  }
 return xmlhttp;
 }
function send_request(m,s)
{
 var conn = createRequest();
 var url="update_session.php?min=" + m + "&sec=" + s;
 conn.open("GET", url);
 conn.send(null);
}
</script>
</body>
</html>
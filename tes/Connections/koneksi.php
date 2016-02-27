<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_koneksi = "localhost";
$database_koneksi = "pmb";
$username_koneksi = "root";
$password_koneksi = "maskandar123";
$koneksi = mysql_pconnect($hostname_koneksi, $username_koneksi, $password_koneksi) or trigger_error(mysql_error(),E_USER_ERROR); 

$pdoc=null;
try{
    $pdoc=new PDO("mysql:host=$hostname_koneksi;dbname=$database_koneksi",$username_koneksi,$password_koneksi);
    $pdoc->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

}
catch(Exception $e){
    //throw new Exception('Tidak bisa menyambung ke database');
    echo "Error: Tidak bisa menyambung ke database";
    exit;
}

$hosted = "http://".$_SERVER['HTTP_HOST']."/tes/";
$hosted2 = "http://".$_SERVER['HTTP_HOST']."/tes/index.php";
$hosted3 = "http://".$_SERVER['HTTP_HOST']."/tes/admin.php";

$ownerName="STIKES Muhammadiyah Lamongan";


?>
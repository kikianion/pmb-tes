<?php
while(@ob_get_clean());

?>
<script type="text/javascript">

setInterval(function(){
    window.scrollTo( 0, 999999 );
},500);
</script>

<?php 



@$jmlpes=(int)$_REQUEST['jmlpes'];
@$prefixuser=$_REQUEST['prefixuser'];
@$waktutes=(int)$_REQUEST['waktutes'];

require_once('../../Connections/koneksi.php'); 
ini_set('max_execution_time', 10000);  


if($jmlpes==0 || $prefixuser=='' || $waktutes=='0') {
    echo 'Parameter harus diset';
    exit;    
}

//$jumlahsoal = 100; 

//$acak = 1; //1=YA, 0=TIDAK

//$sistemkategori = 1; //1=urut Kategori, 0= Semua Acak

$s="delete from userujian";
$st=$pdoc->prepare($s);
$st->execute();

$s="delete from nilai";
$st=$pdoc->prepare($s);
$st->execute();

$s="update settings set val='$waktutes' where name='waktutes' ";
$st=$pdoc->prepare($s);
$st->execute();

$s="ALTER TABLE userujian AUTO_INCREMENT = 1";
$st=$pdoc->prepare($s);
$st->execute();

for($i=0; $i<$jmlpes; $i++){
    $usernum=$prefixuser.str_pad( ($i+1), 5, '0', STR_PAD_LEFT);
    $pwdnew=substr(str_shuffle(str_repeat("1234567890", 5)), 0, 5);
    $s="insert into userujian(username,password,sisawaktu,sisawaktus) values('$usernum','$pwdnew',$waktutes,0)";
    $st=$pdoc->prepare($s);
    $st->execute();

    echo str_pad("membuat user $usernum<br>",4096);          
}

//clean soalujian
$s="delete from soalujian";
$st=$pdoc->prepare($s);
$st->execute();

$s="ALTER TABLE soalujian AUTO_INCREMENT = 1";
$st=$pdoc->prepare($s);
$st->execute();

$s="select kode from jenis";
$st=$pdoc->prepare($s);
$st->execute();
$rs=$st->fetchAll(PDO::FETCH_ASSOC);

$kategoridiambil =[];
for($i=0; $i<count($rs); $i++){
    $kategoridiambil[] = $rs[$i]['kode'];
}

mysql_select_db($database_koneksi, $koneksi);
$query_peserta = "SELECT * FROM userujian";
$peserta = mysql_query($query_peserta, $koneksi) or die(mysql_error());
$row_peserta = mysql_fetch_assoc($peserta);

do {
    $nosoal = 1;

    foreach($kategoridiambil as $key){

        mysql_select_db($database_koneksi, $koneksi);
        $query_soal = "SELECT * FROM soal where jenis='$key' order by RAND()";
        $soal = mysql_query($query_soal, $koneksi) or die(mysql_error());

        $row_soal = mysql_fetch_assoc($soal);

        $totalRows_soal = mysql_num_rows($soal);

        do { 
            $iduser = $row_peserta['username'];
            $idsoal = $row_soal['id'];
            $kunci = $row_soal['kunci'];
            //$kodesoal = $row_soal['kodesoal'];

            $nosoal2 =str_pad( ($nosoal), 5, '0', STR_PAD_LEFT);

            $idsoalujian = $iduser."-".$nosoal2;

            mysql_query("insert into soalujian values('$idsoalujian','$nosoal2','$iduser','$idsoal','','$kunci')
                on duplicate key update idsoalujian='$idsoalujian', nosoal = '$nosoal2', iduser ='$iduser', 
                idsoal='$idsoal', kunci='$kunci' ") or die(mysql_error());
            $nosoal++; 

            echo str_pad("Memgacak soal $idsoalujian <br>",4096);          

        } while ($row_soal = mysql_fetch_assoc($soal)); 
    }
} 
while ($row_peserta = mysql_fetch_assoc($peserta));


echo str_pad("---== SELESAI ==-",4096);          
?>
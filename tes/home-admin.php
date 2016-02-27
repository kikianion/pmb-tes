<?php
if (!isset($_SESSION)) {
    session_start();
}
$MM_authorizedUsers = "1";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
    // For security, start by assuming the visitor is NOT authorized. 
    $isValid = False; 

    // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
    // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
    if (!empty($UserName)) { 
        // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
        // Parse the strings into arrays. 
        $arrUsers = Explode(",", $strUsers); 
        $arrGroups = Explode(",", $strGroups); 
        if (in_array($UserName, $arrUsers)) { 
            $isValid = true; 
        } 
        // Or, you may restrict access to only certain users based on their username. 
        if (in_array($UserGroup, $arrGroups)) { 
            $isValid = true; 
        } 
        if (($strUsers == "") && false) { 
            $isValid = true; 
        } 
    } 
    return $isValid; 
}

$MM_restrictGoTo = $hosted3."/login-admin";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
    $MM_qsChar = "?";
    $MM_referrer = $_SERVER['PHP_SELF'];
    if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
    if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
        $MM_referrer .= "?" . $QUERY_STRING;
    $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
    //header("Location: ". $MM_restrictGoTo); 
    ?>
    <script type="text/javascript">
        window.location="<?php echo $MM_restrictGoTo?>";
    </script>
    <?php

    exit;
}
?>
<style type="text/css">

    table#gen-sesi-from td{
        padding: 5px;
    }

</style>

<div align="center"><h1>Selamat Datang Administrator</h1></div>
<div style="padding: 10px;">

    <h3>Prosedur tes PMB</h3>
    <ol>
        <li>Simpan data peserta & nilai tes yang lama (sesi yang lalu), buka daftar nilai lalu expor ke excel, <a target='_blank' class="btn  btn-xs btn-default" href="./daftar-nilai">Daftar Nilai</a></li>
        <li>Cek pilihan prodi, <a target='_blank' class="btn btn-default btn-xs" href="./daftar-prodi">Pilihan Prodi</a></li>
        <li>Cek soal tes, jenis soal & isi soal, apakah diperlukan penyesuaian, <a target='_blank' class=" btn-xs btn btn-default" href="./master-jenissoal">Jenis Soal</a> | <a target='_blank' class=" btn-xs btn btn-default" href="./daftar-soal">Bank Soal</a></li>

        <li>Tentukan jumlah kursi peserta tes, sistem akan menghapus data sesi lama dan membuat sesi tes baru, <a class="btn btn-default btn-xs" href="#" id='btn-gen-sesi'>Proses</a>
            <div id='gen-sesi' style="display: none;">
                <table id='gen-sesi-from' style="border: 1px solid #22d; border-collapse: collapse">
                    <tr>
                        <td>Jumlah Kursi Peserta</td>
                        <td>:</td>
                        <td><input type="text" id='jmlpes' class="form-control" value="500"></td>
                    </tr>
                    <tr>
                        <td>Prefix Username</td>
                        <td>:</td>
                        <td><input type="text" id='prefixuser' class="form-control" value="PMB"></td>
                    </tr>
                    <tr>
                        <td>Waktu Tes</td>
                        <td>:</td>
                        <td>
                        <div class="form-inline">
                        <input type="text" id='waktutes' class="form-control" value="120"> menit
                        </div>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><button class="btn btn-primary" id='btn-proses-sesi'>Proses Sesi Tes Baru</button></td>
                    </tr>
                </table>
            </div>
        </li>
        <li>Cetak tiket (berisi username & password) kursi peserta tes, dengan mengexpor excel, <a target='_blank' class="btn btn-default btn-xs" href="./daftar-userujian">User tes</a></li>
        <li>Tes siap dilakukan oleh peserta dengan membuka url http://<?php echo $_SERVER['SERVER_NAME']; ?></li>
    </ol>
</div>
<script type="text/javascript">
    $("#btn-gen-sesi").on('click',function(e){
        if($("#gen-sesi").is(':visible')){
            $("#gen-sesi").hide();
        }
        else
            $("#gen-sesi").show();
    });


    var dlg_prog;

    $("#btn-proses-sesi").on('click',function(){
        if(prompt('Apakah ingin menghapus data tes lama dan memproses sesi tes baru?     (isi Y untuk melanjutkan)')=='Y'){
            var jmlpes_val=$("#jmlpes").val();
            var prefixuser_val=$("#prefixuser").val();
            var waktutes_val=$("#waktutes").val();

            dlg_prog.dialog( "open" );
            $("#log-prog").attr("src", '../ds/progress/empty.php');
            $("#log-prog").attr("src", '../ds/progress/set-ujian.php?waktutes='+waktutes_val+'&jmlpes='+jmlpes_val+'&prefixuser='+prefixuser_val);
        }
    });


    $(function(){
        dlg_prog = $( "#dlg-resp" ).dialog({
            autoOpen: false,
            height: 400,
            width: 600,
            modal: true,
            buttons: {
                'Tutup': function() {
                    dlg_prog.dialog( "close" );
                }
            },
        });
    })
</script>

<div id="dlg-resp" title="Progress">
    <p class="validateTips">Memproses, tunggu sampai muncul tanda --==SELESAI==--</p>
    <iframe width="555" height="200" id='log-prog'>
        Sorry your browser does not support inline frames.
    </iframe>    
</div>


<?php

function getModulePath($name){
    $module_path=get_a_field("module","name",$name,"path_");
    return $module_path;
}

function getMenuModuleText($name){
    $module_text=get_a_field("menu","name",$name,"text_");
    return $module_text;
}

function getMenuModulePath($name){
    global $pdo_conn;
    $level=get_a_field("menu","path_menu",$name,"level_");
    
    //cek auth from session
    if(stripos($level,$_SESSION['login']['level'])===false) return "_sys/denied";
    
    $module_path=get_a_field("menu","path_menu",$name,"path_module");
    $module_id=get_a_field("menu","path_menu",$name,"id");
    $s="update menu set lastupd=now() where id='$module_id' ";
    $st=$pdo_conn->prepare($s);
    $st->execute();
    return $module_path;
}


function contentHeader($h_big,$h_small){
    ?>
    <script type="text/javascript">
        $("#header_big").html("<?php echo $h_big?>");
        $("#header_small").html("<?php echo $h_small?>");
    </script>
    <?php
}

function expandHTMLSelectFromTable($_c74b11fc020d,$_b74b2874ea7f,$_76bc452142d9,$_b8d3151429f9,$_f54de1875b8d){
    $_ea73b8369107="select $_b74b2874ea7f,$_76bc452142d9 from $_c74b11fc020d order by $_76bc452142d9 asc";
    $_2d7124e7e8fb=mysql_query($_ea73b8369107);
    $_b8237d0915fd=array("...");
    while($_e3236e163903=mysql_fetch_array($_2d7124e7e8fb)){
        $_b8237d0915fd[$_e3236e163903[0]]=$_e3236e163903[1];
    }
    return expandHTMLSelect($_b8237d0915fd, $_b8d3151429f9, $_f54de1875b8d);
}

function expandHTMLSelect($_b8237d0915fd,$_b8d3151429f9,$_f54de1875b8d){
    $_23e8d6ad432c=<<<EOF
    <select name="$_f54de1875b8d" id="$_f54de1875b8d">
EOF;
    foreach($_b8237d0915fd as $_d76fb022f018=>$_5ac52ed99f5d){
        $_921c3a6a6922="";
        if($_b8d3151429f9==$_d76fb022f018) $_921c3a6a6922="selected=selected";
        $_23e8d6ad432c.=<<<EOF
        <option $_921c3a6a6922 value="$_d76fb022f018">$_5ac52ed99f5d</option>
EOF;
    }
    $_23e8d6ad432c.="</select>";

    return $_23e8d6ad432c;
}

function getGUID(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);        $_7fd9be85e598 = strtoupper(md5(uniqid(rand(), true)));
        $_b3feff3b31cb = chr(45);        $_8c8d8ee1fade = chr(123)        .substr($_7fd9be85e598, 0, 8).$_b3feff3b31cb
        .substr($_7fd9be85e598, 8, 4).$_b3feff3b31cb
        .substr($_7fd9be85e598,12, 4).$_b3feff3b31cb
        .substr($_7fd9be85e598,16, 4).$_b3feff3b31cb
        .substr($_7fd9be85e598,20,12)
        .chr(125);        return $_8c8d8ee1fade;
    }
}

function care_isset(& $_5ac52ed99f5d){
    return addslashes((isset($_5ac52ed99f5d)?$_5ac52ed99f5d:""));
}

function redirect_out($_dc41673fa0ad){
    ob_end_clean();

    $_dc41673fa0ad=(($_dc41673fa0ad));
    header("location: $_dc41673fa0ad");
    exit;
}

function error_box($_5de8de3f6245){
    ?>
    <div style="margin: 25px 15px;">
        <table class="table table-condensed" 
            style="border: 1px solid #D0D0D0; box-shadow: 2px 2px 11px #D0D0D0; margin: 0 auto; width: 350px; background-color: #a2d8d8;">
            <tr>
                <td colspan="2" style="text-align: center;">
                    <h4 style="color: red;">PERINGATAN</h4>
                </td>
            </tr>
            <tr>
                <td width="50px" style="font-size: 38pt; color: red;"><span class="glyphicon glyphicon-ban-circle"></span></td>
                <td style="text-align: center;">
                    <?php
                    echo care_isset($_5de8de3f6245);
                    ?>
                </td>
            </tr>
        </table>
    </div>
    <?php
}

function page_title($_ea73b8369107){
    echo "<h1 style='text-align: center;'>$_ea73b8369107</h1>";
}


function get_a_field($tbl,$key,$val,$select){
    global $pdoc;
    
    $s="select $select from $tbl where $key=:val ";
    
    $st=$pdoc->prepare($s);
    $st->execute(array(
        "val"=>$val
    ));

    $rs=$st->fetchAll();
    
    if(count($rs)){
        return $rs[0][0];
    }
    else{
        return null;
    }
}


function gen_jadwal_angsur($_4aabacb153f2){
    $_7e1af29c7850=<<<EOF2
        Jadwal Angsuran
        <table class="table table-condensed" border=1 width='100%'>
        <tr>
            <td>#</td>
            <td>Tgl tempo</td>
            <td>Pokok</td>
            <td>Jasa</td>
            <td>Total</td>
        </tr>
EOF2;

    $_ea73b8369107="select * from tbl_angsuran where id_kredit=$_4aabacb153f2 order by tgl_tempo asc";
    $_2d7124e7e8fb=mysql_query($_ea73b8369107) or die(mysql_error());

    while($_e3236e163903=mysql_fetch_assoc($_2d7124e7e8fb)){
        $_9379af796aa4=$_e3236e163903["byr_ke"];
        $_8531bc83a4c2=$_e3236e163903["tgl_tempo"];
        $_a9c7ddfb8892=$_e3236e163903["pokok"];
        $_bc27b3fd98a4=$_e3236e163903["jasa"];
        $_04e5eaaeab9a=$_a9c7ddfb8892+$_bc27b3fd98a4;
        $_7e1af29c7850.="
        <tr>
        <td>$_9379af796aa4</td>
        <td>$_8531bc83a4c2</td>
        <td>$_a9c7ddfb8892</td>
        <td>$_bc27b3fd98a4</td>
        <td>$_04e5eaaeab9a</td>
        </tr>
        ";
    }

    $_7e1af29c7850.="
    </table>    
    ";

    return $_7e1af29c7850;
}

function gen_anggota_kelompok($_94b32aa94856){
    $_7e1af29c7850=<<<EOF2
        Anggota Kelompok
        <table class="table table-condensed" border=0 width='100%'>
        <tr>
            <td>#</td>
            <td>Nama</td>
        </tr>
EOF2;

    $_ea73b8369107="select * from tbl_anggota where id_kelompok=$_94b32aa94856 order by nama asc";
    $_2d7124e7e8fb=mysql_query($_ea73b8369107) or die(mysql_error());

    $_d181e8a209b5=1;
    while($_e3236e163903=mysql_fetch_assoc($_2d7124e7e8fb)){
        $_feeeebe30233=$_e3236e163903["nama"];
        $_7e1af29c7850.="
        <tr>
        <td>$_d181e8a209b5</td>
        <td>$_feeeebe30233</td>
        </tr>
        ";

        $_d181e8a209b5++;
    }

    $_7e1af29c7850.="
    </table>    
    ";

    return $_7e1af29c7850;
}


function angsuran_sudah_lunas_ke($_75030b20cfda){
    $_4189efcd2f32="
    select * from tbl_angsuran where id_kredit='$_75030b20cfda' and lunas=1
    order by tgl_tempo asc
    limit 1
    ";
    $_c4b737d7d03d=mysql_query($_4189efcd2f32) or die(mysal_error());

    $_3e5ab422ff7f=mysql_fetch_assoc($_c4b737d7d03d);

    $_7e0a8078b784=$_3e5ab422ff7f["byr_ke"];

    return $_7e0a8078b784;

}

function angsuran_mau_lunas_ke($_75030b20cfda){
    $_4189efcd2f32="
    select * from tbl_angsuran where id_kredit='$_75030b20cfda' and lunas=0
    order by tgl_tempo asc
    limit 1
    ";
    $_c4b737d7d03d=mysql_query($_4189efcd2f32) or die(mysal_error());

    $_3e5ab422ff7f=mysql_fetch_assoc($_c4b737d7d03d);

    $_7e0a8078b784=$_3e5ab422ff7f["byr_ke"];
    return $_7e0a8078b784;

}

function total_sisa_pokok_angsur($_75030b20cfda){
    $_ea73b8369107="select sum(pokok) as totalpokok, sum(jasa) as totaljasa
    from tbl_angsuran where id_kredit='$_75030b20cfda' and lunas=0";   

    $_2d7124e7e8fb=mysql_query($_ea73b8369107) or die(mysql_error());

    $_e3236e163903=mysql_fetch_assoc($_2d7124e7e8fb);

    return $_e3236e163903["totalpokok"];

}

function total_sisa_jasa_angsur($_75030b20cfda){
    $_ea73b8369107="select sum(pokok) as totalpokok, sum(jasa) as totaljasa
    from tbl_angsuran where id_kredit='$_75030b20cfda' and lunas=0";   

    $_2d7124e7e8fb=mysql_query($_ea73b8369107) or die(mysql_error());

    $_e3236e163903=mysql_fetch_assoc($_2d7124e7e8fb);

    return $_e3236e163903["totaljasa"];

}

function total_pokok_angsur($_75030b20cfda){
    $_ea73b8369107="select sum(pokok) as totalpokok, sum(jasa) as totaljasa
    from tbl_angsuran where id_kredit='$_75030b20cfda' and lunas=1";   

    $_2d7124e7e8fb=mysql_query($_ea73b8369107) or die(mysql_error());

    $_e3236e163903=mysql_fetch_assoc($_2d7124e7e8fb);

    return $_e3236e163903["totalpokok"];

}

function total_jasa_angsur($_75030b20cfda){
    $_ea73b8369107="select sum(pokok) as totalpokok, sum(jasa) as totaljasa
    from tbl_angsuran where id_kredit='$_75030b20cfda' and lunas=1";   

    $_2d7124e7e8fb=mysql_query($_ea73b8369107) or die(mysql_error());

    $_e3236e163903=mysql_fetch_assoc($_2d7124e7e8fb);

    return $_e3236e163903["totaljasa"];

}

function total_tunggak_pokok($_75030b20cfda){
    $_ea73b8369107="select sum(pokok) as tunggak_pokok, sum(jasa) as tunggak_jasa 
    from tbl_angsuran where id_kredit=$_75030b20cfda and lunas=0 and tgl_tempo<now()";   

    $_2d7124e7e8fb=mysql_query($_ea73b8369107) or die(mysql_error());

    $_e3236e163903=mysql_fetch_assoc($_2d7124e7e8fb);

    return $_e3236e163903["tunggak_pokok"];

}

function total_tunggak_jasa($_75030b20cfda){
    $_ea73b8369107="select sum(pokok) as tunggak_pokok, sum(jasa) as tunggak_jasa 
    from tbl_angsuran where id_kredit=$_75030b20cfda and lunas=0 and tgl_tempo<now()";   

    $_2d7124e7e8fb=mysql_query($_ea73b8369107) or die(mysql_error());

    $_e3236e163903=mysql_fetch_assoc($_2d7124e7e8fb);

    return $_e3236e163903["tunggak_jasa"];

}

?>

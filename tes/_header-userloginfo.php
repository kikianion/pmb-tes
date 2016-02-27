<?php require_once('common/func.php');?>

<?php if(isset($_SESSION['MM_Username']) ) 
{  ?>
    <div id="kategori" >
        <div align="right" class="style1" style="color:#ddd !important">
            <span >
                <?php
                $nama1=get_a_field("userujian","username",$_SESSION['MM_Username'],'namalengkap');
                ?>
                <span >Username: <?php echo $_SESSION['MM_Username'] ?></span> |
                <span style="margin-right: 15px">Nama: <?php echo $nama1 ?></span>
            </span>
        </div>
    </div>
    <?php 
}?>

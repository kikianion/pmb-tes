<?php 
session_start();


require_once('Connections/koneksi.php');
require_once('common/func.php');
@$pathinfo=$_SERVER['PATH_INFO'];
$p = explode('/',$pathinfo);

if (!isset($p[1])){
    session_destroy();
    session_start();
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    <?php include "_header.php"?>

    <body>
        <div style=" width: 100%;">
            <?php include "_header-logo.php"; ?>
            <?php include "_header-userloginfo.php"; ?>

            <div id="holder">
                <?php 
                if (isset($p[1])){
                    include($p[1].'.php');
                } else {
                    include('login.php');
                }
                ?>
            </div>
        </div>
        <?php include "_footer.php"; ?>
    </body>
</html>

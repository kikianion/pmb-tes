<?php

include "common/func.php";

//init
$table="nilai";
$colMap = array(
    'username',
    'nopes',
    'namalengkap',
    'prodi',
    'nlai',

);
$orderby="username";

//param
@$f=$_REQUEST["f"];

//main
if(function_exists($f)){
    echo json_encode($f());
}
else{
    echo '{"result":"function not exist"}';
}

?>
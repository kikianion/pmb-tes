<?php

include "common/func.php";

//init
$table="userujian";
$colMap = array(
    '-',
    'username',
    'password',
    'level',
    'nopes',
    'namalengkap',
    'nohp',
    'asal',
    'prodi',
    'sisawaktu',
    'sisawaktus',
);

$orderby="id";

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
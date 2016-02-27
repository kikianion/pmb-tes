<?php

include "common/func.php";

//init
$table="jenis";
$colMap = array(
    '-',
    'kode',
    'namajenis',
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
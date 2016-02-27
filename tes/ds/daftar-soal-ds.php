<?php

include "common/func.php";

//init
$table="soal";
$colMap = array(
    '-',
    'kodesoal',
    'jenis',
    'pertanyaan',
    'jwba',
    'jwbb',
    'jwbc',
    'jwbd',
    'jwbe',
    'kunci',

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
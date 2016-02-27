<?php

include "../Connections/koneksi.php";

if (!function_exists('load')){
    function load(){
        global $pdoc, $table, $orderby;

        $s="select * from $table order by $orderby";

        try{
            $stmt=$pdoc->prepare($s);
            $stmt->execute();

            $result=$stmt->fetchAll(PDO::FETCH_ASSOC);
            $res=array('data'=>$result);
            $res["result"]="ok";
        }
        catch(Exception $e){
            $res["result"]="error";
            $res["msg"]=$e->getMessage();
        }
        return $res;
    }
}

if (!function_exists('save')){
    function save(){
        global $pdoc, $table, $colMap,$callback_save1, $callback_perchange_save1, $callback_perchange_beforesave1;

        try{
            if (isset($_POST['changes'])) {
                $change_rows=json_decode($_POST['changes'],true);
                foreach ($change_rows as $change_row) {

                    if(function_exists($callback_perchange_beforesave1)){
                        $res_cbbefore=$callback_perchange_beforesave1();
                        $callback_perchange_beforesave1="";
                    }

                    $rowId  = $change_row['id'];
                    if($rowId==""){
                        $rowId="-1";
                    }

                    $colNames="";
                    $newVals="";

                    $newVals_pdo="";

                    $prepareParamarray=array();
                    $updateColVal_pdo='';

                    $updateColVal='';

                    for($j=0; $j<count($change_row['rowData']); $j++){
                        $row1=$change_row['rowData'][$j];
                        $colId=$row1[1];

                        if($j==count($change_row['rowData'])-1){
                            $colNames.=$colMap[$colId];
                            $newVals.="'".$row1[3]."'";
                            $updateColVal.=$colMap[$colId]."="."'".$row1[3]."'";

                            $updateColVal_pdo.= "$colMap[$colId]=?";
                            $prepareParamarray[]=$row1[3];

                            $newVals_pdo.="?";
                        }
                        else{
                            $colNames.=$colMap[$colId].",";
                            $newVals.="'".$row1[3]."',";
                            $updateColVal.=$colMap[$colId]."="."'".$row1[3]."',";

                            $updateColVal_pdo.= "$colMap[$colId]=?, ";
                            $prepareParamarray[]=$row1[3];

                            $newVals_pdo.="?,";
                        }
                    }

                    $stmt= $pdoc->prepare("SELECT id FROM $table WHERE id=:id LIMIT 1");
                    $stmt->execute(array(
                        'id'=>$rowId
                    ));

                    $lastid=null;

                    //record sudah ada
                    if ($row = $stmt->fetch()) {                     
                        $s="UPDATE $table SET $updateColVal_pdo WHERE id = ? ";

                        $param_arr1=array_merge($prepareParamarray,array($rowId));
                        $query = $pdoc->prepare($s);
                        $query->execute($param_arr1);

                        $lastid=$rowId;

                        $save_info['type']='update';
                    } 
                    //record belum ada
                    else {
                        $s="INSERT INTO $table ($colNames) VALUES($newVals_pdo)";
                        $query = $pdoc->prepare($s);
                        $query->execute($prepareParamarray);
                        $lastid=$pdoc->lastInsertId();
                        $save_info['type']='insert';
                    }


                    if(function_exists($callback_perchange_save1)){
                        $res_cb=$callback_perchange_save1($lastid, $save_info);
                    }

                }
                $callback_perchange_save1="";

                if(function_exists($callback_save1)){
                    $callback_save1();
                    $callback_save1="";
                }

                $res["result"]="ok";
                $res["msg"]="affected: ".$query->rowCount();
                return $res;
            } 
        }
        catch(Exception $e){
            $res["result"]="error";
            $res["msg"]=$e->getMessage();
            return $res;
        }

    }
}

function delete(){
    global $pdoc, $table, $callback_delete1;

    @$ids=$_REQUEST["ids"];

    try{
        if(function_exists($callback_delete1)){
            $callback_delete1();
            $callback_delete1="";
        }

        if(count($ids)>0){
            for($i=0; $i<count($ids); $i++){
                $stmt= $pdoc->prepare("delete from $table WHERE id=:id ");
                $stmt->execute(array(
                    'id'=>$ids[$i]

                ));

            }        
        }

        $res["result"]="ok";
        return $res;

    }
    catch(Exception $e){
        $res["result"]="error";
        $res["msg"]=$e->getMessage();
        return $res;
    }
}

?>
<?php
// cms/api/audit/getmodules.php

// Librerías
    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");
    
// Parametros obligatorios
    $parmsob = array("sessionid");
    if (!parametrosValidos($_REQUEST, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
// Validar sesion  
  $userid = isSessionValidCMS($db, $_REQUEST["sessionid"]); 

// Datos    
    $sql =  "SELECT DISTINCT app, module FROM audit ORDER BY app, module";

    // Se ejecuta el query principal
    if (!$rs=$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    
// Serialize
    $records=array();
    $pre_app="None";
    while ($row = $rs->fetch_assoc()){
        if ($row['app']!=$pre_app){
            if ($pre_app!='None')
                $records[]=$record;
            $record = new stdClass;
            $record->application=$row['app'];
            $record->modules = array();
            $record->modules[] = $row['module']; 
        }
        else{
            $record->modules[] = $row['module'];        
        }
        $pre_app = $row['app'];
    }
    $records[]=$record;
    $out= new stdClass;
    $out->records =$records;

// Output
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>


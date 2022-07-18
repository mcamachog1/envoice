<?php
// cms/api/audit/getusers.php

// Librerías
    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");
    
// Parametros obligatorios
    $parmsob = array("app","module","sessionid");
    if (!parametrosValidos($_REQUEST, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

    // get APP
        $app = strtoupper($_REQUEST["app"]);
       
        
    // get Modulo
        $module = strtolower($_REQUEST["module"]); 
        if ($module=='*')
            $filter_module="";
        else
            $filter_module=" AND module= '$module' ";         

// Validar sesion  
  $userid = isSessionValidCMS($db, $_REQUEST["sessionid"]); 

// Datos    
    $sql =  "SELECT DISTINCT userid FROM audit  ".
            " WHERE app='$app' $filter_module ORDER BY userid ";

    // Se ejecuta el query principal
    if (!$rs=$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    
// Serialize
    $records=array();
    while ($row = $rs->fetch_assoc()){
        $records[]=$row['userid'];
    }

    $out= new stdClass;
    $out->records =$records;

// Output
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>


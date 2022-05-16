<?php
//  cms/api/security/recover.php
    header("Content-Type:application/json");
    include("../../../settings/dbconn.php");
    include("../../../settings/utils.php");   
    
    if (!isset($_GET["hash"]) || !isset($_GET["pwd"])){
        badEnd("400", array("msg"=>"Parametros obligatorios hash,pwd"));           
    }
    
    $hash = $_GET["hash"];
    $pwd = $_GET["pwd"];
    
    $sql =  "UPDATE users " .
            "SET    pwd = '" . $pwd . "', ".
            "       hashrecover = NULL ".
            "WHERE  hashrecover='".$hash."'";

    if (!$db->query($sql)){
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));        
    }
   
    if ($db->affected_rows == 0){
        badEnd("401", array("msg"=>"Hash incorrecto"));        
    }
    $out = new stdClass;
    $out->msg = "Password cambiado satisfactoriamente";
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
    
?>

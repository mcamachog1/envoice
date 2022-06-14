<?php
//  seniat/api/security/recover.php

    header("Content-Type:application/json");
    include("../../../settings/dbconn.php");
    include("../../../settings/utils.php");
    include("../functions.php");    

    // parametros obligatorios
    $parmsob = array("hash");
    if (!parametrosValidos($_GET, $parmsob)){
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
    }
    
    $hash = $_GET["hash"];
    $pwd = $_GET["pwd"];
    
    setPassword($hash,$pwd,$db);

    $out = new stdClass;
    $out->msg = "Password cambiado satisfactoriamente";
    
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>

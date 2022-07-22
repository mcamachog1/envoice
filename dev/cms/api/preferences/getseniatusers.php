<?php
//cms/api/preferences/getseniatusers.php

header("Content-Type:application/json");
include("../../../settings/dbconn.php");
include("../../../settings/utils.php");
include("functions.php");



// Parametros obligatorios
$parmsob = array("sessionid");
if (!parametrosValidos($_GET, $parmsob))
    badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
// Validar sesion
$userid = isSessionValidCMS($db,$_REQUEST["sessionid"]);




// Salida
$out = new stdClass;    
$out->seniatusers =getUsers($db);
header("HTTP/1.1 200");
echo (json_encode($out));
die();      
?>
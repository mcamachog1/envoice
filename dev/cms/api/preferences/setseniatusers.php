<?php
//cms/api/preferences/setseniatusers.php

header("Content-Type:application/json");
include("../../../settings/dbconn.php");
include("../../../settings/utils.php");

function insertUsers($users,$passwords){
    $counter=0;
    $max_users = strlen($users);
    $max_pwd = strlen($passwords);
    if ($max_pwd!=$max_users)
        badEnd("400", array("msg"=>"Cantidad de usuarios debe ser igual a cantidad de passwords"));
    $a_users = explode("-",$users);
    $a_passwords = explode("-",$passwords);
    for ($i=0; $i<$max_users;$i++) {
        $sql = "INSERT INTO preferences (name, value) VALUES (".$a_users[$i].",".$a_passwords[$i].")";
        if (!$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));    
        $counter++;
    }
    return $counter;
}

// Parametros obligatorios
$parmsob = array("users","passwords","sessionid");
if (!parametrosValidos($_GET, $parmsob))
    badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
// Validar sesion
$userid = isSessionValidCMS($db, $_REQUEST["sessionid"]);
// Revisar parametros
$users = avoidInjection($_GET["users"],'list');
$passwords = avoidInjection($_GET["passwords"],'list');
// Salida
$out = new stdClass;    
$out->count =insertUsers($users,$passwords);
header("HTTP/1.1 200");
echo (json_encode($out));
die();      
?>
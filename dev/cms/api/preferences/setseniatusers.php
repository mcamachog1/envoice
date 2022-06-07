<?php
//cms/api/preferences/setseniatusers.php

header("Content-Type:application/json");
include("../../../settings/dbconn.php");
include("../../../settings/utils.php");

function insertUsers($users,$passwords,$db){
    $counter=0;
    $a_users = explode("-",$users);
    $a_passwords = explode("-",$passwords);
    $max_users=count($a_users);
    for ($i=0; $i<$max_users;$i++) {
        $sql = "INSERT INTO preferences (name, value) VALUES ('".$a_users[$i]."','".$a_passwords[$i]."')";
        if (!$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));    
        $counter++;
    }
    return $counter;
}
function checkInput($users,$passwords){
    $a_users = explode("-",$users);
    $a_passwords = explode("-",$passwords);
    $max_users = count($a_users);
    $max_pwd = count($a_passwords);
    if ($max_pwd!=$max_users)
        badEnd("400", array("msg"=>"Cantidad de usuarios debe ser igual a cantidad de passwords"));
    foreach ($a_users as $user)
        if (strlen($user)==0)
            badEnd("400", array("msg"=>"Nombre de usuario no puede ser vacio"));
    foreach ($a_passwords as $password)
        if (strlen($password)==0)
            badEnd("400", array("msg"=>"Password no puede ser vacio"));
    return true;
}
function deleteUsers($users,$db){
    $a_users = explode("-",$users);
    $list="";
    $max = count($a_users);
    for ($i=0;$i<$max-1;$i++)
        $list .= "'".$a_users[$i]."',";
    $list .= "'".$a_users[$max-1]."'";
    $sql = "DELETE FROM preferences WHERE name IN ($list)";
    if (!$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));    
    return $db->affected_rows;
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
checkInput($users,$passwords);
deleteUsers($users,$db);
// Salida
$out = new stdClass;    
$out->count =insertUsers($users,$passwords,$db);
header("HTTP/1.1 200");
echo (json_encode($out));
die();      
?>
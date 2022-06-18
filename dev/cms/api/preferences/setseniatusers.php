<?php
//cms/api/preferences/setseniatusers.php

header("Content-Type:application/json");
include("../../../settings/dbconn.php");
include("../../../settings/utils.php");

function getUsers($db){
  $sql = "SELECT name, value FROM preferences";
  if (!$rs=$db->query($sql))
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  $records=array();
// Serialize
  while ($row = $rs->fetch_assoc()){
    if(filter_var($row['value'], FILTER_VALIDATE_EMAIL))       {
        $record = new stdClass();
        $record->id = $row['name'];
        $record->value = $row['value'];
        $records[] = $record;
    }
  }
  return $records;
}

function insertUsers($users,$emails,$db){
    $counter=0;
    $a_users = explode("-",$users);
    $a_emails = explode("-",$emails);
    $max_users=count($a_users);
    // Seleccionar usuarios que ya existen
    $usersinserted=getUsers($db);   
    
    for ($i=0; $i<$max_users;$i++) {
        $insert=true;
        foreach ($usersinserted as $inserted){
            if ($a_emails[$i]==$inserted->value){
                $insert=false;
                break;
            }
        }
        // Si el usuario a insertar no existe se inserta
        if ($insert){    
            $sql = "INSERT INTO preferences (name, value) VALUES ('".$a_users[$i]."','".$a_emails[$i]."')";
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));    
            $counter++;
        }
        // Si existe se limpian los fails y se desbloquea
        else {
            // Fails
            $email_fails = $a_emails[$i]."_fails";
            $sql = "UPDATE preferences SET value=0 WHERE name = '$email_fails' ";
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));   
            // Desbloquear
            $email_active = $a_emails[$i]."_active";
            $sql = "UPDATE preferences SET value=1 WHERE name = '$email_active' ";
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));   
        }
    }
    return $counter;
}
function checkInput($users,$emails){
    $a_users = explode("-",$users);
    $a_emails = explode("-",$emails);
    $max_users = count($a_users);
    $max_emails = count($a_emails);
    if ($max_emails!=$max_users)
        badEnd("400", array("msg"=>"La cantidad de usuarios debe ser igual a la cantidad de emails"));
    foreach ($a_users as $user)
        if (strlen($user)==0)
            badEnd("400", array("msg"=>"El nombre del usuario no puede estar vacío"));
    foreach ($a_emails as $email){
        avoidinjection($email,'email');
        if (strlen($email)==0)
            badEnd("400", array("msg"=>"El email no puede estar vacío"));
        
    }
    return true;
}
function deleteUsers($emails,$db){
    $condition="0";
    $a_emails= explode("-",$emails);
    
    foreach ($a_emails as $email)
        $condition .= " OR name LIKE '$email%' OR value = '$email'";
    $sql = "DELETE FROM preferences WHERE NAME NOT IN (SELECT NAME FROM (SELECT NAME FROM preferences WHERE $condition) AS t )";
    if (!$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));        
    return $db->affected_rows;
}
// Parametros obligatorios
$parmsob = array("usernames","emails","sessionid");
if (!parametrosValidos($_GET, $parmsob))
    badEnd("400", array("msg"=>"Parámetros obligatorios " . implode(", ", $parmsob)));
// Validar sesion
$userid = isSessionValidCMS($db, $_REQUEST["sessionid"]);
// Revisar parametros
$users = avoidInjection($_GET["usernames"],'list');
$emails = avoidInjection($_GET["emails"],'list');
checkInput($users,$emails);
deleteUsers($emails,$db);
// Salida
$out = new stdClass;    
$out->count =insertUsers($users,$emails,$db);
header("HTTP/1.1 200");
echo (json_encode($out));
die();      
?>
<?php
//cms/api/preferences/setseniatusers.php

header("Content-Type:application/json");
include("../../../settings/dbconn.php");
include("../../../settings/utils.php");
include("functions.php");


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
        // Nombre del usuario
        $email = $a_emails[$i];
        $user_name = $a_users[$i];
        // Si el usuario a insertar no existe se inserta
        if ($insert){    
            $sql = "INSERT INTO preferences (name, value) VALUES ('".$a_users[$i]."','".$a_emails[$i]."')";
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));    
            $counter++;
            // Auditoria
              insertAudit($db,getEmail($_REQUEST["sessionid"],APP_CMS,$db),$_SERVER['REMOTE_ADDR'],APP_CMS,MODULE_PREFERENCES,"Se creó el Usuario $user_name de Seniat - $email");        
        
        }
        // Si existe se limpian los fails y se pone status 1
        else {
            $updated_something = False;
            // Fails
            $email_fails = $a_emails[$i]."_fails";
            $sql = "UPDATE preferences SET value=0 WHERE name = '$email_fails' ";
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));   
            if ($db->affected_rows > 0)
                $updated_something= True;
            // Desbloquear
            $email_status = $a_emails[$i]."_status";
            $sql = "UPDATE preferences SET value=1 WHERE name = '$email_status' ";
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));   
            if ($db->affected_rows > 0)
                $updated_something= True;
            // Actualiza nombre de usuario        
            $sql = "UPDATE preferences SET name='$user_name' WHERE value = '$email' ";
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error)); 
            if ($db->affected_rows > 0)
                $updated_something= True;
            }

            // Auditoria
            if ($updated_something)
                insertAudit($db,getEmail($_REQUEST["sessionid"],APP_CMS,$db),$_SERVER['REMOTE_ADDR'],APP_CMS,MODULE_PREFERENCES,"Se modificaron los datos del Usuario: $user_name de Seniat - $email");        
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
    $userid = isSessionValidCMS($db,$_REQUEST["sessionid"]);
// Revisar parametros
$users = avoidInjection($_GET["usernames"],'list');
$emails = avoidInjection($_GET["emails"],'list');
// Validar datos para crear usuarios
checkInput($users,$emails);

// Guarda usuarios actuales
$pre_users = getUsers($db);   
// Borra todos los usuarios
deleteUsers($emails,$db);

// Inserta todos los usuarios
$count = insertUsers($users,$emails,$db);


// Auditoria
  // emails enviados
  $a_emails= explode("-",$emails);
  // Hacer lista de usuarios a modifcar
  $email_list="";  
  for ($x=0;$x<count($a_emails);$x++){
    $email = $a_emails[$x];
    if ($x != count($a_emails)-1) //No es el ultimo
        $email_list .= "'$email',";
    else
        $email_list .= "'$email'";
  }
  // Tomar los usuarios que quedan
  $sql = "SELECT value FROM preferences WHERE value IN ($email_list)";
  if (!$rs=$db->query($sql))
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  $users_array = array();
  while ($row = $rs->fetch_assoc())
     $users_array[] = $row['value'];
  $audit_email=getEmail($_REQUEST["sessionid"],APP_CMS,$db);
  // Si el usuario original no está, se va a eliminar
  foreach ($pre_users as $object)
    if (!in_array($object->value,$a_emails))
      insertAudit($db,$audit_email,$_SERVER['REMOTE_ADDR'],APP_CMS,MODULE_PREFERENCES,"Se eliminó el Usuario de Seniat - $object->value");        

// Salida
  $out = new stdClass;    
  $out->count = $count;  
  header("HTTP/1.1 200");
  echo (json_encode($out));
  die();      
?>
<?php
//cms/api/preferences/getseniatusers.php

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
    $record = new stdClass();
    $record->id = $row['name'];
    $record->value = $row['value'];
    $records[] = $record;
  }
  return $records;
}


// Parametros obligatorios
$parmsob = array("sessionid");
if (!parametrosValidos($_GET, $parmsob))
    badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
// Validar sesion
$userid = isSessionValidCMS($db, $_REQUEST["sessionid"]);


// Salida
$out = new stdClass;    
$out->seniatusers =getUsers($db);
header("HTTP/1.1 200");
echo (json_encode($out));
die();      
?>
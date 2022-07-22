<?php


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


?>
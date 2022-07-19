<?php
// cms/api/users/entry

    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");
    
    // parametros obligatorios
    $parmsob = array("id","sessionid");
    if (!parametrosValidos($_REQUEST, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
    
    $userid = isSessionValidCMS($db, $_REQUEST["sessionid"]);
    
    $sql="SELECT id, usr, name, status ".
        " FROM users ".
        " WHERE id=".intval ($_REQUEST["id"]);
    
    // Se ejecuta el query principal
    if (!$rs=$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    if ($row = $rs->fetch_assoc()){
        $record = new stdClass;
        $record->id=(integer)$row["id"];
        $record->usr=$row["usr"];
        $record->name= $row["name"];
        $record->status=new stdClass;
        if ($row["status"]==1) {
            $record->status->id=1;
            $record->status->dsc="Activo";
        }
        else {
            $record->status->id=0;
            $record->status->dsc="Inactivo";
        }
    }

    $out = new stdClass;  
    $out->entry =$record;

    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>
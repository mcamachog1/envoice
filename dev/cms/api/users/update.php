<?php
// cms/api/users/update

    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");
    
    // parametros obligatorios
    $parmsob = array("id","usr","name","status","sessionid");
    if (!parametrosValidos($_REQUEST, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

    $userid = isSessionValidCMS($db, $_REQUEST["sessionid"]);
    
    $columns="id,usr,name,status";
    $values=$_REQUEST["id"].",'".$_REQUEST["usr"]."','".$_REQUEST["name"]."',".$_REQUEST["status"];
    $updatelist = "usr='".$_REQUEST["usr"]."',name='".$_REQUEST["name"]."',status=".$_REQUEST["status"];

    if ($_REQUEST["id"]==0) { 
        // Es un insert
        $sql =  "INSERT INTO users (usr, name, status) " .
                "VALUES         ('" . $_REQUEST["usr"] . "'," .
                "                '" . $_REQUEST["name"] . "'," .
                "                '" . $_REQUEST["status"] . "') "; 
        if (!$db->query($sql)) {
            if ($db->errno == 1062){
                badEnd("409", array("msg"=>"Registro Duplicado"));
            }
            else {
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
            }
        }
        $id =$db->insert_id;
    }
    else {
        $id = $_REQUEST["id"];
        $sql =  "UPDATE     users " .
                "SET        usr='" . $_REQUEST["usr"] . "'," .
                "           name='" . $_REQUEST["name"] . "'," .
                "           status='" . $_REQUEST["status"] . "' " .
                "WHERE      id=" . $_REQUEST["id"];
        if (!$db->query($sql)) {
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
            
        }
    }
    

    $out = new stdClass;    
    $out->id =(integer)$id;
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>

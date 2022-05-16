<?php
// cms/api/users/quickupdate

    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");
    
    // parametros obligatorios
    $parmsob = array("id","status","sessionid");
    if (!parametrosValidos($_REQUEST, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

    $userid = isSessionValidCMS($db, $_REQUEST["sessionid"]);
    
    $id = $_REQUEST["id"];
    $sql =  "UPDATE     users " .
            "SET        status='" . $_REQUEST["status"] . "' " .
            "WHERE      id=" . $_REQUEST["id"];
    if (!$db->query($sql)) {
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    }
    
    $out = new stdClass;    
    $out->id =(integer)$id;
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>

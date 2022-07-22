<?php
// cms/api/users/quickupdate

    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");
    include_once("functions.php");

    function getStatus($userid,$db){
        $sql = "SELECT status FROM users WHERE id=$userid ";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        
        $row = $rs->fetch_assoc();
        return $row['status'];
    } 
    
    // parametros obligatorios
    $parmsob = array("id","status","sessionid");
    if (!parametrosValidos($_REQUEST, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

    $userid = isSessionValidCMS($db, $_REQUEST["sessionid"]);
    
    $id = $_REQUEST["id"];
    $status = $_REQUEST["status"];    
    $resetfails="";    
    if ($status==1 && $status!=getStatus($id,$db))
      $resetfails=", fails=0 ";
    
    $sql =  "UPDATE     users " .
            "SET        status='" . $_REQUEST["status"] . "' " . $resetfails.
            "WHERE      id=" . $_REQUEST["id"];
    if (!$db->query($sql)) {
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    }
    
    $out = new stdClass;    
    $out->id =(integer)$id;

    // Audit
    $useremail = getUserCMS($db,$id);
    if ($status==1)
        insertAudit($db,getEmail($_REQUEST["sessionid"],APP_CMS,$db),$_SERVER['REMOTE_ADDR'],APP_CMS,MODULE_USERS,"Se inhabilitó un usuario de CMS - $useremail");        
    else
        insertAudit($db,getEmail($_REQUEST["sessionid"],APP_CMS,$db),$_SERVER['REMOTE_ADDR'],APP_CMS,MODULE_USERS,"Se habilitó un usuario de CMS - $useremail");            

    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>

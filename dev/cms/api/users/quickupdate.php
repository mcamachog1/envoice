<?php
// cms/api/users/quickupdate

    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");


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

    $userid = isSessionValidCMS($db, $_REQUEST["sessionid"],array('ip'=>$_SERVER['REMOTE_ADDR'],'app'=>'CMS','module'=>'users','dsc'=>'quickupdate.php'));    
    
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
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>

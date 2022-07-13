<?php
// cms/api/customers/quickupdate

    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");

    function getStatus($customerid,$db){
        $sql = "SELECT status FROM customers WHERE id=$customerid ";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        
        $row = $rs->fetch_assoc();
        return $row['status'];
    } 
    // parametros obligatorios
    $parmsob = array("id","status","sessionid");
    if (!parametrosValidos($_REQUEST, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

    $userid = isSessionValidCMS($db, $_REQUEST["sessionid"],array('ip'=>$_SERVER['REMOTE_ADDR'],'app'=>'CMS','module'=>'customers','dsc'=>'Activar-Desactivar cliente Dayco'));
    $status=$_REQUEST["status"];
    
    $id = $_REQUEST["id"];
    $resetfails="";
    if ($status==1 && $status!=getStatus($id,$db))
      $resetfails=", fails=0 ";
    $sql =  "UPDATE     customers " .
            "SET        status='" . $_REQUEST["status"] . "' " .$resetfails.
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

<?php
    header("Content-Type:application/json");
    include("../../../settings/dbconn.php");
    include("../../../settings/utils.php"); 
    include("../functions.php");     


    function logoutUser($sessionid,$db){
        $email=validSession($db, $_REQUEST["sessionid"],array('ip'=>$_SERVER['REMOTE_ADDR'],'app'=>'SENIAT','module'=>'security','dsc'=>'logout.php'));    
        $name_session = $email."_session";
        $name_validthru = $email."_validthru"; 
        $name_fails = $email."_fails";           

        $sql ="UPDATE preferences SET value=NULL WHERE LOWER(name)='$name_session'";
        if (!$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));        
        $sql ="UPDATE preferences SET value=NULL WHERE LOWER(name)='$name_validthru'";
        if (!$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));        
        $sql ="UPDATE preferences SET value=0 WHERE LOWER(name)='$name_fails'";
        if (!$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));        
    }



    // Parametros obligatorios
    $parmsob = array("sessionid");
    if (!parametrosValidos($_REQUEST, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

    $sessionid = $_GET["sessionid"];
    logoutUser($sessionid,$db);


    // Salida
    $out=new stdClass;
    $out->id = 0;
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>

<?php
    header("Content-Type:application/json");
    include("../../../settings/dbconn.php");
    include("../../../settings/utils.php");    

    //validamos que recibimos siempre los datos por GET
    if (!isset($_GET["sessionid"])){
       header("HTTP/1.1 400");
        echo (json_encode(array(msg=>"Parametros obligatorios sessionid")));
       die();
    }
    
    $sessionid = $_GET["sessionid"];
    $email=getEmail($_REQUEST["sessionid"],APP_APP,$db);
    isSessionValid($db, $_REQUEST["sessionid"]);        
    
    // actualizamos la base de datos blanqueando la session id y estableciendo el validthru null
    $sql =  "UPDATE customers " .
            "SET    sessionid = NULL,".
            "       validthru = NULL ".
            "WHERE   " .
            "    sessionid='".$sessionid."'";
    //ejecutamos el sql        
    if (!$db->query($sql)){
        header("HTTP/1.1 500");
        echo (json_encode(array(msg=>$db->error)));
        die();
    }
    //revisamos la respuesta del query y verificamos si fue ejecutada correctamente 
    if ($db->affected_rows == 0){
        header("HTTP/1.1 401");
        echo (json_encode(array('msg'=>"id/session incorrectos")));
        die();
    }

    //mostramos los datos recibidos
    $out=new stdClass;
    $out->id = 0;

    // Audit
    insertAudit($db,$email,$_SERVER['REMOTE_ADDR'],APP_APP,MODULE_SECURITY,"Cerró sesión en DaycoPrint");  
    //lanzamos un codigo que todo salio bien
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>

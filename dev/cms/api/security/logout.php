<?php
// cms/api/login/logout

    header("Content-Type:application/json");
    include("../../../settings/dbconn.php");
    include("../../../settings/utils.php");  
    
    //validamos que recibimos siempre los datos por GET
    if (!isset($_GET["sessionid"])){
        badEnd("400", array("msg"=>"Parametros obligatorios sessionid"));
    }
    
    //definimos las variables y las rellenamos con los datos recibidos del GET
    $sessionid = $_GET["sessionid"];
    $email=getEmail($_REQUEST["sessionid"],APP_CMS,$db);
    isSessionValidCMS($db, $_REQUEST["sessionid"]);
    
    // actualizamos la base de datos blanqueando la session id y estableciendo el validthru null
    $sql =  "UPDATE users " .
            "SET    sessionid = NULL,".
            "       validthru = NULL ".
            "WHERE  sessionid='".$sessionid."'";
    //ejecutamos el sql        
    if (!$db->query($sql)){
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    }
    //revisamos la respuesta del query y verificamos si fue ejecutada correctamente 
    if ($db->affected_rows == 0){
        badEnd("401", array("msg"=>"id/session incorrectos"));        
    }

    //mostramos los datos recibidos
    $out = new stdClass;
    $out->id = 0;
    
    // Audit
    insertAudit($db,$email,$_SERVER['REMOTE_ADDR'],APP_CMS,MODULE_SECURITY,"Cerró sesión en CMS");  

    //lanzamos un codigo que todo salio bien
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>

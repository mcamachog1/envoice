<?php
// cms/api/users/update

// Librerias
    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");

// Funciones locales    
    function getStatus($userid,$db){
        $sql = "SELECT status FROM users WHERE id=$userid ";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        
        $row = $rs->fetch_assoc();
        return $row['status'];
    }   
    function createSystemUser($db){
        $sql= "INSERT INTO `users`(
            `id`,
            `usr`,
            `name`,
            `sessionid`,
            `validthru`,
            `lastsession`,
            `hashrecover`,
            `fails`,
            `maxfails`,
            `pwd`,
            `status`,
            `datecreated`
            )
            VALUES(
                -1,
                'sys@totalsoftware.com.ve',
                'Sistema',
                NULL,
                NULL,
                NULL,
                NULL,
                '0',
                '5',
                NULL,
                -1,
                CURRENT_TIMESTAMP
            )";

        if (!$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        return $db->insert_id;
    }
    function existSystemUser($db){
        $sql= "SELECT COUNT(*) Cnt FROM `users` WHERE id=-1";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        $row = $rs->fetch_assoc();
        if ($row['Cnt']!=0)
            return true;
        else 
            return false;
     }          
// Parametros obligatorios
    $parmsob = array("id","usr","name","status","sessionid");
    if (!parametrosValidos($_REQUEST, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
// Validar sesion
    $userid = isSessionValidCMS($db, $_REQUEST["sessionid"]);

// Crear usuario del Sistema para auditoria si no existe
    if (!existSystemUser($db))
        createSystemUser($db);

// Leer parametros    
    $status = $_REQUEST["status"];      
    $columns="id,usr,name,status";
    $values=$_REQUEST["id"].",'".$_REQUEST["usr"]."','".$_REQUEST["name"]."',".$_REQUEST["status"];
    $updatelist = "usr='".$_REQUEST["usr"]."',name='".$_REQUEST["name"]."',status=".$_REQUEST["status"];
    $oldstatus=getStatus($_REQUEST['id'],$db);
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

        $resetfails="";    
        if ($status==1 && $status!=getStatus($id,$db))
            $resetfails=", fails=0 ";        
        
        $sql =  "UPDATE     users " .
                "SET        usr='" . $_REQUEST["usr"] . "'," .
                "           name='" . $_REQUEST["name"] . "'," .
                "           status='" . $_REQUEST["status"] . "' " .$resetfails.
                "WHERE      id=" . $_REQUEST["id"];
        if (!$db->query($sql)) {
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
            
        }
    }
    $usr = $_REQUEST["usr"];

    if ($_REQUEST["id"]==0)
        insertAudit($db,getEmail($_REQUEST["sessionid"],'CMS',$db),$_SERVER['REMOTE_ADDR'],'CMS','users',"Se creó un usuario de CMS - $usr ");        
    else{
        insertAudit($db,getEmail($_REQUEST["sessionid"],'CMS',$db),$_SERVER['REMOTE_ADDR'],'CMS','users',"Se modificó un usuario de CMS - $usr ");        
        if ($status==1 && $oldstatus==0)
            insertAudit($db,getEmail($_REQUEST["sessionid"],'CMS',$db),$_SERVER['REMOTE_ADDR'],'CMS','users',"Se habilitó un usuario de CMS - $usr");            
        elseif ($status==0 && $oldstatus==1)
            insertAudit($db,getEmail($_REQUEST["sessionid"],'CMS',$db),$_SERVER['REMOTE_ADDR'],'CMS','users',"Se inhabilitó un usuario de CMS - $usr");            
    }
        

    $out = new stdClass;    
    $out->id =(integer)$id;
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>

<?php
// cms/api/security/login
    header("Content-Type:application/json");
    include("../../../settings/dbconn.php");
    include("../../../settings/utils.php");
    
    // parametros obligatorios
    $parmsob = array("usr","pwd");
    if (!parametrosValidos($_REQUEST, $parmsob)){
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
    }
    
    $usr = strtoupper($_REQUEST["usr"]);
    $pwd = $_REQUEST["pwd"];
    
    $sessid = randomString(32);
    
    // destruir cualquier session anterior existente
    $sql =  "UPDATE users " .
            "SET    sessionid = '" . $sessid . "',".
            "       validthru = DATE_ADD(NOW(), INTERVAL 1 DAY), ".
            "       lastsession = NOW(),".
            "       fails=0 " .
            "WHERE  UPPER(usr)='".$usr."' ".
            "AND    pwd='".$pwd."' " .
            "AND    status<>0";
    if (!$db->query($sql)){
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    }
    
    if ($db->affected_rows == 0){
        // se incrementa los reintentos y se desactiva la cuenta en caso de llegar al maxfails
        $sql =  "UPDATE users " .
                "       SET fails = " .
                "             CASE WHEN fails < maxfails THEN " .
                "               fails + 1 " .
                "             ELSE " .
                "               fails " .
                "             END, " .
                "           status = " .
                "             CASE WHEN fails >= maxfails THEN " .
                "               0 " .
                "             ELSE " .
                "               status " .
                "             END " .
                "WHERE  UPPER(usr)=UPPER('" . $usr . "') " .
                "AND    fails < maxfails";
        if (!$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
            
        $sql="SELECT status FROM users ".
             "WHERE  UPPER(usr)='".$usr."' ";
        if (!$rs = $db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        $row = $rs->fetch_assoc();
        
        if ($row["status"]==0)
            badEnd("401", array("msg"=>"Usuario Inactivo" ));
        else
            badEnd("401", array("msg"=>"Usuario/Clave Inválidos" ));
    }
    
    $sql =  "SELECT  id, sessionid, DATE_FORMAT(validthru, '%Y%m%d%H%i%s') vt, name ".
            "FROM    users ".
            "WHERE   UPPER(usr)='".$usr."' ".
            "AND     pwd='".$pwd."'";
    
    if (!$rs = $db->query($sql)){
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    }
    $row = $rs->fetch_assoc();
    $out = new stdClass;
    $out->id = (integer) $row["id"];
    $out->sessionid = $row["sessionid"];
    $out->validthru = $row["vt"];
    $out->name = $row["name"];
    
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>
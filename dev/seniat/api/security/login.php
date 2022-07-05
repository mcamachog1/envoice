<?php
// seniat/api/security/login.php

    header("Content-Type:application/json");
    include("../../../settings/dbconn.php");
    include("../../../settings/utils.php");
    include("../functions.php");
    
    function createUserSession($email,$db){
        $name_session = $email."_session";
        $name_validthru = $email."_validthru";
        $name_fails = $email."_fails";        
        $sessid = randomString(32);        
        // Actualizar session
        $sql ="UPDATE preferences SET value='$sessid' WHERE LOWER(name)='$name_session'";
        if (!$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        // Si no existe el registro session, se crea
        if ($db->affected_rows==0){
            $sql ="INSERT INTO preferences (name, value) VALUES ('$name_session','$sessid')";
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        }
        // Actualizar validthru
        $sql ="UPDATE preferences SET value= DATE_ADD(NOW(), INTERVAL 1 DAY) WHERE LOWER(name)='$name_validthru'";
        if (!$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        // Si no existe el registro validthru, se crea
        if ($db->affected_rows==0){
            $sql ="INSERT INTO preferences (name, value) VALUES ('$name_validthru',DATE_ADD(NOW(), INTERVAL 1 DAY))";
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        }
        // Actualizar fails
        $sql ="UPDATE preferences SET value= 0 WHERE LOWER(name)='$name_fails'";
        if (!$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        // Si no existe el registro validthru, se crea
        if ($db->affected_rows==0 && getNumOfFails($email,$db)!=0){
            $sql ="INSERT INTO preferences (name, value) VALUES ('$name_fails',0)";
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        }
        // Actualizar status
        setStatus($email,1,$db);


        // Obtener el id de la session
        $sql ="SELECT id  FROM preferences WHERE LOWER(name)='$name_session'";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        $row = $rs->fetch_assoc();            
        $id = $row['id'];        

        return array('sessionid'=>$sessid, 'validthru'=>Date('Ymdhms', strtotime('+1 days')), 'id'=>$id);
    }
    
    function validatePassw($email,$password,$db){
        $name = $email."_password";
        $sql ="SELECT COUNT(*) Cnt FROM preferences WHERE LOWER(name)='$name' AND value='$password'";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        $row = $rs->fetch_assoc();            

        return $row['Cnt'];
    }

    function getNumOfFails($email,$db){
        $name = $email."_fails";
        $sql = "SELECT value FROM preferences WHERE LOWER(name)='$name'";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        $row = $rs->fetch_assoc();
        return (integer)$row['value'];        
    }

    function setNumOfFails($email,$db){
        if (getStatus($email,$db)==1 || getStatus($email,$db)==-1){
            $qtyfails=getNumOfFails($email,$db)+1;
            $name = $email."_fails";
            $sql = "UPDATE preferences set value=$qtyfails WHERE LOWER(name) = '$name'";
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
            // Si no existe el registro fails, se crea
            if ($db->affected_rows==0){
                $sql = "INSERT INTO preferences (name, value) VALUES ('$name',$qtyfails)";    
                if (!$db->query($sql))
                    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));              
            }
            return (integer)$db->affected_rows;
        }
        return 0;
    }

    function getStatus($email,$db){
        $name = $email."_status";
        $sql = "SELECT COUNT(*) Cnt FROM preferences WHERE LOWER(name)='$name'";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        $row = $rs->fetch_assoc();
        if ($row['Cnt']==0)
            return -1;
        $sql = "SELECT value FROM preferences WHERE LOWER(name)='$name'";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        $row = $rs->fetch_assoc();
        return $row['value'];        
    }

    function setStatus($email,$status,$db){
        if ((getStatus($email,$db)==1 && $status==0)|| getStatus($email,$db)==-1 ){
            $name = $email."_status";
            $sql = "UPDATE preferences set value=$status WHERE LOWER(name) = '$name'";
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
            // Si no existe el registro fails, se crea
            if ($db->affected_rows==0){
                $sql = "INSERT INTO preferences (name, value) VALUES ('$name',$status)";    
                if (!$db->query($sql))
                    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));              
            }
            return $db->affected_rows;
        }
        else return 0;
    }

    // Parametros obligatorios
    $parmsob = array("usr","pwd");
    if (!parametrosValidos($_REQUEST, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

    $usr = strtolower($_REQUEST["usr"]);
    $pwd = $_REQUEST["pwd"];

   
    $username = validateEmail($usr,$db);

    //print_r(validatePassw($usr,$pwd,$db));
    //die();
    if (validatePassw($usr,$pwd,$db)!=1 && (getStatus($usr,$db)==1 || getStatus($usr,$db)==-1)) {
        setNumOfFails($usr,$db);
        $qtytry=getNumOfFails($usr,$db);
        if ($qtytry>=5){
            setStatus($usr,0,$db);
            badEnd("400", array("msg"=>"Usuario $usr bloqueado"));
        }
        badEnd("400", array("msg"=>"Clave incorrecta intento $qtytry "));        
    }
    elseif (validatePassw($usr,$pwd,$db)==1 && (getStatus($usr,$db)==1 || getStatus($usr,$db)==-1))
        $usersession=createUserSession($usr,$db);
    else 
        badEnd("400", array("msg"=>"Usuario $usr se encuentra bloqueado"));

    $out= new stdClass;
    $out->id = (integer)$usersession['id'];
    $out->sessionid = $usersession['sessionid'];
    $out->validthru = $usersession['validthru'];
    $out->name = $usr;
 
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>


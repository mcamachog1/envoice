<?php
    function validateUser($user,$db){
        $sql ="SELECT COUNT(*) Cnt FROM preferences WHERE LOWER(name)='$user'";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        $row = $rs->fetch_assoc();
        if ( $row['Cnt']!=1)
            badEnd("400", array("msg"=>"Usuario $user no existe")); 
        $sql ="SELECT value AS email FROM preferences WHERE LOWER(name)='$user'";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error,"errid"=>1));
        $row = $rs->fetch_assoc();

        return strtolower($row['email']);
    }
    
    function validateEmail($email,$db){
        $sql ="SELECT COUNT(*) Cnt FROM preferences WHERE LOWER(value)='".strtolower($email)."'";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error,"errid"=>2));
        if (!$row = $rs->fetch_assoc())
            badEnd("401", array("msg"=>"Usuario/Clave Inválidos" ));
        if ( $row['Cnt']!=1)
            badEnd("400", array("msg"=>"Usuario $email no existe")); 
        $sql ="SELECT name FROM preferences WHERE LOWER(value)='".strtolower($email)."'";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error,"errid"=>3));
        $row = $rs->fetch_assoc();

        return $row['name'];
    }    

    function setPassword($hash,$pwd,$db){
        // Obtener email del hash
        $sql ="SELECT name FROM preferences WHERE value = '$hash'";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error,"errid"=>4));
        $row = $rs->fetch_assoc();
        $pos = strrpos($row['name'],"_");
        $email=substr($row['name'],0,$pos);
        
        // Validar email
        validateEmail($email,$db);     

        $name_password=strtolower($email)."_password";
        $name_hash = strtolower($email)."_hash";
        $name_fails = strtolower($email)."_fails";
        //Validar que existe el hash
        $sql ="SELECT COUNT(*) Cnt FROM preferences WHERE value = '$hash'";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error,"errid"=>5));
        $row = $rs->fetch_assoc();
        if ( $row['Cnt']!=1)
            badEnd("400", array("msg"=>"Hash de recuperación $hash incorrecto"));
        
        //Validar que existe el registro password
        $sql ="SELECT COUNT(*) Cnt FROM preferences WHERE name = '$name_password'";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error,"errid"=>5));
        $row = $rs->fetch_assoc();

        // Actualizar password si existe
        if ( $row['Cnt']==1) {
            $sql ="UPDATE preferences SET value= '$pwd' WHERE name='$name_password'";
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error,"errid"=>6));
        }
        // Si no existe el registro password, se crea
        else {
            $sql ="INSERT INTO preferences (name, value) VALUES ('$name_password','$pwd')";
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        }
        // Actualizar fails
        $sql ="UPDATE preferences SET value= NULL WHERE name='$name_fails'";
        if (!$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        $sql ="UPDATE preferences SET value= 0 WHERE name='$name_fails'";
        if (!$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        // Si no existe el registro fails, se crea
        if ($db->affected_rows==0){
            $sql ="INSERT INTO preferences (name, value) VALUES ('$name_fails',0)";
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        }        
    }     
    
    function validSession($db,$sessionid,$data=array()){
        // Existe la sesion?
        $sql= "SELECT COUNT(*) AS Cnt FROM preferences WHERE value='$sessionid' AND name LIKE '%@%.%_session'";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        $row = $rs->fetch_assoc();  
        // Si existe obtener email
        if ($row['Cnt']==1){
            // Email
            $sql= "SELECT name FROM preferences WHERE value='$sessionid'";
            if (!$rs=$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
            $row = $rs->fetch_assoc();        
            $name_session = $row['name']; 
            $pos = strrpos($row['name'],"_");
            $email = substr($row['name'],0,$pos);                 
            // Buscar registro v��lido con sesion vigente
            $name_validthru = $email."_validthru";
            $sql= "SELECT COUNT(*) Cnt FROM preferences WHERE name='$name_validthru' AND value > NOW()" ;
            if (!$rs=$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
            $row = $rs->fetch_assoc();        
            // No hay sesi��n vigente, mensaje de error
            if ($row['Cnt']==0)
                badEnd("400", array("msg"=>"Sesi��n expirada o incorrecta"));
            // Hay sesion vigente guardar auditoria y retornar email
            if (count($data)>0)
                insertAudit($db,'-1',$data['ip'],$data['app'],$data['module'],$data['dsc']." - SeniatUser:".getEmail($sessionid,$data['app'],$db));
    
            return $email;
        }
            
        // Si no existe, enviar  mensaje de error
        elseif ($row['Cnt']==0)
            badEnd("400", array("msg"=>"Sesi��n expirada o incorrecta"));
    }    
    

?>
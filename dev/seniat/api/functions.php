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
        $row = $rs->fetch_assoc();
        if ( $row['Cnt']!=1)
            badEnd("400", array("msg"=>"Email $email no existe")); 
        $sql ="SELECT name AS email FROM preferences WHERE LOWER(value)='".strtolower($email)."'";
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
        //Validar que existe el hash
        $sql ="SELECT COUNT(*) Cnt FROM preferences WHERE value = '$hash'";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error,"errid"=>5));
        $row = $rs->fetch_assoc();
        if ( $row['Cnt']!=1)
            badEnd("400", array("msg"=>"Hash de recuperación $hash incorrecto"));

        // Actualizar password
        $sql ="UPDATE preferences SET value= '$pwd' WHERE name='$name_password'";
        if (!$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error,"errid"=>6));
        // Si no existe el registro password, se crea
        if ($db->affected_rows==0){
            $sql ="INSERT INTO preferences (name, value) VALUES ('$name_password','$pwd')";
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        }
    }     
    
    function validSession($sessionid,$db){
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
            $email = getEmail($row['name']);
            // Buscar registro v��lido con sesion vigente
            $name_validthru = $email."_validthru";
            $sql= "SELECT COUNT(*) Cnt FROM preferences WHERE name='$name_validthru' AND value > NOW()" ;
            if (!$rs=$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
            $row = $rs->fetch_assoc();        
            // No hay sesi��n vigente, mensaje de error
            if ($row['Cnt']==0)
                badEnd("400", array("msg"=>"Sesi��n expirada o incorrecta"));
            // Hay sesion vigente retornar email
            return $email;
        }
            
        // Si no existe mensaje de error
        elseif ($row['Cnt']==0)
            badEnd("400", array("msg"=>"Sesi��n expirada o incorrecta"));
    }    
    
    function getEmail($name){
        $pos = strrpos($name,"_");
        return strtolower(substr($name,0,$pos));
    }
    
            
        
?>
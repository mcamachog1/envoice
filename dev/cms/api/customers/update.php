<?php
// cms/api/customers/update

    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");

    function existsInvoices($customerid,$serie,$db){
        $sql= "SELECT COUNT(*) as Cnt FROM  invoiceheader ".
            " WHERE customerid=$customerid AND SUBSTRING(LPAD(ctrnumber,12,'0'),1,2) = LPAD('$serie',2,'0') ";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        $row = $rs->fetch_assoc();
        if ($row["Cnt"]>0) 
            return true;
        else
            return false;
    }
    function getStatus($customerid,$db){
        $sql = "SELECT status FROM customers WHERE id=$customerid ";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        
        $row = $rs->fetch_assoc();
        return $row['status'];
    }     
    function getCustomers($db){
        $sql = "SELECT id, serie, initialcontrol, nextcontrol FROM customers ";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        $customers = array();
        while ($row = $rs->fetch_assoc()) {
            $customer = new stdClass();
            $id = $row["id"];
            $customer->id = $id;
            $customer->serie = $row["serie"];
            $customer->initial= $row["initialcontrol"];
            $customer->nextcontrol=$row["nextcontrol"];
            $customers[$id] = $customer;
        }
        return $customers;
    }

    
    function validateUpdateSerie($series,$controls,$customerid,$db){
        $back_series = getSeries($customerid,$db);
        $back_initials = getInitialControls($customerid,$db);
        $back_nexts = getNextControls($customerid,$db);
        $front_series = explode("-",$series);
        $front_initials = explode("-",$controls);
        // Revisar lista de front
            for ($j=0; $j< count($front_series); $j++){
                $exists = false;
                for ($i=0; $i < count($back_series); $i++) {


                    if (trim($front_series[$j]) == trim($back_series[$i]) && !$exists) {
                        $exists = true;

                        if ($back_initials[$i] != $front_initials[$j])
                            badEnd("400", array("msg"=>"Error en los numeros de control iniciales"));
                        break;
                    }
                }
                // Incluir serie nueva
                if (!$exists) {
                    $back_series[] = $front_series[$j];
                    $back_initials[] = $front_initials[$j];
                    $back_nexts[]=$front_initials[$j];
                }
            }
        // Revisar lista de back
            for ($j=0; $j< count($back_series); $j++){
                $exists = false;
                for ($i=0; $i < count($front_series); $i++) {
 

                    if (trim($back_series[$j]) == trim($front_series[$i]) && !$exists) {
                        $exists = true;
 
                        if ($front_initials[$i] != $back_initials[$j])
                            badEnd("400", array("msg"=>"Error en los numeros de control iniciales"));
                        break;
                    }
                }
                if (!$exists) {
                    if (existsInvoices($customerid,$back_series[$j],$db))
                        badEnd("400", array("msg"=>"No puede eliminarse la serie, tiene facturas emitidas"));
                    unset($back_series[$j]);
                    unset($back_initials[$j]);
                    unset($back_nexts[$j]);
                }
            }

        $out = new stdClass();
        $out->series = implode("-",$back_series);
        $out->initials = implode("-",$back_initials);
        $out->nexts = implode("-",$back_nexts);

        return ($out);    
    }

            
        
    // parametros obligatorios
    // si serie viene vacio, es valido
        if (parametroValidoVacio($_REQUEST, 'serie')) {
            $serie= " ";
            // se valida el resto de los parametros
            $parmsob = array("id","name","address","rif","phone","control","contactname","contactemail","status","sessionid");
            if (!parametrosValidos($_REQUEST, $parmsob))
                badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
        }
        // si no viene vacio, se valida normal
        else {
            $serie = avoidInjection($_REQUEST["serie"],"list");  
            $parmsob = array("id","name","address","rif","phone","serie","control","contactname","contactemail","status","sessionid");
            if (!parametrosValidos($_REQUEST, $parmsob))
                badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
        }


        $id=$_REQUEST["id"];
        $name=avoidInjection($_REQUEST["name"],"str");
        $address=avoidInjection($_REQUEST["address"],"str");
        $rif=avoidInjection($_REQUEST["rif"],"str");
        $phone=avoidInjection($_REQUEST["phone"],"str");
        $control=avoidInjection($_REQUEST["control"],"list");
        $contactname=avoidInjection($_REQUEST["contactname"],"str");
        $contactemail=avoidInjection($_REQUEST["contactemail"],"email");
        $status=$_REQUEST["status"];
        $sessionid=$_REQUEST["sessionid"];
        
        $userid = isSessionValidCMS($db, $sessionid);
        
        // Validar consistencia entre cantidad de series e initialcontrol
        if (!series_validate($serie,$control))
            badEnd("400", array("msg"=>"Disparidad entre numeros de serie y numeros de control"));
        
        $columns="id,name,address,rif,phone,serie,initialcontrol,contactname,contactemail,status";
        $values="$id,'$name','$address','$rif','$phone','$serie','$control','$contactname','$contactemail',$status";
        $updatelist = "name='$name',address='$address',rif='$rif',phone='$phone',contactname='$contactname',".
                    "contactemail='$contactemail',status=$status";    

        // parametros opcionales
        if (isset($_REQUEST["ftpusr"]))
            $ftpusr=avoidInjection($_REQUEST["ftpusr"],"str");
        if (isset($_REQUEST["ftppwd"]))
            $ftppwd=avoidInjection($_REQUEST["ftppwd"],"str");        
        
        // Validar duplicidad de rif
        $sql="SELECT Count(*) Cnt ".
        " FROM customers ".
        " WHERE rif= '$rif' AND id <> $id";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        if ($row = $rs->fetch_assoc())
            if ($row["Cnt"]>0)
                badEnd("400", array("msg"=>"Rif $rif ya existe"));
        //
        // Validar duplicidad de correo de contacto
        $sql="SELECT Count(*) Cnt ".
        " FROM customers ".
        " WHERE contactemail= '$contactemail' AND id <> $id";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        if ($row = $rs->fetch_assoc())
            if ($row["Cnt"]>0) 
                badEnd("400", array("msg"=>"Correo $contactemail ya existe"));
        //
        

        if ($id==0) { 
            // Es un insert
            $columns .= ",nextcontrol";
            $values .= ",'$control'";
            $sql =  "INSERT INTO customers ($columns) " .
                    "VALUES         ($values)";
            if (!$db->query($sql)) {
                if ($db->errno == 1062)
                    badEnd("409", array("msg"=>$msg));
                else 
                    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
            }
            $id_result =$db->insert_id;
        }
        else {
            // Es un update
            // El nextcontrol debe mantener los valores anteriores e incorporar los nuevos o eliminar los que se indiquen
            $object=validateUpdateSerie($serie,$control,$id,$db);
            $updatelist .= ",nextcontrol='".$object->nexts."',serie='".$object->series."',initialcontrol='".$object->initials."' ";
            
            $resetfails="";
            if ($status==1 && $status!=getStatus($id,$db))
              $resetfails=", fails=0 ";
            
            $sql =  "UPDATE     customers " .
                    "SET        $updatelist " . $resetfails.
                    "WHERE      id=$id" ;
         

            if (!$db->query($sql)) {
                if ($db->errno == 1062)
                    badEnd("409", array("msg"=>$msg));
                else 
                    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
            }
            $id_result = $id;
        }

        //  IMAGEN //
        if (isset($_FILES["IMAGEN"]) and count($_FILES)>0){
            if (!$_FILES["IMAGEN"]["tmp_name"])
                badEnd("500", array(id=>0,msg=>"Archivo no enviado"));
            switch ($_FILES["IMAGEN"]["type"]){
                case "image/png":
                    $ext = ".png";
                    break;
                case "image/jpeg":
                case "image/jpg":
                    $ext = ".jpg";
                    break;
                default:
                    badEnd("500", array(id=>1,msg=>"El formato del documento debe ser PNG o JPG"));
            }
            $ruta='../../uploads/customers/'.$id_result.$ext;
            if(move_uploaded_file($_FILES["IMAGEN"]["tmp_name"], $ruta)){
                // se colocan datos para actualizar en la BD la imagen subida
                $columns = "imgtype";
                $updatelist ="imgtype='".$_FILES["IMAGEN"]["type"]."'";
            } else 
                badEnd("500", array(id=>2,msg=>"Error en funcion move_uploaded_file"));
            
            $sql =  "UPDATE     customers " .
                    "SET        $updatelist " .
                    "WHERE      id=$id_result" ;
            if (!$db->query($sql)) 
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));            
        
        }  
        //  IMAGEN //


        if (!is_null($ftpusr) || !is_null($ftppwd)) {
            $columns = $columns.",ftpusr,ftppwd";
            $values = $values.",'$ftpusr','$ftppwd'";
            $updatelist = $updatelist.",ftpusr='$ftpusr', ftppwd='$ftppwd'";
        }    

        $out = new stdClass;    
        $out->id =(integer)$id_result;
        header("HTTP/1.1 200");
        echo (json_encode($out));
        die();
        
?>

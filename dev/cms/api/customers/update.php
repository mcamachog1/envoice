<?php
// cms/api/customers/update

    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");

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
    
    function validateUpdateSerie($serie,$control,$customerid,$db){
        // Valores que vienen
        $series_coming = explode("-",$serie);
        $controls_coming = explode("-",$control);
        $coming_size = count($series_coming);        
        $coming = array();
        for ($i = 0; $i < $coming_size; $i++) {
            $object = new stdClass();
            $object->initial=$controls_coming[$i];
            $object->next = null;
            $coming["$series_coming[$i]"] =  $object;
        }
              
        // Valores guardados en la bd
        $customers = getCustomers($db);
        $customer = $customers[$customerid];
        $series_saved = explode("-",$customer->serie);
        $controls_saved = explode("-",$customer->initial);
        $nextcontrols_saved = explode("-",$customer->nextcontrol);        
        $saved_size = count($series_saved);     
        $saved = array();
        for ($i = 0; $i < $saved_size; $i++) {
            $object = new stdClass();
            $object->initial=$controls_saved[$i];
            $object->next = $nextcontrols_saved[$i];
            $saved["$series_saved[$i]"] =  $object;
        }
        
        // Guardar todos los valores        
        $all_values = array();

        foreach($saved as $key => $value) 
            $all_values["$key"] = $value;

        foreach ($coming as $coming_key => $coming_value) 
            if (!$all_values["$coming_key"]) 
                $all_values["$coming_key"] = $coming_value;

        //Caso 1: La serie viene y ya existe. El control debe ser igual
        foreach ($coming as $coming_key => $coming_value) 
            if ($all_values[$coming_key]->initial != $coming_value->initial ) 
                badEnd("400", array("msg"=>"La serie '$coming_key' trae diferente initial"));                 
        //Caso 2: La serie es nueva. Se actualiza el nextcontrol con el initial
        foreach ($all_values as $key => $value) 
            if (is_null($value->next))
                $value->next = $value->initial;
        //Caso 3: Si falta una serie, intentar eliminarla
            //Buscar series a eliminar
            //Validar cada serie para tratar de eliminarla
        $output_serie = array();
        $output_initial = array();
        $output_next = array();
        foreach ($all_values as $key => $value) {
            $output_serie[] = $key;
            $output_initial[] = $value->initial;
            $output_next[] = $value->next;
        }
        $output = array("serie"=>implode("-",$output_serie), "initial"=>implode("-",$output_initial),"next"=>implode("-",$output_next));
        return $output;
        /*
        print_r ("output\n");
        print_r ($output); 
        badEnd("400", array("msg"=>"Fin"));
        validateUpdateSerie('-A-B-C-F','0000000001-0000000001-0100000100-0200000001-0000000001',92,$db);
        */
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
        // El next control es el mismo control initial
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
        $updatelist .= ",nextcontrol='".$object['next']."',serie='".$object['serie']."',initialcontrol='".$object['initial']."' ";
        $sql =  "UPDATE     customers " .
                "SET        $updatelist " .
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

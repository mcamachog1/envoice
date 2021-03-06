<?php
    //  app/api/invoices/update.php
    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");

  
    function existsInvoice($ctrref,$db){
        return true;
    }

    //Recibir JSON y convertir a objeto
    $data = json_decode(file_get_contents('php://input'), false);

    // parametros obligatorios
    $id=$data->id;
    $sessionid=$data->sessionid;
    if (is_null($id) || is_null($sessionid))
        badEnd("400", array("msg"=>"Parametros obligatorios id, sessionid" ));
        
    // Validar user session
    $customerid = isSessionValid($db,$sessionid);

    //Llenar variables
    $serie=avoidInjection($data->seriecontrol->serie,'str');
    $control=avoidInjection($data->seriecontrol->control,'str');
    $type=avoidInjection($data->type,'str');
    $ctrref_serie = avoidInjection($data->ctrref->serie,'str');
    $ctrref_control = avoidInjection($data->ctrref->control,'str');
    $ctrref_numero = avoidInjection($data->ctrref->numero,'str');
    if(($type=='NDB' || $type == 'NDC') && trim($ctrref_control)=="")
        badEnd("400", array("msg"=>"Numero de serie y de control es requerido"));
    $ctrref =  $ctrref_serie.str_pad($ctrref_control,2,"0",STR_PAD_LEFT).str_pad($ctrref_numero,8,"0",STR_PAD_LEFT);
    $clientrif = avoidInjection($data->client->rif,'rif');
    $issuedate=avoidInjection($data->issuedate,'date');
    $duedate=avoidInjection($data->duedate,'date');    
    $refnumber=avoidInjection($data->refnumber,'str');  
    $obs = avoidInjection($data->obs,'str');    
    $clientname = avoidInjection($data->client->name,'str');
    $clientaddress = avoidInjection($data->client->address,'str');
    $mobilephone = avoidInjection($data->client->mobile,'str');
    $otherphone = avoidInjection($data->client->phone,'str');
    $clientemail =avoidInjection($data->client->email,'email');
    $currencyrate= avoidInjection($data->currencyrate,'float');
    $currency=avoidInjection($data->currency,'str');
    $discount=avoidInjection($data->discount,'float');
    $arraydetails = $data->details;


    // Si id de la factura es 0, es un insert de factura con header y detalle
    // Esto deber??a ser una transaccion (try)
    if ($id == 0){
        // Se calcula el numero de control del cliente para la nueva factura
        // Obtener index de la serie (no contempla series repetidas)
        $series = getSeries($customerid,$db);
        $index=array_search($serie, $series);
        if (is_null($index))
            badEnd("400", array("msg"=>"Serie no existe para este cliente"));
        
        
        // Obtener el nextcontrol actual
        $ctrnumber = getNextControl($serie,$customerid,$db);
        // Ubicar el next control
        $nexts=getNextControls($customerid,$db);
        $next = $nexts[$index];
        // Construir nextcontrol en string
        $nexts[$index] = $next + 1;
        $str_nextcontrol = implode("-",$nexts);
        // Se actualiza el nextcontrol del cliente
        $update = "UPDATE customers SET nextcontrol = '$str_nextcontrol' WHERE id=$customerid ";
        if (!$db->query($update)) 
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));        
        
        // SQL del Insert
        $sql="INSERT INTO `invoiceheader` (`id`, `type`,`customerid`, `issuedate`, `duedate`, `refnumber`, `ctrnumber`,`ctrref`, `clientrif`, " .
            " `clientname`, `clientaddress`, `mobilephone`, `otherphone`, " .
            " `obs`, `clientemail`, `creationdate`, `currencyrate`, `currency`, `discount`) " .
            " VALUES (NULL, '$type',$customerid, '$issuedate', '$duedate', '$refnumber', CONCAT('$serie',LPAD($ctrnumber,10,'0')), '$ctrref','$clientrif', '$clientname', '$clientaddress', " .
            " '$mobilephone', '$otherphone', '$obs', '$clientemail', CURRENT_TIMESTAMP, $currencyrate, '$currency',  $discount)";
        if (!$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        $invoiceid = $db->insert_id;
        
        // Bucle para insertar los details
        foreach($arraydetails as $index => $object) {
            $itemref = $object->itemref;
            $itemdsc = $object->itemdsc;
            $qty = $object->qty;
            $unitprice = $object->unitprice;
            $itemtax = $object->tax;
            $itemdiscount = $object->discount;
            
            $sql="INSERT INTO `invoicedetails` (`id`, `invoiceid`, `itemref`, `itemdsc`, `qty`, `unitprice`, `itemtax`, `itemdiscount`) ".
             "VALUES (NULL, $invoiceid, '$itemref', '$itemdsc', $qty, $unitprice, $itemtax, $itemdiscount) ";  
 
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
            
        }
    }
    // Si id de la factura es <> 0 es un update. 
    else {
        $invoiceid= $id;
        //Validar que la factura exista
        $sql = "SELECT COUNT(*) Cnt FROM invoiceheader WHERE id = $invoiceid AND customerid=$customerid ";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        $row = $rs->fetch_assoc();
        if ($row["Cnt"] == 0)       
            badEnd("400", array("msg"=>"La factura $invoiceid no existe o no esta asociada al cliente logueado"));

        //Update de todos los campos del header
        $update = "UPDATE `invoiceheader` ". 
                    " SET ". 
                    " `issuedate` = '$issuedate', ".
                    " `type` = '$type', ".                    
                    " `duedate` = '$duedate', ".
                    " `refnumber` = '$refnumber', ".
                    " `ctrref` = '$ctrref', ".                    
                    " `clientrif` = '$clientrif', ".
                    " `clientname` = '$clientname', ".
                    " `clientaddress` = '$clientaddress', ".
                    " `mobilephone` = '$mobilephone', ".
                    " `otherphone` = '$otherphone', ".
                    " `obs` = '$obs', ".
                    " `clientemail` = '$clientemail', ".
                    " `currencyrate` = '$currencyrate', ".
                    " `currency` = '$currency', ".
                    " `discount` = '$discount' ".
                    " WHERE `invoiceheader`.`id` = $invoiceid AND `customerid`=$customerid " ;
        if (!$db->query($update)) 
            if ($db->errno == 1062)
                badEnd("409", array("msg"=>$msg));
            else 
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        
        // Update de los renglones del detail.  Siempre se borran todos los detalles existentes y se insertan los nuevos
        // 1.- Borrar los detalles existentes
        $delete = " DELETE FROM invoicedetails WHERE invoiceid = $invoiceid ";
        if (!$db->query($delete))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));        
        
        // 2.- Insertar los nuevos
        foreach($arraydetails as $index => $object) {
            $itemref = $object->itemref;
            $itemdsc = $object->itemdsc;
            $qty = $object->qty;
            $unitprice = $object->unitprice;
            $itemtax = $object->tax;
            $itemdiscount = $object->discount;
            
            $sql="INSERT INTO `invoicedetails` (`id`, `invoiceid`, `itemref`, `itemdsc`, `qty`, `unitprice`, `itemtax`, `itemdiscount`) ".
             "VALUES (NULL, $invoiceid, '$itemref', '$itemdsc', $qty, $unitprice, $itemtax, $itemdiscount) "; 
            
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));             
        }        

    }
    
    // Salida
    $out = new stdClass;    
    $out->id =(integer)$invoiceid;

    header("HTTP/1.1 200");
    if($type=='NDB' || $type == 'NDC') 
        if (!existsInvoice($ctrref,$db)) //Si la factura de referencia no existe, se envia 203
            header("HTTP/1.1 203");
    echo (json_encode($out));
    die();    
?>

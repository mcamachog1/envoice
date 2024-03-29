<?php
    //  app/api/invoices/update.php
    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");

  
    function existsReferenceInvoice($customerid,$ctrref,$db){
        $ctrref = trim($ctrref);
        $sql = "SELECT ctrnumber FROM `invoiceheader` ".
        "       WHERE TRIM(ctrnumber) = '$ctrref' AND customerid=$customerid LIMIT 1";
        if (!$rs = $db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));     
        if (!$row = $rs->fetch_assoc())
            return false;
        return true;
    }
    function isUniqueInvoice($customerid,$type,$refnumber,$ctrnumber,$db){
        $ctrnumber = trim($ctrnumber);
        $sql = "SELECT customerid,type,refnumber,ctrnumber ".
        "       FROM invoiceheader ".
        "       WHERE   ".
        "       (customerid=$customerid AND type='$type' AND refnumber='$refnumber')  ".
        "       OR (customerid=$customerid AND TRIM(ctrnumber)='$ctrnumber')";

        if (!$rs = $db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));     
        if (!$row = $rs->fetch_assoc())
            return true;
        return false;        
    }
    function esFacturaExonerada ($arraydetails) {
        foreach($arraydetails as $index => $object) 
            if ($object->tax==-2)
                return True;
        return False;
    }
    //Recibir JSON y convertir a objeto
    $data = json_decode(file_get_contents('php://input'), false);

    // parametros obligatorios
    $id=$data->id;
    $sessionid=$data->sessionid;
    $customerid = isSessionValid($db, $sessionid);
    if (is_null($id) || is_null($sessionid))
        badEnd("400", array("msg"=>"Parametros obligatorios id, sessionid" ));
        
    //Llenar variables
    $serie=avoidInjection($data->seriecontrol->serie,'str');
    if (strlen($serie)==0)
        $serie = ' ';
    $control=avoidInjection($data->seriecontrol->control,'str');
    $type=avoidInjection($data->type,'str');
    $ctrref="";
    if($type=='NDB' || $type == 'NCR') {
        $ctrref_serie = avoidInjection($data->ctrref->serie,'str');
        $ctrref_control = avoidInjection($data->ctrref->control,'str');
        $ctrref_number = avoidInjection($data->ctrref->number,'str');
        if(trim($ctrref_control)=="")
            badEnd("400", array("msg"=>"Numero de serie y de control es requerido"));
        $ctrref =  $ctrref_serie.str_pad($ctrref_control,2,"0",STR_PAD_LEFT).str_pad($ctrref_number,8,"0",STR_PAD_LEFT);
        if (!existsReferenceInvoice($customerid,$ctrref,$db)) 
            badEnd("400", array("msg"=>"Factura de referencia no existe $ctrref"));
    }
  
    $clientrif = avoidInjection($data->client->rif,'rif');
    $issuedate=avoidInjection($data->issuedate,'date');
    $duedate=avoidInjection($data->duedate,'date');    
    $refnumber=avoidInjection($data->refnumber,'str'); 
    
    $obs = avoidInjection($data->obs,'str');    
    $clientname = avoidInjection($data->client->name,'str');
    $clientaddress = avoidInjection($data->client->address,'str');
    $mobilephone = avoidInjection($data->client->mobile,'mobile');
    $otherphone = avoidInjection($data->client->phone,'str');
    $clientemail =avoidInjection($data->client->email,'email');
    $currencyrate= avoidInjection($data->currencyrate,'float');
    $currency=avoidInjection($data->currency,'str');
    $discount=avoidInjection($data->discount,'float');
    $arraydetails = $data->details;

// Validar user session 
    $customerid = isSessionValid($db, $sessionid);

    // Apagar autocommit
    $db->autocommit(FALSE);
    $exception_id=0;
    try {
        // Si id de la factura es 0, es un insert de factura con header y detalle
         if ($id == 0){
            $invoiceid=0;

            // Obtener el nextcontrol actual
            $ctrnumber = getNextControl($serie,$customerid,$db);
       
            // Completar a 10 digitos
            $ctrnumber = str_pad($ctrnumber,10,"0",STR_PAD_LEFT);
            // Se actualiza el nextcontrol del cliente
            
            if (incrementNextControl($customerid,trim($serie),1,$db) === False)
                throw new Exception("Serie $serie no existe para este cliente $customerid");                        
          
            if (!isUniqueInvoice($customerid,$type,$refnumber,trim($ctrnumber),$db)){
                $exception_id = 4;
                throw new Exception("El documento $refnumber ya existe o el numero de control $ctrnumber ya está asignado a otro documento");
            }
        
            //Creamos la direccion del folder
            $urlfolder = "../../../formats/customers/".str_pad($customerid,  5, "0", STR_PAD_LEFT);            
            //Se utiliza el 000 para que ubique el directorio default
            $printformat = "000";
            if(is_dir($urlfolder)){  
                $files = [];
                $filesfromdir = glob($urlfolder.'/*');        
                //Recorremos todos los files y obtenemos solo los del tipo que se está guardando
                foreach($filesfromdir as $filefromdir){            
                    $extfromdir = pathinfo($filefromdir, PATHINFO_EXTENSION);
                    $filename = basename($filefromdir,".".$extfromdir);
                    $typeFormat = substr($filename, 0, 3);
                    if($typeFormat == strtolower($type))$files[] = $filename;
                }              
                //Obtenemos el último de todos para considerar el "mas reciente"
                $last = $files[count($files)-1];    
                $extfromdir = pathinfo($last, PATHINFO_EXTENSION);
                $filename = basename($last,".".$extfromdir);
                $typeFormat = substr($filename, 0, 3);
                //Se valida que exista el formato y si existe se guardan los números del último formato
                //Solo aplica para nuevos
                if($typeFormat != strtolower($type))$printformat = "";
                else $printformat = substr($filename, 3);    
            }
     
            // SQL del Insert
            $sql="INSERT INTO `invoiceheader` (`id`, `type`,`customerid`, `issuedate`, `duedate`, `refnumber`, `ctrnumber`,`ctrref`, `clientrif`, " .
                " `clientname`, `clientaddress`, `mobilephone`, `otherphone`, " .
                " `obs`, `clientemail`, `creationdate`, `currencyrate`, `currency`, `discount`, `printformat`, manualload) " .
                " VALUES (NULL, '$type',$customerid, '$issuedate', '$duedate', '$refnumber', CONCAT('$serie',LPAD($ctrnumber,10,'0')), '$ctrref','$clientrif', '$clientname', '$clientaddress', " .
                " '$mobilephone', '$otherphone', '$obs', '$clientemail', CURRENT_TIMESTAMP, $currencyrate, '$currency',  $discount, '$printformat', 1)";

            if (!$db->query($sql)){
                $exception_id = 3;
                throw new Exception("$db->error");
                //badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
            }
            $invoiceid = $db->insert_id;
            $exonerado = esFacturaExonerada($arraydetails);
            // Bucle para insertar los details
            foreach($arraydetails as $index => $object) {
                $itemref = $object->itemref;
                $itemdsc = $object->itemdsc;
                $qty = $object->qty;
                $unitdsc = $object->unit;
                $unitprice = $object->unitprice;
                if ($exonerado)
                    $itemtax=-2;
                else                    
                    $itemtax = $object->tax;
                $itemdiscount = $object->discount;
                
                $sql="INSERT INTO `invoicedetails` (`id`, `invoiceid`, `itemref`, `itemdsc`, `qty`, `unit`,`unitprice`, `itemtax`, `itemdiscount`) ".
                "VALUES (NULL, $invoiceid, '$itemref', '$itemdsc', $qty, '$unitdsc', $unitprice, $itemtax, $itemdiscount) ";  
    
                if (!$db->query($sql)){
                    $exception_id = 3;
                    throw new Exception("$db->error");
                    //badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
                }
                
            }
        }
        // Si id de la factura es <> 0 es un update. 
        else {
            $invoiceid= $id;
            //Validar que la factura exista
            $sql = "SELECT COUNT(*) Cnt FROM invoiceheader WHERE id = $invoiceid AND customerid=$customerid ";
            if (!$rs=$db->query($sql)){
                $exception_id = 3;
                throw new Exception($db->error);
                //badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
            }
            $row = $rs->fetch_assoc();
            if ($row["Cnt"] == 0){
                $exception_id=1;       
                throw new Exception("La factura $invoiceid no existe o no esta asociada al cliente logueado");
                //badEnd("400", array("msg"=>"La factura $invoiceid no existe o no esta asociada al cliente logueado"));
            }

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
                throw new Exception("$db->error");
                //badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
            
            // Update de los renglones del detail.  Siempre se borran todos los detalles existentes y se insertan los nuevos
            // 1.- Borrar los detalles existentes
            $delete = " DELETE FROM invoicedetails WHERE invoiceid = $invoiceid ";
            if (!$db->query($delete))
                throw new Exception("$db->error");
                //badEnd("500", array("sql"=>$sql,"msg"=>$db->error));        
            
            // 2.- Insertar los nuevos
            $exonerado = esFacturaExonerada($arraydetails);
            foreach($arraydetails as $index => $object) {
                $itemref = $object->itemref;
                $itemdsc = $object->itemdsc;
                $qty = $object->qty;
                $unitdsc = $object->unit;
                $unitprice = $object->unitprice;
                if ($exonerado)
                    $itemtax=-2;
                else                    
                    $itemtax = $object->tax;
                $itemdiscount = $object->discount;
                
                $sql="INSERT INTO `invoicedetails` (`id`, `invoiceid`, `itemref`, `itemdsc`, `qty`, `unit`,`unitprice`, `itemtax`, `itemdiscount`) ".
                "VALUES (NULL, $invoiceid, '$itemref', '$itemdsc', $qty, '$unitdsc', $unitprice, $itemtax, $itemdiscount) "; 

                if (!$db->query($sql)){
                    $exception_id = 3;
                    throw new Exception("$db->error");
                    //badEnd("500", array("sql"=>$sql,"msg"=>$db->error));             
                }
            }        

        }
        
    }
// Catch exception
    catch(Exception $e){
        // Rollback Transaction
        $db->rollback();
        // Prender autocommit
        $db->autocommit(TRUE); 

        // Si la factura de referencia no existe, se envía 203
        if ($exception_id==2)
            badEnd("203", array("msg"=>$e->getMessage()));
        // Si la factura no está asociada al cliente se envía 204    
        if ($exception_id==1)    
            badEnd("204", array("msg"=>$e->getMessage()));
        // Si hay error de BD se envía 500    
        if ($exception_id==3)    
            badEnd("500", array("msg"=>$e->getMessage()));            
        // Si la factura no es unica se envía 400
        if ($exception_id==4)    
            badEnd("400", array("msg"=>$e->getMessage()));            

    }
// Si todo bien Commit Transaction
    $db->commit();   
    // Prender autocommit
    $db->autocommit(TRUE); 

// Auditoria

    if ($id==0)
        insertAudit($db,getEmail($sessionid,APP_APP,$db),$_SERVER['REMOTE_ADDR'],APP_APP,MODULE_INVOICES,"Se creó un documento nuevo $refnumber");
    else
        insertAudit($db,getEmail($sessionid,APP_APP,$db),$_SERVER['REMOTE_ADDR'],APP_APP,MODULE_INVOICES,"Se modificó el documento $refnumber");

// Salida
    $out = new stdClass;    
    $out->id =(integer)$invoiceid;


    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();    
?>

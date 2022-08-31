<?php
    // app/api/invoices/show.php
    include("../../settings/dbconn.php");
    include("../../settings/utils.php");

    function exist_invoices ($db, $hash) {
        $sql = "SELECT COUNT(*) Cnt, MAX(id) as id FROM invoiceheader WHERE emailhash='$hash' ";
        if (!$rs = $db->query($sql))
            die("Factura no encontrada");     

        $row = $rs->fetch_assoc();
        if ($row["Cnt"]>0)
            return $row["id"];
        else
            return -1;
    }

    // parametros obligatorios
    $parmsob = array("hash");
    if (!parametrosValidos($_GET, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

    $hash = $_GET["hash"];

    // Validar user session
    //validSession($sessionid,$db);


    //Funcion para formatear el nro de control
    function formatRefctr($valor){  
        $cleaned = $valor;
        if (strlen($cleaned)){
             $prefijo = ""; $area = ""; $numero = "";
          
            if(strlen($cleaned)>=12){
              $cleaned = substr($cleaned,0,12);
            }
      
            if(strlen($cleaned)>8){                
              $numero = substr($cleaned,(strlen($cleaned)-8),8);
              if(abs(strlen($cleaned)-8)>0){
                if(abs(strlen($cleaned)-8)>2){            
                  $area = substr($cleaned,(abs(strlen($cleaned)-8)-2),2);
                  $prefijo = substr($cleaned,0,(abs(strlen($cleaned)-8)-2));
                }else{            
                  $area = substr($cleaned,0,abs(strlen($cleaned)-8));            
                  $prefijo = "";
                }
              }
              $fullnum = "";
              if($prefijo!="")
                $fullnum = ($prefijo."-".$area."-".$numero);
              else if($area!="")
                $fullnum = ($area."-".$numero);
              else if($numero!="")
                $fullnum = ($numero);
      
              
              return $fullnum;
            }else{
              return $cleaned;
            }
        }else{
            return("");
        }
    }
    $id = exist_invoices($db,$hash);
    if ($id>-1) {
        $out = new stdClass;
        
        $sql =  "SELECT " .
                " H.id, H.issuedate, H.duedate, H.refnumber, H.ctrnumber, H.clientrif, H.clientname, H.printformat, ".
                " mobilephone, otherphone, clientemail, clientaddress, obs, currency, currencyrate, ".
                " SUM( (unitprice*qty*(1-itemdiscount/100)) ) gross, ".
                " SUM( unitprice*qty*(itemtax/100)*(1-itemdiscount/100) ) tax, ".
                " H.discount, H.type, H.ctrref, ".
                " DATE_FORMAT(H.issuedate, '%d/%m/%Y') formatteddate, ".
                " DATE_FORMAT(H.duedate, '%d/%m/%Y') formattedduedate, ".            
                " H.sentdate, H.viewdate, SUM(D.qty) qty,   ".
                " C.name customername, C.RIF customerrif, C.address customeraddr, C.phone customerphone, ".
                " H.customerid customerid, C.imgtype imgtype ".
                " FROM    invoiceheader H ".
                " INNER JOIN invoicedetails D ON ".
                "            D.invoiceid = H.id ".
                " INNER JOIN customers C ON ".
                "            H.customerid = C.id ". 
                " WHERE H.id = $id "; 
        if (!$rs = $db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));   
            
        // Header   
        
        if ($row = $rs->fetch_assoc()){            
            $record = new stdClass();
            //#new
            $pre = "fac";
            $printformat = $row["printformat"];
            //###
            $record->id = (integer) $row["id"];
            $record->type =new stdClass();
                $record->type->id=$row['type'];
                switch ($row['type']) {
                    case 'FAC':
                        $record->type->name='Factura';
                        $pre = "fac";
                        break;
                    case 'NDB':
                        $record->type->name='Nota de Debito';
                        $pre = "ndb";
                        break;
                    case 'NCR':
                        $record->type->name='Nota de Credito';
                        $pre = "ncr";
                        break;
                }
            //Nro control de ref
            $record->ctrref =$row['ctrref'];
            //Objetos de fechas
            $record->issuedate = new stdClass();        
                $record->issuedate->date = $row["issuedate"];
                $record->issuedate->formatted = $row["formatteddate"];
            $record->duedate =new stdClass();
                $record->duedate->date = $row["duedate"];
                $record->duedate->formatted = $row["formattedduedate"];    
            //Campo referencia
            $record->refnumber = nvl($row["refnumber"],"");
            //Campo control
            $record->ctrnumber = formatRefctr(nvl($row["ctrnumber"],""));
            $record->customer = new stdClass();
            //Información del CUSTOMER dueño de FACTURA (cliente de dayco)
            $record->customer->rif = $row["customerrif"];
            $record->customer->address = $row["customeraddr"];
            $record->customer->phone = $row["customerphone"];
            $record->customer->name = $row["customername"];
            $record->customer->id = $row["customerid"];
            $record->customer->image="";        
            // Image
            if (!is_null($row["imgtype"])) {
                switch ($row["imgtype"]){
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
                $record->customer->image="customers/".$row["customerid"].$ext;
            }

            //Información del cliente A SER FACTURADO 
            $record->client =new stdClass();
                $record->client->rif = $row["clientrif"];
                $record->client->name = $row["clientname"];        
                $record->client->mobile = $row["mobilephone"];
                $record->client->phone = $row["otherphone"];
                $record->client->email = $row["clientemail"]; 
                $record->client->address = $row["clientaddress"]; 

            //Estado de la factura
            $record->status =new stdClass();
                $status=1;
                $status_dsc = "Por Enviar";
                if (!is_null($row["sentdate"])) {
                    $status=2;
                    $status_dsc = "Enviado";            
                }
                elseif (!is_null($row["viewdate"])) {
                    $status=3;
                    $status_dsc = "Visto";            
                }
                $record->status->id = $status;
                $record->status->dsc = $status_dsc;
            //Información sobre la tasa de cambio 
            $record->multicurrency =new stdClass(); 
                $record->multicurrency->rate =new stdClass();
                    $record->multicurrency->rate->number= $row["currencyrate"];
                    $record->multicurrency->rate->formatted= number_format($row["currencyrate"], 2, ",", ".");
                $record->multicurrency->currency=$row["currency"];

            //Observaciones adicionales
            $record->obs=nvl($row["obs"],"");
            //Subtotal
            $record->amounts = new stdClass();        
            $record->amounts->gross = new stdClass(); 
            $record->amounts->gross->number = (float)$row["gross"];
            $record->amounts->gross->formatted = number_format($row["gross"], 2, ",", ".");
            //IVA
            $record->amounts->tax = new stdClass(); 
            $record->amounts->tax->number = (float)$row["tax"];
            $record->amounts->tax->formatted = number_format($row["tax"], 2, ",", ".");          
            //Descuento
            $record->amounts->discount = new stdClass(); 
            //$record->amounts->discount->number = (float)$row["discount"];
            //$record->amounts->discount->formatted = number_format($row["discount"], 2, ",", ".");  
            $record->amounts->discount->number= (float)$row["discount"];
            $record->amounts->discount->percentage = $row["discount"]."%";
            //TOTAL
            $record->amounts->total = new stdClass(); 
            $record->amounts->total->number = (float)$row["gross"]*(1-(float)$row["discount"]/100) + (float)$row["tax"];
            $record->amounts->total->formatted = number_format((float)$row["gross"]*(1-(float)$row["discount"]/100) + (float)$row["tax"], 2, ",", ".");          
        
            
        }
        $record->details = [];
        // Details
        $sql = "SELECT id, itemref ref, itemdsc dsc, qty, unitprice, unit, ".
        " itemtax tax, itemdiscount discount, ".
        //" ROUND(unitprice*qty*(1+itemtax/100)*(1-itemdiscount/100),2) total ". 
        " ROUND(unitprice*qty*(1-itemdiscount/100),2) total ".     
        " FROM invoicedetails WHERE invoiceid = $id";
        if (!$rs = $db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error)); 
        $details = array(); // $details=[]
        while ($row = $rs->fetch_assoc()){
            $detail = new stdClass();
            $detail->id = (integer) $row["id"];
            $detail->item =new stdClass();
            $detail->item->ref = $row["ref"];
            $detail->item->dsc = $row["dsc"];
            $detail->qty =new stdClass();
            $detail->qty->number = (integer)$row["qty"];
            $detail->qty->formatted = number_format($row["qty"], 2, ",", ".");             
            $detail->item->unit = $row["unit"];
            $detail->unitprice =new stdClass();
            $detail->unitprice->number = (float)$row["unitprice"];
            $detail->unitprice->formatted = number_format($row["unitprice"], 2, ",", ".");
            $detail->tax =new stdClass();
            $detail->tax->number = (float)$row["tax"]/100;
            $detail->tax->formatted = $row["tax"]."%";        
            $detail->discount =new stdClass();
            $detail->discount->number = (float)$row["discount"]/100;
            $detail->discount->formatted = number_format($row["discount"], 2, ",", ".")."%";  
            $detail->total =new stdClass();
            $detail->total->number = (float)$row["total"];
            $detail->total->formatted = number_format($row["total"], 2, ",", ".");         
    
            $details[] = $detail;
        } 
        $record->details = $details;

        $image = $record->customer->image;
        $commercerif = $record->customer->rif; 
        $commercename = $record->customer->name;
        $commerceaddr = $record->customer->address;
        $commercephn = $record->customer->phone;
        $type = $record->type->name;
        $invoiceDate = $record->issuedate->formatted;
        $dueDate = $record->duedate->formatted;
        $invoiceCtrl = $record->ctrnumber;
        $invoiceNum =  $record->refnumber;
        $invoiceClient =  $record->client->name;
        $customerRif =  $record->client->rif;
        $customerAddr =  $record->client->address;
        $customerPhn =  $record->client->mobile;
        $customerPhn2 =  $record->client->phone;
        $customerEmail =  $record->client->email;
        $conditions = $record->obs;        
        $urlfonts = "../../formats/";
        $urllogo = "../../cms/uploads/";
        $iddir = str_pad($record->customer->id,5,"0", STR_PAD_LEFT);
        $nro = rand(5,getrandmax());
        
        //Si todo ok hasta acá validamos la fecha de lectura y actualizamos
        $sql = "SELECT viewdate FROM invoiceheader WHERE id=".$id;
        if ($rs = $db->query($sql)){
            $row = $rs->fetch_assoc();
            if($row['viewdate']==null){
                $sqlup = "UPDATE invoiceheader SET viewdate=NOW() WHERE id=".$id;
                if (!$rsup = $db->query($sqlup))
                    echo("Factura no encontrada al actualizar la visualización"); 
                
            }
        }

        //Lo primero que debemos hacer es construir la url con la plantilla almacenada en la tabla
        if ($printformat=="") $printformat="000";
        $customerview = "../../formats/customers/".$iddir."/".$pre.$printformat.".php";
        //Si no existe validamos que exista ese formato en el directorio default 00000
        $defaultview = "../../formats/customers/00000/".$pre.$printformat.".php";
        if(file_exists($customerview)){            
            //Si existe se imprime la plantilla customizada  
            include($customerview);
        }else if(file_exists($defaultview)){
            include($defaultview);
        }
       
    }else{
        echo("Factura no encontrada"); 
    }
?>
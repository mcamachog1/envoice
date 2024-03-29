<?php
// app/api/invoices/entry.php

    header("Content-Type:application/json");
    include("../../../settings/dbconn.php");
    include("../../../settings/utils.php");
    
    function exist_invoices ($db, $customerid, $invoiceid) {
        $sql = "SELECT COUNT(*) Cnt FROM invoiceheader WHERE customerid=$customerid AND id=$invoiceid ";
        if (!$rs = $db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));     
        $row = $rs->fetch_assoc();
        if ($row["Cnt"]>0)
            return true;
        else
            return false;
    }

    // Parametros obligatorios
    $parmsob = array("id","sessionid");
    if (!parametrosValidos($_GET, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

    $id = $_GET["id"];
    $sessionid= $_GET["sessionid"];

    // Validar user session 
    $customerid = isSessionValid($db, $_REQUEST["sessionid"]);
    
    // Si la factura no está asociada al cliente, salir
    if (!exist_invoices($db,$customerid,$id)) {
        $out = new stdClass();
        $out->entry = new stdClass();
        $out->entry->header = new stdClass();
        $out->entry->header = array();
        $out->entry->details = new stdClass();
        $out->entry->details = array();
        header("HTTP/1.1 200");
        echo (json_encode($out));
        die();        
    }
    //Incluir fechas de leido y enviado
    $sql =  setQueryEntry($customerid,$id);
    if (!$rs = $db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));   
        
    // Header
    if ($row = $rs->fetch_assoc()){
        $record = new stdClass();
        $record->id = (integer) $row["id"];
        ($row["manualload"]==1) ? ($record->manualload = true) : ($record->manualload = false);
        $record->type =new stdClass();
            $record->type->id=$row['type'];
            switch ($row['type']) {
                case 'FAC':
                    $record->type->name='Factura';
                    break;
                case 'NDB':
                    $record->type->name='Nota de Debito';
                    break;
                case 'NDC':
                    $record->type->name='Nota de Credito';
                    break;
            }
        $record->ctrref =$row['ctrref'];
        $record->issuedate =new stdClass();
            $record->issuedate->date = $row["issuedate"];
            $record->issuedate->formatted = $row["formatteddate"];
        $record->duedate =new stdClass();
            $record->duedate->date = $row["duedate"];
            $record->duedate->formatted = $row["formattedduedate"];            

        $record->sentdate =new stdClass();
            $record->sentdate->date = $row["sentdate"];
            $record->sentdate->formatted = $row["formattedsentdate"];
        $record->viewdate =new stdClass();
            $record->viewdate->date = $row["viewdate"];
            $record->viewdate->formatted = $row["formattedviewdate"];            


        $record->refnumber = nvl($row["refnumber"],"");
        $record->ctrnumber = nvl($row["ctrnumber"],"");
        $record->client =new stdClass();
            $record->client->rif = $row["clientrif"];
            $record->client->name = $row["clientname"];        
            $record->client->mobile = $row["mobilephone"];
            $record->client->phone = $row["otherphone"];
            $record->client->email = $row["clientemail"]; 
            $record->client->address = $row["clientaddress"]; 
        $record->status =new stdClass();
            $status=1;
            $status_dsc = "Pendiente";
            if (!is_null($row["sentdate"])) {
                $status=2;
                $status_dsc = "Enviado";            
            }
            elseif (!is_null($row["viewdate"])) {
                $status=3;
                $status_dsc = "Leído";            
            }
            $record->status->id = $status;
            $record->status->dsc = $status_dsc;
        $record->multicurrency =new stdClass(); 
            $record->multicurrency->rate =new stdClass();
                $record->multicurrency->rate->number= $row["currencyrate"];
                $record->multicurrency->rate->formatted= number_format($row["currencyrate"], 2, ",", ".");
            $record->multicurrency->currency=$row["currency"];
        $record->obs=nvl($row["obs"],"");
        $record->amounts =new stdClass();        
        $record->amounts->gross = new stdClass(); 
        $record->amounts->gross->number = (float)$row["gross"];
        $record->amounts->gross->formatted = number_format($row["gross"], 2, ",", ".");
        

        $record->amounts->tax = new stdClass(); 
        $record->amounts->tax->number = (float)$row["tax"];
        $record->amounts->tax->formatted = number_format($row["tax"], 2, ",", ".");          
        
        $record->amounts->discount = new stdClass(); 
        //$record->amounts->discount->number = (float)$row["discount"];
        //$record->amounts->discount->formatted = number_format($row["discount"], 2, ",", ".");  
        $record->amounts->discount->number= (float)$row["discount"];
        $record->amounts->discount->percentage = $row["discount"]."%";

        $record->amounts->total = new stdClass(); 
        $record->amounts->total->number = (float)$row["gross"]*(1-(float)$row["discount"]/100) + (float)$row["tax"];
        $record->amounts->total->formatted = number_format((float)$row["gross"]*(1-(float)$row["discount"]/100) + (float)$row["tax"], 2, ",", ".");        
    }
    // Details
    $sql = setQueryDetail($id);
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
        $detail->qty->formatted = $row["qty"];   
        $detail->unit = $row["unit"];   
        $detail->unitprice =new stdClass();
        $detail->unitprice->number = (float)$row["unitprice"];
        $detail->unitprice->formatted = $row["unitprice"]; 
        $detail->tax =new stdClass();

        if ($row["tax"]==-1 || $row["tax"]==-2 )
            $tax=0;
        else 
            $tax = (float)$row["tax"];

        $detail->tax->number = $tax/100;
        $detail->tax->formatted = (string)$tax."%";        
        $detail->discount =new stdClass();
        $detail->discount->number = (float)$row["discount"]/100;
        $detail->discount->formatted = $row["discount"]."%";  
        $detail->total =new stdClass();
        $detail->total->number = (float)$row["total"];
        $detail->total->formatted = $row["total"];          
   
        $details[] = $detail;
    } 
    $out = new stdClass();
    //$out->sql = $sql;
    $out->entry = new stdClass();
    $out->entry->header = new stdClass();
    $out->entry->header = $record;

    $out->entry->details = new stdClass();
    $out->entry->details = $details;
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();    

?>


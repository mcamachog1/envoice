<?php
// seniat/api/invoices/list.php

    header("Content-Type:application/json");
    include("../../../settings/dbconn.php");
    include("../../../settings/utils.php");
  

    // parametros obligatorios
    $parmsob = array("offset","numofrec","order","sessionid","datefrom","dateto","status","customerid");
    
    if (!parametrosValidos($_GET, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

    $offset = $_GET["offset"];
    $numofrec = $_GET["numofrec"];
    $sessionid= $_GET["sessionid"];
    $datefrom = $_GET["datefrom"] ." 00:00:00";
    $dateto = $_GET["dateto"]." 23:59:59";
    $status = $_GET["status"];     
    $customerid = $_GET["customerid"];     
    if (strlen($status==1) && $status!=1 && $status!=2 && $status!=3)
        badEnd("400", array("msg"=>"Valor de estatus $status fuera de rango"));      
    
    // Validar user session
        isSessionValidCMS($db, $sessionid,array('ip'=>$_SERVER['REMOTE_ADDR'],'app'=>'CMS','module'=>'invoices','dsc'=>'list.php'));

    // Filter
    $filter="";
    if (isset($_GET["filter"])) {
        $pattern = avoidInjection($_GET["filter"],'str');
        $filter  = " AND (";
        $filter .= "'H.id' LIKE '%$pattern%' OR ";
        $filter .= "H.ctrnumber LIKE '%$pattern%' OR ";
        $filter .= "H.refnumber LIKE '%$pattern%' OR ";        
        $filter .= "clientrif LIKE '%$pattern%' OR ";
        $filter .= "clientname LIKE '%$pattern%'  ";
        $filter .= ") ";
    }
    
// Status un solo valor
    if (strlen($status)==1){
        $status_condition = "";
        switch ($status) {
            case 1:    
                $status_condition = " AND sentdate IS NULL ";
                break;
            case 2:
                $status_condition = " AND sentdate IS NOT NULL AND viewdate IS NULL";            
                break;
            case 3:
                $status_condition = " AND viewdate IS NOT NULL ";            
                break;
        }
    }
// Status varios valores
    else {

        $status_list =explode("-",$status);
     
        $status_condition = " AND ( 0  ";
        foreach ($status_list as $value){
            if ($value!=1 && $value!=2 && $value!=3)
                badEnd("400", array("msg"=>"Valor de estatus $value fuera de rango")); 
            switch ($value) {
                case 1:    
                    $status_condition .= " OR (sentdate IS NULL) ";
                    break;
                case 2:
                    $status_condition .= " OR (sentdate IS NOT NULL AND viewdate IS NULL) ";            
                    break;
                case 3:
                    $status_condition .= " OR (viewdate IS NOT NULL) ";            
                    break;
            }
        }
        $status_condition .= " ) ";                
    }
// validar el order
    $order = "";
    if (isset($_GET["order"])) {
        $order = "ORDER BY " . abs($_GET["order"]);
        if ($_GET["order"] < 0 )
            $order = $order .  " DESC";
    }

    $sql =  "SELECT " .
            " H.id, H.issuedate, H.refnumber, H.ctrnumber, H.clientrif, H.clientname, ".
            " H.type, H.ctrref, ".            
            " SUM((unitprice*qty*(1-itemdiscount/100))) gross, ".
            " SUM( unitprice*qty*(itemtax/100)*(1-itemdiscount/100) ) tax, ".
            " H.discount discount, ".
            " 100 * SUM( unitprice*qty*(itemdiscount/100) )/SUM(unitprice*qty) discount_percentage, ".
            //" SUM(D.itemdiscount) discount, SUM(D.unitprice)-SUM(D.itemdiscount) total, ".
            " DATE_FORMAT(H.issuedate, '%d/%m/%Y') formatteddate, ".
            " H.sentdate, H.viewdate, SUM(D.qty) qty   ".
            " FROM    invoiceheader H ".
            " LEFT JOIN invoicedetails D ON ".
                " D.invoiceid = H.id ".
            " WHERE H.customerid=$customerid AND H.issuedate BETWEEN '$datefrom' AND '$dateto' ".
              $status_condition.$filter.   
            " GROUP BY ".
            "   H.id, H.issuedate, H.refnumber, H.ctrnumber, H.clientrif, H.clientname, DATE_FORMAT(H.issuedate, '%d/%m/%Y'), ".
            "   H.sentdate, H.viewdate " .
                $order;

    // calcular numero de registros
    if (!$rs = $db->query("SELECT COUNT(*) cnt FROM (" . $sql . ") A "))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));

    $row = $rs->fetch_assoc();
    //$out->sql = $sql;
    $out = new stdClass();
    $out->numofrecords = new stdClass();
    $out->numofrecords = (integer) $row["cnt"];

    // Calcular Totales
    if (!$rs = $db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    $taxbase=0;
    $tax=0;
    $total=0;    
    while ($row = $rs->fetch_assoc()){    
        $taxbase += (float)$row["gross"]*(1-(float)$row["discount"]/100);
        $tax += (float)$row["tax"];
        $total += (float)$row["gross"]*(1-(float)$row["discount"]/100) + (float)$row["tax"];        
    }
    $object = new stdClass();
    $object->taxbase=new stdClass();
    $object->tax=new stdClass();
    $object->total=new stdClass();
    $object->taxbase->number=(float)$taxbase;
    $object->tax->number=(float)$tax;
    $object->total->number=(float)$total;
    $object->taxbase->formatted=number_format($taxbase, 2, ",", ".");          
    $object->tax->formatted=number_format($tax, 2, ",", ".");
    $object->total->formatted=number_format($total, 2, ",", ".");    

    // limitar numero de registros
    $sql =  "SELECT A.* FROM (" . $sql . ") A " .
            "LIMIT " . $offset . "," . $numofrec;
    if (!$rs = $db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
/*
    $records = array();
    while ($row = $rs->fetch_assoc()){
        $record = new stdClass();
        $record->id = (integer) $row["id"];
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
        $record->refnumber = nvl($row["refnumber"],"");
        $record->ctrnumber = nvl($row["ctrnumber"],"");
        $record->client =new stdClass();
        $record->client->rif = $row["clientrif"];
        $record->client->name = $row["clientname"];        
        $record->status =new stdClass();
        $status=1;
        $status_dsc = "Pendiente";
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
        
        $record->amounts =new stdClass();        
        $record->amounts->gross = new stdClass(); 
        $record->amounts->gross->number = (float)$row["gross"]*(1-(float)$row["discount"]/100);
        $record->amounts->gross->formatted = number_format($row["gross"]*(1-(float)$row["discount"]/100), 2, ",", ".");
        
        $record->amounts->tax = new stdClass(); 
        $record->amounts->tax->number = (float)$row["tax"];
        $record->amounts->tax->formatted = number_format($row["tax"], 2, ",", ".");         
        
        //$record->amounts->discount = new stdClass(); 
        //$record->amounts->discount->number = (float)$row["discount"];
        //$record->amounts->discount->formatted = number_format($row["discount"], 2, ",", ".");        

        $record->amounts->total = new stdClass(); 
        $record->amounts->total->number = (float)$row["gross"]*(1-(float)$row["discount"]/100) + (float)$row["tax"];
        $record->amounts->total->formatted = number_format((float)$row["gross"]*(1-(float)$row["discount"]/100) + (float)$row["tax"], 2, ",", ".");          


        $records[] = $record;
    }
*/
    $records = jsonInvoiceList($rs);

    //Totals
    $out->totals=$object;
    $out->records = $records;
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>


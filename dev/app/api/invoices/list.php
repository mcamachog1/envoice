<?php
// app/api/invoices/list.php

    header("Content-Type:application/json");
    include("../../../settings/dbconn.php");
    include("../../../settings/utils.php");

    // parametros obligatorios
    $parmsob = array("offset","numofrec","order","sessionid","datefrom","dateto","status");
    if (!parametrosValidos($_GET, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

    $offset = $_GET["offset"];
    $numofrec = $_GET["numofrec"];
    $sessionid= $_GET["sessionid"];
    $datefrom = $_GET["datefrom"] ." 00:00:00";
    $dateto = $_GET["dateto"]." 23:59:59";
    $status = $_GET["status"];     
    
    // Validar user session
    $customerid = isSessionValid($db, $_REQUEST["sessionid"]);
    
    // Filter
    if (isset($_GET["filter"])) {
        $pattern = avoidInjection($_GET["filter"],'str');
        $filter  = " AND (";
        $filter .= "'H.id' LIKE '%$pattern%' OR ";
        $filter .= "ctrnumber LIKE '%$pattern%' OR ";
        $filter .= "clientrif LIKE '%$pattern%' OR ";
        $filter .= "clientname LIKE '%$pattern%'  ";
        $filter .= ") ";
    }
    
    
    // Status
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
            " SUM(unitprice*qty) gross, ".
            " SUM( unitprice*qty*(itemtax/100)*(1-itemdiscount/100) ) tax, ".
            " SUM( unitprice*qty*(itemdiscount/100) ) discount, ".
            " 100 * SUM( unitprice*qty*(itemdiscount/100) )/SUM(unitprice*qty) discount_percentage, ".
            //" SUM(D.itemdiscount) discount, SUM(D.unitprice)-SUM(D.itemdiscount) total, ".
            " DATE_FORMAT(H.issuedate, '%d/%m/%Y') formatteddate, ".
            " H.sentdate, H.viewdate, SUM(D.qty) qty   ".
            " FROM    invoiceheader H ".
            " INNER JOIN invoicedetails D ON ".
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

    // limitar numero de
    $sql =  "SELECT A.* FROM (" . $sql . ") A " .
            "LIMIT " . $offset . "," . $numofrec;
    if (!$rs = $db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));

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
        
        $record->amounts =new stdClass();        
        $record->amounts->gross = new stdClass(); 
        $record->amounts->gross->number = (float)$row["gross"];
        $record->amounts->gross->formatted = number_format($row["gross"], 2, ",", ".");
        
        $record->amounts->tax = new stdClass(); 
        $record->amounts->tax->number = (float)$row["tax"];
        $record->amounts->tax->formatted = number_format($row["tax"], 2, ",", ".");         
        
        //$record->amounts->discount = new stdClass(); 
        //$record->amounts->discount->number = (float)$row["discount"];
        //$record->amounts->discount->formatted = number_format($row["discount"], 2, ",", ".");        

        $record->amounts->total = new stdClass(); 
        $record->amounts->total->number = (float)$row["gross"]+$row["tax"]-$row["discount"];
        $record->amounts->total->formatted = number_format($row["gross"]+$row["tax"]-$row["discount"], 2, ",", ".");        


        $records[] = $record;
    }

    $out->records = $records;
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>


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

    if (strlen($status==1) && $status!=1 && $status!=2 && $status!=3 && $status!=4)
        badEnd("400", array("msg"=>"Valor de estatus $status fuera de rango"));    
    // Validar user session 
    $customerid = isSessionValid($db, $_REQUEST["sessionid"]);
    $filter="";
    // Filter
    if (isset($_GET["filter"])) {
        $pattern = avoidInjection($_GET["filter"],'str');
        $filter  = " AND (";
        $filter .= "'H.id' LIKE '%$pattern%' OR ";
        $filter .= "ctrnumber LIKE '%$pattern%' OR ";
        $filter .= "H.refnumber LIKE '%$pattern%' OR ";          
        $filter .= "clientrif LIKE '%$pattern%' OR ";
        $filter .= "clientname LIKE '%$pattern%'  ";
        $filter .= ") ";
    }
    
    // Status un solo valor
    if (strlen($status)==1)
        $status_condition = filterByOneStatus($status);
    
    // Status varios valores
    else 
        $status_condition = filterByStatus($status);             
    
    // validar el order
    $order = "";
    if (isset($_GET["order"])) {
        $order = "ORDER BY " . abs($_GET["order"]);
        if ($_GET["order"] < 0 )
            $order = $order .  " DESC";
    }

    $sql = setQuery($customerid,$datefrom,$dateto,$status_condition,$filter,$order);


    // calcular numero de registros
    if (!$rs = $db->query("SELECT COUNT(*) cnt FROM (" . $sql . ") A "))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));

    $row = $rs->fetch_assoc();
    //$out->sql = $sql;
    $out = new stdClass();
    $out->numofrecords = new stdClass();
    $out->numofrecords = (integer) $row["cnt"];

    // limitar numero de registros
    $sql =  "SELECT A.* FROM (" . $sql . ") A " .
            "LIMIT " . $offset . "," . $numofrec;
    if (!$rs = $db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));

    $records = jsonInvoiceList($rs);

    $out->records = $records;
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>


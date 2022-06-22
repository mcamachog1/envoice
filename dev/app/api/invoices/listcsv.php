<?php
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=informe.csv");

include("../../../settings/dbconn.php");
include("../../../settings/utils.php");

function badEndCsv($message){
  $BOM = "\xEF\xBB\xBF"."\xEF\xBB\xBF";
  $fp = fopen('php://output', 'wb');
  fwrite($fp, $BOM);  
  $line = array(0=>$message);
  fputcsv($fp, $line, ';', '"');
  fclose($fp);
  die();
}

// Parametros obligatorios
  $parmsob = array("offset","numofrec","order","sessionid","datefrom","dateto","status");
  if (!parametrosValidos($_GET, $parmsob))
      badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

  $offset = $_GET["offset"];
  $numofrec = $_GET["numofrec"];
  $sessionid= $_GET["sessionid"];
  $datefrom = $_GET["datefrom"] ." 00:00:00";
  $dateto = $_GET["dateto"]." 23:59:59";
  $status = $_GET["status"];     

  if (strlen($status==1) && $status!=1 && $status!=2 && $status!=3)
      badEnd("400", array("msg"=>"Valor de estatus $status fuera de rango"));    

// Validar user session
  $customerid = isSessionValid($db, $_REQUEST["sessionid"]);

// Filter
  $filter="";
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
                $status_condition .= " OR (sentdate IS NOT NULL) ";            
                break;
            case 3:
                $status_condition .= " OR (viewdate IS NOT NULL) ";            
                break;
        }
    }
    $status_condition .= " ) ";                
  }
// Validar el order
  $order = "";
  if (isset($_GET["order"])) {
    $order = "ORDER BY " . abs($_GET["order"]);
    if ($_GET["order"] < 0 )
        $order = $order .  " DESC";
  }
// SQL
  $sql =  "SELECT " .
    " H.id, H.issuedate, H.refnumber, H.ctrnumber, H.clientrif, H.clientname, ".
    " H.type, H.ctrref, ".            
    " SUM((unitprice*qty*(1-itemdiscount/100))) gross, ".
    " SUM( unitprice*qty*(itemtax/100)*(1-itemdiscount/100) ) tax, ".
    " H.discount discount, ".
    " 100 * SUM( unitprice*qty*(itemdiscount/100) )/SUM(unitprice*qty) discount_percentage, ".
    " DATE_FORMAT(H.issuedate, '%d/%m/%Y') formatteddate, ".
    " H.sentdate, H.viewdate, SUM(D.qty) qty   ".
    " FROM    invoiceheader H ".
    " LEFT JOIN invoicedetails D ON ".
      " D.invoiceid = H.id ".
    " WHERE H.customerid=$customerid AND H.issuedate BETWEEN '$datefrom' AND '$dateto' ".
    $status_condition.$filter.   
    " GROUP BY ".
    "   H.id, H.issuedate, H.refnumber, H.ctrnumber, H.clientrif, H.clientname, DATE_FORMAT(H.issuedate, '%d/%m/%Y'), ".
    "   H.sentdate, H.viewdate " . $order;


// Calcular numero de registros
  if (!$rs = $db->query("SELECT COUNT(*) cnt FROM (" . $sql . ") A "))
    badEndCsv("Error de Base de Datos\n $db->error");
  if (!$row = $rs->fetch_assoc())
    badEndCsv("No hay facturas para mostrar");
  $totalrecords = (integer) $row["cnt"];


// Aplicar LIMIT a los registros
  $sql .= " LIMIT $offset, $numofrec"; 
  if (!$rs = $db->query($sql))
    badEndCsv("Error de Base de Datos\n $db->error");
// Guardar la data  
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
    if (!is_null($row["viewdate"])) {
        $status=3;
        $status_dsc = "Leído";            
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
    $record->amounts->total = new stdClass(); 
    $record->amounts->total->number = (float)$row["gross"]*(1-(float)$row["discount"]/100) + (float)$row["tax"];
    $record->amounts->total->formatted = number_format((float)$row["gross"]*(1-(float)$row["discount"]/100) + (float)$row["tax"], 2, ",", ".");          
    $records[] = $record;
  }

// Preparar archivo csv
  $BOM = "\xEF\xBB\xBF"."\xEF\xBB\xBF";
  $fp = fopen('php://output', 'wb');
  fwrite($fp, $BOM);

  $line = array(
    0=>'FECHA',
    1=>'TIPO',
    2=>'NÚMERO',
    3=>'CONTROL',    
    4=>'CÉDULA/RIF',
    5=>'CLIENTE',
    6=>'MONTO',
    7=>'IVA',
    8=>'TOTAL',
    9=>'ESTATUS');

  $csvarray = array();
  foreach ($records as $record){
    $fila = array(
      0=>$record->issuedate->formatted,
      1=>$record->type->name,
      2=>$record->refnumber,
      3=>$record->ctrnumber,
      4=>$record->client->rif,
      5=>$record->client->name,
      6=>$record->amounts->gross->formatted,
      7=>$record->amounts->tax->formatted,
      8=>$record->amounts->total->formatted,
      9=>$record->status->dsc);
    $csvarray[] = $fila;
  }
// Encabezado 
  fputcsv($fp, $line, ';', '"');

// Lineas
  foreach($csvarray as $arr){
    fputcsv($fp,$arr,';');
  }
// Cerrar archivo
  fclose($fp);
  die(); 
?>
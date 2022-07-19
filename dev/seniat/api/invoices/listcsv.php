<?php
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=informe.csv");

include("../../../settings/dbconn.php");
include("../../../settings/utils.php");
include("../functions.php");  


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
validSession($db, $_REQUEST["sessionid"]);    

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
          case 4:
              $status_condition = " AND canceldate IS NOT NULL ";            
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
            case 4:
                $status_condition .= " OR (canceldate IS NOT NULL) ";            
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
  $sql = setQuery($customerid,$datefrom,$dateto,$status_condition,$filter,$order);
 


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

  $records = jsonInvoiceList($rs);
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
// Auditoria
  $customer = getCustomerName($id,$db);
  insertAudit($db,getEmail($_REQUEST["sessionid"],'APP',$db),$_SERVER['REMOTE_ADDR'],'SENIAT','invoices',"Se exportó la lista de documentos del cliente $customer");  
// Cerrar archivo
  fclose($fp);
  die(); 
?>
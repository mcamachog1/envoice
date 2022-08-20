<?php
// cms/api/reports/maindashboard.php

header("Content-Type:application/json");
include_once("../../../settings/dbconn.php");
include_once("../../../settings/utils.php");

//Tratar que el mes se vea en espanol. No sirve
setlocale(LC_ALL,"VE");
define('APP_TIME_ZONE', 'America/Caracas');

function loadedTotalNumber($from,$to,$customerid,$db){
  $condition = "";
  if ($customerid != 0)
    $condition = " AND h.customerid = $customerid ";
  $sql = "SELECT COUNT(*) loaded_total_number FROM invoiceheader h WHERE 
        h.creationdate BETWEEN '$from' AND '$to' $condition ";
  $rs = $db->query($sql);
  if (!$rs)
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  $row = $rs->fetch_assoc();
  return $row['loaded_total_number'];

}
function sentTotalNumber($from,$to,$customerid,$db){
  $condition = "";
  if ($customerid != 0)
    $condition = " AND h.customerid = $customerid ";    
  $sql = "SELECT COUNT(*) sent_total_number FROM invoiceheader h WHERE
            h.creationdate BETWEEN '$from' AND '$to' AND sentdate IS NOT NULL $condition; ";
  $rs = $db->query($sql);
  if (!$rs)
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  $row = $rs->fetch_assoc();
  return $row['sent_total_number'];
}
function toSendTotalNumber($from,$to,$customerid,$db){
  $condition = "";
  if ($customerid != 0)
    $condition = " AND h.customerid = $customerid ";      
  $sql = "SELECT COUNT(*) status_bysend_sent_qty_number FROM invoiceheader h WHERE
            h.creationdate BETWEEN '$from' AND '$to' AND sentdate IS NULL $condition";
  $rs = $db->query($sql);
  if (!$rs)
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  $row = $rs->fetch_assoc();
  return $row['status_bysend_sent_qty_number'];
}
function readedTotalNumber($from,$to,$customerid,$db){
  $condition = "";
  if ($customerid != 0)
    $condition = " AND h.customerid = $customerid ";      
  $sql = "SELECT COUNT(*) status_byread_readed_qty_number FROM invoiceheader h WHERE
            h.creationdate BETWEEN '$from' AND '$to' AND viewdate IS NOT NULL $condition";
  $rs = $db->query($sql);
  if (!$rs)
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  $row = $rs->fetch_assoc();
  return $row['status_byread_readed_qty_number'];    
}
function unreadedTotalNumber($from,$to,$customerid,$db){
  $condition = "";
  if ($customerid != 0)
    $condition = " AND h.customerid = $customerid ";      
  $sql = "SELECT COUNT(*) status_byread_unreaded_qty_number FROM invoiceheader h WHERE
            h.creationdate BETWEEN '$from' AND '$to' AND sentdate IS NOT NULL AND viewdate IS NULL $condition";
  $rs = $db->query($sql);
  if (!$rs)
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  $row = $rs->fetch_assoc();
  return $row['status_byread_unreaded_qty_number'];      
}
function loginCustomers($from,$to,$db){
  $condition = "";
  if ($customerid != 0)
    $condition = " AND h.customerid = $customerid ";  
  $sql = "SELECT COUNT(DISTINCT userid) logins_customers_total_number FROM audit WHERE datecreation 
        BETWEEN '$from' AND '$to' AND loginaction = 1 $condition";
  if ($customerid != 0)
    $condition = " AND h.customerid = $customerid ";
  $rs = $db->query($sql);
  if (!$rs)
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  $row = $rs->fetch_assoc();
  return $row['logins_customers_total_number'];  
}
function loginSeniat($from,$to,$db){
  $sql = "SELECT COUNT(DISTINCT userid) logins_seniat_total_number FROM audit WHERE datecreation BETWEEN '$from' AND '$to' AND loginaction = 2";
  $rs = $db->query($sql);
  if (!$rs)
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  $row = $rs->fetch_assoc();
  return $row['logins_seniat_total_number'];  
}
function targetValuesNuevos($from,$to,$customerid,$interval,$db) {
  $from_date = date_create($from,timezone_open(APP_TIME_ZONE));
  date_sub($from_date,date_interval_create_from_date_string($interval));
  $from_previous = date_format($from_date,'Y-m-d');
  $to_previous = $from;
  $condition = "";
  if ($customerid != 0)
    $condition = " AND customerid = $customerid ";
  $sql = "  SELECT
      COUNT(DISTINCT clientemail)  nuevos
    FROM
      invoiceheader
    WHERE
      creationdate BETWEEN '$from' AND '$to' AND clientemail NOT IN(
      SELECT
          clientemail
      FROM
          invoiceheader
      WHERE
          creationdate BETWEEN '$from_previous' AND '$to_previous' $condition
    )";

  $rs = $db->query($sql);
  if (!$rs)
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  $row = $rs->fetch_assoc();
  return $row['nuevos']; 
}
function targetValuesExistente($from,$to,$customerid,$interval,$db) {
  $from_date = date_create($from,timezone_open(APP_TIME_ZONE));
  date_sub($from_date,date_interval_create_from_date_string($interval));
  $from_previous = date_format($from_date,'Y-m-d');
  $to_previous = $from;
  $condition = "";
  if ($customerid != 0)
    $condition = " AND customerid = $customerid ";
  $sql = "  SELECT
      COUNT(DISTINCT clientemail) existentes
    FROM
      invoiceheader
    WHERE
      creationdate BETWEEN '$from' AND '$to' AND clientemail IN (
      SELECT
          clientemail
      FROM
          invoiceheader
      WHERE
          creationdate BETWEEN '$from_previous' AND '$to_previous' $condition
    )";
  
  $rs = $db->query($sql);
  if (!$rs)
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  $row = $rs->fetch_assoc();
  return $row['existentes']; 
}
function targetValuesBaja($from,$to,$customerid,$interval,$db) {
  $from_date = date_create($from,timezone_open(APP_TIME_ZONE));
  date_sub($from_date,date_interval_create_from_date_string($interval));
  $from_previous = date_format($from_date,'Y-m-d');
  $to_previous = $from;
  $condition = "";
  if ($customerid != 0)
    $condition = " AND customerid = $customerid ";
  $sql = "  SELECT
      COUNT(DISTINCT clientemail) baja
    FROM
      invoiceheader
    WHERE
      creationdate BETWEEN '$from_previous' AND '$to_previous' AND clientemail NOT IN (
      SELECT
          clientemail
      FROM
          invoiceheader
      WHERE
      creationdate BETWEEN '$from' AND '$to' $condition
    )";
   
  $rs = $db->query($sql);
  if (!$rs)
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  $row = $rs->fetch_assoc();
  return $row['baja']; 
}
function customersRanking($from,$to,$db) {
  $sql = "SELECT
      c.name,
      COUNT(DISTINCT c.contactemail) total
    FROM
      `audit` a
    INNER JOIN customers c ON
      a.userid = c.contactemail
    WHERE
      loginaction = 1 AND module = 'Seguridad' AND datecreation 
      BETWEEN '$from' AND '$to' AND app = 'APP'
    GROUP BY
      c.contactname; ";
   
  $rs = $db->query($sql);
  if (!$rs)
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  $customers = [];
  while ($row = $rs->fetch_assoc()) {
    $customers[$row['name']] = (integer)$row['total'];
    
  }
  return $customers;
}

// parametros obligatorios
$parmsob = array("datefrom","dateto","customerid","sessionid");
if (!parametrosValidos($_GET, $parmsob))
    badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

//Validar user session
isSessionValidCMS($db, $_GET["sessionid"]);

//Convertir a fecha desde-hasta
$fromIni = date_create($_GET["datefrom"],timezone_open(APP_TIME_ZONE));
$toIni = date_create($_GET["dateto"],timezone_open(APP_TIME_ZONE));
$customerid = $_GET["customerid"];  

//Se ejecutan las consultas con las fechas iniciales
$loaded_total_number = (integer)loadedTotalNumber(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$customerid,$db);
$sent_total_number=(integer)sentTotalNumber(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$customerid,$db);
$status_bysend_sent_qty_number = (integer)sentTotalNumber(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$customerid,$db);
$status_bysend_notsend_qty_number = (integer)toSendTotalNumber(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$customerid,$db);
$status_byread_readed_qty_number = (integer)readedTotalNumber(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$customerid,$db);
$status_byread_unreaded_qty_number = (integer)unreadedTotalNumber(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$customerid,$db);
$logins_customers_total_number = (integer)loginCustomers(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$db);
$logins_seniat_total_number = (integer)loginSeniat(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$db);

//Se calcula la diferencia entre las fechas y se obtiene el valor en días
$diff = date_diff($fromIni,$toIni);
$diff_days = $diff->days;

//Se busca el período anterior
date_sub($fromIni,date_interval_create_from_date_string("$diff_days days"));
date_sub($toIni,date_interval_create_from_date_string("$diff_days days"));

//Se hacen los calculos de fechas anteriores para los increment
$loaded_total_number_previous = (integer)loadedTotalNumber(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$customerid,$db);
$sent_total_number_previous = (integer)sentTotalNumber(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$customerid,$db);
$logins_customers_total_number_previous = (integer)loginCustomers(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$db);
$logins_seniat_total_number_previous = (integer)loginSeniat(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$db);
$loaded_increment_number = $loaded_total_number-$loaded_total_number_previous;
$sent_increment_number = $sent_total_number - $sent_total_number_previous;
$logins_customers_increment_number = $logins_customers_total_number - $logins_customers_total_number_previous;
$logins_seniat_increment_number = $logins_seniat_total_number - $logins_seniat_total_number_previous;
//Se calculan los porcentajes
if ($loaded_total_number != 0){
    $status_bysend_sent_pct_number = round($status_bysend_sent_qty_number*100/$loaded_total_number, 2);
    $status_bysend_notsend_pct_number = round($status_bysend_notsend_qty_number*100/$loaded_total_number, 2);
}
else {
    $status_bysend_sent_pct_number = 0;
    $status_bysend_notsend_pct_number = 0;
}
if ($sent_total_number != 0){
    $status_byread_readed_pct_number = $status_byread_readed_qty_number*100/$sent_total_number;
    $status_byread_unreaded_pct_number = $status_byread_unreaded_qty_number*100/$sent_total_number;
}
else {
    $status_byread_readed_pct_number = 0;
    $status_byread_unreaded_pct_number = 0;
}

//Se da formato a los numeros
$loaded_total_formatted=number_format($loaded_total_number, 0, ",", ".");
$sent_total_formatted=number_format($sent_total_number, 0, ",", ".");
$loaded_increment_formatted=number_format($loaded_increment_number, 0, ",", ".");
$sent_increment_formatted=number_format($sent_increment_number, 0, ",", ".");
$status_bysend_sent_qty_formatted = number_format($status_bysend_sent_qty_number, 0, ",", ".");
$status_bysend_sent_pct_formatted = $status_bysend_sent_pct_number . "%";
$status_bysend_notsend_qty_formatted = number_format($status_bysend_notsend_qty_number, 0, ",", ".");
$status_bysend_notsend_pct_formatted = $status_bysend_notsend_pct_number . "%";
$status_byread_readed_qty_formatted = number_format($status_byread_readed_qty_number, 0, ",", ".");
$status_byread_readed_pct_formatted = $status_byread_readed_pct_number . "%";
$status_byread_unreaded_qty_formatted = number_format($status_byread_unreaded_qty_number, 0, ",", ".");
$status_byread_unreaded_pct_formatted = $status_byread_unreaded_pct_number . "%";
$logins_customers_total_formatted = number_format($logins_customers_total_number, 0, ",", ".");
$logins_customers_increment_formatted = number_format($logins_customers_increment_number, 0, ",", ".");
$logins_seniat_total_formatted = number_format($logins_seniat_total_number, 0, ",", ".");
$logins_seniat_increment_formatted = number_format($logins_seniat_increment_number, 0, ",", ".");

//Caso del target
if ($diff_days <= 9)
  $interval = '1 day';
elseif ($diff_days <= 63)
  $interval = '7 days';
elseif ($diff_days <= 270)
  $interval = '30 days';
else
  $interval = '365 days';

$from = date_create($_GET["datefrom"],timezone_open(APP_TIME_ZONE));
$to = date_create($_GET["dateto"],timezone_open(APP_TIME_ZONE));  
$targets = [];
while ($from < $to) {
  switch ($interval) {
    case '1 day':
      $short = date_format($from,'M-d');
      $long = date_format($from,"d - F - Y");
      $long = str_replace("-","de",$long);       
      break;
    case '7 days':
      $short = date_format($from,'M-d');
      $long = date_format($from,"d - F - Y");
      $long = str_replace("-","de",$long);       
      break;
    case '30 days':
      $short = date_format($from,'M-Y');
      $long = date_format($from,'F - Y');
      $long = str_replace("-","de",$long);       
      break;
    case '365 days':
      $short = date_format($from,'Y');
      $long = date_format($from,'Y');
      break;      
  }  
 
  $from_formatted=date_format($from,'Y-m-d');
  date_add($from,date_interval_create_from_date_string("$interval"));
  $to_formatted = date_format($from,'Y-m-d');  


  $record = new stdClass();
  $record->label = new stdClass();
  $record->label->short = $short;
  $record->label->long = $long;
  $record->values = new stdClass();
  $record->values->nuevos = targetValuesNuevos($from_formatted,$to_formatted,$customerid,$interval,$db);
  $record->values->existente = targetValuesExistente($from_formatted,$to_formatted,$customerid,$interval,$db);
  $record->values->baja = (-1)*targetValuesBaja($from_formatted,$to_formatted,$customerid,$interval,$db);
  $targets[] = $record;
  
}

//Caso del customers
$from = date_create($_GET["datefrom"],timezone_open(APP_TIME_ZONE));
$to = date_create($_GET["dateto"],timezone_open(APP_TIME_ZONE));  
$customers = [];
while ($from < $to) {
  switch ($interval) {
    case '1 day':
      $short = date_format($from,'M-d');
      $long = date_format($from,"d - F - Y");
      $long = str_replace("-","de",$long);       
      break;
    case '7 days':
      $short = date_format($from,'M-d');
      $long = date_format($from,"d - F - Y");
      $long = str_replace("-","de",$long);       
      break;
    case '30 days':
      $short = date_format($from,'M-Y');
      $long = date_format($from,'F - Y');
      $long = str_replace("-","de",$long);       
      break;
    case '365 days':
      $short = date_format($from,'Y');
      $long = date_format($from,'Y');
      break;      
  }  
 
  $from_formatted=date_format($from,'Y-m-d');
  date_add($from,date_interval_create_from_date_string("$interval"));
  $to_formatted = date_format($from,'Y-m-d');  


  $record = new stdClass();
  $record->label = new stdClass();
  $record->label->short = $short;
  $record->label->long = $long;
  $record->values = customersRanking($from_formatted,$to_formatted,$db);
  $customers[] = $record;
  
}

//Se llena la estructura
$out = new stdClass();

$out->documentsloaded = new stdClass();
$out->documentsloaded->total = new stdClass();
$out->documentsloaded->total->number = $loaded_total_number;
$out->documentsloaded->total->formatted = $loaded_total_formatted;
$out->documentsloaded->increment = new stdClass();
$out->documentsloaded->increment->number = $loaded_increment_number;
$out->documentsloaded->increment->formatted = $loaded_increment_formatted;

$out->documentssent = new stdClass();
$out->documentssent->total = new stdClass();
$out->documentssent->total->number = $sent_total_number;
$out->documentssent->total->formatted = $sent_total_formatted;
$out->documentssent->increment = new stdClass();
$out->documentssent->increment->number = $sent_increment_number;
$out->documentssent->increment->formatted = $sent_increment_formatted;

$out->documentsstatus = new stdClass();
$out->documentsstatus->bysendstatus = new stdClass();
$out->documentsstatus->bysendstatus->sent = new stdClass();
$out->documentsstatus->bysendstatus->sent->qty = new stdClass();
$out->documentsstatus->bysendstatus->sent->qty->number = $status_bysend_sent_qty_number;
$out->documentsstatus->bysendstatus->sent->qty->formatted = $status_bysend_sent_qty_formatted;
$out->documentsstatus->bysendstatus->sent->pct = new stdClass();
$out->documentsstatus->bysendstatus->sent->pct->number = $status_bysend_sent_pct_number;
$out->documentsstatus->bysendstatus->sent->pct->formatted = $status_bysend_sent_pct_formatted;
$out->documentsstatus->bysendstatus->notsend = new stdClass();
$out->documentsstatus->bysendstatus->notsend->qty = new stdClass();
$out->documentsstatus->bysendstatus->notsend->qty->number = $status_bysend_notsend_qty_number;
$out->documentsstatus->bysendstatus->notsend->qty->formatted = $status_bysend_notsend_qty_formatted;
$out->documentsstatus->bysendstatus->notsend->pct = new stdClass();
$out->documentsstatus->bysendstatus->notsend->pct->number = $status_bysend_notsend_pct_number;
$out->documentsstatus->bysendstatus->notsend->pct->formatted = $status_bysend_notsend_pct_formatted;
$out->documentsstatus->byreadstatus = new stdClass();
$out->documentsstatus->byreadstatus->readed = new stdClass();
$out->documentsstatus->byreadstatus->readed->qty = new stdClass();
$out->documentsstatus->byreadstatus->readed->qty->number = $status_byread_readed_qty_number;
$out->documentsstatus->byreadstatus->readed->qty->formatted = $status_byread_readed_qty_formatted;
$out->documentsstatus->byreadstatus->readed->pct = new stdClass();
$out->documentsstatus->byreadstatus->readed->pct->number = $status_byread_readed_pct_number;
$out->documentsstatus->byreadstatus->readed->pct->formatted = $status_byread_readed_pct_formatted;
$out->documentsstatus->byreadstatus->unreaded = new stdClass();
$out->documentsstatus->byreadstatus->unreaded->qty = new stdClass();
$out->documentsstatus->byreadstatus->unreaded->qty->number = $status_byread_unreaded_qty_number;
$out->documentsstatus->byreadstatus->unreaded->qty->formatted = $status_byread_unreaded_qty_formatted;
$out->documentsstatus->byreadstatus->unreaded->pct = new stdClass();
$out->documentsstatus->byreadstatus->unreaded->pct->number = $status_byread_unreaded_pct_number;
$out->documentsstatus->byreadstatus->unreaded->pct->formatted = $status_byread_unreaded_pct_formatted;

$out->targets = &$targets;

$out->logins = new stdClass();
$out->logins->customers = new stdClass();
$out->logins->customers->total = new stdClass(); 
$out->logins->customers->total->number = $logins_customers_total_number; 
$out->logins->customers->total->formatted = $logins_customers_total_formatted;
$out->logins->customers->increment = new stdClass(); 
$out->logins->customers->increment->number = $logins_customers_increment_number; 
$out->logins->customers->increment->formatted = $logins_customers_increment_formatted;

$out->logins->seniat = new stdClass();
$out->logins->seniat->total = new stdClass(); 
$out->logins->seniat->total->number = $logins_seniat_total_number; 
$out->logins->seniat->total->formatted = $logins_seniat_total_formatted;
$out->logins->seniat->increment = new stdClass(); 
$out->logins->seniat->increment->number = $logins_seniat_increment_number; 
$out->logins->seniat->increment->formatted = $logins_seniat_increment_formatted;

$out->customers = $customers;

header("HTTP/1.1 200");
echo (json_encode($out));
setlocale(LC_ALL,NULL);
die();
?>
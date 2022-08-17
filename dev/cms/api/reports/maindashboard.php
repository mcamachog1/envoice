<?php
// cms/api/reports/maindashboard.php

header("Content-Type:application/json");
include_once("../../../settings/dbconn.php");
include_once("../../../settings/utils.php");

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
  $sql = "SELECT COUNT(DISTINCT userid) logins_customers_total_number FROM audit WHERE datecreation BETWEEN '$from' AND '$to' AND loginaction = 1";
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

// parametros obligatorios
$parmsob = array("datefrom","dateto","customerid","sessionid");

if (!parametrosValidos($_GET, $parmsob))
    badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));



// Validar user session
isSessionValidCMS($db, $_GET["sessionid"]);

$fromIni = date_create($_GET["datefrom"]);
$toIni = date_create($_GET["dateto"]);
$customerid = $_GET["customerid"];  







//Se ejecuta una primera vez antes de recibir el date sub
$loaded_total_number = (integer)loadedTotalNumber(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$customerid,$db);
$sent_total_number=(integer)sentTotalNumber(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$customerid,$db);
$status_bysend_sent_qty_number = (integer)sentTotalNumber(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$customerid,$db);
$status_bysend_notsend_qty_number = (integer)toSendTotalNumber(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$customerid,$db);
$status_byread_readed_qty_number = (integer)readedTotalNumber(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$customerid,$db);
$status_byread_unreaded_qty_number = (integer)unreadedTotalNumber(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$customerid,$db);
$logins_customers_total_number = (integer)loginCustomers(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$db);
$logins_seniat_total_number = (integer)loginSeniat(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$db);

//Se calcula la diferencia y se obtiene el valor en días
$diff = date_diff($fromIni,$toIni);
$diff_days = $diff->days;

//Los date sub restan el valor no solo en forma de "return como respuesta" también aplica la resta a la variable
//seteada cómo parametro en este caso "fromIni"
date_sub($fromIni,date_interval_create_from_date_string("$diff_days days"));
date_sub($toIni,date_interval_create_from_date_string("$diff_days days"));

//Se ejecuta de nuevo el loadTotalNumber pero ahora con las variables recalculadas -- Calculos
$loaded_total_number_previous = (integer)loadedTotalNumber(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$customerid,$db);
$sent_total_number_previous = (integer)sentTotalNumber(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$customerid,$db);
$logins_customers_total_number_previous = (integer)loginCustomers(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$db);
$logins_seniat_total_number_previous = (integer)loginSeniat(date_format($fromIni,'Y-m-d'),date_format($toIni,'Y-m-d'),$db);

$loaded_increment_number = $loaded_total_number-$loaded_total_number_previous;
$sent_increment_number = $sent_total_number - $sent_total_number_previous;
$logins_customers_increment_number = $logins_customers_total_number - $logins_customers_total_number_previous;
$logins_seniat_increment_number = $logins_seniat_total_number - $logins_seniat_total_number_previous;

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
$loaded_total_formatted=number_format($loaded_total_number, 2, ",", ".");
$sent_total_formatted=number_format($sent_total_number, 2, ",", ".");
$loaded_increment_formatted=number_format($loaded_increment_number, 2, ",", ".");
$sent_increment_formatted=number_format($sent_increment_number, 2, ",", ".");
$status_bysend_sent_qty_formatted = number_format($status_bysend_sent_qty_number, 2, ",", ".");
$status_bysend_sent_pct_formatted = (string)number_format($status_bysend_sent_pct_number, 2, ",", ".") . "%";
$status_bysend_notsend_qty_formatted = number_format($status_bysend_notsend_qty_number, 2, ",", ".");
$status_bysend_notsend_pct_formatted = (string)number_format($status_bysend_notsend_pct_number, 2, ",", ".") . "%";
$status_byread_readed_qty_formatted = number_format($status_byread_readed_qty_number, 2, ",", ".");
$status_byread_readed_pct_formatted = $status_byread_readed_pct_number . "%";
$status_byread_unreaded_qty_formatted = number_format($status_byread_unreaded_qty_number, 2, ",", ".");
$status_byread_unreaded_pct_formatted = $status_byread_unreaded_pct_number . "%";
$logins_customers_total_formatted = number_format($logins_customers_total_number, 2, ",", ".");
$logins_customers_increment_formatted = number_format($logins_customers_increment_number, 2, ",", ".");
$logins_seniat_total_formatted = number_format($logins_seniat_total_number, 2, ",", ".");
$logins_seniat_increment_formatted = number_format($logins_seniat_increment_number, 2, ",", ".");

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

$targets = array();
$out->targets = &$targets;

$target = new stdClass();
$target->label = new stdClass();
$target->label->short =  $targets_label_short;
$target->label->long =  $targets_label_long;
$target->values = new stdClass();
$target->values->nuevos =  $targets_values_nuevos;
$target->values->existente =  $targets_values_existente;
$target->values->baja =  $targets_values_baja;
$targets[] = $target;


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

$customers = array();
$out->customers = &$customers;

$customer = new stdClass();
$customer->label =  new stdClass();
$customer->label->short = $date_short;
$customer->label->short = $date_long;
$customer->values =  new stdClass();
$customer->values->c1 = "Qué poner aquí?_1";
$customer->values->c2 = "Qué poner aquí?_2";
$customer->values->c3 = "Qué poner aquí?_3";
$customers[] = $customer;

header("HTTP/1.1 200");
echo (json_encode($out));
die();

?>
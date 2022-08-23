<?php
// app/api/reports/salesbook.php

header("Content-Type:application/json");
include_once("../../../settings/dbconn.php");
include_once("../../../settings/utils.php");

function getTotalsData($from,$to,$customerid,$db){
  $sql = "SELECT
    ROUND(SUM(qty * unitprice *(1 - itemdiscount / 100)),2) taxbase,
    ROUND(SUM(qty * unitprice *(1 - itemdiscount / 100) *(1 + itemtax / 100)),2) total,
    ROUND(SUM(CASE WHEN itemtax = 0 THEN qty * unitprice *(1 - itemdiscount / 100) ELSE 0 END),2) exempt_taxbase,
    ROUND(SUM(CASE WHEN itemtax > 0 THEN qty * unitprice *(1 - itemdiscount / 100) * itemtax / 100 ELSE 0 END),2) exempt_taxtotal,
    ROUND(SUM(CASE WHEN itemtax = -2 THEN qty * unitprice *(1 - itemdiscount / 100) ELSE 0 END),2) notaxable_taxbase 
    FROM invoicedetails d
    INNER JOIN invoiceheader h ON h.id = d.invoiceid 
    WHERE h.creationdate BETWEEN '$from' AND '$to' AND customerid = $customerid ";
      
  $rs = $db->query($sql);
  if (!$rs)
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  return $rs;  
}

function getRecordsetData($from,$to,$customerid,$db){
  $sql = "SELECT id, issuedate, clientrif, clientname,
        refnumber, ctrnumber, `type`, ctrref, D.taxbase,
        D.total, D.ivas, D.taxtotal, D.notaxable_taxbase,
        D.perceived_taxbase
        FROM invoiceheader h
        INNER JOIN
        (
          SELECT
              invoiceid,
              SUM(qty * unitprice *(1 - itemdiscount / 100)) taxbase,
              SUM(qty * unitprice *(1 - itemdiscount / 100)*(1 + itemtax / 100)) total,
              SUM(CASE WHEN itemtax = 0 THEN qty * unitprice *(1 - itemdiscount / 100) ELSE 0 END) exempt_taxbase,
              GROUP_CONCAT(itemtax) ivas,
              SUM(CASE WHEN itemtax > 0 THEN qty * unitprice *(1 - itemdiscount / 100)*itemtax/100 ELSE 0 END) taxtotal,
              SUM(CASE WHEN itemtax = -2 THEN qty * unitprice *(1 - itemdiscount / 100) ELSE 0 END) notaxable_taxbase,
              SUM(CASE WHEN itemtax = -1 THEN qty * unitprice *(1 - itemdiscount / 100) ELSE 0 END) perceived_taxbase

          FROM
              invoicedetails d INNER JOIN invoiceheader h
              ON h.id = d.invoiceid 
          WHERE
              h.creationdate BETWEEN '$from' AND '$to' 
              AND customerid = $customerid
          GROUP BY
              invoiceid
        ) D
        ON D.invoiceid=h.id
        WHERE creationdate 
        BETWEEN '$from' AND '$to' AND customerid = $customerid ";
      
  $rs = $db->query($sql);
  if (!$rs)
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  
  return $rs;  
}
function getTotalRecords($from,$to,$customerid,$db){
  $sql = "SELECT COUNT(*) Cnt FROM invoiceheader 
        WHERE creationdate 
        BETWEEN '$from' AND '$to' AND customerid = $customerid ";
  $rs = $db->query($sql);
  if (!$rs)
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  $row = $rs->fetch_assoc();
  return $row['Cnt'];         
}
function formatListIvas($list){
  $ivas = explode(",",$list);
  $formatted = [];
  foreach ($ivas as $iva) {
    $formatted[] = number_format($iva,2,",",".")."%";
  }
  return implode(",",$formatted);
}
$parmsob = array("datefrom","dateto","offset","numofrec","sessionid");
if (!parametrosValidos($_GET, $parmsob))
    badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

$from = $_REQUEST["datefrom"];
$to = $_REQUEST["dateto"];
//Validar user session
$customerid = 105;
//$customerid = isSessionValid($db, $_REQUEST["sessionid"]);

//Llenar el arreglo de records
  $records = [];
  $rs = getRecordsetData($from,$to,$customerid,$db);
  while ($row = $rs->fetch_assoc()) {
    $record = new stdClass();
    $record->id = (integer)$row['id'];
    $record->issuedate = new stdClass();
    $record->issuedate->date = $row['issuedate'];
    $record->issuedate->formatted = date_format(date_create($row['issuedate']),'d/m/Y');
    $record->client = new stdClass();
    $record->client->rif = $row['clientrif'];
    $record->client->name = $row['clientname'];
    $record->refnumber = $row['refnumber'];
    $record->ctrnumber = $row['ctrnumber'];
    $record->type = new stdClass();
    $record->type->id = $row['type'];
    switch ($row['type']) {
      case 'FAC':
        $typename = 'Factura';
        break;
      case 'NDB':
        $typename = 'Nota de Débito';
        break;
      case 'NCR':
        $typename = 'Nota de Crédito';
        break;        
    }
    $record->type->name = $typename;
    $record->transactiontype = $transactiontype;
    $record->ctrref = $row['ctrref'];
    $record->amounts = new stdClass();

    $record->amounts->totals = new stdClass();
    $record->amounts->totals->taxbase = new stdClass();
    $record->amounts->totals->taxbase->number = (float)$row['taxbase'];
    $record->amounts->totals->taxbase->formatted = number_format($row['taxbase'],2,",",".");
    $record->amounts->totals->total = new stdClass();
    $record->amounts->totals->total->number = (float)round($row['total'],2);
    $record->amounts->totals->total->formatted = number_format($row['total'],2,",",".");

    //exempt
    $record->amounts->exempt = new stdClass();
    $record->amounts->exempt->taxbase = new stdClass();
    $record->amounts->exempt->taxbase->number = (float)$row['exempt_taxbase'];
    $record->amounts->exempt->taxbase->formatted = number_format($row['exempt_taxbase'],2,",",".");
    $record->amounts->exempt->taxpct = new stdClass();
    $record->amounts->exempt->taxpct->number = $row['ivas'];
    $record->amounts->exempt->taxpct->formatted = formatListIvas($row['ivas']);
    $record->amounts->exempt->taxtotal = new stdClass();
    $record->amounts->exempt->taxtotal->number = (float)$row['taxtotal'];
    $record->amounts->exempt->taxtotal->formatted = number_format($row['taxtotal'],2,",",".");
    //notaxable
    $record->amounts->notaxable = new stdClass();
    $record->amounts->notaxable->taxbase = new stdClass();
    $record->amounts->notaxable->taxbase->number = (float)$row['notaxable_taxbase'];
    $record->amounts->notaxable->taxbase->formatted = number_format($row['notaxable_taxbase'],2,",",".");
    $record->amounts->notaxable->taxpct = new stdClass();
    $record->amounts->notaxable->taxpct->number = $row['ivas'];
    $record->amounts->notaxable->taxpct->formatted = formatListIvas($row['ivas']);
    $record->amounts->notaxable->taxtotal = new stdClass();
    $record->amounts->notaxable->taxtotal->number = (float)$row['taxtotal'];
    $record->amounts->notaxable->taxtotal->formatted = number_format($row['taxtotal'],2,",",".");
    //perceived
    $record->amounts->perceived = new stdClass();
    $record->amounts->perceived->taxbase = new stdClass();
    $record->amounts->perceived->taxbase->number = (float)$row['perceived_taxbase'];
    $record->amounts->perceived->taxbase->formatted = number_format($row['perceived_taxbase'],2,",",".");
    $record->amounts->perceived->taxpct = new stdClass();
    $record->amounts->perceived->taxpct->number = $row['ivas'];
    $record->amounts->perceived->taxpct->formatted = formatListIvas($row['ivas']);
    $record->amounts->perceived->taxtotal = new stdClass();
    $record->amounts->perceived->taxtotal->number = (float)$row['taxtotal'];
    $record->amounts->perceived->taxtotal->formatted = number_format($row['taxtotal'],2,",",".");
    //generaliva
    $record->amounts->generaliva = new stdClass();
    $record->amounts->generaliva->taxbase = new stdClass();
    $record->amounts->generaliva->taxbase->number = $amounts_generaliva_taxbase;
    $record->amounts->generaliva->taxbase->formatted = number_format($amounts_generaliva_taxbase,2,",",".");
    $record->amounts->generaliva->taxpct = new stdClass();
    $record->amounts->generaliva->taxpct->number = $amounts_generaliva_taxpct;
    $record->amounts->generaliva->taxpct->formatted = number_format($amounts_generaliva_taxpct,2,",",".");
    $record->amounts->generaliva->taxtotal = new stdClass();
    $record->amounts->generaliva->taxtotal->number = $amounts_generaliva_taxtotal;
    $record->amounts->generaliva->taxtotal->formatted = number_format($amounts_generaliva_taxtotal,2,",",".");
    //reducediva
    $record->amounts->reducediva = new stdClass();
    $record->amounts->reducediva->taxbase = new stdClass();
    $record->amounts->reducediva->taxbase->number = $amounts_reducediva_taxbase;
    $record->amounts->reducediva->taxbase->formatted = number_format($amounts_reducediva_taxbase,2,",",".");
    $record->amounts->reducediva->taxpct = new stdClass();
    $record->amounts->reducediva->taxpct->number = $amounts_reducediva_taxpct;
    $record->amounts->reducediva->taxpct->formatted = number_format($amounts_reducediva_taxpct,2,",",".");
    $record->amounts->reducediva->taxtotal = new stdClass();
    $record->amounts->reducediva->taxtotal->number = $amounts_reducediva_taxtotal;
    $record->amounts->reducediva->taxtotal->formatted = number_format($amounts_reducediva_taxtotal,2,",",".");
    //addediva
    $record->amounts->addediva = new stdClass();
    $record->amounts->addediva->taxbase = new stdClass();
    $record->amounts->addediva->taxbase->number = $amounts_addediva_taxbase;
    $record->amounts->addediva->taxbase->formatted = number_format($amounts_addediva_taxbase,2,",",".");
    $record->amounts->addediva->taxpct = new stdClass();
    $record->amounts->addediva->taxpct->number = $amounts_addediva_taxpct;
    $record->amounts->addediva->taxpct->formatted = number_format($amounts_addediva_taxpct,2,",",".");
    $record->amounts->addediva->taxtotal = new stdClass();
    $record->amounts->addediva->taxtotal->number = $amounts_addediva_taxtotal;
    $record->amounts->addediva->taxtotal->formatted = number_format($amounts_addediva_taxtotal,2,",",".");

    $records[] = $record;
  }
//Se llena la estructura de salida
$out = new stdClass();
$out->numofrecords = (integer)getTotalRecords($from,$to,$customerid,$db);
$out->records = $records;


$out->totals = new stdClass();
  $rs = getTotalsData($from,$to,$customerid,$db);
  $row = $rs->fetch_assoc();

  $out->totals->totals = new stdClass();
  $out->totals->totals->taxbase = new stdClass();
  $out->totals->totals->taxbase->number = (float)$row['taxbase'];
  $out->totals->totals->taxbase->formatted = number_format($row['taxbase'],2, ",", ".");
  $out->totals->totals->total->number = (float)$row['total'];
  $out->totals->totals->total->formatted = number_format($row['total'],2, ",", ".");

  $out->totals->exempt = new stdClass();
  $out->totals->exempt->taxbase = new stdClass();
  $out->totals->exempt->taxbase->number = $totals_exempt_taxbase_number;
  $out->totals->exempt->taxbase->formatted = number_format($totals_exempt_taxbase_number,2, ",", ".");
  $out->totals->exempt->taxpct->number = $totals_exempt_taxpct_number;
  $out->totals->exempt->taxpct->formatted = number_format($totals_exempt_taxpct_number,2, ",", ".");
  $out->totals->exempt->taxtotal->number = $totals_exempt_taxtotal_number;
  $out->totals->exempt->taxtotal->formatted = number_format($totals_exempt_taxtotal_number,2, ",", ".");

  $out->totals->notaxable = new stdClass();
  $out->totals->notaxable->taxbase = new stdClass();
  $out->totals->notaxable->taxbase->number = $totals_notaxable_taxbase_number;
  $out->totals->notaxable->taxbase->formatted = number_format($totals_notaxable_taxbase_number,2, ",", ".");
  $out->totals->notaxable->taxpct->number = $totals_notaxable_taxpct_number;
  $out->totals->notaxable->taxpct->formatted = number_format($totals_notaxable_taxpct_number,2, ",", ".");
  $out->totals->notaxable->taxtotal->number = $totals_notaxable_taxtotal_number;
  $out->totals->notaxable->taxtotal->formatted = number_format($totals_notaxable_taxtotal_number,2, ",", ".");

  $out->totals->perceived = new stdClass();
  $out->totals->perceived->taxbase = new stdClass();
  $out->totals->perceived->taxbase->number = $totals_perceived_taxbase_number;
  $out->totals->perceived->taxbase->formatted = number_format($totals_perceived_taxbase_number,2, ",", ".");
  $out->totals->perceived->taxpct->number = $totals_perceived_taxpct_number;
  $out->totals->perceived->taxpct->formatted = number_format($totals_perceived_taxpct_number,2, ",", ".");
  $out->totals->perceived->taxtotal->number = $totals_perceived_taxtotal_number;
  $out->totals->perceived->taxtotal->formatted = number_format($totals_perceived_taxtotal_number,2, ",", ".");

  $out->totals->generaliva = new stdClass();
  $out->totals->generaliva->taxbase = new stdClass();
  $out->totals->generaliva->taxbase->number = $totals_generaliva_taxbase_number;
  $out->totals->generaliva->taxbase->formatted = number_format($totals_generaliva_taxbase_number,2, ",", ".");
  $out->totals->generaliva->taxpct->number = $totals_generaliva_taxpct_number;
  $out->totals->generaliva->taxpct->formatted = number_format($totals_generaliva_taxpct_number,2, ",", ".");
  $out->totals->generaliva->taxtotal->number = $totals_generaliva_taxtotal_number;
  $out->totals->generaliva->taxtotal->formatted = number_format($totals_generaliva_taxtotal_number,2, ",", ".");

  $out->totals->reducediva = new stdClass();
  $out->totals->reducediva->taxbase = new stdClass();
  $out->totals->reducediva->taxbase->number = $totals_reducediva_taxbase_number;
  $out->totals->reducediva->taxbase->formatted = number_format($totals_reducediva_taxbase_number,2, ",", ".");
  $out->totals->reducediva->taxpct->number = $totals_reducediva_taxpct_number;
  $out->totals->reducediva->taxpct->formatted = number_format($totals_reducediva_taxpct_number,2, ",", ".");
  $out->totals->reducediva->taxtotal->number = $totals_reducediva_taxtotal_number;
  $out->totals->reducediva->taxtotal->formatted = number_format($totals_reducediva_taxtotal_number,2, ",", ".");

  $out->totals->addediva = new stdClass();
  $out->totals->addediva->taxbase = new stdClass();
  $out->totals->addediva->taxbase->number = $totals_addediva_taxbase_number;
  $out->totals->addediva->taxbase->formatted = number_format($totals_addediva_taxbase_number,2, ",", ".");
  $out->totals->addediva->taxpct->number = $totals_addediva_taxpct_number;
  $out->totals->addediva->taxpct->formatted = number_format($totals_addediva_taxpct_number,2, ",", ".");
  $out->totals->addediva->taxtotal->number = $totals_addediva_taxtotal_number;
  $out->totals->addediva->taxtotal->formatted = number_format($totals_addediva_taxtotal_number,2, ",", ".");

$out->resume = new stdClass();
//notax
  $out->resume->notax = new stdClass();
  
  $out->resume->notax->debits = new stdClass();
  $out->resume->notax->debits->taxbase = new stdClass();
  $out->resume->notax->debits->taxbase->number = $resume_notax_debits_taxbase_number;
  $out->resume->notax->debits->taxbase->formatted = number_format($resume_notax_debits_taxbase_number,2,",",".");
  $out->resume->notax->debits->taxtotal = new stdClass();
  $out->resume->notax->debits->taxtotal->number = $resume_notax_debits_taxtotal_number;
  $out->resume->notax->debits->taxtotal->formatted = number_format($resume_notax_debits_taxtotal_number,2,",",".");

  $out->resume->notax->credits = new stdClass();
  $out->resume->notax->credits->taxbase = new stdClass();
  $out->resume->notax->credits->taxbase->number = $resume_notax_credits_taxbase_number;
  $out->resume->notax->credits->taxbase->formatted = number_format($resume_notax_credits_taxbase_number,2,",",".");
  $out->resume->notax->credits->taxtotal = new stdClass();
  $out->resume->notax->credits->taxtotal->number = $resume_notax_credits_taxtotal_number;
  $out->resume->notax->credits->taxtotal->formatted = number_format($resume_notax_credits_taxtotal_number,2,",",".");

  $out->resume->notax->totals = new stdClass();
  $out->resume->notax->totals->taxbase = new stdClass();
  $out->resume->notax->totals->taxbase->number = $resume_notax_totals_taxbase_number;
  $out->resume->notax->totals->taxbase->formatted = number_format($resume_notax_totals_taxbase_number,2,",",".");
  $out->resume->notax->totals->taxtotal = new stdClass();
  $out->resume->notax->totals->taxtotal->number = $resume_notax_totals_taxtotal_number;
  $out->resume->notax->totals->taxtotal->formatted = number_format($resume_notax_totals_taxtotal_number,2,",",".");

//generaltax
  $out->resume->generaltax = new stdClass();
  
  $out->resume->generaltax->debits = new stdClass();
  $out->resume->generaltax->debits->taxbase = new stdClass();
  $out->resume->generaltax->debits->taxbase->number = $resume_generaltax_debits_taxbase_number;
  $out->resume->generaltax->debits->taxbase->formatted = number_format($resume_generaltax_debits_taxbase_number,2,",",".");
  $out->resume->generaltax->debits->taxtotal = new stdClass();
  $out->resume->generaltax->debits->taxtotal->number = $resume_generaltax_debits_taxtotal_number;
  $out->resume->generaltax->debits->taxtotal->formatted = number_format($resume_generaltax_debits_taxtotal_number,2,",",".");

  $out->resume->generaltax->credits = new stdClass();
  $out->resume->generaltax->credits->taxbase = new stdClass();
  $out->resume->generaltax->credits->taxbase->number = $resume_generaltax_credits_taxbase_number;
  $out->resume->generaltax->credits->taxbase->formatted = number_format($resume_generaltax_credits_taxbase_number,2,",",".");
  $out->resume->generaltax->credits->taxtotal = new stdClass();
  $out->resume->generaltax->credits->taxtotal->number = $resume_generaltax_credits_taxtotal_number;
  $out->resume->generaltax->credits->taxtotal->formatted = number_format($resume_generaltax_credits_taxtotal_number,2,",",".");

  $out->resume->generaltax->totals = new stdClass();
  $out->resume->generaltax->totals->taxbase = new stdClass();
  $out->resume->generaltax->totals->taxbase->number = $resume_generaltax_totals_taxbase_number;
  $out->resume->generaltax->totals->taxbase->formatted = number_format($resume_generaltax_totals_taxbase_number,2,",",".");
  $out->resume->generaltax->totals->taxtotal = new stdClass();
  $out->resume->generaltax->totals->taxtotal->number = $resume_generaltax_totals_taxtotal_number;
  $out->resume->generaltax->totals->taxtotal->formatted = number_format($resume_generaltax_totals_taxtotal_number,2,",",".");

//reducedtax  
  $out->resume->reducedtax = new stdClass();
  $out->resume->reducedtax->debits = new stdClass();
  $out->resume->reducedtax->debits->taxbase = new stdClass();
  $out->resume->reducedtax->debits->taxbase->number = $resume_reducedtax_debits_taxbase_number;
  $out->resume->reducedtax->debits->taxbase->formatted = number_format($resume_reducedtax_debits_taxbase_number,2,",",".");
  $out->resume->reducedtax->debits->taxtotal = new stdClass();
  $out->resume->reducedtax->debits->taxtotal->number = $resume_reducedtax_debits_taxtotal_number;
  $out->resume->reducedtax->debits->taxtotal->formatted = number_format($resume_reducedtax_debits_taxtotal_number,2,",",".");

  $out->resume->reducedtax->credits = new stdClass();
  $out->resume->reducedtax->credits->taxbase = new stdClass();
  $out->resume->reducedtax->credits->taxbase->number = $resume_reducedtax_credits_taxbase_number;
  $out->resume->reducedtax->credits->taxbase->formatted = number_format($resume_reducedtax_credits_taxbase_number,2,",",".");
  $out->resume->reducedtax->credits->taxtotal = new stdClass();
  $out->resume->reducedtax->credits->taxtotal->number = $resume_reducedtax_credits_taxtotal_number;
  $out->resume->reducedtax->credits->taxtotal->formatted = number_format($resume_reducedtax_credits_taxtotal_number,2,",",".");

  $out->resume->reducedtax->totals = new stdClass();
  $out->resume->reducedtax->totals->taxbase = new stdClass();
  $out->resume->reducedtax->totals->taxbase->number = $resume_reducedtax_totals_taxbase_number;
  $out->resume->reducedtax->totals->taxbase->formatted = number_format($resume_reducedtax_totals_taxbase_number,2,",",".");
  $out->resume->reducedtax->totals->taxtotal = new stdClass();
  $out->resume->reducedtax->totals->taxtotal->number = $resume_reducedtax_totals_taxtotal_number;
  $out->resume->reducedtax->totals->taxtotal->formatted = number_format($resume_reducedtax_totals_taxtotal_number,2,",",".");

//addedtax
  $out->resume->addedtax = new stdClass();  
  $out->resume->addedtax->debits = new stdClass();
  $out->resume->addedtax->debits->taxbase = new stdClass();
  $out->resume->addedtax->debits->taxbase->number = $resume_addedtax_debits_taxbase_number;
  $out->resume->addedtax->debits->taxbase->formatted = number_format($resume_addedtax_debits_taxbase_number,2,",",".");
  $out->resume->addedtax->debits->taxtotal = new stdClass();
  $out->resume->addedtax->debits->taxtotal->number = $resume_addedtax_debits_taxtotal_number;
  $out->resume->addedtax->debits->taxtotal->formatted = number_format($resume_addedtax_debits_taxtotal_number,2,",",".");

  $out->resume->addedtax->credits = new stdClass();
  $out->resume->addedtax->credits->taxbase = new stdClass();
  $out->resume->addedtax->credits->taxbase->number = $resume_addedtax_credits_taxbase_number;
  $out->resume->addedtax->credits->taxbase->formatted = number_format($resume_addedtax_credits_taxbase_number,2,",",".");
  $out->resume->addedtax->credits->taxtotal = new stdClass();
  $out->resume->addedtax->credits->taxtotal->number = $resume_addedtax_credits_taxtotal_number;
  $out->resume->addedtax->credits->taxtotal->formatted = number_format($resume_addedtax_credits_taxtotal_number,2,",",".");

  $out->resume->addedtax->totals = new stdClass();
  $out->resume->addedtax->totals->taxbase = new stdClass();
  $out->resume->addedtax->totals->taxbase->number = $resume_addedtax_totals_taxbase_number;
  $out->resume->addedtax->totals->taxbase->formatted = number_format($resume_addedtax_totals_taxbase_number,2,",",".");
  $out->resume->addedtax->totals->taxtotal = new stdClass();
  $out->resume->addedtax->totals->taxtotal->number = $resume_addedtax_totals_taxtotal_number;
  $out->resume->addedtax->totals->taxtotal->formatted = number_format($resume_addedtax_totals_taxtotal_number,2,",",".");

header("HTTP/1.1 200");
echo (json_encode($out));
die();


?>
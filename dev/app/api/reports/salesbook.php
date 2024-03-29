<?php
// app/api/reports/salesbook.php

header("Content-Type:application/json");
include_once("../../../settings/dbconn.php");
include_once("../../../settings/utils.php");

function getTotalsData($from,$to,$customerid,$db){
  $sql = "SELECT ROUND(SUM(CASE WHEN canceldate > '1900-01-01' THEN 0 WHEN `type` = 'NCR' THEN (-1)*qty * unitprice *(1 - itemdiscount / 100) ELSE qty * unitprice *(1 - itemdiscount / 100) END),2) taxbase
            ,ROUND(SUM(CASE WHEN itemtax < 0 THEN qty * unitprice *(1 - itemdiscount / 100) WHEN canceldate > '1900-01-01' THEN 0 WHEN `type` = 'NCR' THEN (-1)*qty * unitprice *(1 - itemdiscount / 100)*(1 + itemtax / 100) ELSE qty * unitprice *(1 - itemdiscount / 100)*(1 + itemtax / 100) END),2) total
            ,ROUND(SUM(CASE WHEN itemtax = 0 THEN qty * unitprice *(1 - itemdiscount / 100) ELSE 0 END),2) exempt_taxbase
            ,ROUND(SUM(CASE WHEN itemtax = -2 THEN qty * unitprice *(1 - itemdiscount / 100) ELSE 0 END),2) notaxable_taxbase
            ,ROUND(SUM(CASE WHEN itemtax = -1 THEN qty * unitprice *(1 - itemdiscount / 100) ELSE 0 END),2) perceived_taxbase
            ,ROUND(SUM(CASE WHEN canceldate > '1900-01-01' THEN 0 WHEN  `type` = 'NCR'  THEN (-1)*qty*unitprice*(1 - itemdiscount / 100) ELSE 0 END),2) credit_taxbase
          FROM invoicedetails d
          INNER JOIN invoiceheader h
          ON h.id = d.invoiceid
          WHERE h.issuedate BETWEEN '$from' AND '$to'
          AND customerid = $customerid";
      
  $rs = $db->query($sql);
  if (!$rs)
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  return $rs;  
}
function getRecordsetData($from,$to,$customerid,$offset,$numofrec,$db){
  $sql = "SELECT  h.issuedate
            ,clientrif
            ,clientname
            ,refnumber
            ,ctrnumber
            ,`type`
            ,ctrref
            ,D.taxbase
            ,D.total
            ,D.taxtotal
            ,D.notaxable_taxbase
            ,D.perceived_taxbase
            ,A.tot16
            ,A.tot8
            ,A.tot27
        FROM invoiceheader h
        INNER JOIN
        (
          SELECT  invoiceid
                ,SUM(CASE WHEN canceldate > '1900-01-01' THEN 0 WHEN `type` = 'NCR' THEN (-1)*qty*unitprice*(1 - itemdiscount/100) ELSE qty * unitprice *(1 - itemdiscount / 100) END) taxbase
                ,SUM(CASE WHEN canceldate > '1900-01-01' THEN 0 WHEN `type` = 'NCR' THEN (-1)*qty*unitprice*(1 - itemdiscount/100)*(1 + itemtax / 100) WHEN itemtax < 0 THEN qty * unitprice *(1 - itemdiscount/100) ELSE qty * unitprice *(1 - itemdiscount / 100)*(1 + itemtax / 100) END) total
                ,SUM(CASE WHEN canceldate > '1900-01-01' THEN 0 WHEN `type` = 'NCR' THEN (-1)*qty * unitprice *(1 - itemdiscount / 100) WHEN itemtax = 0 THEN qty * unitprice *(1 - itemdiscount / 100)  ELSE 0 END) exempt_taxbase
                ,SUM(CASE WHEN canceldate > '1900-01-01' THEN 0 WHEN `type` = 'NCR' THEN (-1)*qty * unitprice *(1 - itemdiscount / 100)*itemtax/100 WHEN itemtax > 0 THEN qty * unitprice *(1 - itemdiscount / 100)*itemtax/100  ELSE 0 END) taxtotal
                ,SUM(CASE WHEN canceldate > '1900-01-01' THEN 0 WHEN `type` = 'NCR' THEN (-1)*qty * unitprice *(1 - itemdiscount / 100) WHEN itemtax = -2 THEN qty * unitprice *(1 - itemdiscount / 100)  ELSE 0 END) notaxable_taxbase
                ,SUM(CASE WHEN canceldate > '1900-01-01' THEN 0 WHEN `type` = 'NCR' THEN (-1)*qty * unitprice *(1 - itemdiscount / 100) WHEN itemtax = -1 THEN qty * unitprice *(1 - itemdiscount / 100)  ELSE 0 END) perceived_taxbase
          FROM invoicedetails d
          INNER JOIN invoiceheader h
          ON h.id = d.invoiceid
          WHERE h.issuedate BETWEEN '$from' AND '$to'
          AND customerid = $customerid
          GROUP BY  invoiceid
        ) D
        ON D.invoiceid = h.id
        LEFT JOIN
        (
          SELECT  E.id
                ,ROUND(SUM(CASE WHEN E.canceldate > '1900-01-01' THEN 0 WHEN E.type = 'NCR' THEN IF(D.itemtax IN(16),(-1)*D.qty * D.unitprice,0) ELSE IF(D.itemtax IN(16),D.qty * D.unitprice,0) END),2 ) tot16
                ,ROUND(SUM(CASE WHEN E.canceldate > '1900-01-01' THEN 0 WHEN E.type = 'NCR' THEN IF(D.itemtax IN(8),(-1)*D.qty * D.unitprice,0) ELSE IF(D.itemtax IN(8),D.qty * D.unitprice,0) END),2) tot8
                ,ROUND(SUM(CASE WHEN E.canceldate > '1900-01-01' THEN 0 WHEN E.type = 'NCR' THEN IF(D.itemtax IN(27),(-1)*D.qty * D.unitprice,0) ELSE IF(D.itemtax IN(27),D.qty * D.unitprice,0) END),2) tot27
          FROM invoiceheader E
          INNER JOIN invoicedetails D
          ON E.id = D.invoiceid
          WHERE E.issuedate BETWEEN '$from' AND '$to'
          AND customerid = $customerid
          GROUP BY  E.id
        ) A
        ON A.id = h.id
        WHERE h.issuedate BETWEEN '$from' AND '$to'
        AND customerid = $customerid
        ORDER BY h.issuedate
        LIMIT $offset, $numofrec";
      
  $rs = $db->query($sql);
  if (!$rs)
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  
  return $rs;  
}
function getTotalRecords($from,$to,$customerid,$db){
  $sql = "SELECT  COUNT(*) Cnt
          FROM invoiceheader
          WHERE issuedate BETWEEN '$from' AND '$to'
          AND customerid = $customerid ";
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
function getTotalsAlicuotas($from,$to,$customerid,$db){
  $sql = "SELECT
                 ROUND(SUM(CASE WHEN E.canceldate > '1900-01-01' THEN 0 WHEN E.type = 'NCR' THEN IF(D.itemtax IN(16),(-1)*D.qty*D.unitprice,0) ELSE IF(D.itemtax IN(16),D.qty*D.unitprice,0) END),2) totgeneral
                ,ROUND(SUM(CASE WHEN E.canceldate > '1900-01-01' THEN 0 WHEN E.type = 'NCR' THEN IF(D.itemtax IN(8),(-1)*D.qty*D.unitprice,0) ELSE IF(D.itemtax IN(8),D.qty*D.unitprice,0) END),2) totreducido
                ,ROUND(SUM(CASE WHEN E.canceldate > '1900-01-01' THEN 0 WHEN E.type = 'NCR' THEN IF(D.itemtax IN(27),(-1)*D.qty*D.unitprice,0) ELSE IF(D.itemtax IN(27),D.qty*D.unitprice,0) END),2) totadicional
          FROM invoiceheader E
          INNER JOIN invoicedetails D
          ON E.id = D.invoiceid
          WHERE E.issuedate BETWEEN '$from' AND '$to'
          AND customerid = $customerid ";
  //print_r($sql);    
  $rs = $db->query($sql);
  if (!$rs)
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  return $rs;  
}
$parmsob = array("datefrom","dateto","offset","numofrec","sessionid");
if (!parametrosValidos($_GET, $parmsob))
    badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

$from = $_REQUEST["datefrom"];
$to = $_REQUEST["dateto"];
//Validar user session
//$customerid = 105;
$customerid = isSessionValid($db, $_REQUEST["sessionid"]);

//Llenar el arreglo de records
  $records = [];
  $rs = getRecordsetData($from,$to,$customerid,$_REQUEST["offset"],$_REQUEST["numofrec"],$db);
  $count = $_REQUEST["offset"]+1;
  while ($row = $rs->fetch_assoc()) {
    $record = new stdClass();
    $record->id = $count++;
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
    $record->amounts->exempt->taxpct->number = 0;
    $record->amounts->exempt->taxpct->formatted = "0%";
    $record->amounts->exempt->taxtotal = new stdClass();
    $record->amounts->exempt->taxtotal->number = 0;
    $record->amounts->exempt->taxtotal->formatted = "0,00";
    //notaxable
    $record->amounts->notaxable = new stdClass();
    $record->amounts->notaxable->taxbase = new stdClass();
    $record->amounts->notaxable->taxbase->number = (float)$row['notaxable_taxbase'];
    $record->amounts->notaxable->taxbase->formatted = number_format($row['notaxable_taxbase'],2,",",".");
    $record->amounts->notaxable->taxpct = new stdClass();
    $record->amounts->notaxable->taxpct->number = 0;
    $record->amounts->notaxable->taxpct->formatted = "0%";
    $record->amounts->notaxable->taxtotal = new stdClass();
    $record->amounts->notaxable->taxtotal->number = (float)$row['notaxable_taxbase'];
    $record->amounts->notaxable->taxtotal->formatted = number_format($row['notaxable_taxbase'],2,",",".");
    //perceived
    $record->amounts->perceived = new stdClass();
    $record->amounts->perceived->taxbase = new stdClass();
    $record->amounts->perceived->taxbase->number = (float)$row['perceived_taxbase'];
    $record->amounts->perceived->taxbase->formatted = number_format($row['perceived_taxbase'],2,",",".");
    $record->amounts->perceived->taxpct = new stdClass();
    $record->amounts->perceived->taxpct->number = 0;
    $record->amounts->perceived->taxpct->formatted = "0%";
    $record->amounts->perceived->taxtotal = new stdClass();
    $record->amounts->perceived->taxtotal->number = (float)$row['perceived_taxbase'];
    $record->amounts->perceived->taxtotal->formatted = number_format($row['perceived_taxbase'],2,",",".");
    //generaliva
    $record->amounts->generaliva = new stdClass();
    $record->amounts->generaliva->taxbase = new stdClass();
    $record->amounts->generaliva->taxbase->number = (float)$row['tot16'];
    $record->amounts->generaliva->taxbase->formatted = number_format((float)$row['tot16'],2,",",".");
    $record->amounts->generaliva->taxpct = new stdClass();
    $record->amounts->generaliva->taxpct->number = 16;
    $record->amounts->generaliva->taxpct->formatted = number_format(16,2,",",".");
    $record->amounts->generaliva->taxtotal = new stdClass();
    $record->amounts->generaliva->taxtotal->number = $row['tot16']*(1+16/100);
    $record->amounts->generaliva->taxtotal->formatted = number_format($row['tot16']*(1+16/100),2,",",".");
    //reducediva
    $record->amounts->reducediva = new stdClass();
    $record->amounts->reducediva->taxbase = new stdClass();
    $record->amounts->reducediva->taxbase->number = (float)$row['tot8'];
    $record->amounts->reducediva->taxbase->formatted = number_format((float)$row['tot8'],2,",",".");
    $record->amounts->reducediva->taxpct = new stdClass();
    $record->amounts->reducediva->taxpct->number = 8;
    $record->amounts->reducediva->taxpct->formatted = number_format(8,2,",",".");
    $record->amounts->reducediva->taxtotal = new stdClass();
    $record->amounts->reducediva->taxtotal->number = $row['tot8']*(1+8/100);
    $record->amounts->reducediva->taxtotal->formatted = number_format($row['tot8']*(1+8/100),2,",",".");
    //addediva
    $record->amounts->addediva = new stdClass();
    $record->amounts->addediva->taxbase = new stdClass();
    $record->amounts->addediva->taxbase->number = (float)$row['tot27'];
    $record->amounts->addediva->taxbase->formatted =  number_format((float)$row['tot27'],2,",",".");
    $record->amounts->addediva->taxpct = new stdClass();
    $record->amounts->addediva->taxpct->number = 27;
    $record->amounts->addediva->taxpct->formatted = number_format(27,2,",",".");
    $record->amounts->addediva->taxtotal = new stdClass();
    $record->amounts->addediva->taxtotal->number = $row['tot27']*(1+27/100);
    $record->amounts->addediva->taxtotal->formatted = number_format($row['tot27']*(1+27/100),2,",",".");

    $records[] = $record;
  }
//Se llena la estructura de salida
$out = new stdClass();
$out->numofrecords = (integer)getTotalRecords($from,$to,$customerid,$db);
$out->records = $records;

$out->totals = new stdClass();

  $rs = getTotalsData($from,$to,$customerid,$db);
  $row = $rs->fetch_assoc();
  $resumenTotalNotax = (float)($row['exempt_taxbase'] + $row['notaxable_taxbase'] + $row['perceived_taxbase']);
  $resumenTotalNotaxCredit = (float)$row['credit_taxbase'];
  
  $out->totals->totals = new stdClass();
  $out->totals->totals->taxbase = new stdClass();
  $out->totals->totals->taxbase->number = (float)$row['taxbase'];
  $out->totals->totals->taxbase->formatted = number_format($row['taxbase'],2, ",", ".");
  $out->totals->totals->total->number = (float)$row['total'];
  $out->totals->totals->total->formatted = number_format($row['total'],2, ",", ".");

  $out->totals->exempt = new stdClass();
  $out->totals->exempt->taxbase = new stdClass();
  $out->totals->exempt->taxbase->number = (float)$row['exempt_taxbase'];
  $out->totals->exempt->taxbase->formatted = number_format($row['exempt_taxbase'],2, ",", ".");
  $out->totals->exempt->taxpct->number = 0;
  $out->totals->exempt->taxpct->formatted = "0,00";
  $out->totals->exempt->taxtotal->number = (float)$row['exempt_taxbase'];
  $out->totals->exempt->taxtotal->formatted = number_format($row['exempt_taxbase'],2, ",", ".");

  $out->totals->notaxable = new stdClass();
  $out->totals->notaxable->taxbase = new stdClass();
  $out->totals->notaxable->taxbase->number = (float)$row['notaxable_taxbase'];
  $out->totals->notaxable->taxbase->formatted = number_format($row['notaxable_taxbase'],2, ",", ".");
  $out->totals->notaxable->taxpct->number = 0;
  $out->totals->notaxable->taxpct->formatted = "0,00";
  $out->totals->notaxable->taxtotal->number = $row['notaxable_taxbase'];
  $out->totals->notaxable->taxtotal->formatted = number_format($row['notaxable_taxbase'],2, ",", ".");

  $out->totals->perceived = new stdClass();
  $out->totals->perceived->taxbase = new stdClass();
  $out->totals->perceived->taxbase->number = (float)$row['perceived_taxbase'];
  $out->totals->perceived->taxbase->formatted = number_format($row['perceived_taxbase'],2, ",", ".");
  $out->totals->perceived->taxpct->number = 0;
  $out->totals->perceived->taxpct->formatted = "0,00";
  $out->totals->perceived->taxtotal->number = (float)$row['perceived_taxbase'];
  $out->totals->perceived->taxtotal->formatted = number_format($row['perceived_taxbase'],2, ",", ".");

  $rs = getTotalsAlicuotas($from,$to,$customerid,$db);
  $row = $rs->fetch_assoc();

  $out->totals->generaliva = new stdClass();
  $out->totals->generaliva->taxbase = new stdClass();
  $out->totals->generaliva->taxbase->number =(float)$row['totgeneral'];
  $out->totals->generaliva->taxbase->formatted = number_format($row['totgeneral'],2, ",", ".");
  $out->totals->generaliva->taxpct->number = 16;
  $out->totals->generaliva->taxpct->formatted = number_format(16,2, ",", ".");
  $out->totals->generaliva->taxtotal->number = (float)$row['totgeneral']*(1+16/100);
  $out->totals->generaliva->taxtotal->formatted = number_format($row['totgeneral']*(1+16/100),2, ",", ".");

  $out->totals->reducediva = new stdClass();
  $out->totals->reducediva->taxbase = new stdClass();
  $out->totals->reducediva->taxbase->number = (float)$row['totreducido'];
  $out->totals->reducediva->taxbase->formatted = number_format($row['totreducido'],2, ",", ".");
  $out->totals->reducediva->taxpct->number = 8;
  $out->totals->reducediva->taxpct->formatted = number_format(8,2, ",", ".");
  $out->totals->reducediva->taxtotal->number = (float)$row['totreducido']*(1+8/100);
  $out->totals->reducediva->taxtotal->formatted = number_format($row['totreducido'],2, ",", ".");

  $out->totals->addediva = new stdClass();
  $out->totals->addediva->taxbase = new stdClass();
  $out->totals->addediva->taxbase->number = (float)$row['totadicional'];
  $out->totals->addediva->taxbase->formatted = number_format($row['totadicional'],2, ",", ".");
  $out->totals->addediva->taxpct->number = 27;
  $out->totals->addediva->taxpct->formatted = number_format(27,2, ",", ".");
  $out->totals->addediva->taxtotal->number = (float)$row['totadicional']*(1+27/100);
  $out->totals->addediva->taxtotal->formatted = number_format($row['totadicional']*(1+27/100),2, ",", ".");

$out->resume = new stdClass();
//notax
  $out->resume->notax = new stdClass();
  
  $out->resume->notax->debits = new stdClass();
  $out->resume->notax->debits->taxbase = new stdClass();

  $out->resume->notax->debits->taxbase->number = $resumenTotalNotax;
  $out->resume->notax->debits->taxbase->formatted = number_format($resumenTotalNotax,2,",",".");
  $out->resume->notax->debits->taxtotal = new stdClass();
  $out->resume->notax->debits->taxtotal->number = 0;
  $out->resume->notax->debits->taxtotal->formatted = number_format(0,2,",",".");

  $out->resume->notax->credits = new stdClass();
  $out->resume->notax->credits->taxbase = new stdClass();
  $out->resume->notax->credits->taxbase->number = $resumenTotalNotaxCredit;
  $out->resume->notax->credits->taxbase->formatted = number_format($resumenTotalNotaxCredit ,2,",",".");
  $out->resume->notax->credits->taxtotal = new stdClass();
  $out->resume->notax->credits->taxtotal->number = 0;
  $out->resume->notax->credits->taxtotal->formatted = number_format(0,2,",",".");

  $out->resume->notax->totals = new stdClass();
  $out->resume->notax->totals->taxbase = new stdClass();
  $out->resume->notax->totals->taxbase->number =  $resumenTotalNotax + $resumenTotalNotaxCredit;
  $out->resume->notax->totals->taxbase->formatted = number_format($out->resume->notax->totals->taxbase->number,2,",",".");
  $out->resume->notax->totals->taxtotal = new stdClass();
  $out->resume->notax->totals->taxtotal->number = 0;
  $out->resume->notax->totals->taxtotal->formatted = number_format(0,2,",",".");

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
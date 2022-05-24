<?php
// /api/invoices/uploadupdate.php
header("Content-Type:application/json");
include_once("../../../settings/dbconn.php");
include_once("../../../settings/utils.php");

// parametros obligatorios
$parmsob = array("sessionid");
if (!parametrosValidos($_REQUEST, $parmsob))
    badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
// Validar user session
$customerid = isSessionValid($db,$_REQUEST["sessionid"]); 

// Obtener la serie del lote
$sql = "SELECT serie FROM loadinvoiceheader LIMIT 1";
if (!$rs = $db->query($sql))
  badEnd("500", array("sql"=>$sql,"msg"=>$db->error));     
if ($row = $rs->fetch_assoc())
  $serie = $row['serie'];
// Asignar ctrnumber inicial según la serie
$ctrnumber = getNextControl($serie,$customerid,$db)-1;

// Copiar registros a la tabla definitiva
$sql = "INSERT INTO invoiceheader ( " . 
  " ctrnumber, ".
  " customerid, type, issuedate, duedate, refnumber, clientrif, clientname, clientaddress, mobilephone, ".
  " otherphone,  clientemail, obs, currency, currencyrate, ctrref, discount )".
  " SELECT ".
  "   CONCAT('$serie',LPAD((@rn := @rn +1),10,'0')) AS ctrnumber, ".
  "   customerid,".
  "   CASE ".
  "       WHEN type = 'F' THEN 'FAC' ".
  "       WHEN type = 'D' THEN 'NDB' ".
  "       WHEN type = 'C' THEN 'NCR' ".
  "   END, ".
  "   issuedate,duedate,refnumber,clientrif,clientname,".
  "   clientaddress,mobilephone,otherphone,clientemail,obs,currency,currencyrate,ctrref,discount ".
  " FROM ".
  "  ( SELECT @rn := $ctrnumber, tmp.* ".
  "    FROM ".
  "      loadinvoiceheader tmp ".
  "    WHERE ".
  "      tmp.customerid = $customerid) H ";
 
if (!$db->query($sql))
  badEnd("500", array("sql"=>$sql,"msg"=>$db->error)); 
  //print_r ($db->affected_rows);
if ($db->affected_rows == 0)  
  badEnd("400", array("msg"=>"0 facturas insertadas")); 
 // Copiar registros de detalle
$sql = "INSERT INTO invoicedetails (invoiceid, itemref, itemdsc, qty, unitprice, itemtax, itemdiscount) " .
" SELECT         FH.id, D.itemref, D.itemdsc, D.qty, D.unitprice, D.itemtax, D.itemdiscount ".
" FROM         loadinvoicedetail D ".
" INNER JOIN    loadinvoiceheader H ".
" ON            D.loadinvoiceheaderid = H.id ".
" INNER JOIN     invoiceheader FH ".
" ON            H.type = FH.type ".
" AND            H.refnumber = FH.refnumber ".
" WHERE        H.customerid=$customerid ".
" AND            FH.customerid=$customerid ";

badEnd("400", array("msg"=>$sql));
<?php
// app/api/invoices/uploadupdate.php

// Cargar librerías
  header("Content-Type:application/json");
  include_once("../../../settings/dbconn.php");
  include_once("../../../settings/utils.php");

function validateInvoicesBeforeLoad($db,$customerid){
  // Validar documentos duplicados con mismo Tipo y misma Referencia
  $sql = "SELECT COUNT(*) AS Cnt
          FROM invoiceheader h
          INNER JOIN(
              SELECT refnumber, TYPE
              FROM loadinvoiceheader
              WHERE customerid = $customerid) l
          ON
            l.refnumber = h.refnumber 
            AND(CASE WHEN l.type = 'F' THEN 'FAC' WHEN l.type = 'D' THEN 'NDB' 
                  WHEN l.type = 'C' THEN 'NCR' ELSE '0' END) = h.type
          WHERE h.customerid = $customerid";
  if (!$rs=$db->query($sql))
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  $row = $rs->fetch_assoc();
  $count=$row['Cnt'];
  if ($count>0)
    badEnd("400", array("msg"=>"existen $count documentos con códigos duplicados "));
}  
// Parametros obligatorios
  $parmsob = array("sessionid");
  if (!parametrosValidos($_REQUEST, $parmsob))
      badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
// Validar usersession
  $customerid = isSessionValid($db, $_REQUEST["sessionid"]); 

// Obtener la serie del lote
  $sql = "SELECT serie FROM loadinvoiceheader  WHERE customerid=$customerid LIMIT 1 ";
  if (!$rs = $db->query($sql))
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));     
  if ($row = $rs->fetch_assoc())
    $serie = $row['serie'];
// Asignar ctrnumber inicial de la corrida según la serie
  $ctrnumber = getNextControl($serie,$customerid,$db);
// Validar que las facturas (refnumber) que se van a cargar no existan
  validateInvoicesBeforeLoad($db,$customerid);  
  // Copiar registros header a la tabla definitiva
  $sql = "INSERT INTO invoiceheader ( " . 
    " ctrnumber, ".
    " customerid, type, issuedate, duedate, refnumber, clientrif, clientname, clientaddress, mobilephone, ".
    " otherphone,  clientemail, obs, currency, currencyrate, ctrref, discount, printformat )".
    " SELECT ".
    "   CONCAT('$serie',LPAD((@rn := @rn +1),10,'0')) AS ctrnumber, ".
    "   customerid,".
    "   CASE ".
    "       WHEN type = 'F' THEN 'FAC' ".
    "       WHEN type = 'D' THEN 'NDB' ".
    "       WHEN type = 'C' THEN 'NCR' ".
    "   END, ".
    "   issuedate,duedate,refnumber,clientrif,clientname,".
    "   clientaddress,mobilephone,otherphone,clientemail,obs,currency,currencyrate,ctrref,discount,printformat ".
    " FROM ".
    "  ( SELECT @rn := $ctrnumber, tmp.* ".
    "    FROM ".
    "      loadinvoiceheader tmp ".
    "    WHERE ".
    "      tmp.customerid = $customerid) H ";

  if (!$db->query($sql))
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error)); 
  if ($db->affected_rows == 0)  
    badEnd("400", array("msg"=>"0 facturas insertadas"));
  $qtyinvoices =  $db->affected_rows;

// Copiar registros de detalle
  $sql = "INSERT INTO invoicedetails (invoiceid, itemref, itemdsc, qty, unit,unitprice, itemtax, itemdiscount) " .
    " SELECT         FH.id, D.itemref, D.itemdsc, D.qty, D.unit,D.unitprice, D.itemtax, D.itemdiscount ".
    " FROM         loadinvoicedetail D ".
    " INNER JOIN    loadinvoiceheader H ".
    " ON            D.loadinvoiceheaderid = H.id ".
    " INNER JOIN     invoiceheader FH ".
    " ON        H.refnumber = FH.refnumber    ".
    " WHERE        H.customerid=$customerid ".
    " AND            FH.customerid=$customerid ".
    " AND  POSITION(H.type IN FH.type) > 0 "; 

  if (!$db->query($sql))
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error)); 

// Obtener y actualizar next control
  $series = getSeries($customerid,$db);
  $index=array_search($serie, $series);
  if (is_null($index))
      badEnd("400", array("msg"=>"Serie no existe para este cliente"));
  // Ubicar el next control
    $nexts=getNextControls($customerid,$db);
    $next = $nexts[$index];
  // Construir nextcontrol en string
    $nexts[$index] = $next + $qtyinvoices;
    $str_nextcontrol = implode("-",$nexts);
  // Se actualiza el nextcontrol del cliente
    $update = "UPDATE customers SET nextcontrol = '$str_nextcontrol' WHERE id=$customerid ";
    if (!$db->query($update)) 
      badEnd("500", array("sql"=>$sql,"msg"=>$db->error)); 

  // Auditoria
    insertAudit($db,getEmail($_REQUEST["sessionid"],APP_APP,$db),$_SERVER['REMOTE_ADDR'],APP_APP,MODULE_INVOICES,"Se hizo una carga masiva de $qtyinvoices documentos");
  // Salida
  $out = new stdClass(); 
  $out->recordsupdates = $qtyinvoices;
  header("HTTP/1.1 200");
  echo (json_encode($out));
  die();  

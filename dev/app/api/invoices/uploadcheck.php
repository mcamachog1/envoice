<?php
// api/invoices/uploadcheck.php

// Cargar librerias
    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");
    include_once("../../../settings/uploadfunctions.php");    
    
// Parámetros obligatorios
  $parmsob = array("sessionid");
  if (!parametrosValidos($_REQUEST, $parmsob))
      badEnd("400", array("msg"=>"Parámetros obligatorios " . implode(", ", $parmsob)));
  // Validar user session
  $customerid = isSessionValid($db, $_REQUEST["sessionid"]);

// Validar archivo cargado
  if (!isset($_FILES["invoicesfile"]))
      badEnd("400", array("msg"=>"No se adjuntó el archivo "));
  if ($_FILES["invoicesfile"]["error"]<>0) 
      badEnd("400", array("msg"=>"Error en la carga del archivo"));
  if($_FILES["invoicesfile"]["size"] <= 0)    
      badEnd("400", array("msg"=>"Error: archivo vacío"));

// (A) OPEN FILE
  $handle = fopen($_FILES["invoicesfile"]["tmp_name"], "r") or die("Error reading file!");
  // Borrar registros temporales anteriores
  deleteHeaders($customerid,$db);
// (B) READ LINE BY LINE
  $counter=0;  

  while (($getLine = fgetcsv($handle , 10000, ",")) !== FALSE){
    $counter++;
    // Quitar espacios y tabs al final de la linea
    $size = count($getLine);
    for ($x=0;$x<$size;$x++) {
      $getLine[$x] = rtrim($getLine[$x]);
    }    
    // Es Total
    if ($counter==1) {
        $line = validateFields($getLine,'T');
        $indexerr=count($line)-2;        
        $indexmsg=count($line)-1;
        if ($line[$indexerr]!=0)
            badEnd("400", array("msg"=>$line[$indexmsg]));        
        $totalfacturas=$getLine[1];
        $totalmonto=$getLine[2];      
        $serie = $getLine[3];
    }   
    elseif ($counter==2) {
        if (substr($getLine[0],-1)!='E')
            badEnd("400", array("msg"=>"El tipo de registro debe ser E (Encabezado) en la línea 2"));      
        $line= validateFields($getLine,substr($getLine[0],-1));  
        $indexerr=count($line)-2;        
        $indexmsg=count($line)-1; 
        $headerid=insertHeader($line,$customerid,$serie,$db);
    }
    elseif (substr($getLine[0],-1) != 'T') {
        $line= validateFields($getLine,substr($getLine[0],-1));
        $indexerr=count($line)-2;        
        $indexmsg=count($line)-1;
      
        if ($line[0] == 'E') {
            $headerid=insertHeader($line,$customerid,$serie,$db);
        }
        elseif ($line[0] == 'D') {
            $sql = "INSERT INTO loadinvoicedetail ".
                " (id, loadinvoiceheaderid, itemref, itemdsc, qty, unit, unitprice, itemtax, itemdiscount )".
                " VALUES ( 0, ". 
                $headerid.",'".$line[1]."','".$line[2]."',".$line[3].",'".$line[4]."',".$line[5].",".$line[6].",".$line[7].
                ") ";
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
            // Si hay errores en el detail y no hay errores en el header se inserta en el header
                $sql= "SELECT err FROM loadinvoiceheader WHERE id = $headerid AND customerid=$customerid ";      
                if (!$rs=$db->query($sql))
                    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
                $row = $rs->fetch_assoc();
                if ($row["err"]==0){
                    $sql = "UPDATE loadinvoiceheader set err =".$line[$indexerr].", errmsg='".$line[$indexmsg]."' WHERE id = $headerid AND customerid=$customerid ";
                    if (!$db->query($sql))
                        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
                }         

        }
    }
    else {
        badEnd("400", array("msg"=>"No puede haber dos lines de totales (T)"));
    }
  }
// (C) CLOSE FILE
  fclose($handle);

// Contar errores
    $sql =  "SELECT     SUM(IF(err=0,1,0)) ERR0, SUM(IF(err=1,1,0)) ERR1, SUM(IF(err=2,1,0)) ERR2, SUM(IF(err=3,1,0)) ERR3, " .
            "           SUM(IF(err=4,1,0)) ERR4, SUM(IF(err=5,1,0)) ERR5, SUM(IF(err=6,1,0)) ERR6, SUM(IF(err=7,1,0)) ERR7 " .
            "FROM       loadinvoiceheader " .
            "WHERE      customerid=" . $customerid;
    if (!$rs=$db->query($sql))
      badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    $row = $rs->fetch_assoc();
    $errors = array(array("err0"=>(integer)$row["ERR0"], "errmsg"=>"Sin error"),
            array("err1"=>(integer)$row["ERR1"], "errmsg"=>"Cantidad de campos requeridos incorrecta"),
            array("err2"=>(integer)$row["ERR2"], "errmsg"=>"Formato de campo incorrecto"),
            array("err3"=>(integer)$row["ERR3"], "errmsg"=>"Inconsistencia de fechas"),
            array("err4"=>(integer)$row["ERR4"], "errmsg"=>"Campos obligatorios vacíos"));

// Validar totales y cantidad de facturas
    $sql =  "SELECT     SUM(total) as total_monto, COUNT(*) as total_facturas, SUM(err) as total_errores " .
            "FROM       loadinvoiceheader " .
            "WHERE      customerid=" . $customerid;
   
    if (!$rs=$db->query($sql))
      badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    $row = $rs->fetch_assoc();
    $totalerrors=$row["total_errores"];
    if (($totalfacturas != $row["total_facturas"]  || $totalmonto !=  round($row["total_monto"],2)) && $totalerrors==0)
        badEnd("400", array("msg"=>"Cantidad de facturas incorrectas o monto total incorrecto."));
// Validar serie
    $customerseries = getSeries($customerid,$db);
    if (strlen($serie)==0)
        $serie=' ';
    if (!in_array($serie, $customerseries) && $totalerrors==0)
        badEnd("400", array("msg"=>"Serie seleccionada $serie no es válida para el cliente."));
// Detectar encabezados sin detalles
    $withoutDetails=withoutDetails($customerid,$db);
    if (count($withoutDetails)>0 && $totalerrors==0)
        badEnd("400", array("msg"=>"Factura[s] sin detalle[s] ".implode(",",$withoutDetails).""));
// Detectar totales diferentes entre el encabezado y todos los detalles
    $diffHeaderDetailTotals=diffHeaderDetailTotals($customerid,$db);
    if (count($diffHeaderDetailTotals)>0 && $totalerrors==0)
        badEnd("400", array("msg"=>"Total encabezado de factura[s] difiere del total detalles. Facturas: ".implode(",",$diffHeaderDetailTotals).""));
// Detectar facturas duplicadas
    $duplicatedInvoices=duplicatedInvoices($customerid,$db);
    if (count($duplicatedInvoices)>0 && $totalerrors==0)
        badEnd("400", array("msg"=>"Hay documentos en el archivo con numeración duplicada. Documentos: ".implode(",",$duplicatedInvoices).""));

// Auditoria
  $countdocs = 0;
  for ($x=0;$x<count($errors);$x++)
    $countdocs += $errors[$x]['err'.$x];
  
  insertAudit($db,getEmail($_REQUEST["sessionid"],APP_APP,$db),$_SERVER['REMOTE_ADDR'],APP_APP,MODULE_INVOICES,"Se validó una carga masiva de $countdocs documentos");


// Salida
  $out = new stdClass(); 
  $out->errors = $errors;
  header("HTTP/1.1 200");
  echo (json_encode($out));
  die();
?>

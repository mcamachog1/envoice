<?php
// api/invoices/uploadcheck.php

// Cargar librerias
    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");
// Declarar funciones locales
    function withoutDetails($customerid,$db){
        $sql="SELECT refnumber FROM `loadinvoiceheader` h ".
            " WHERE h.id NOT IN( SELECT loadinvoiceheaderid FROM loadinvoicedetail )".
            " AND customerid = $customerid";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        
        $invoices=array();
        while ($row = $rs->fetch_assoc()) 
            $invoices[]=$row["refnumber"];
        return $invoices;
    }
    function duplicatedInvoices($customerid,$db){
        $sql = "SELECT
                H.refnumber,
                COUNT(*) qty
            FROM
                loadinvoiceheader H
            WHERE
                customerid = $customerid
            GROUP BY
                H.refnumber
            HAVING
                COUNT(*) > 1";

        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));

        $invoices=array();
        while ($row = $rs->fetch_assoc()) 
            $invoices[]=$row["refnumber"];
        return $invoices;
    }

    function diffHeaderDetailTotals($customerid,$db){
        $sql=   "SELECT
                H.refnumber,
                H.total,
                ROUND(
                    SUM(
                        D.qty * D.unitprice *(1 - D.itemdiscount / 100)
                    ) *(1 - H.discount / 100) + SUM(
                        D.qty * D.unitprice *(1 - D.itemdiscount / 100) *(D.itemtax / 100)
                    ),
                    2
                ) AS detailsTotal
            FROM
                loadinvoicedetail D
            INNER JOIN loadinvoiceheader H ON
                H.id = D.loadinvoiceheaderid
            WHERE 
                H.customerid=$customerid
            GROUP BY
                H.refnumber,
                H.total ";      

        if (!$rs=$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));

        $invoices=array();
        while ($row = $rs->fetch_assoc()) 
            if ($row["total"] != $row["detailsTotal"])
                $invoices[]=$row["refnumber"];
        return $invoices;
    }
    function validateFields($line,$type){
        $err = 0;
        $errmsg = "";

        if ($type =='D') {
            // Validar numero de campos     
            $maxfields=7;
            $qtyfields = count($line);
            if ($qtyfields<$maxfields) {
                for ($i=$qtyfields;$i<$maxfields;$i++)
                    $line[]="0";
                $err = 1;
                $errmsg = "Cantidad de campos esperados incorrecto en el Detalle de la factura"; 
            }
            if ($qtyfields>$maxfields){
                for ($i=$qtyfields;$i>$maxfields;$i--)
                    array_pop($line);
                $err = 1;
                $errmsg = "Cantidad de campos esperados incorrecto en el Detalle de la factura";             
            }
            // Recorrer campos, validar tipo de dato
            $fieldmsgs = array(0=>"Formato de campo incorrecto.",1=>"",2=>"",
            3=>"Formato de campo cantidad incorrecto en línea de Detalle",
            4=>"Formato de campo precio unitario incorrecto en línea de Detalle",
            5=>"Formato de campo impuesto incorrecto en línea de Detalle",
            6=>"Formato de campo descuento incorrecto en línea de Detalle");
            if ($err==0){
                for ($i=0;$i<$maxfields;$i++){
                    switch ($i) {
                        case 0:
                            if (strlen($line[$i])!=0 && substr($line[$i],-1) != 'D') {
                                $err = 2;
                                $errmsg = $fieldmsgs[$i];
                            }
                            elseif (strlen($line[$i])==0)
                                $line[$i]= "";
                        break;
                        case 3:
                        case 4:
                        case 5:
                        case 6:
                            if (strlen($line[$i])!=0 && !is_numeric($line[$i])) {
                                $err = 2;
                                $errmsg =$fieldmsgs[$i];
                                $line[$i]= 0;
                            }
                            elseif (strlen($line[$i])==0)
                                $line[$i]= 0;
                        break;                        
                    }
                }
            }
        }
        elseif ($type == 'E'){
            // Validar numero de campos 
            $maxfields=18;

            $qtyfields = count($line);
            if ($qtyfields<$maxfields) {
                for ($i=$qtyfields;$i<$maxfields;$i++)
                    $line[]="";
                $err = 1;
                $errmsg = "Cantidad de campos esperados incorrecto en el Encabezado de la factura"; 
            }
            if ($qtyfields>$maxfields){
                for ($i=$qtyfields;$i>$maxfields;$i--)
                    array_pop($line);
                $err = 1;
                $errmsg = "Cantidad de campos esperados incorrecto en el Encabezado de la factura";
            }
            // Recorrer campos, validar tipo de dato
            if ($err==0){
                $fieldmsgs = array(
                    0=>"Campo tipo de registro incorrecto en línea de Encabezado",
                    1=>"Campo tipo de documento incorrecto en línea de Encabezado",
                    2=>"Campo fecha emision incorrecto en línea de Encabezado",
                    3=>"Campo fecha vencimiento incorrecto en línea de Encabezado",
                    4=>"Campo numero de factura incorrecto en línea de Encabezado",
                    5=>"Campo rif incorrecto en línea de Encabezado",
                    6=>"Campo nombre del cliente obligatorio en línea de Encabezado",
                    7=>"",
                    8=>"Campo celular incorrecto en línea de Encabezado",
                    9=>"",
                    10=>"Campo email incorrecto en línea de Encabezado",
                    11=>"",                
                    12=>"",                
                    13=>"Campo tasa de cambio incorrecto en línea de Encabezado",
                    14=>"Campo factura de referencia incorrecto en línea de Encabezado",
                    15=>"Campo %descuento incorrecto en línea de Encabezado",
                    16=>"Campo total impuesto incorrecto en línea de Encabezado",
                    17=>"Campo total general incorrecto en línea de Encabezado"
                );         
                for ($i=0;$i<$maxfields;$i++){
                    switch ($i) {
                        case 0:
                            if (strlen($line[$i])!=0 && substr($line[$i],-1) != 'E') {
                            $err = 2;
                                $errmsg = $fieldmsgs[$i];
                            }
                            elseif (strlen($line[$i])==0){
                                $err = 4;
                                $errmsg = $fieldmsgs[$i] ;
                            }
                        break;
                        case 1:
                            if (strlen($line[$i])!=0 && $line[$i]!='F'&& $line[$i]!='C' && $line[$i]!='D'  ) {
                                $err = 2;
                                $errmsg = $fieldmsgs[$i] ;
                            }
                            elseif (strlen($line[$i])==0){
                                $err = 4;
                                $errmsg = $fieldmsgs[$i] ;
                                $line[$i]= "";
                            }
                        break;
                        case 2:
                        case 3:
                            if (strlen($line[$i])!=0 && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$line[$i])) {
                                $err = 2;
                                $errmsg = $fieldmsgs[$i];
                            }
                            elseif (strlen($line[$i])==0) {
                                $err = 4;
                                $errmsg = $fieldmsgs[$i] ;                            
                                $line[$i]= "";
                            }
                        break;
                        case 5:
                            if (strlen($line[$i])==0){
                                $err = 4;
                                $errmsg = $fieldmsgs[$i] ;
                                $line[$i] = "";
                            }
                            elseif (!(preg_match("/[J,G,V,E]{1}[0-9]{9}/",$line[$i])) ){
                                $err = 2;
                                $errmsg = $fieldmsgs[$i] ;
                            }
                            elseif (strlen($line[$i]) != 10) {
                                $err = 2;
                                $errmsg = $fieldmsgs[$i] ;
                            }
                        break; 
                        case 6:
                            if (strlen($line[$i])==0) {
                                $err = 4;
                                $errmsg = $fieldmsgs[$i] ;                            
                                $line[$i]= "";
                            }
                        break;
                        case 8:
                            if (strlen($line[$i])==0) {
                                $err = 4;
                                $errmsg = $fieldmsgs[$i] ;                            
                                $line[$i]= "";
                            }
                            elseif (!preg_match("/^04[1,2,4,6]{2} [0-9]{7}/",$line[$i])) {
                                $err = 2;
                                $errmsg = $fieldmsgs[$i] ;                            
                            }
                        break;                    
                        case 10:
                            if (strlen($line[$i])!=0 && !filter_var($line[$i], FILTER_VALIDATE_EMAIL)) {
                                $err = 2;
                                $errmsg =$fieldmsgs[$i] ;     
                            }
                            elseif (strlen($line[$i])==0){
                                $err = 4;
                                $errmsg =$fieldmsgs[$i] ;                                 
                                $line[$i]= "";
                            }
                        break;  
                        //case 12:
                            //if (strlen($line[$i])!=0 && $line[$i] != 'VES' && $line[$i]!='USD'){
                            //    $err = 2;
                            //    $errmsg = $fieldmsgs[$i]; 
                            //    $line[$i]= "";                            
                            //}
                        //break;
                        case 13:
                        case 14:                        
                            if (strlen($line[$i])!=0 && !is_numeric($line[$i])) {
                                $err = 2;
                                $errmsg = $fieldmsgs[$i]; 
                                $line[$i]= 0;
                            }
                        break;                        
                        case 15:
                            if (strlen($line[$i])==0) {
                                $line[$i]= 0;                            
                            }
                            elseif (!is_numeric($line[$i])) {
                                $err = 2;
                                $errmsg = $fieldmsgs[$i];
                                $line[$i]= 0;
                            }
                        break;                        
                        case 4:
                        case 16:
                        case 17:
                            if (strlen($line[$i])==0) {
                                $err = 4;
                                $errmsg = $fieldmsgs[$i];
                                $line[$i]= 0;                            
                            }
                            elseif (!is_numeric($line[$i])) {
                                $err = 2;
                                $errmsg = $fieldmsgs[$i];
                                $line[$i]= 0;
                            }
                        break;                        
                    }       
                    
                    if ($err==0 && strlen($line[2])!=0 && strlen($line[3])!=0 && strtotime($line[2]) > strtotime($line[3])){
                        $err = 3;
                        $errmsg = "Fecha de emisión mayor que fecha de vencimiento en factura ".$line[4]."";                    
                    }            
                }
            }
        }
        elseif ($type=='T') {

            if (substr($line[0],-1) != 'T') {
                echo $line;
                badEnd("400", array("data"=>$line,"msg"=>"Se espera línea de TOTALES en la primera fila.297 "));
            }
       
            // Validar numero de campos 
            $qtyfields = count($line);
            $maxfields=4;            
            if ($qtyfields<$maxfields) {
                for ($i=$qtyfields;$i<$maxfields;$i++)
                    $line[$i]="";
                $err = 1;
                $errmsg = "Cantidad de campos esperados incorrecto en línea: T"; 
            }
            if ($qtyfields>$maxfields){
                for ($i=$qtyfields;$i>$maxfields;$i--)
                    array_pop($line);
                $err = 1;
                $errmsg = "Cantidad de campos esperados incorrecto en línea: T";                
            }
            // Recorrer campos, validar tipo de dato
            if ($err==0){

                $fieldmsgs = array(
                    0=>"Campo tipo de registro incorrecto en línea de Totales",
                    1=>"Campo cantidad de facturas incorrecto en línea de Totales",
                    2=>"Campo monto total incorrecto en línea de Totales",                    
                    3=>"Campo serie incorrecto en línea de Totales");                                        

                for ($i=0;$i<$maxfields;$i++){
                    switch ($i) {
                        case 0:
                            if (strlen($line[$i])!=0 && substr($line[$i],-1) != 'T') {
                                $err = 2;
                                $errmsg = $fieldmsgs[$i];
                            }
                            elseif (strlen($line[$i])==0)
                                 $line[$i]= "";
                        break;                                                   
                        case 1:
                            if (strlen($line[$i])!=0 && !is_numeric($line[$i])) {
                                $err = 2;
                                $errmsg = $fieldmsgs[$i];
                                $line[$i]= 0;
                            }
                            elseif (strlen($line[$i])==0)
                                $line[$i]= 0;
                        break;
                        case 2:
                            if (strlen($line[$i])!=0 && !is_numeric($line[$i])) {
                                $err = 2;
                                $errmsg = $fieldmsgs[$i];
                                $line[$i]= 0.0;
                            }
                            elseif (strlen($line[$i])==0)
                                $line[$i]= 0;
                        break;
                        case 3:
                            if ((strlen($line[$i])!=0 && !is_string($line[$i])) || strlen($line[$i])>2) {
                                $err = 2;
                                $errmsg = $fieldmsgs[$i];
                            }
                            elseif (strlen($line[$i])==0)
                                $line[$i]= "";
                        break;                    
                    
                    }
                }
            }
        }
        // Se agrega el error y el mensaje al final de la línea
        $line[] = $err;
        $line[] = $errmsg;
        return($line);  
    }
    function insertHeader($line,$customerid,$serie,$db){
            
        $sql = "INSERT INTO loadinvoiceheader ".
        " (id, customerid, type, serie, issuedate, duedate, refnumber, clientrif, clientname, clientaddress, mobilephone, ".
        " otherphone,  clientemail, obs, currency, currencyrate, ctrref, discount, totaltax, total, err, errmsg ) ". 
        " VALUES ( 0, ". 
        $customerid.",'".$line[1]."','".$serie."','".$line[2]."','".$line[3]."','".$line[4]."','".$line[5]."','".$line[6]."','".$line[7]."','".$line[8]."','".
        $line[9]."','".$line[10]."','".$line[11]."','".$line[12]."','".$line[13]."','".$line[14]."','".$line[15]."','".$line[16]."','".$line[17]."','".
        $line[18]."','".$line[19]."'".
        ") ";
        
        if (!$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error)); 
        
        return $db->insert_id; 
    }

// Parámetros obligatorios
  $parmsob = array("sessionid");
  if (!parametrosValidos($_REQUEST, $parmsob))
      badEnd("400", array("msg"=>"Parámetros obligatorios " . implode(", ", $parmsob)));
// Validar user session
  $customerid = isSessionValid($db, $_REQUEST["sessionid"],array('ip'=>$_SERVER['REMOTE_ADDR'],'app'=>'APP','module'=>'invoices','dsc'=>'Validación carga masiva de facturas'));

// Borrar datos en caso que existan
  $sql = "DELETE FROM loadinvoiceheader ".
  " WHERE  customerid=$customerid ";

  if (!$db->query($sql))
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error)); 

// Validar archivo cargado
  if (!isset($_FILES["invoicesfile"]))
      badEnd("400", array("msg"=>"No se adjuntó el archivo "));
  if ($_FILES["invoicesfile"]["error"]<>0) 
      badEnd("400", array("msg"=>"Error en la carga del archivo"));
  if($_FILES["invoicesfile"]["size"] <= 0)    
      badEnd("400", array("msg"=>"Error: archivo vacío"));

// (A) OPEN FILE
  $handle = fopen($_FILES["invoicesfile"]["tmp_name"], "r") or die("Error reading file!");
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
                " (id, loadinvoiceheaderid, itemref, itemdsc, qty, unitprice, itemtax, itemdiscount )".
                " VALUES ( 0, ". 
                $headerid.",'".$line[1]."','".$line[2]."',".$line[3].",".$line[4].",".$line[5].",".$line[6].
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
        badEnd("400", array("msg"=>"Hay documentos con numeración duplicada. Documentos: ".implode(",",$duplicatedInvoices).""));
// Salida
  $out = new stdClass(); 
  $out->errors = $errors;
  header("HTTP/1.1 200");
  echo (json_encode($out));
  die();
?>

<?php
// Funciones compartidas para el app/api/uploadcheck.php y el ftp/ftp.php
  function withoutDetails($customerid,$db){
    $sql="SELECT refnumber FROM `loadinvoiceheader` h ".
        " WHERE h.id NOT IN( SELECT loadinvoiceheaderid FROM loadinvoicedetail )".
        " AND customerid = $customerid";
    if (!$rs=$db->query($sql))
        throw new Exception("500-".$db->error);  
    
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
        throw new Exception("500-".$db->error);  
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
        throw new Exception("500-".$db->error);  
  
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
        $maxfields=8;
  
        $qtyfields = count($line);
        if ($qtyfields<$maxfields) {
            for ($i=$qtyfields;$i<$maxfields;$i++)
                $line[]="0";
            $err = 1;
            $errmsg = "Cantidad de campos esperados incorrecto en el Detalle de la factura (faltan)"; 
        }
        if ($qtyfields>$maxfields){
            for ($i=$qtyfields;$i>$maxfields;$i--)
                array_pop($line);
            $err = 1;
            $errmsg = "Cantidad de campos esperados incorrecto en el Detalle de la factura (sobran)";             
        }
        // Recorrer campos, validar tipo de dato
        $fieldmsgs = array(0=>"Formato de campo incorrecto.",
        1=>"",
        2=>"",
        3=>"Formato de campo cantidad incorrecto en línea de Detalle",
        4=>"", //unidad           
        5=>"Formato de campo precio unitario incorrecto en línea de Detalle",
        6=>"Formato de campo impuesto incorrecto en línea de Detalle",
        7=>"Formato de campo descuento incorrecto en línea de Detalle");
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
                    case 5:
                    case 6:
                    case 7:
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
            for ($i=$qtyfields;$i<$maxfields;$i++) // Completa hasta el maxfields para poder guardar en la tabla de errores
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
                        if (strlen($line[$i])!=0 && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$line[$i])) 
                        {
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
                
                if ($err==0 && strlen($line[2])!=0 && strlen($line[3])!=0 && strtotime($line[2]) > strtotime($line[3]))
                {
                    $err = 3;
                    $errmsg = "Fecha de emisión mayor que fecha de vencimiento en factura ".$line[4]."";                    
                }            
            }
        }   
    }
    elseif ($type=='T') {
  
        if (substr($line[0],-1) != 'T') {
            throw new Exception("400-"."Se espera línea de TOTALES en la primera fila"); 
            
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
        throw new Exception("500-".$db->error); 
        
    
    return $db->insert_id; 
  }

  function deleteHeaders($customerid,$db){
    // Borrar datos previos en caso que existan
    $sql = "DELETE FROM loadinvoiceheader ".
    " WHERE  customerid=$customerid ";
    if (!$db->query($sql))
        throw new Exception("500-".$db->error);     
  }
  function insertDetail($line,$headerid,$customerid,$db){
    $indexerr=count($line)-2;        
    $indexmsg=count($line)-1;    
    $sql = "INSERT INTO loadinvoicedetail ".
    " (id, loadinvoiceheaderid, itemref, itemdsc, qty, unit, unitprice, itemtax, itemdiscount )".
    " VALUES ( 0, ". 
    $headerid.",'".$line[1]."','".$line[2]."',".$line[3].",'".$line[4]."',".$line[5].",".$line[6].",".$line[7].
    ") ";
    if (!$db->query($sql))
        throw new Exception("500-".$db->error);     
        
    // Si hay errores en el detail y no hay errores en el header se inserta en el header el error del detail
    $sql= "SELECT err FROM loadinvoiceheader WHERE id = $headerid AND customerid=$customerid ";      
    if (!$rs=$db->query($sql))
        throw new Exception("500-".$db->error); 
    $row = $rs->fetch_assoc();
    if ($row["err"]==0){
        $sql = "UPDATE loadinvoiceheader set err =".$line[$indexerr].", errmsg='".$line[$indexmsg]."' WHERE id = $headerid AND customerid=$customerid ";
        if (!$db->query($sql))
            throw new Exception("500-".$db->error); 
    }  
  }
?>
<?php
include_once("../settings/dbconn.php");
include_once("../settings/utils.php");
include_once("../settings/uploadfunctions.php");
require '../hooks/PHPMailer5/PHPMailerAutoload.php';
date_default_timezone_set('America/Caracas');




function validateFileName($filename){

  return True;
}

function successfulLoad($email,$filefromdir,$homeurl,$db){

  $name = getCustomerNameByEmail($email,$db);
  $body = 
      "<html>" .
          "<head>" .
              "<title></title>" .
          "</head>" .
          "<body style='font-family:sans-serif'>" .
              "<div style='padding:20px;width:100%;background-color:gray'>" .
                  "<div style='padding:10px;background-color:white;max-width:600px;width:95%;margin-left:auto;margin-right:auto;border-radius:4px'>" .
                      "<div style='display:table;border-spacing:30px;width:95%;margin-left:auto;margin-right:auto'>" .
                          "<div style='display:table-row'>" .
                              "<div style='display:table-cell'><img style='max-width:30%' src='".$homeurl."img/logo.png' /></div>" .
                          "</div>" .
                          "<div style='display:table-row'>" .
                              "<div style='text-align:center;display:table-cell;font-size:200%;font-weight:bold'>" .
                                  "<br/>Carga autom&aacute;tica de archivos de $name<br/>&nbsp;" .
                              "</div>" .
                          "</div>" .
                          "<div style='display:table-row'>" .
                              "<div style='display:table-cell;font-size:120%;text-align:center'><br><p>Carga exitosa del archivo: <strong>".basename($filefromdir, ".txt")."</strong><p><br></div>" .
                          "</div>" .
                          "<div style='display:table-row'>" .
                              "<div style='display:table-cell'>Gracias de antemano<br />" .
                                  "Equipo de DaycoPrint</div>" .
                          "</div>" .
                      "</div>" .
                  "</div>" .
              "</div>" .
          "</body>" .
      "</html>";


  $altbody = "\n\nCarga exitosa del archivo: ".basename($filefromdir, ".txt")."\n\n".
        "Gracias de antemano\n" .
        "Equipo de DaycoPrint";
  $subject ="Carga exitosa archivo: ".basename($filefromdir, ".txt"); 
  enviarCorreo("no-responder@espacioseguroDayco.com", $email, $subject, $body, $altbody);  
  //enviarCorreo("no-responder@espacioseguroDayco.com", 'developer4@totalsoftware.com.ve', $subject, $body, $altbody);
  echo "Carga exitosa del archivo: ".$filefromdir." en fecha: ".date("d-m-Y H:i:s")."\n";
}

function errorLoad($email,$filefromdir,$homeurl,$msg,$db){

  $description = "Error en el archivo: ".basename($filefromdir, ".txt")."<br>".$msg."<br>";
  if (substr($msg,0,3)=="500")
    $description = "Error interno del servidor en carga de archivo<br>".basename($filefromdir, ".txt")."<br>".$msg;
  elseif (substr($msg,0,3)=="400")  
    $description = "Error de formato en el archivo: ".basename($filefromdir, ".txt")."<br>".$msg."<br>";
  $name = getCustomerNameByEmail($email,$db);
  $body = 
      "<html>" .
          "<head>" .
              "<title></title>" .
          "</head>" .
          "<body style='font-family:sans-serif'>" .
              "<div style='padding:20px;width:100%;background-color:gray'>" .
                  "<div style='padding:10px;background-color:white;max-width:600px;width:95%;margin-left:auto;margin-right:auto;border-radius:4px'>" .
                      "<div style='display:table;border-spacing:30px;width:95%;margin-left:auto;margin-right:auto'>" .
                          "<div style='display:table-row'>" .
                              "<div style='display:table-cell'><img style='max-width:30%' src='".$homeurl."img/logo.png' /></div>" .
                          "</div>" .
                          "<div style='display:table-row'>" .
                              "<div style='text-align:center;display:table-cell;font-size:200%;font-weight:bold'>" .
                                  "<br/>Carga autom&aacute;tica de archivos de $name<br/>&nbsp;" .
                              "</div>" .
                          "</div>" .
                          "<div style='display:table-row'>" .
                              "<div style='display:table-cell;font-size:120%;text-align:left'><br><p>$description</p><br></div>" .
                          "</div>" .
                          "<div style='display:table-row'>" .
                              "<div style='display:table-cell'>Gracias de antemano<br />" .
                                  "Equipo de DaycoPrint</div>" .
                          "</div>" .
                      "</div>" .
                  "</div>" .
              "</div>" .
          "</body>" .
      "</html>";


  $altbody = "\n\nError en la carga del archivo: ".basename($filefromdir, ".txt")."\n\n".
        "Gracias de antemano\n" .
        "Equipo de DaycoPrint";
  $subject ="Error en la carga del archivo: ".basename($filefromdir, ".txt"); 
  enviarCorreo("no-responder@espacioseguroDayco.com", $email, $subject, $body, $altbody);
  //enviarCorreo("no-responder@espacioseguroDayco.com", 'developer4@totalsoftware.com.ve', $subject, $body, $altbody);
  echo "Error en la carga del archivo: ".$filefromdir." en fecha: ".date("d-m-Y H:i:s")."\n";

}
function countErrors($errors){
    $count = 0;
    for ($x=1;$x<count($errors);$x++)
    $count += $errors[$x]['err'.$x];
    return $count;
}
function getErrors($customerid,$db){
  // Leer errores
  $sql =  "SELECT     SUM(IF(err=0,1,0)) ERR0, SUM(IF(err=1,1,0)) ERR1, SUM(IF(err=2,1,0)) ERR2, SUM(IF(err=3,1,0)) ERR3, " .
        "           SUM(IF(err=4,1,0)) ERR4, SUM(IF(err=5,1,0)) ERR5, SUM(IF(err=6,1,0)) ERR6, SUM(IF(err=7,1,0)) ERR7 " .
        "FROM       loadinvoiceheader " .
        "WHERE      customerid=" . $customerid;
  if (!$rs=$db->query($sql))
    throw new Exception("500-".$db->error);  
  $row = $rs->fetch_assoc();
  $errors = array(array("err0"=>(integer)$row["ERR0"], "errmsg"=>"Sin error"),
        array("err1"=>(integer)$row["ERR1"], "errmsg"=>"Cantidad de campos requeridos incorrecta"),
        array("err2"=>(integer)$row["ERR2"], "errmsg"=>"Formato de campo incorrecto"),
        array("err3"=>(integer)$row["ERR3"], "errmsg"=>"Inconsistencia de fechas"),
        array("err4"=>(integer)$row["ERR4"], "errmsg"=>"Campos obligatorios vacíos"));  
  return $errors;

}
function validarTotales($customerid,$totalfacturas,$totalmonto,$db){
      // Validar totales y cantidad de facturas
      $sql =  "SELECT     SUM(total) as total_monto, COUNT(*) as total_facturas, SUM(err) as total_errores " .
            "FROM       loadinvoiceheader " .
            "WHERE      customerid=" . $customerid;
          
      if (!$rs=$db->query($sql))
        throw new Exception("500-".$db->error);  
      $row = $rs->fetch_assoc();
      $totalerrors=$row["total_errores"];
      if (($totalfacturas != $row["total_facturas"]  || $totalmonto !=  round($row["total_monto"],2)) && $totalerrors==0)
        throw new Exception("Cantidad de facturas incorrectas o monto total incorrecto.");  
    return $totalerrors;                         
}
function validarSerie($serie,$customerid,$totalerrors,$db){

      // Validar serie
      $customerseries = getSeries($customerid,$db);
      if (strlen($serie)==0)
        $serie=' ';
      if (!in_array($serie, $customerseries) && $totalerrors==0)
        throw new Exception("Serie seleccionada $serie no es v&aacute;lida para el cliente.");  
}
function encabezadoSinDetalle($customerid,$totalerrors,$db){
        // Detectar encabezados sin detalles
        $withoutDetails=withoutDetails($customerid,$db);
        if (count($withoutDetails)>0 && $totalerrors==0)
          throw new Exception("Factura[s] sin detalle[s] ".implode(",",$withoutDetails)."");
}

// Almacenamos todos los clientes
$customers = getCustomers($db);

// Estructura para guardar clientes con archivos validos colocados
$customers_to_upload = array();
$files_to_upload = array();
// Obtenemos el directorio de cada cliente
foreach ($customers as $customer) {
  $customerid=$customer->id;
  
  $dirpath = "..".SEPARADOR."ftpfiles".SEPARADOR."$customerid";

  // En caso de que no exista, creamos el directorio 
  if (!is_dir($dirpath)) 
    mkdir($dirpath);
  
  //  Si ya existe obtenemos los archivos por cargar de ese directorio
  else {
    // Archivos de ese cliente
    $filesfromdir = glob($dirpath.SEPARADOR."*.txt"); 
    
    // Recorrer cada archivo por cargar
    foreach ($filesfromdir as $filefromdir) {

      // Setear a 0 variables de control
      $msg = null;
      $totalerrors = 0;
      // Si el archivo tiene nombre correcto, size>0 y es .txt se intenta cargar
      $extfromdir = pathinfo($filefromdir, PATHINFO_EXTENSION);
      $size =  filesize($filefromdir);
      $filename = basename($filefromdir, ".txt");
      if ($size>0 && strtolower($extfromdir)=='txt' && validateFileName($filename)){
        // (A) OPEN FILE
        $handle = fopen($filefromdir, "r") or die("Error reading file!");
        
        // Borrar registros temporales anteriores
        deleteHeaders($customerid,$db);

        // (B) READ LINE BY LINE
        $counter=0;  
        try {

          while (($getLine = fgetcsv($handle , 10000, ",")) !== FALSE){
            $counter++;

            // Quitar espacios y tabs al final de la linea
            $size = count($getLine);
            for ($x=0;$x<$size;$x++) 
              $getLine[$x] = rtrim($getLine[$x]);
              
            // 1ra fila debe ser Total
            if ($counter==1) {
              $line = validateFields($getLine,'T');
              $indexerr=count($line)-2;        
              $indexmsg=count($line)-1;
              if ($line[$indexerr]!=0){
                  throw new Exception("400-".$line[$indexmsg]); 
              }       
              $totalfacturas=$getLine[1];
              $totalmonto=$getLine[2];      
              $serie = $getLine[3];
            }
            // 2da fila debe ser Encabezado     
            elseif ($counter==2) {
              if (substr($getLine[0],-1)!='E'){
                throw new Exception("400-El tipo de registro debe ser E (Encabezado) en la línea 2");                 
              }   
              $line= validateFields($getLine,substr($getLine[0],-1));  
              $headerid=insertHeader($line,$customerid,$serie,$db);
            }

            // Validar las siguientes filas  
            elseif (substr($getLine[0],-1) != 'T') {
              $line= validateFields($getLine,substr($getLine[0],-1));
              if ($line[0] == 'E') 
                $headerid=insertHeader($line,$customerid,$serie,$db);
              elseif ($line[0] == 'D') 
                insertDetail($line,$headerid,$customerid,$db);
            }
            else {
              throw new Exception("400-No puede haber dos lines de totales (T)");               
            }
          }
          $totalerrors=validarTotales($customerid,$totalfacturas,$totalmonto,$db);

          if ($totalerrors>0) {
            $errors=getErrors($customerid,$db);
            $msgtosend="";
            for ($x=0;$x<count($errors);$x++) {
                $cant=$errors[$x]['err'.$x];
                if ($cant>0 && $x!=0){
                  $errmsg="<br>Hay $cant fila(s) en el archivo con error: ".$errors[$x]['errmsg']."<br>";
                  $msgtosend.=$errmsg;
                }
            }
            errorLoad(getCustomerEmail($customerid,$db),$filefromdir,$homeurl,$msgtosend,$db);
            fclose($handle);
            unlink($filefromdir); 
            continue;

          }
          
          validarSerie($serie,$customerid,$totalerrors,$db);
          encabezadoSinDetalle($customerid,$totalerrors,$db);

          // Detectar totales diferentes entre el encabezado y todos los detalles
          $diffHeaderDetailTotals=diffHeaderDetailTotals($customerid,$db);
          if (count($diffHeaderDetailTotals)>0 && $totalerrors==0)
            throw new Exception("Total encabezado de factura[s] difiere del total detalles. Facturas: ".implode(",",$diffHeaderDetailTotals)."");    

          // Detectar facturas duplicadas
          $duplicatedInvoices=duplicatedInvoices($customerid,$db);
          if (count($duplicatedInvoices)>0 && $totalerrors==0)
            throw new Exception("Hay documentos en el archivo con numeración duplicada. Documentos: ".implode(",",$duplicatedInvoices)."");
           
        }
        catch (Exception $e){
          if ($totalerrors == 0) {
            $msg = $e->getMessage();
            errorLoad(getCustomerEmail($customerid,$db),$filefromdir,$homeurl,$msg,$db); 
            fclose($handle);
            unlink($filefromdir);             
            continue;
          }
        }
        // (C) CLOSE FILE
        fclose($handle);
        unlink($filefromdir);
        if ($totalerrors==0){
          // Grabar customerid para subirlos a la tabla invoiceheader
          $customers_to_upload[] = $customerid;
          // Grabar correspondiente archivo (nombre) para enviar correo
          $files_to_upload[] = $filefromdir;
        }
     
      }
    }
  }
}


/********  SUBIR A TABLAS DEFINITIVAS********/

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
    throw new Exception("500-".$db->error);
  $row = $rs->fetch_assoc();
  $count=$row['Cnt'];
  return $count;
}  

for ($x=0; $x<count($customers_to_upload);$x++) {
  $customerid = $customers_to_upload[$x];
  $filename = $files_to_upload[$x];

  try {
      // Obtener la serie del lote
        $sql = "SELECT serie FROM loadinvoiceheader  WHERE customerid=$customerid LIMIT 1 ";
        if (!$rs = $db->query($sql))
          throw new Exception("500-".$db->error);     
        if ($row = $rs->fetch_assoc())
          $serie = $row['serie'];
      // Asignar ctrnumber inicial de la corrida según la serie
        $ctrnumber = getNextControl($serie,$customerid,$db);

      // Validar que las facturas (refnumber) que se van a cargar no existan
        $duplicadas = validateInvoicesBeforeLoad($db,$customerid);
        if ($duplicadas>0)
          throw new Exception("Hay $duplicadas facturas duplicadas en el archivo");         
      // Copiar registros header a la tabla definitiva
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
          throw new Exception("500-".$db->error);     
        if ($db->affected_rows == 0)  
          throw new Exception("Cero facturas insertadas");     
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
          throw new Exception("500-".$db->error);  
     incrementNextControl($customerid,$serie,$qtyinvoices,$db);
     /*
      // Obtener y actualizar next control
        $series = getSeries($customerid,$db);
        $index=array_search($serie, $series);
        if (is_null($index))
            throw new Exception("Serie no existe para este cliente");   
        // Ubicar el next control
          $nexts=getNextControls($customerid,$db);
          $next = $nexts[$index];
        // Construir nextcontrol en string
          $nexts[$index] = $next + $qtyinvoices;
          $str_nextcontrol = implode("-",$nexts);
        // Se actualiza el nextcontrol del cliente
          $update = "UPDATE customers SET nextcontrol = '$str_nextcontrol' WHERE id=$customerid ";
          if (!$db->query($update)) 
            throw new Exception("500-".$db->error);  
    */
  }

  catch (Exception $e){
      $msg = $e->getMessage();
      errorLoad(getCustomerEmail($customerid,$db),$filename,$homeurl,$msg,$db); 
      continue;
  }    
  
  successfulLoad(getCustomerEmail($customerid,$db),$filename,$homeurl,$db); 
  
  // Auditoria
  insertAudit($db,'SYSTEM@gmail.com','SYSTEM-IP','APP-CRON','MOD-CRON',"Se hizo una carga masiva de $qtyinvoices documentos");
  
}

die();  

?>
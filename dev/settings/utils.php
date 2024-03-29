<?php

define ('SEPARADOR','/');
define ("APP_APP", "APP");
define ('APP_CMS', 'CMS');
define ('APP_SENIAT', 'SENIAT');

define ("MODULE_INVOICES", "Documentos");
define ('MODULE_PREFERENCES', 'Usuarios-Seniat');
define ('MODULE_SECURITY', 'Seguridad');
define ('MODULE_CUSTOMERS', 'Clientes');
define ('MODULE_USERS', 'Usuarios');

// Dayco api/utils
function randomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function nvl($v,$r){
    if (is_null($v))
        return($r);
    else
        return($v);
}
function dlookup($db, $campo, $tabla, $where){
    try {
        $sql = "SELECT `" . $campo . "` retval " . 
               "FROM   `" . $tabla . "` " . 
               "WHERE  " . $where;
        $rs = $db->query($sql);
        $row = $rs->fetch_assoc();
        return($row["retval"]);
    } catch (Exception $e){
        return(null);
    }
}


function insertAudit($db,$userid,$ip,$app,$module,$dsc){
    
    // Si es un login en la descripción viene la palabra Inició
    $loginaction = 'NULL';
    if(substr_count($dsc,"Inici")>0 && $module=='Seguridad') {
        // Validar que modulo se logueó
        switch($app){
            case 'CMS':
                $loginaction = 0;
                break;
            case 'APP':
                $loginaction = 1;
                break;
            case 'SENIAT':
                $loginaction = 2;
                break;
        }
    }
    
    $sql = "INSERT INTO `audit`(
        `id`,
        `datecreation`,
        `userid`,
        `app`,
        `module`,
        `dsc`,
        `ip`,
        `loginaction`
    )
    VALUES(
        NULL,
        CURRENT_TIMESTAMP,
        '$userid',
        '$app',
        '$module',
        '$dsc',
        '$ip',
        $loginaction
    )";

    if (!$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    return $db->insert_id;
}

function getEmail($sessionid,$app,$db){

    switch ($app) {
        case APP_CMS:
            $sql = "SELECT usr AS CMS FROM users WHERE sessionid='$sessionid' ";
            break;
        case APP_APP:
            $sql = "SELECT contactemail AS APP FROM customers WHERE sessionid='$sessionid' ";
            break;
        case APP_SENIAT:
            $sql = "SELECT name AS SENIAT FROM preferences WHERE value='$sessionid' ";
            break;                        
    }

    if (!$rs = $db->query($sql))
        badEnd("500", array("msg"=>$db->error));    
    $row = $rs->fetch_assoc();

    if (count($row)==0)
        badEnd("400", array("msg"=>"$sql Sesion no valida o expirada"));  
    if ($app==APP_SENIAT){
        $pos = strrpos($row['SENIAT'],"_");
        $row['SENIAT']=substr($row['SENIAT'],0,$pos);        
    }
    return strtolower($row["$app"]);  
}
function isSessionValid($db, $sessionid){
    // Validar sessionid activo
    $sql =  "SELECT COUNT(*) cnt, min(id) id " .
            "FROM   customers " .
            "WHERE  sessionid = '" . $sessionid . "' " .
            "AND    validthru > NOW() ";
    if (!$rs = $db->query($sql)){
        header("HTTP/1.1 500");
        echo (json_encode(array("msg"=>$db->error,"sql"=>$sql)));
        die();
    }
    $row = $rs->fetch_assoc();
    if ($row["cnt"]<=0){
        header("HTTP/1.1 401");
        echo (json_encode(array("msg"=>"Sesión inválida o expirada")));
        die();
    }

    return($row["id"]);
}

function tienePrivilegio($db, $sessionid, $privilegeid){
    try {
        // obtener usuario
        $sql = "SELECT id " . 
               "FROM   users " . 
               "WHERE  sessionid = '" . $sessionid . "' ";
        $rs = $db->query($sql);
        $row = $rs->fetch_assoc();
        $userid = $row["id"];
        
        // incluir auditoria
        $sql = "SELECT COUNT(*) cnt " .
               "FROM   userprivileges " .
               "WHERE  userid=" . $userid . " " . 
               "AND    privilegeid=" . $privilegeid;
        $rs = $db->query($sql);
        $row = $rs->fetch_assoc();
        if ((integer) $row["cnt"] > 0){
            return(true);
        }else{
            return(false);
        }
    } catch (Exception $e){
        return(false);
    }
}
function parametrosValidos($parms, $campos){
    $out = true;
    foreach ($campos as $campo){
        if ($out)
            $out = isset($parms[$campo]) && trim($parms[$campo])!="";
    }
    return ($out);
}
function parametroValidoVacio($parms, $campo){
    return isset($parms[$campo]) && trim($parms[$campo])=="";
}

function badEnd($htmlError, $output){
    header("HTTP/1.1 ".$htmlError);
    echo (json_encode($output));
    die();
}

function enviarSMS($to, $msg){
    if (strlen($msg)>0){
        
        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_URL,"http://totalsoftware.la/sms/send.php?serviceid=veneden&recipient=".$to."&message=".urlencode($msg));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = json_decode(curl_exec($ch));
        curl_close($ch);
        return($response->{"err"}==0);
    }
}

function enviarCorreo($from, $to, $subject, $body, $altbody=""){
    //mail ($to,$subject,$body);
    //return;
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = "kromasys.com";
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = false;
    $mail->SMTPAutoTLS = false;
    $mail->setFrom('no-responder@kromasys.com', 'DaycoPrint');
    $mail->Username = 'no-responder@kromasys.com';
    $mail->Password = "*d=G0bu1xjks";
    $mail->addAddress($to);
    $mail->Subject = $subject;
    //$mail->msgHTML(file_get_contents('forgot.html'), __DIR__);
    $mail->isHTML(true);
    //$mail->msgHTML = $body;
    $mail->Body = $body;
    //if ($altbody=="")
    //    $mail->AltBody = $body;
    //else
    //    $mail->AltBody = $altbody;
    //send the message, check for errors
    return ($mail->send());
    
}
function isSessionValidCust($db, $sessionid){
    // Validar sessionid activo
    $sql =  "SELECT COUNT(*) cnt, min(id) id " .
            "FROM   custusers " .
            "WHERE  sessionid = '" . $sessionid . "' " .
            "AND    validthru > NOW() ";
    if (!$rs = $db->query($sql)){
        header("HTTP/1.1 500");
        echo (json_encode(array("msg"=>$db->error)));
        die();
    }
    $row = $rs->fetch_assoc();
    if ($row["cnt"]<=0){
        header("HTTP/1.1 401");
        echo (json_encode(array("msg"=>"Sesión inválida o expirada")));
        die();
    }
    return($row["id"]);
}
function isSessionValidCMS($db, $sessionid){
    // Validar sessionid activo
    $sql =  "SELECT COUNT(*) cnt, min(id) id " .
            "FROM   users " .
            "WHERE  sessionid = '" . $sessionid . "' " .
            "AND    validthru > NOW() ";
    if (!$rs = $db->query($sql)){
        header("HTTP/1.1 500");
        echo (json_encode(array("msg"=>$db->error,"sql"=>$sql)));
        die();
    }
    $row = $rs->fetch_assoc();
    if ($row["cnt"]<=0){
        header("HTTP/1.1 401");
        echo (json_encode(array("msg"=>"Sesión inválida o expirada")));
        die();
    }

    return($row["id"]);
}
function avoidInjection($param,$type) {
    switch ($type) {
        case "dashes":
            return str_replace("-",",",$param);
        case "date":
            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$param)) 
                return $param;
            else 
                badEnd("400", array("msg"=>"El parametro $param no pudo ser tipeado al tipo $type"));
        case "email":
            if(filter_var($param, FILTER_VALIDATE_EMAIL)) 
                return $param;
            else
                badEnd("400", array("msg"=>"El parametro $param no pudo ser tipeado al tipo $type"));
        case "float":
            return floatval($param); 
        case "int":
            return intval($param);
        case "list":
            # Caso lista separada con guiones, hay que validar patron -XX-XX-XX
            if (preg_match("/[-]?\w{1,2}/",$param))            
                return $param;
            else
                badEnd("400", array("msg"=>"El parametro $param no pudo ser tipeado al tipo $type"));
        case "mobile":
            if (preg_match("/^04[1,2,4,6]{2} [0-9]{7}/",$param) && strlen($param)==12) 
                return $param;
            else 
                badEnd("400", array("msg"=>"El parametro $param no pudo ser tipeado al tipo $type"));
        case "rif":
            if (preg_match("/[J,G,V,E]{1}[0-9]{9}/",$param) && strlen($param)==10) 
                return $param;
            else
                badEnd("400", array("msg"=>"El parametro $param no pudo ser tipeado al tipo $type"));
        case "str":
            return preg_replace("/[^a-zA-ZáéíóúñÁÉÚÍÓÑ0-9\s.]+/","",$param);
        default:
            echo (json_encode(array("msg"=>"El parametro $param no pudo ser tipeado al tipo $type")));
    }
}
function getCustomerId($clientrif,$db){
    $sql =  "SELECT id  " .
            "FROM   customers " .
            "WHERE  rif = '$clientrif'" ;
    if (!$rs = $db->query($sql))
        badEnd("500", array("msg"=>$db->error));
    if (!$row = $rs->fetch_assoc())
        badEnd("400", array("msg"=>"No existe cliente con rif: $clientrif"));
    else
        return ($row["id"]);
}
function getCustomerEmail($id,$db){
    $sql =  "SELECT contactemail  " .
            "FROM   customers " .
            "WHERE  id = $id" ;
    if (!$rs = $db->query($sql))
        badEnd("500", array("msg"=>$db->error));
    if (!$row = $rs->fetch_assoc())
        badEnd("400", array("msg"=>"No existe cliente con id: $id"));
    else
        return ($row["contactemail"]);
}
function getInvoiceDetailIds($id,$customerid,$db){
        $sql =  "SELECT d.id  " .
            "FROM   invoicedetails d INNER JOIN invoiceheader h ON h.id=d.invoiceid " .
            "WHERE  d.invoiceid = $id AND h.customerid=$customerid";
        if (!$rs = $db->query($sql))
            badEnd("500", array("msg"=>$db->error));
        $detail_ids = array();
        if (!$row = $rs->fetch_assoc())
            return $detail_ids;
        else {
            do {
                $detail_ids[]= $row["id"];
            } while ($row = $rs->fetch_assoc());
        }
        return $detail_ids;
}
function series($serie, $initial,$next){
    #Ejemplo llamada $seniat = series("-AA-BB","0000000001-0000000001-0100000001","0000000009-0000000067-0100000020");
    $seniat = new stdClass();
    $objects = array ();
    $serie_array = explode("-",$serie);
    $initial_array = explode("-",$initial);
    $next_array = explode("-",$next);        
    $max_length = count($serie_array);
    for ($i = 0; $i <= $max_length-1; $i++) {
        $object = new stdClass();
        if (strlen($serie_array[$i])==0)
            $object->serie = ' ';
        else            
            $object->serie = $serie_array[$i];
        $object->prefix=substr($initial_array[$i],0,2);
        $object->control= new stdClass();
            $object->control->initial=str_pad(substr($initial_array[$i],2),8,"0",STR_PAD_LEFT); 
            $object->control->next=str_pad($next_array[$i],10,"0",STR_PAD_LEFT);
        $objects[] = $object;
    }
    return $objects;
}
function series_validate($serie, $initial){
    #Ejemplo llamada series_validate("-AA-BB","0000000001-0000000001-0100000001");
    $serie_size = count(explode("-",$serie));
    $initial_size = count(explode("-",$initial));
    if ($serie_size == $initial_size)
        return true;
    else
        return false;
}
function getSeries($customerid,$db){
    $sql = "SELECT serie FROM customers WHERE id=$customerid ";
    if (!$rs=$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    if ($row = $rs->fetch_assoc()) {
        $series = explode("-",$row['serie']);
        for ($i=0;$i<count($series);$i++)
            if (strlen($series[$i])==0)
                $series[$i]=' ';
        return $series;
    }
    else
        return null;
}
function getInitialControls($customerid,$db){
    $sql = "SELECT initialcontrol FROM customers WHERE id=$customerid ";
    if (!$rs=$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    if ($row = $rs->fetch_assoc()) 
        return explode("-",$row['initialcontrol']);
    else
        return null;        
}
function getNextControls($customerid,$db){
    $sql = "SELECT nextcontrol FROM customers WHERE id=$customerid ";
    if (!$rs=$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    if ($row = $rs->fetch_assoc()) 
        return explode("-",$row['nextcontrol']);
    else
        return null;        
} 
function getNextControl($serie,$customerid,$db){

    // Obtener string de nextControl y de series
    $sql = "SELECT serie, nextcontrol
            FROM customers WHERE id=$customerid";
    if (!$rs=$db->query($sql))
      badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    $row = $rs->fetch_assoc();
    // Convertirlos en arreglo
    $array_serie = explode("-",$row['serie']);
    $array_next = explode("-",$row['nextcontrol']);
    // Ubicar el index de la serie
    $i = False;
    for ($x=0; $x < count($array_serie); $x++) { 
        if (trim($array_serie[$x]) == trim($serie)) {
            $i = $x;
            break;
        }
    }

    if ($i === False)
        return null;
    else
        // Con ese indice, obtener el nextcontrol
        return $array_next[$i];
}
function incrementNextControl($customerid,$serie,$n,$db){
  $next = getNextControl($serie,$customerid,$db);

  if ($next===null)
    badEnd("400", array("msg"=>"Serie $serie no existe para este cliente $customerid (utils.php)"));
  $next = $next + $n;

  // Obtener string de nextControl y de series
  $sql = "SELECT serie, nextcontrol
          FROM customers WHERE id=$customerid";
  if (!$rs=$db->query($sql))
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  $row = $rs->fetch_assoc();
  // Convertirlos en arreglo
  $array_serie = explode("-",$row['serie']);
  $array_next = explode("-",$row['nextcontrol']);
  // Ubicar el index de la serie
  $i = False;
  for ($x=0; $x < count($array_serie); $x++) { 
      if (trim($array_serie[$x]) == trim($serie)) {
          $i = $x;
          break;
      }
  }

  // Con ese indice, actualizar el nextcontrol
  if ($i===False)
    return $i;
  $array_next[$i] = $next;
 
  // Convertir nextcontrol a string
  $str_next = implode("-",$array_next);
  // Update la tabla customers con el nuevo valor del nextcontrol
  $sql = "UPDATE customers SET nextcontrol='$str_next' WHERE id=$customerid";

  if (!$db->query($sql)) 
    badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
  return $db->affected_rows;   
}

function loadDataByInvoice($invoices,$customerid,$db){
    $condition="";
    if (!is_null($invoices)) 
      $condition=" AND H.id IN (".avoidInjection($invoices,'dashes').")";
    $sql =
      "SELECT
          H.id,
          H.discount,
          H.emailhash,
          H.clientemail,
          H.clientname,
          H.clientrif,
          H.customerid,
          C.name daycoClientName,
          H.refnumber,
          H.issuedate,
          DATE_FORMAT(H.issuedate, '%d\/%m\/%Y') issueformatteddate,
          DATE_FORMAT(H.duedate, '%d\/%m\/%Y') dueformatteddate,
          SUM(
              D.qty * D.unitprice *(1 - D.itemdiscount/100)
          ) AS gross,

          SUM(
              D.qty * D.unitprice *(1 - D.itemdiscount/100) * (D.itemtax/100)
          ) AS totaltax            
      FROM
          invoiceheader H
      INNER JOIN customers C ON
          H.customerid = C.id
      INNER JOIN invoicedetails D ON
          D.invoiceid = H.id WHERE H.customerid=$customerid AND tosend=0 ".
        " $condition ".
      " GROUP BY
          H.id,
          H.discount,  
          H.emailhash,
          H.clientemail,
          H.clientname,
          H.clientrif,
          H.customerid,
          C.name,
          H.refnumber,
          H.issuedate,
          DATE_FORMAT(H.issuedate, '%d\/%m\/%Y'),
          DATE_FORMAT(H.duedate, '%d\/%m\/%Y') ";

    if (!$rs=$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error,"errid"=>1));
    $records=array();
    while ($row = $rs->fetch_assoc()) {
      $record = new stdClass();
      $record->invoiceid = $row['id'];
      $record->rif = $row['clientrif'];        
      $record->emailhash = md5("$record->invoiceid"."$customerid"."$record->rif");        
      $record->refnumber = $row['refnumber'];
      $record->issuedate = $row['issueformatteddate'];
      $record->duedate = $row['dueformatteddate'];
      $record->amount = number_format(     $row['gross']*(1-$row['discount']/100) + $row['totaltax']    , 2, ",", ".");
      $record->email = $row['clientemail'];
      $record->name = $row['clientname'];
      $record->clientName=$row['daycoClientName'];
      $records[] = $record;
    }

    return $records;
} 
function setQuery($customerid,$datefrom,$dateto,$status_condition,$filter,$order) {
    $sql =  "SELECT " .
        " H.id, H.issuedate, H.refnumber, H.ctrnumber, H.clientrif, H.clientname, ".
        " H.type, H.ctrref, manualload, ".            
        " SUM((unitprice*qty*(1-itemdiscount/100))) gross, ".
        " SUM( unitprice*qty*(".
        
        " (CASE WHEN itemtax<0 THEN 0 ELSE itemtax END)".
        
        "/100)*(1-itemdiscount/100) ) tax, ".
        " H.discount discount, ".
        " 100 * SUM( unitprice*qty*(itemdiscount/100) )/SUM(unitprice*qty) discount_percentage, ".
        " DATE_FORMAT(H.issuedate, '%d/%m/%Y') formatteddate, ".
        " H.sentdate, H.viewdate, H.canceldate, SUM(D.qty) qty   ".
        " FROM    invoiceheader H ".
        " LEFT JOIN invoicedetails D ON ".
            " D.invoiceid = H.id ".
        " WHERE H.customerid=$customerid AND H.issuedate BETWEEN '$datefrom' AND '$dateto' ".
        $status_condition.$filter.   
        " GROUP BY ".
        "   H.id, H.issuedate, H.refnumber, H.ctrnumber, H.clientrif, H.clientname, DATE_FORMAT(H.issuedate, '%d/%m/%Y'), ".
        "   H.sentdate, H.viewdate, H.canceldate " .
            $order;
    return $sql;                    
}
function setQueryEntry($customerid,$id){
    $sql = "SELECT " .
        " H.id, H.issuedate, H.duedate, H.refnumber, H.ctrnumber, H.clientrif, H.clientname, ".
        " mobilephone, otherphone, clientemail, clientaddress, obs, currency, currencyrate, manualload, ".
        " SUM( (unitprice*qty*(1-itemdiscount/100)) ) gross, ".
        " SUM( unitprice*qty*(".
            " (CASE WHEN itemtax<0 THEN 0 ELSE itemtax END)".
            "/100)*(1-itemdiscount/100) ) tax, ".
        " H.discount, H.type, H.ctrref, ".
        " DATE_FORMAT(H.issuedate, '%d/%m/%Y') formatteddate, ".
        " DATE_FORMAT(H.duedate, '%d/%m/%Y') formattedduedate, ".      
        " DATE_FORMAT(H.sentdate, '%d/%m/%Y') formattedsentdate, ".
        " DATE_FORMAT(H.viewdate, '%d/%m/%Y') formattedviewdate, ".                    
        " H.sentdate, H.viewdate, SUM(D.qty) qty   ".
        " FROM    invoiceheader H ".
        " INNER JOIN invoicedetails D ON ".
            " D.invoiceid = H.id ".
        " WHERE H.customerid=$customerid AND H.id = $id ";
    return $sql;        
}
function setQueryDetail($id) {
    $sql = "SELECT id, itemref ref, itemdsc dsc, qty, unit, unitprice, ".
        " (CASE WHEN itemtax<0 THEN 0 ELSE itemtax END) tax, ".
        " itemdiscount discount, ".
        " ROUND(unitprice*qty*(1-itemdiscount/100),2) total ".     
        " FROM invoicedetails WHERE invoiceid = $id";
    return $sql;        
}
// Prepara el JSON para los 3 modulos (CMS-APP-Seniat)
function jsonInvoiceList($rs){    
  $records = array();
  while ($row = $rs->fetch_assoc()){
      $record = new stdClass();
      $record->id = (integer) $row["id"];
      ($row["manualload"]==1) ? ($record->manualload = true) : ($record->manualload = false);
      $record->type =new stdClass();
          $record->type->id=$row['type'];
          switch ($row['type']) {
              case 'FAC':
                  $record->type->name='Factura';
                  break;
              case 'NDB':
                  $record->type->name='Nota de Débito';
                  break;
              case 'NCR':
                  $record->type->name='Nota de Crédito';
                  break;
          }
      $record->ctrref =$row['ctrref'];
      $record->issuedate =new stdClass();
      $record->issuedate->date = $row["issuedate"];
      $record->issuedate->formatted = $row["formatteddate"];
      $record->refnumber = nvl($row["refnumber"],"");
      $record->ctrnumber = nvl($row["ctrnumber"],"");
      $record->client =new stdClass();
      $record->client->rif = $row["clientrif"];
      $record->client->name = $row["clientname"];        
      $record->status =new stdClass();
      $status=1;
      $status_dsc = "Pendiente";
      if (!is_null($row["sentdate"])) {
          $status=2;
          $status_dsc = "Enviado";            
      }
      if (!is_null($row["viewdate"])) {
          $status=3;
          $status_dsc = "Leído";            
      }
      if (!is_null($row["canceldate"])) {
        $status=4;
        $status_dsc = "Anulada";            
      }      
      $record->status->id = $status;
      $record->status->dsc = $status_dsc;
      
      $record->amounts =new stdClass();        
      $record->amounts->gross = new stdClass(); 
      $record->amounts->gross->number = (float)$row["gross"]*(1-(float)$row["discount"]/100);
      $record->amounts->gross->formatted = number_format($row["gross"]*(1-(float)$row["discount"]/100), 2, ",", ".");
      
      if ($row["tax"]==-1 || $row["tax"]==-2 )
        $tax=(float)0;
      else 
        $tax = (float)$row["tax"];

      $record->amounts->tax = new stdClass(); 
      $record->amounts->tax->number = $tax;
      $record->amounts->tax->formatted = number_format($tax, 2, ",", ".");         

      $record->amounts->total = new stdClass(); 
      $record->amounts->total->number = (float)$row["gross"]*(1-(float)$row["discount"]/100) + $tax;
      $record->amounts->total->formatted = number_format((float)$row["gross"]*(1-(float)$row["discount"]/100) + $tax, 2, ",", ".");          

      $records[] = $record;
  }
  return $records;
}
function badEndCsv($message){
    $BOM = "\xEF\xBB\xBF"."\xEF\xBB\xBF";
    $fp = fopen('php://output', 'wb');
    fwrite($fp, $BOM);  
    $line = array(0=>$message);
    fputcsv($fp, $line, ';', '"');
    fclose($fp);
    die();
}
function getCustomerName($id,$db){
    $sql ="SELECT name FROM customers WHERE id = $id";
    if (!$rs=$db->query($sql))
        badEndCsv("500 ".$sql." msg ".$db->error." errid:4 ");
    $row = $rs->fetch_assoc();

    if (count($row)==0)
        badEndCsv("Cliente id '$id' no encontrado");
    return $row['name'];    
}
function getCustomerNameByEmail($email,$db){
    $sql ="SELECT name FROM customers WHERE contactemail = '$email'";
    if (!$rs=$db->query($sql))
        
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));         
    $row = $rs->fetch_assoc();

    if (count($row)==0)
        badEnd("400", array("msg"=>"Cliente email '$email' no encontrado"));         
        
    return $row['name'];    
}
function filterByStatus($status){
    $status_list =explode("-",$status);
    $status_condition = " AND ( 0  ";
    foreach ($status_list as $value){
        if ($value!=1 && $value!=2 && $value!=3 && $value!=4)
            badEnd("400", array("msg"=>"Valor de estatus $value fuera de rango")); 
        switch ($value) {
            case 1:    
                $status_condition .= " OR (sentdate IS NULL AND canceldate IS NULL)  ";
                break;
            case 2:
                $status_condition .= " OR (sentdate IS NOT NULL AND viewdate IS NULL) ";            
                break;
            case 3:
                $status_condition .= " OR (viewdate IS NOT NULL) ";            
                break;
            case 4:
              $status_condition .= " OR (canceldate IS NOT NULL) ";            
              break;                   
             
        }
    }
    $status_condition .= " ) ";   
    return $status_condition;   
}
function filterByOneStatus($status){
    $status_condition = "";
    switch ($status) {
        case 1:    
            $status_condition = " AND sentdate IS NULL AND canceldate IS NULL ";
            break;
        case 2:
            $status_condition = " AND sentdate IS NOT NULL AND viewdate IS NULL";            
            break;
        case 3:
            $status_condition = " AND viewdate IS NOT NULL ";            
            break;
        case 4:
            $status_condition = " AND canceldate IS NOT NULL ";            
            break;                   
    }  
    return $status_condition;  
}
function getCustomers($db){
    $sql = "SELECT id, serie, initialcontrol, nextcontrol FROM customers ";
    if (!$rs=$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    $customers = array();
    while ($row = $rs->fetch_assoc()) {
        $customer = new stdClass();
        $id = $row["id"];
        $customer->id = $id;
        $customer->serie = $row["serie"];
        $customer->initial= $row["initialcontrol"];
        $customer->nextcontrol=$row["nextcontrol"];
        $customers[$id] = $customer;
    }
    return $customers;
}


?>

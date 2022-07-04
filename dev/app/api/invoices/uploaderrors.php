<?php
// api/invoices/uploaderrors.php
// Cargar librerias
  header("Content-Type:application/json");
  include_once("../../../settings/dbconn.php");
  include_once("../../../settings/utils.php");


// Parametros obligatorios
  $parmsob = array("errorcode","order","offset","numofrec","sessionid");
  if (!parametrosValidos($_REQUEST, $parmsob))
      badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
// Validar user session
  $customerid = isSessionValid($db, $_REQUEST["sessionid"],array('ip'=>$_SERVER['REMOTE_ADDR'],'app'=>'APP','module'=>'invoices','dsc'=>'uploaderrors.php'));



// Llenar variables
    $errorcode = $_REQUEST["errorcode"];
    $offset=$_REQUEST["offset"];
    $numofrec=$_REQUEST["numofrec"];

// Consulta a la BD
    // validar el order
        $order = "ORDER BY " . abs($_REQUEST["order"]);
        if ($_REQUEST["order"] < 0 )
            $order = $order .  " DESC";

    $sql = "SELECT *, DATE_FORMAT(issuedate, '%d/%m/%Y') dateformatted FROM loadinvoiceheader WHERE customerid=$customerid AND err='$errorcode' ".$order." ";

    // calcular numero de registros
    if (!$rs = $db->query("SELECT COUNT(*) cnt FROM (" . $sql . ") A "))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    $row = $rs->fetch_assoc();
    $totalreg=(integer) $row["cnt"];


    // limitar numero de registros
    $sql =  "SELECT A.* FROM (" . $sql . ") A " .
            "LIMIT " . $offset . "," . $numofrec;
    if (!$rs = $db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));

    $records = array();
    while ($row = $rs->fetch_assoc()){
        $record = new stdClass();
        $record->type = $row["type"];
        $record->refnumber = $row["refnumber"];   
        $record->issuedate = new stdClass();
            $record->issuedate->date = $row["issuedate"]; 
            $record->issuedate->formatted = $row["dateformatted"]; 
        $record->client = new stdClass();            
            $record->client->rif=$row["clientrif"]; ;
            $record->client->name=$row["clientname"]; 
        $record->total = new stdClass();
            $record->total->amount=(float)$row["total"]; 
            $record->total->formatted=number_format($row["total"], 2, ",", ".");     
        $record->msg = $row["errmsg"];            
        $records[] =$record; 
        
    }
  
// Salida
  $out = new stdClass(); 
  $out->numofrecords = $totalreg;
  $out->records = $records;
  header("HTTP/1.1 200");
  echo (json_encode($out));
  die();
?>

<?php
// api/invoices/uploadcheck.php

// Cargar librerias
  header("Content-Type:application/json");
  include_once("../../../settings/dbconn.php");
  include_once("../../../settings/utils.php");
// Declarar funciones locales
  function validateFields($line,$type){
    $err = 0;
    $errmsg = "";
    switch ($type) {
      case 'T':
        if (count($line) != 4) {
          $err = 1;
          $errmsg = "Cantidad de campos esperados incorrecto en linea: T";
        }
        break;
      case 'E':
        if (count($line) != 18) {
          $err = 1;
          $errmsg = "Cantidad de campos esperados incorrecto en linea: E";
 
        }  
        break;
      case 'D':
        if (count($line) != 8) {
          $err = 1;
          $errmsg = "Cantidad de campos esperados incorrecto en linea: D";
        }        
        break;  
    }

    return(array("err"=>$err, "msg"=>$errmsg));  
  }
  function storageTotals($line){
    $err = 0;
    $errmsg = "";
    return(array("err"=>$err, "msg"=>$errmsg));  
  }

// Parametros obligatorios
  $parmsob = array("sessionid");
  if (!parametrosValidos($_REQUEST, $parmsob))
      badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
// Validar user session
  $customerid = isSessionValid($db,$_REQUEST["sessionid"]); 

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
      badEnd("400", array("msg"=>"Error: archivo vacio"));

// (A) OPEN FILE
  $handle = fopen($_FILES["invoicesfile"]["tmp_name"], "r") or die("Error reading file!");
  $firstLine=true;
// (B) READ LINE BY LINE
  while (($getLine = fgetcsv($handle , 10000, ",")) !== FALSE)    {
  // Es Total
    if ($firstLine && substr($getLine[0],-1) != 'T') {
      badEnd("400", array("msg"=>"Formato Incorrecto: se espera linea de TOTALES"));
    }
    elseif ($firstLine && substr($getLine[0],-1) == 'T') {
      $resp = validateFields($getLine,'T');
      if ($resp['err'] != 0)
        badEnd("400", array("msg"=>$resp['msg']));
      storageTotals($getLine);
      $firstLine = false;
      $serie = $getLine[3];
      continue;
    }
    if (($getLine[0] != 'E') && ($getLine[0] != 'D'))
      badEnd("400", array("msg"=>"Formato Incorrecto: se espera linea de ENCABEZADO o DETALLE"));
  // Es Encabezado
    if ($getLine[0] == 'E') {
      $resp = validateFields($getLine,'E');
      $err = $resp["err"];
      $errmsg = $resp["msg"];    
      $sql = "INSERT INTO loadinvoiceheader ".
        " (id, customerid, type, serie, issuedate, duedate, refnumber, clientrif, clientname, clientaddress, mobilephone, ".
        " otherphone,  clientemail, obs, currency, currencyrate, ctrref, discount, totaltax, total, err, errmsg ) ". 
        " VALUES ( 0, ". 
        $customerid.",'".$getLine[1]."','".$serie."','".$getLine[2]."','".$getLine[3]."','".$getLine[4]."','".$getLine[5]."','".$getLine[6]."','".$getLine[7]."','".$getLine[8]."','".
        $getLine[9]."','".$getLine[10]."','".$getLine[11]."','".$getLine[12]."','".$getLine[13]."','".$getLine[14]."','".$getLine[15]."','".$getLine[16]."','".$getLine[17]."','".
        $err."','".$errmsg."'".
        ") ";

        if (!$db->query($sql))
          badEnd("500", array("sql"=>$sql,"msg"=>$db->error)); 
        $headerid = $db->insert_id; 
    }
  // Es Detalle  
    elseif ($getLine[0] == 'D') {
      $resp = validateFields($getLine,'D');
      $err = $resp["err"];
      $errmsg = $resp["msg"];       
      $sql = "INSERT INTO loadinvoicedetail ".
        " (id, loadinvoiceheaderid, itemref, itemdsc, qty, unitprice, itemtax, itemdiscount )".
        " VALUES ( 0, ". 
        $headerid.",'".$getLine[1]."','".$getLine[2]."',".$getLine[3].",".$getLine[4].",".$getLine[5].",".$getLine[6].
        ") ";
        if (!$db->query($sql))
          badEnd("500", array("sql"=>$sql,"msg"=>$db->error));     

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
    $errors = array(array("err"=>(integer)$row["ERR0"], "errmsg"=>"Sin error"),
            array("err"=>(integer)$row["ERR1"], "errmsg"=>"Cantidad de campos requeridos incorrecta"),
            array("err"=>(integer)$row["ERR2"], "errmsg"=>"Formato de campo incorrecto"));
            //array("err"=>(integer)$row["ERR3"], "errmsg"=>"Error tipo 3"),
            
// Salida
  $out = new stdClass(); 
  $out->errors = $errors;
  header("HTTP/1.1 200");
  echo (json_encode($out));
  die();
?>

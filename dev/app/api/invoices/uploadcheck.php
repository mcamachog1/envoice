<?php
// /api/invoices/uploadcheck.php
header("Content-Type:application/json");
include_once("../../../settings/dbconn.php");
include_once("../../../settings/utils.php");

// parametros obligatorios
$parmsob = array("sessionid");
if (!parametrosValidos($_REQUEST, $parmsob))
    badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
// Validar user session
$customerid = isSessionValid($db,$_REQUEST["sessionid"]);        

// parametro especial: file
if (!isset($_FILES["invoicesfile"]))
    badEnd("400", array("msg"=>"No se adjuntó el archivo "));
if ($_FILES["invoicesfile"]["error"]<>0) 
    badEnd("400", array("msg"=>"Error en la carga del archivo"));
if($_FILES["invoicesfile"]["size"] <= 0)    
    badEnd("400", array("msg"=>"Error: archivo vacio"));

// (A) OPEN FILE
$handle = fopen($_FILES["invoicesfile"]["tmp_name"], "r") or die("Error reading file!");
 
// (B) READ LINE BY LINE
// La primera linea debe ser la de TOTALES
if ($line = fgets($handle))
  if (substr($line,0,1)<>"T")
    badEnd("400", array("msg"=>"Formato Incorrecto: se espera linea de TOTALES"));
// Sigue leyendo por linea a partir de la 2da linea
while (($getLine = fgetcsv($handle , 10000, ",")) !== FALSE)    {
  if ($getLine[0] != 'E' && $getLine[0] != 'D')
    badEnd("400", array("msg"=>"Formato Incorrecto: se espera linea de ENCABEZADO o DETALLE"));
  if ($getLine[0] == 'E') {
    $sql = "INSERT INTO loadinvoiceheader ".
      " (id, customerid, type, issuedate, duedate, refnumber, clientrif, clientname, clientaddress, mobilephone, ".
      " otherphone,  clientemail, obs, currency, currencyrate, ctrref, discount, totaltax, total) ". 
      " VALUES ( 0, ". 
      $customerid.",'".$getLine[1]."','".$getLine[2]."','".$getLine[3]."','".$getLine[4]."','".$getLine[5]."','".$getLine[6]."','".$getLine[7]."','".$getLine[8]."','".
      $getLine[9]."','".$getLine[10]."','".$getLine[11]."','".$getLine[12]."',".$getLine[13].",'".$getLine[14]."',".$getLine[15].",".$getLine[16].",".$getLine[17].
      ") ";
      if (!$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error)); 
      $headerid = $db->insert_id; 
  }
  elseif ($getLine[0] == 'D') {
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

//$out->file =$_FILES[invoicesfile];
header("HTTP/1.1 200");
//echo (json_encode($out));
die();
?>

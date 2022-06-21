<?php
// seniat/api/invoices/sendtocheck.php

    header("Content-Type:application/json");
    include("../../../settings/dbconn.php");
    include("../../../settings/utils.php");
    include("functions.php");
 

     
    
    function sendCheck($invoices,$homeurl,$customerid,$db){
      $data = loadDataByInvoice($invoices,$customerid,$db);
      return count($data);  
    }
    // parametros obligatorios
    $parmsob = array("sessionid");

    if (!parametrosValidos($_GET, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

    $sessionid= $_GET["sessionid"];

    // Validar user session
    $customerid=isSessionValid($db,$sessionid);

    // Enviar todas las pendientes
    if (!isset($_GET["invoiceids"])){
        $received="";
        $sent=sendCheck(null,$homeurl,$customerid,$db);
      }
    // Enviar solo las seleccionadas
    else {
      $received=count(explode("-",$_GET["invoiceids"]));  
      $sent=sendCheck($_GET["invoiceids"],$homeurl,$customerid,$db);
    }
    
    // Salida
    $out = new stdClass();
    $out->invoices=new stdClass();
    $out->invoices->received = $received;
    $out->invoices->tosend = $sent;

    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();    

?>
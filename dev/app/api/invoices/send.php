<?php
// seniat/api/invoices/send.php

    header("Content-Type:application/json");
    include("../../../settings/dbconn.php");
    include("../../../settings/utils.php");
    require '../../../hooks/PHPMailer5/PHPMailerAutoload.php';
    date_default_timezone_set('Etc/UTC');    


    // Update status 
    function updateStatus($invoices,$customerid,$db){
      $condition="";
      if (!is_null($invoices)) 
        $condition=" AND id IN (".avoidInjection($invoices,'dashes').")";
      
      // tosend=0 Por enviar tosend=1 Enviado
      $sql = "UPDATE invoiceheader SET tosend=1, sentdate=NOW() WHERE customerid=$customerid ".$condition;
      if (!$db->query($sql))
          badEnd("500", array("sql"=>$sql,"msg"=>$db->error,"errid"=>1));      
      return $db->affected_rows;
    }
    // Update emailhash
    function updateEmailHash($invoiceid,$customerid,$clientrif,$db){
      //MD5 de la tabla de facturas con los campos (id+customerid+clientrif)
      $hash=md5("$invoiceid"."$customerid"."$clientrif");
      // Forzar que cuente las filas actualizadas
      $sql = "UPDATE invoiceheader SET emailhash=NULL WHERE customerid=$customerid ".
            " AND id=$invoiceid AND clientrif='$clientrif'";
      if (!$db->query($sql))
          badEnd("500", array("sql"=>$sql,"msg"=>$db->error,"errid"=>2));         
      // Actualizar el hash
      $sql = "UPDATE invoiceheader SET emailhash='$hash' WHERE customerid=$customerid ".
            " AND id=$invoiceid AND clientrif='$clientrif'";
      if (!$db->query($sql))
          badEnd("500", array("sql"=>$sql,"msg"=>$db->error,"errid"=>3));      
      return $db->affected_rows;
    }      

    function sendInvoices($invoices,$homeurl,$customerid,$db){
      $data = loadDataByInvoice($invoices,$customerid,$db);

      $affected_hash=0;
      foreach ($data as $object){
        $subject = "Factura #$object->refnumber disponible - $object->clientName";
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
                      "<div style='display:table-cell'><img style='max-width:30%' src='".$homeurl."img/logo.png' />".
                      "</div>" .
                    "</div>" .

                    "<div style='display:table-row'>" .
                      "<div style='text-align:center;display:table-cell;font-size:200%;font-weight:bold'>" .
                        "<br/>Factura #$object->refnumber disponible - $object->clientName<br/>&nbsp;" .
                      "</div>" .
                    "</div>" .                    

                    "<div style='display:table-row'>" .
                      "<div style='display:table-cell;font-size:120%;text-align:left'>Hola, <b>".$object->name.".</b>".
                        "<p>Te queremos informar que la factura de <strong>$object->clientName</strong> ya se encuentra disponible para descargar.".
                          " A continuación el resumen de tu factura digital: ".
                        "</p>".
                      "</div>" .
                    "</div>" .


                    "<div style='display:table-row'>" .
                      "<div style='display:table-cell;font-size:120%;text-align:left'>".
                        "<ul style='list-style: none'>".
                          "<li>Fecha: $object->issuedate</li>".
                          "<li>Número de Factura: $object->refnumber</li>".
                          "<li>Monto: $object->amount Bs.</li>".
                          "<li>P&aacute;guese antes de: $object->duedate</li>".
                        "</ul>".
                       "</div>" .
                    "</div>" .                    

                    "<div style='display:table-row'>" .
                      "<div style='display:table-cell'>" .
                        "<a href='".$homeurl ."../customer/view.php". "?hash=".$object->emailhash."'>" .
                          "<div style='font-weight:bold;margin-left:auto;margin-right:auto;border-radius:12px;cursor: pointer;margin-top: 50px;margin-bottom: 50px;color: white;line-height: 50px;text-align:center;background-color: #0033A0;height: 50px;width: 320px;border: none;transition: all ease 300ms;'>Ver factura</div>" .
                        "</a>" .
                      "</div>" .
                    "</div>" .

                    "<div style='display:table-row'>" .
                      "<div style='display:table-cell'>No conteste a este correo, la dirección desde la que se envía no está habilitada para la recepción de mensajes".
                      "</div>" .
                    "</div>" .
                  "</div>" .
                "</div>" .
              "</div>" .
            "</body>" .
          "</html>";

        $altbody = "Hola ".$object->name.", para ver su factura diríjase a la dirección indicada a continuación:\n\n".
                $homeurl . "../customer/view.php"."?hash=".$object->emailhash."\n\n".
                "No conteste a este correo, la dirección desde la que se envía no está habilitada para la recepción de mensajes\n" .
                "Equipo de DaycoPrint";
 
        enviarCorreo("no-responder@espacioseguroDayco.com", $object->email, $subject, $body, $altbody);
        // Incrementa contador cuando se crea el hash
        if (updateEmailHash($object->invoiceid,$customerid,$object->rif,$db)==1)
          $affected_hash++;
      }
      // Cambia el status de todas las facturas enviadas y se obtiene la cantidad
      $affected_status=updateStatus($invoices,$customerid,$db);    
      // Valida que los hashes creados,los status cambiados y las facturas enviadas por correo sea la misma cantidad      
      if ($affected_hash==count($data) && $affected_hash==$affected_status)
        return count($data);  
      else  
        badEnd("400", array("msg"=>"Facturas enviadas no coincide con los status actualizados"));
    }
    // parametros obligatorios
    $parmsob = array("sessionid");

    if (!parametrosValidos($_GET, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

    $sessionid= $_GET["sessionid"];

    // Validar user session
    $customerid = isSessionValid($db, $sessionid);

    
    // Enviar todas las pendientes
    if (!isset($_GET["invoiceids"]))
      $sent=sendInvoices(null,$homeurl,$customerid,$db);
    // Enviar solo las seleccionadas
    else
      $sent=sendInvoices($_GET["invoiceids"],$homeurl,$customerid,$db);
    
    // Auditoria
    insertAudit($db,getEmail($_REQUEST["sessionid"],'APP',$db),$_SERVER['REMOTE_ADDR'],'APP','invoices',"Se enviaron $sent documentos por email");  
    // Salida
    $out = new stdClass();
    $out->invoices=new stdClass();
    $out->invoices->sent = $sent;
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();    

?>
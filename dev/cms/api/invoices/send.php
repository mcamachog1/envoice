<?php
// seniat/api/invoices/send.php

    header("Content-Type:application/json");
    include("../../../settings/dbconn.php");
    include("../../../settings/utils.php");
    require '../../../hooks/PHPMailer5/PHPMailerAutoload.php';
    date_default_timezone_set('Etc/UTC');    

    function loadDataByInvoice($invoices,$db){
      $condition="";
      if (!is_null($invoices)) 
        $condition="WHERE H.id IN (".avoidInjection($invoices,'dashes').")";
      $sql =
        "SELECT
            H.id,
            H.clientemail,
            H.clientname,
            H.customerid,
            C.name daycoClientName,
            H.refnumber,
            H.issuedate,
            DATE_FORMAT(H.issuedate, '%d\/%m\/%Y') issueformatteddate,
            DATE_FORMAT(H.duedate, '%d\/%m\/%Y') dueformatteddate,
            SUM(
                D.qty * D.unitprice *(1 - D.itemdiscount)
            ) AS gross,
            SUM(
                D.qty * D.unitprice *(1 - D.itemdiscount) *(1 + D.itemtax)
            ) AS total
        FROM
            invoiceheader H
        INNER JOIN customers C ON
            H.customerid = C.id
        INNER JOIN invoicedetails D ON
            D.invoiceid = H.id ".
          " $condition ".
        " GROUP BY
            H.id,
            H.clientemail,
            H.clientname,
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
        $record->refnumber = $row['refnumber'];
        $record->issuedate = $row['issueformatteddate'];
        $record->duedate = $row['dueformatteddate'];
        $record->amount = number_format($row['total'], 2, ",", ".");
        $record->email = $row['clientemail'];
        $record->name = $row['clientname'];
        $record->clientName=$row['daycoClientName'];
        $records[] = $record;
      }

      return $records;
    }      
    
    function sendInvoices($invoices,$homeurl,$db){
      $data = loadDataByInvoice($invoices,$db);
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
                          " A continuación el resumen de tu factura digital:
                        </p>
                      </div>" .
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
                        "<a href='".$homeurl . "?id=login&sid=recoverpwd&hash= "."&email=".$object->email."'>" .
                          "<div style='font-weight:bold;margin-left:auto;margin-right:auto;border-radius:12px;cursor: pointer;margin-top: 50px;margin-bottom: 50px;color: white;line-height: 50px;text-align:center;background-color: #0033A0;height: 50px;width: 320px;border: none;transition: all ease 300ms;'>Ver factura</div>" .
                        "</a>" .
                      "</div>" .
                    "</div>" .

                    "<div style='display:table-row'>" .
                      "<div style='display:table-cell'>Gracias de antemano<br />$object->clientName" .
                      "</div>" .
                    "</div>" .
                  "</div>" .
                "</div>" .
              "</div>" .
            "</body>" .
          "</html>";

          $altbody = "Hola ".$object->name.", para ver su factura diríjase a la dirección indicada a continuación:\n\n".
                $homeurl . "?id=login&sid=recoverpwd&hash= "."&email=".$object->email."\n\n".
                "Gracias de antemano\n" .
                "Equipo de DaycoPrint";

        enviarCorreo("no-responder@espacioseguroDayco.com", $object->email, $subject, $body, $altbody);

      }
      
      return count($data);  
    }
    // parametros obligatorios
    $parmsob = array("sessionid");

    if (!parametrosValidos($_GET, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

    $sessionid= $_GET["sessionid"];

    // Validar user session
    isSessionValidCMS($db,$sessionid);

    // Enviar todas las pendientes
    if (!isset($_GET["invoiceids"]))
      $sent=sendInvoices(null,$homeurl,$db);
    // Enviar solo las seleccionadas
    else
      $sent=sendInvoices($_GET["invoiceids"],$homeurl,$db);
    
    // Salida
    $out = new stdClass();
    $out->invoices=new stdClass();
    $out->invoices->sent = $sent;
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();    

?>
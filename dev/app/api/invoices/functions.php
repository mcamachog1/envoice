<?php
function loadDataByInvoice($invoices,$customerid,$db){
      $condition="";
      if (!is_null($invoices)) 
        $condition=" AND H.id IN (".avoidInjection($invoices,'dashes').")";
      $sql =
        "SELECT
            H.id,
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
            D.invoiceid = H.id WHERE H.customerid=$customerid AND tosend=0 ".
          " $condition ".
        " GROUP BY
            H.id,
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
        $record->amount = number_format($row['total'], 2, ",", ".");
        $record->email = $row['clientemail'];
        $record->name = $row['clientname'];
        $record->clientName=$row['daycoClientName'];
        $records[] = $record;
      }

      return $records;
    } 
?>
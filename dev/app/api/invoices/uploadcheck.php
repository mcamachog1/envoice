<?php
// /api/charges/checkfile.php

// Nota: el archivo viene en el parametro file para buscarlo en el $_FILE

    header("Content-Type:application/json");
    include_once("../connection.php");
    include_once("../utils.php");

    // parametros obligatorios
    $parmsob = array("sessionid");
    if (!parametrosValidos($_REQUEST, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
    // parametro especial: file
    
    if (!isset($_FILES["file"]))
        badEnd("400", array("msg"=>"No se adjuntó el archivo "));
    if ($_FILES["file"].["error"]<>0) 
        badEnd("400", array("msg"=>"Error en la carga del archivo"));
    
    
    // (A) OPEN FILE
    $handle = fopen($_FILES["file"]["tmp_name"], "r") or die("Error reading file!");
     
    // (B) READ LINE BY LINE
    while (($line = fgets($handle)) !== false) {
        if (substr_count($line,"Procesado por el banco") == 0 && substr($line,0,2)=="02")  {
            $referencia = substr($line,12,20);
            $cedula = substr($line,2,9);
            $err = substr($line,252,5);
            $motive = substr($line,256,20);
            //$print = $cedula."|".$referencia."\n";
            //echo nl2br($print);
            $sql = "INSERT INTO fileupload (originalid, contractid, err, motive, type, amount, number, expiration, dsc) SELECT $referencia, contractid, '$err', TRIM('$motive'), 'DEV', amount*(-1), number, expiration, CONCAT('Error enviado por el banco: ','$err','$motive',' correspondiente al contrato: ',contractid,' Tipo de cobro: ',dsc)  FROM movements WHERE id = 116 ";
            if (!$db->query($sql))
                badEnd("500", array("sql"=>$sql,"msg"=>$db->error));            
        }
    }
    // (C) CLOSE FILE
    fclose($handle);
    
    //Luego de vaciar la tabla se prepara el JSON de salida
    $sql= "SELECT c2.id AS customerid, c2.rif, CONCAT_WS(' ',name, surname) AS fullname, f.amount, f.amount AS amountformatted, f.dsc, f.number, f.originalid, f.motive, f.contractid FROM fileupload f INNER JOIN contracts c1 ON f.contractid = c1.id ".
            " INNER JOIN customers c2 ON c2.id = c1.customerid";
    if (!$rs = $db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    $totaldues=0;
    $totalamount=0;
    $records = array();
    while ($row = $rs->fetch_assoc()){
        $record = new stdClass;
        $record->id = (integer) $row["originalid"];
        $record->motive = $row["motive"];
        $record->contract = (integer) $row["contractid"]; 
        $record->customer = new stdClass;
        $record->customer->id = $row["customerid"];
        $record->customer->fullname = $row["fullname"];
        $record->customer->rif = $row["rif"];
        $record->due = new stdClass;

        $record->due->id =(integer) $row["originalid"];
        $record->due->number =$row["number"];
        $record->due->emisiondate = new stdClass;
        $record->due->emisiondate->date="";
        $record->due->emisiondate->formatted="";
        $record->due->expirationdate = new stdClass;
        $record->due->expirationdate->date="";
        $record->due->expirationdate->formatted="";
        $record->due->dsc=$row["dsc"];
        $record->amount = new stdClass;
        $record->amount->number = (float)$row["amount"];        
        $record->amount->formatted = $row["amountformatted"];        
    
        $totaldues++;
        $totalamount+=(float)$row["amount"];        
        $records[] = $record;
    }    
    
    $out = new stdClass;  
    $out->origin = new stdClass;
    $out->origin->type=1; // Domiciliaci��n
    $out->origin->source = new stdClass;
    $out->origin->source->id =20;
    $out->origin->source->name ="Banco de Venezuela S.A.C.A. Banco Universal";

    $out->dues = $records;
    $out->totals = new stdClass;
    $out->totals->dues=$totaldues;
    $out->totals->total= new stdClass;
    $out->totals->total->amount=$totalamount;
    $out->totals->total->formatted=$totalamount;    


    //$out->file =$_FILES["file"];
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>

<?php
// cms/api/reports/exportcsv

    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=informe.csv");

    
    $BOM = "\xEF\xBB\xBF"."\xEF\xBB\xBF";
    $fp = fopen('php://output', 'wb');
    fwrite($fp, $BOM);
    
    $line = array(
      0=>'CONTRATO',
      1=>'PLAN',
      2=>'CONTRATANTE',
      3=>'CLIENTE',
      4=>'CEDULA',
      5=>'NACIMIENTO',
      6=>'EDAD 1ra EMISION',
      7=>'EDAD',
      8=>'CIUDAD',
      9=>'DIRECCION DE HABITACION',
      10=>'TLF. HABITACION',
      11=>'TLF. OFICINA',
      12=>'TLF. CELULAR',
      13=>'CORREO',
      14=>'ULT. PAGO',
      15=>'SALDO',
      16=>'CUOTAS CARTULINA',
      17=>'VENDEDOR',
      18=>'AGENCIA',
      19=>'VENCIMIENTO',
      20=>'COBRADOR',
      21=>'FRECUENCIA',
      22=>'CUOTA',
      23=>'INF. COBRO',
      24=>'BANCO',
      25=>'CUENTA',
      26=>'NOMINA',
      27=>'1ra EMISION',
      28=>'EMISION ACTUAL');
        
      $record = array('F','R','T','D','U','M','M');
    
    $csvarray = array();
    $csvarray[]= $record;

    fputcsv($fp, $line, ';', '"');
    
    foreach($csvarray as $arr){
        fputcsv($fp,$arr,';');
    }
    
    fclose($fp);
    
    die();
    
   
?>
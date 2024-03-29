<?php

$url = "https://totalsoftware.la/~envoice/dev/app/api/invoices/update.php?";   



//create a new cURL resource

$ch = curl_init($url);



//setup request to send json via POST

$invoice = new stdClass();

$invoice->id=0;

$invoice->sessionid=1;



$invoice->seriecontrol = new stdClass();

    $invoice->seriecontrol->serie='AA';

    $invoice->seriecontrol->control='01';

$invoice->type='NDB';



$invoice->ctrref = new stdClass();



$invoice->ctrref->serie = 'AA';

$invoice->ctrref->control='01';

$invoice->ctrref->numero='00000001';



$invoice->issuedate = "2022-05-30";

$invoice->duedate = "2022-06-30";

$invoice->obs = "Factura 23";

$invoice->refnumber = "12345";

$invoice->client = new stdClass();

    $invoice->client->rif = "V12123123";

    $invoice->client->name = "Factura 16 INSERT";

    $invoice->client->mobile = "";

    $invoice->client->phone = "";

    $invoice->client->email = "update@gmail.com";

    $invoice->client->address = "";

$invoice->currencyrate = 10.50;    

$invoice->currency = "VES"; 

$invoice->discount = 10.5;

$array = array();

$invoice->details=&$array;



// Llenar arreglo de registros

$array = array();

$record = new stdClass();

$record->id = 0;

$record->invoiceid = 0;

$record->itemref = "001";

$record->itemdsc = "Factura 15 Item 1";

$record->qty = 2;

$record->unitprice = 1200;

$record->tax = 0.2;

$record->discount = 0.1;

$array[] = $record;



$record = new stdClass();

$record->id = 0;

$record->invoiceid = 0;

$record->itemref = "002";

$record->itemdsc = "Factura 15 Item 2";

$record->qty = 3;

$record->unitprice = 8200;

$record->tax = 0.2;

$record->discount = 0.1;

$array[] = $record;



$record = new stdClass();

$record->id = 0;

$record->invoiceid = 0;

$record->itemref = "003";

$record->itemdsc = "Factura 15 Item 3";

$record->qty = 3;

$record->unitprice = 8200;

$record->tax = 0.2;

$record->discount = 0.1;

$array[] = $record;



$payload = json_encode($invoice);



//attach encoded JSON string to the POST fields

curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);



//set the content type to application/json

curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));



//return response instead of outputting

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);



//execute the POST request

$result = curl_exec($ch);

echo $result;

//close cURL resource

curl_close($ch);




?>
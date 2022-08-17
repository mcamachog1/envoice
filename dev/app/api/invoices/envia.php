<?php

#$url = "https://totalsoftware.la/~envoice/dev/app/api/invoices/update.php?";   
$url = "http://localhost/totalsoftware/envoice/dev/app/api/invoices/update.php?";   

//create a new cURL resource

$ch = curl_init($url);

//setup request to send json via POST

$invoice = new stdClass();

$invoice->id=0;

$invoice->sessionid='tlFGcB3SlsCUSJAcouQIbXX595bciszp';


$invoice->seriecontrol = new stdClass();

    $invoice->seriecontrol->serie='';

    $invoice->seriecontrol->control='02';

$invoice->type='FAC';



$invoice->ctrref = new stdClass();


/*
$invoice->ctrref->serie = 'AA';
$invoice->ctrref->control='01';
$invoice->ctrref->numero='00000001';
*/

$invoice->issuedate = "2022-07-04";

$invoice->duedate = "2022-07-26";

$invoice->obs = "OBS";

$invoice->refnumber = "501";



$invoice->client = new stdClass();

    $invoice->client->rif = "J000000126";

    $invoice->client->name = "AR C.A.";

    $invoice->client->mobile = "0424 8822350";

    $invoice->client->phone = "";

    $invoice->client->email = "mcamachog@hotmail.com";

    $invoice->client->address = "El Marques 1";

$invoice->currencyrate = 0;    
$invoice->currency = ""; 
$invoice->discount = null;

$array = array();
$invoice->details=&$array;



// Llenar arreglo de registros

$array = array();


$record = new stdClass();
$record->itemref = "01";
$record->itemdsc = "Desc";
$record->unit = "Kg";
$record->qty = 3;
$record->unitprice = 8200;
$record->tax = 16;
$record->discount = 1;

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
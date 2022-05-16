<?php
$url = "https://totalsoftware.la/~envoice/dev/app/api/invoices/update.php?";   

//create a new cURL resource
$ch = curl_init($url);

//setup request to send json via POST
$invoice = new stdClass();
$invoice->id=0;
$invoice->sessionid=11;

$invoice->seriecontrol = new stdClass();
    $invoice->seriecontrol->serie='G';
    $invoice->seriecontrol->control='00';
$invoice->type='NDB';

$invoice->ctrref = new stdClass();

$invoice->ctrref->serie = 'G';
$invoice->ctrref->control='00';
$invoice->ctrref->numero='00000011';

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

/*
$content = json_encode("your data to be sent");

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER,
        array("Content-type: application/json"));
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

$json_response = curl_exec($curl);

$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

if ( $status != 201 ) {
    die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
}


curl_close($curl);

$response = json_decode($json_response, true);


$invoice->header = new stdClass();
$invoice->header->custmerid = 3;
$invoice->header->issuedate = "28-04-2022";
$invoice->header->duedate = "28-05-2022";
$invoice->header->refnumber = "12346";
$invoice->header->ctrnumber = "0124";
$invoice->header->clientrif = "J000999888";
$invoice->header->clientname = "El nombre";
$invoice->header->clientaddress = "address";
$invoice->header->mobilephone = "04248822350";
$invoice->header->clientemail = "micorreo@gmail.com";
$invoice->header->creationdate = "2022-04-28";
$invoice->header->currencyrate = 1;
$invoice->header->currency = "VES";
$invoice->header->discount = 0;

// Preparar arreglo de registros
$array = array();
$record = new stdClass();
$record->id = 0;
$record->invoiceid = 0;
$record->itemref = "referencia del item";
$record->itemdsc = "descripcion del item";
$record->qty = 2;
$record->unitprice = 1200;
$record->itemtax = 0.2;
$record->itemdiscount = 0.1;
$array[] = $record;

$record = new stdClass();
$record->id = 0;
$record->invoiceid = 0;
$record->itemref = "referencia del item2";
$record->itemdsc = "descripcion del item2";
$record->qty = 2;
$record->unitprice = 8200;
$record->itemtax = 0.2;
$record->itemdiscount = 0.1;
$array[] = $record;

$invoice->details = $array;


$header = '{
      "id": 0,
      "customerid": 3,
      "issuedate": "28/04/2022",
      "duedate": "28/05/2022",
      "refnumber": "12346",
      "ctrnumber": "0124",
      "clientrif": "J0000002",
      "clientname": "Factura con un solo detalle",
      "clientaddress":"address",
      "mobilephone": "04248822350",
      "clientemail": "micorreo@gmail.com",
      "creationdate": "2022-04-28",
      "currencyrate": "1",
      "currency": "VES",
      "discount": 0}';
$details= '[{
        "id":0,
        "invoiceid":0,
        "itemref":"referencia del item",
        "itemdsc":"descripcion del item",
        "qty":2,
        "unitprice":1200,
        "itemtax":0.2,
        "itemdiscount":0.1
    },
    {
        "id":0,
        "invoiceid":0,
        "itemref":"referencia del item2",
        "itemdsc":"descripcion del item2",
        "qty":2,
        "unitprice":1800,
        "itemtax":0.4,
        "itemdiscount":0.2
    }]';
      
*/
?>
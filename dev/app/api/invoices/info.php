<?php
    //  app/api/invoices/info.php
    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");
    
    function getSeriesControls($customerid, $db){
        $sql = "SELECT serie, initialcontrol FROM customers WHERE id=$customerid ";
        if (!$rs = $db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));     
        $records=array();
        if ($row = $rs->fetch_assoc()){
            $series = explode("-",$row['serie']);
            $controls = explode("-",$row['initialcontrol']);
            for ($i = 0; $i < count($series); $i++) {
                $record = new stdClass();
                $record->serie = $series[$i];
                $record->control = substr($controls[$i],0,2);
                $records[] = $record;
            }
        }
        return $records;
    }
    
    // parametros obligatorios
    $parmsob = array("sessionid");
    if (!parametrosValidos($_GET, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

    $sessionid= $_GET["sessionid"];

    // Validar user session
    $customerid = isSessionValid($db, $sessionid,array('ip'=>$_SERVER['REMOTE_ADDR'],'app'=>'APP','module'=>'invoices','dsc'=>"Se consultó serie y numero de control del cliente $customerid"));

    // Salida
    $out = new stdClass;    
    $out->seriescontrol=getSeriesControls($customerid,$db);

    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();    
?>

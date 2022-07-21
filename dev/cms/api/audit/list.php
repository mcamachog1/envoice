<?php
// cms/api/audit/list.php

// Librerías
    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");
    
// Parametros obligatorios
    $parmsob = array("module","user","datefrom","dateto","order","offset","numofrec","sessionid");
    if (!parametrosValidos($_REQUEST, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));

    // 1=>`datecreation`, 2=>`userid`, 3=>`app`, 4=>`module`, 5=>`dsc`, 6=>`ip`
    if (abs($_REQUEST["order"])>6)
      badEnd("400", array("msg"=>"El valor del campo orden esta fuera del rango de opciones permitido"));
    
    ($_REQUEST["order"]>0) ? ($type=' ASC ') : ($type=' DESC ');
    $strorderby=" ORDER BY ".abs($_REQUEST["order"])." $type ";  
    
    // get APP
    $pos = strrpos($_REQUEST["module"],"-");
    $app = strtoupper(substr($_REQUEST["module"],0,$pos)); 
    // get Modulo
    $module = strtolower(substr($_REQUEST["module"],$pos+1)); 
    if ($module=='*')
        $filter_module="";
    else
        $filter_module=" AND module= '$module' ";    
    // get user
    if ($_REQUEST['user']=='*')
        $filter_user="";
    else
        $filter_user=" AND userid= '".$_REQUEST['user']."' ";
// Validar sesion  
  $userid = isSessionValidCMS($db, $_REQUEST["sessionid"]); 

// Parametros opcionales
    $filter = "";
    if (isset($_REQUEST["filter"]) && strlen($_REQUEST["filter"])>0){
        $filtro = $_REQUEST["filter"];
        $filter .= " userid LIKE '%" . $filtro. "%' OR ";
        $filter .= " dsc LIKE '%" . $filtro. "%'  ";
    }

// Datos    
    $sql =  "SELECT `datecreation`, `userid`, `app`, `module`, `dsc`, `ip`,`id`" .
    "         FROM  audit WHERE app='$app' $filter_module $filter_user " ;
    if (strlen($filter)>0)
        $sql= $sql." AND (".$filter.") ";

    $sqlCnt =  "SELECT COUNT(*) cnt FROM (" . $sql  .") B";
    if (!$rsCnt=$db->query($sqlCnt))
        badEnd("500", array("sql"=>$sqlCnt,"msg"=>$db->error));
    else{
        $cnt = $rsCnt->fetch_assoc();
        $numofrecords = $cnt['cnt'];
    }

    // Se concatena el order by 
    $sql= $sql.$strorderby;
    // Se limita numero de registros segun parametros offset y numofrec
    $sql =  "SELECT A.* FROM (" . $sql . ") A " .
            "LIMIT " . $_REQUEST["offset"] . "," . $_REQUEST["numofrec"];

    // Se ejecuta el query principal
    if (!$rs=$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    
// Serialize
    $records=array();
    while ($row = $rs->fetch_assoc()){
        $record = new stdClass;
        $record->id=(integer)$row["id"];
        $record->created = new stdClass;        
        $record->created->date=$row['datecreation']; 
        $record->created->formatted=date("d/m/Y h:i a", strtotime($row['datecreation']));
        $record->usr=$row["userid"];
        $record->module=$row["module"];
        $record->dsc=$row["dsc"];      
        $record->ip=$row["ip"];
        $records[]=$record;
    }
    $out= new stdClass;
    $out->numofrecords=(integer)$numofrecords;
    $out->records =$records;

// Output
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>


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

// Validar sesion  
  $userid = isSessionValidCMS($db, $_REQUEST["sessionid"]); 

// Parametros opcionales
    $filter = "";
    if (isset($_REQUEST["filter"])){
        $filtro = $_REQUEST["filter"];
        $filter .= " userid LIKE '%" . $filtro. "%' OR ";
        $filter .= " dsc LIKE '%" . $filtro. "%'  ";
    }
 

// Datos    
    $sql =  "SELECT `datecreation`, `userid`, `app`, `module`, `dsc`, `ip`" .
    "         FROM  audit " ;

    if (strlen($filter)>0)
        $sql= $sql." WHERE ".$filter." ";

    $sqlCnt =  "SELECT COUNT(*) cnt FROM (" . $sql  .") B";
    if (!$rsCnt=$db->query($sqlCnt)){
        badEnd("500", array("sql"=>$sqlCnt,"msg"=>$db->error));
    }else{
        $cnt = $rsCnt->fetch_assoc();
        $numofrecords = $cnt['cnt'];
    }

    // Se concatena el order by 
    $sql= $sql.$strorderby;
    // Se limita numero de registros segun parametros offset y numrecordstoshow
    $sql =  "SELECT A.* FROM (" . $sql . ") A " .
            "LIMIT " . $_REQUEST["offset"] . "," . $_REQUEST["numofrec"];

    // Se ejecuta el query principal
    if (!$rs=$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    $records=array();
    
// Serialize
    while ($row = $rs->fetch_assoc()){
        $record = new stdClass;
        $record->id=(integer)$row["id"];
        $record->name=$row["name"];
        $record->address= $row["address"];
        $record->contact=new stdClass();
            $record->contact->name=$row["contactname"];
            $record->contact->email=$row["contactemail"];
        $record->rif=$row["rif"];
        $record->phone= $row["phone"];
        
        $record->seniat= series($row["serie"],$row["initialcontrol"],$row["nextcontrol"]);
        $record->image="";        
        $record->ftp=new stdClass();  
        $record->ftp->usr=$row["ftpusr"];      
        $record->ftp->pwd=$row["ftppwd"];
        $record->status=new stdClass();        
        if ($row["status"]==1) {
            $record->status->id=1;
            $record->status->dsc="Activo";
        }
        else {
            $record->status->id=0;
            $record->status->dsc="Inactivo";
        }
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


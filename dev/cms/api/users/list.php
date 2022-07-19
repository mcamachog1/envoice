<?php
// cms/api/users/list.php

    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");
   
    // parametros obligatorios
    $parmsob = array("order","offset","numofrec","sessionid");
    if (!parametrosValidos($_REQUEST, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
    $filter="";
    // parametros opcionales
    if (isset($_REQUEST["filter"])){
        $filtro = explode("+",$_REQUEST["filter"]);
        for ($i=0; $i<sizeof($filtro); $i++){
            if ($i>0) $filter .= " AND ";
            $filter .= "(";
            if ($filtro[$i]=='Activo')
                $filter .= "U.status=1 OR ";
            if ($filtro[$i]=='Inactivo')
                $filter .= "U.status=0 OR ";
            $filter .= "U.name LIKE '%" . $filtro[$i] . "%' OR ";
            $filter .= "U.usr LIKE '%" . $filtro[$i] . "%' ";
            $filter .= ") ";
        }
    }
 
    $userid = isSessionValidCMS($db, $_REQUEST["sessionid"]);

    // order
    $strorderby="";
    if ($_REQUEST["order"]>0)
        $type=' ASC ';
    else $type=' DESC ';
    switch (abs($_REQUEST["order"])) {
        case 1:
            $strorderby=" ORDER BY U.datecreated ".$type;
            break;
        case 2:
            $strorderby=" ORDER BY U.name ".$type;
            break;
        case 3:
            $strorderby=" ORDER BY U.usr ".$type;
            break;
        case 4:
            $strorderby=" ORDER BY U.status ".$type;
            break;
        default:
            badEnd("400", array('msg'=>"El valor del campo orden esta fuera del rango de opciones permitido"));
    }   

    
    $sql =  "SELECT     U.id, U.usr, U.name, U.status " .
            "FROM       users U  WHERE U.id <> '-1' " ;

    if (strlen($filter)>0)
        $sql= $sql." AND ".$filter." ";

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
    while ($row = $rs->fetch_assoc()){
        $record = new stdClass;
        $record->id=(integer)$row["id"];
        $record->usr=$row["usr"];
        $record->name= $row["name"];
        $record->status=new stdClass;
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

    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
    
?>
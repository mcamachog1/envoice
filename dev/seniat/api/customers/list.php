<?php
// seniat/api/customers/list.php

    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");
    include_once("../functions.php");
    
    // parametros obligatorios
    $parmsob = array("order","offset","numofrec","sessionid");
    if (!parametrosValidos($_REQUEST, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
 
    // parametros opcionales
    $filter = "";
    if (isset($_REQUEST["filter"])){
        $filtro = explode("+",$_REQUEST["filter"]);
        for ($i=0; $i<sizeof($filtro); $i++){
            if ($i>0) $filter .= " AND ";
            $filter .= "(";
            if ($filtro[$i]=='Activo')
                $filter .= "C.status=1 OR ";
            if ($filtro[$i]=='Inactivo')
                $filter .= "C.status=0 OR ";
            $filter .= "C.name LIKE '%" . $filtro[$i] . "%' OR ";
            $filter .= "C.rif LIKE '%" . $filtro[$i] . "%' OR ";
            $filter .= "C.contactemail LIKE '%" . $filtro[$i] . "%' ";            
            $filter .= ") ";
        }
    }
   
    validSession($db, $_REQUEST["sessionid"]);
    
    // order
    $strorderby="";
    if ($_REQUEST["order"]>0)
        $type=' ASC ';
    else $type=' DESC ';
    switch (abs($_REQUEST["order"])) {
        case 1:
            $strorderby=" ORDER BY C.datecreated ".$type;
            break;
        case 2:
            $strorderby=" ORDER BY C.name ".$type;
            break;
        case 3:
            $strorderby=" ORDER BY C.contactname ".$type;
            break;
        case 4:
            $strorderby=" ORDER BY C.phone ".$type;
            break;
        case 5:
            $strorderby=" ORDER BY C.contactemail ".$type;
            break;            
        default:
            badEnd("400", array('msg'=>"El valor del campo orden esta fuera del rango de opciones permitido"));
    }   

    $sql =  "SELECT     * " .
            "FROM       customers C " ;

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
        // Image
        if (!is_null($row["imgtype"])) {
            switch ($row["imgtype"]){
                case "image/png":
                    $ext = ".png";
                    break;
                case "image/jpeg":
                case "image/jpg":
                    $ext = ".jpg";
                    break;
                default:
                    badEnd("500", array(id=>1,msg=>"El formato del documento debe ser PNG o JPG"));
            }
            $record->image="./uploads/customers/".$row["id"].$ext;
        }
        // Fin Image
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

    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
    
    
    
?>
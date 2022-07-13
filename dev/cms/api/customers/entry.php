<?php
// cms/api/users/entry

    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");
    
    // parametros obligatorios
    $parmsob = array("id","sessionid");
    if (!parametrosValidos($_REQUEST, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
    
    $userid = isSessionValidCMS($db, $_REQUEST["sessionid"],array('ip'=>$_SERVER['REMOTE_ADDR'],'app'=>'CMS','module'=>'customers','dsc'=>'Consultar cliente Dayco'));

    $sql="SELECT * ".
        " FROM customers ".
        " WHERE id=".intval ($_REQUEST["id"]);
    
    // Se ejecuta el query principal
    if (!$rs=$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    if ($row = $rs->fetch_assoc()){
        $record = new stdClass;
        $record->id=(integer)$row["id"];
        $record->name=$row["name"];
        $record->address= $row["address"];
        $record->contact=new stdClass();
        $record->contact->name=$row["contactname"];
        $record->contact->email=$row["contactemail"];
        $record->rif=$row["rif"];
        $record->phone= $row["phone"];
        $record->seniat=series($row["serie"],$row["initialcontrol"],$row["nextcontrol"]);
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
    }

    $out = new stdClass;  
    $out->entry =$record;

    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>
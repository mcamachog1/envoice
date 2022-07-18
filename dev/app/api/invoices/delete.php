<?php
// app/api/invoices/delete
    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");
    
    // parametros obligatorios
    $parmsob = array("id","sessionid");
    if (!parametrosValidos($_REQUEST, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
    
    $ids=avoidInjection($_REQUEST["id"],'dashes');   
    $customerid = isSessionValid($db, $_REQUEST["sessionid"]);
    // Validar que existe algun registro y que el documento esté por enviar
    $sql="SELECT COUNT(*) Cnt FROM invoiceheader WHERE id IN ($ids) ".
        " AND customerid=$customerid AND tosend=0 AND sentdate IS NULL ";
    if (!$rs=$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    $row = $rs->fetch_assoc();
    if ($row["Cnt"]==0)
        badEnd("204", array('msg'=>"El registro no existe"));

    // Seleccionar los ids que pertenecen al cliente 
    $sql="SELECT id, refnumber FROM invoiceheader WHERE id IN ($ids)".
    " AND customerid=$customerid AND tosend=0 AND sentdate IS NULL";
    if (!$rs=$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    $ids_array = array();
    $refs_array = array();
    while ($row = $rs->fetch_assoc()) {
        $ids_array [] = $row["id"];
        $refs_array[] = $row["refnumber"];
    }
    $ids=join(",",$ids_array);
    $refs=join(",",$refs_array);

    $countdocs=count($ids_array);
    $sql="UPDATE invoiceheader SET canceldate= NOW() WHERE id IN ($ids) AND customerid=$customerid";

    if (!$db->query($sql)) 
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    if ($db->affected_rows==0)
        badEnd("304", array('msg'=>"El registro existe pero no se pudo anular"));
    // Audit
    if ($countdocs<10)
        insertAudit($db,getEmail($_REQUEST["sessionid"],'APP',$db),$_SERVER['REMOTE_ADDR'],'APP','invoices',"Se anularon $countdocs documentos ($refs)");
    else
        insertAudit($db,getEmail($_REQUEST["sessionid"],'APP',$db),$_SERVER['REMOTE_ADDR'],'APP','invoices',"Se anularon $countdocs documentos");        

    $out = new stdClass;
    $out->id =$ids;
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>

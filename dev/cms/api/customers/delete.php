<?php
// cms/api/customers/delete
    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");
    
    // parametros obligatorios
    $parmsob = array("id","sessionid");
    if (!parametrosValidos($_REQUEST, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
    
    $userid = isSessionValidCMS($db, $_REQUEST["sessionid"]);
    // Validar que existe el registro
    $sql="SELECT COUNT(*) Cnt FROM customers WHERE id=".$_REQUEST["id"];
    if (!$rs=$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    $row = $rs->fetch_assoc();
    if ($row["Cnt"]==0)
        badEnd("204", array('msg'=>"El registro no existe"));
    
    // Si el cliente tiene facturas asociadas enviar mensaje
    $sql="SELECT COUNT(*) Cnt FROM invoiceheader WHERE customerid=".$_REQUEST["id"];
    if (!$rs=$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    $row = $rs->fetch_assoc();
    if ($row["Cnt"]>0)
        badEnd("400", array('msg'=>"El cliente tiene facturas asociadas que no permiten eliminarlo"));
    
    
    $sql="DELETE FROM customers WHERE id=".$_REQUEST["id"];
    if (!$db->query($sql)) 
        badEnd("304", array('msg'=>"El registro existe pero no se pudo eliminar"));
    $id=$_REQUEST["id"];    

    $out = new stdClass;
    $out->id =(integer)$id;
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>

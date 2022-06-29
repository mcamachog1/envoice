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
    $customerid = isSessionValid($db, $_REQUEST["sessionid"],array('ip'=>$_SERVER['REMOTE_ADDR'],'app'=>'app','module'=>'invoices','dsc'=>'delete.php'));
    // Validar que existe algun registro
    $sql="SELECT COUNT(*) Cnt FROM invoiceheader WHERE id IN ($ids) AND customerid=$customerid";
    if (!$rs=$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    $row = $rs->fetch_assoc();
    if ($row["Cnt"]==0)
        badEnd("204", array('msg'=>"El registro no existe"));

    // Seleccionar los ids que pertenecen al cliente (por si acaso)
    $sql="SELECT id FROM invoiceheader WHERE id IN ($ids) AND customerid=$customerid";
    if (!$rs=$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    $ids_array = array();
    while ($row = $rs->fetch_assoc()) 
        $ids_array [] = $row["id"];
    $ids=join(",",$ids_array);

    
    $sql="DELETE FROM invoiceheader WHERE id IN ($ids) AND customerid=$customerid";
    if (!$db->query($sql)) 
        badEnd("304", array('msg'=>"El registro existe pero no se pudo eliminar"));
 

    $out = new stdClass;
    $out->id =$ids;
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>

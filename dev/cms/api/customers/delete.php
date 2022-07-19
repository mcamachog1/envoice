<?php
// cms/api/customers/delete
    header("Content-Type:application/json");
    include_once("../../../settings/dbconn.php");
    include_once("../../../settings/utils.php");
    
    function getCustomerEmail($db,$customerid){
        $sql = "SELECT contactemail FROM customers WHERE id=$customerid ";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        
        if (!$row = $rs->fetch_assoc())
            badEnd("400", array("msg"=>"Cliente id: $customerid no encontrado"));
        
        return $row['contactemail'];
    }

    // parametros obligatorios
    $parmsob = array("id","sessionid");
    if (!parametrosValidos($_REQUEST, $parmsob))
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
    // Validar session 
    $userid = isSessionValidCMS($db, $_REQUEST["sessionid"]);
    $customeremail = getCustomerEmail($db,$_REQUEST["id"]); 
    $email=getEmail($_REQUEST["sessionid"],'CMS',$db);    
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


// Auditoria  
   
    insertAudit($db,$email,$_SERVER['REMOTE_ADDR'],'CMS','customers',"Se eliminó un cliente - $customeremail");        

    $out = new stdClass;
    $out->id =(integer)$_REQUEST["id"];
    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>

<?php    
    function getUserCMS($db,$id){
        $sql ="SELECT id, usr FROM users WHERE id = $id";
        if (!$rs=$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error,"errfunction"=>'getUserCMS'));
        if (!$row = $rs->fetch_assoc())
            badEnd("400", array("msg"=>"Cliente id $id no encontrado"));
        return $row['usr'];         

    }
?>
<?php
// seniat/api/security/forgot.php

    header("Content-Type:application/json");
    include("../../../settings/dbconn.php");
    include("../../../settings/utils.php");
    include("../functions.php");
    require '../../../hooks/PHPMailer5/PHPMailerAutoload.php';
    date_default_timezone_set('Etc/UTC');
    
    
    // parametros obligatorios
    $parmsob = array("email");
    if (!parametrosValidos($_GET, $parmsob)){
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
    }
    

    // Validar que el email existe
    $email = avoidinjection(trim($_GET["email"]),'email');
    $user=validateEmail($email,$db);
    
    // se genera el hash que servirá para la validar la recuperación
    $hash = randomString(128);
    $name_hash = strtolower($email)."_hash";
    
    // Actualizar hash
    $sql ="UPDATE preferences SET value='$hash' WHERE LOWER(name)='$name_hash'";
    if (!$db->query($sql))
        badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    // Si no existe el registro hash, se crea
    if ($db->affected_rows==0){
        $sql ="INSERT INTO preferences (name, value) VALUES ('$name_hash','$hash')";
        if (!$db->query($sql))
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
    }    


    $subject = "Recuperación de clave SENIAT DaycoPrint";    
    $body = 
        "<html>" .
        "<head>" .
        "<title></title>" .
        "</head>" .
        "<body style='font-family:sans-serif'>" .
    "<div style='padding:20px;width:100%;background-color:gray'>" .
    "<div style='padding:10px;background-color:white;max-width:600px;width:95%;margin-left:auto;margin-right:auto;border-radius:4px'>" .
    "<div style='display:table;border-spacing:30px;width:95%;margin-left:auto;margin-right:auto'>" .
    "<div style='display:table-row'>" .
    "<div style='display:table-cell'><img style='max-width:30%' src='".$homeurl."img/logo.png' /></div>" .
    "</div>" .
    "<div style='display:table-row'>" .
    "<div style='text-align:center;display:table-cell;font-size:200%;font-weight:bold'>" .
    "<br/>Recuperaci&oacute;n de Clave SENIAT DaycoPrint<br/>&nbsp;" .
    "</div>" .
    "</div>" .
    "<div style='display:table-row'>" .
    "<div style='display:table-cell;font-size:120%;text-align:center'>Para definir una nueva contraseña debe presionar el bot&oacute;n de Recuperar</div>" .
    "</div>" .
    "<div style='display:table-row'>" .
    "<div style='display:table-cell'>" .
    "<a href=$homeurl../seniat/?id=login&sid=recoverpwd&hash=".$hash."&email=".$email.">" .
    "<div style='font-weight:bold;margin-left:auto;margin-right:auto;border-radius:12px;cursor: pointer;margin-top: 50px;margin-bottom: 50px;color: white;line-height: 50px;text-align:center;background-color: #0033A0;height: 50px;width: 320px;border: none;transition: all ease 300ms;'>RECUPERAR</div>" .
    "</a>" .
    "</div>" .
    "</div>" .
    "<div style='display:table-row'>" .
    "<div style='display:table-cell'>Gracias de antemano<br />" .
        "Equipo de DaycoPrint</div>" .
        "</div>" .
        "</div>" .
        "</div>" .
        "</div>" .
        "</body>" .
        "</html>";

    
    $altbody = "Hola ".$user.", para recuperar su clave diríjase a la dirección indicada a continuación:\n\n".
            $homeurl . "?id=login&sid=recoverpwd&hash=".$hash."&email=".$email."\n\n".
            "Gracias de antemano\n" .
            "Equipo de DaycoPrint";
    //enviarCorreoSMTP($fromeMail, $email, $subject, $message,"");        
    enviarCorreo("no-responder@espacioseguroDayco.com", $email, $subject, $body, $altbody);

    //mostramos los datos recibidos
    $out = new stdClass;
    $out->email = $email;

    header("HTTP/1.1 200");
    echo (json_encode($out));
    die();
?>

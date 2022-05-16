<?php
//  cms/api/security/forgot.php
    header("Content-Type:application/json");
    include("../../../settings/dbconn.php");
    include("../../../settings/utils.php");
    require '../../../hooks/PHPMailer5/PHPMailerAutoload.php';
    date_default_timezone_set('Etc/UTC');

    // parametros obligatorios
    $parmsob = array("email");
    if (!parametrosValidos($_GET, $parmsob)){
        badEnd("400", array("msg"=>"Parametros obligatorios " . implode(", ", $parmsob)));
    }
    

        //definimos las variables y las rellenamos con los datos recibidos del GET
        $email = $_GET["email"];
        $hash = randomString(128);
        
        // se genera el hash que servirá para la validar la recuperación
        $sql =  "UPDATE users " .
                "SET    hashrecover = '".$hash."' ".
                "WHERE  UPPER(usr)='".strtoupper($email)."' ";
        //ejecutamos el sql        
        if (!$db->query($sql)){
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        }
        //revisamos la respuesta del query y verificamos si fue ejecutada correctamente 
        if ($db->affected_rows == 0){
            badEnd("401", array("msg"=>"No tenemos ningún usuario registrado con ese email"));
        }
        
        // Conseguir el nombre del usuario
        $sql =  "SELECT name " .
                "FROM   users " .
                "WHERE  UPPER(usr)='".strtoupper($email)."' ";
        //ejecutamos el sql        
        if (!$rs = $db->query($sql)){
            badEnd("500", array("sql"=>$sql,"msg"=>$db->error));
        }
        $username = "--";
        if ($row = $rs->fetch_assoc()){
            $username = $row["name"];
        }    
        $subject = "Recuperación de clave CMS DaycoPrint";
        
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
        //"<div style='display:table-cell'><img src='".file_get_contents('logobase64.txt', __DIR__)."' /></div>" .
        "<div style='display:table-cell'><img style='max-width:30%' src='".$homeurl."img/logo.png' /></div>" .
        "</div>" .
        "<div style='display:table-row'>" .
        "<div style='text-align:center;display:table-cell;font-size:200%;font-weight:bold'>" .
        "<br/>Recuperaci&oacute;n de Clave del CMS<br/>&nbsp;" .
        "</div>" .
        "</div>" .
        "<div style='display:table-row'>" .
        "<div style='display:table-cell;font-size:120%;text-align:center'>Hola <b>".$username."</b>, para recuperar tu clave debes presionar el bot&oacute;n indicado m&aacute;s abajo</div>" .
        "</div>" .
        "<div style='display:table-row'>" .
        "<div style='display:table-cell'>" .
        "<a href='".$homeurl . "?id=login&sid=recoverpwd&hash=".$hash."&email=".$email."'>" .
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
    
        
        $altbody = "Hola ".$username.", para recuperar su clave diríjase a la dirección indicada a continuación:\n\n".
                $homeurl . "?id=login&sid=recoverpwd&hash=".$hash."&email=".$email."\n\n".
                "Gracias de antemano\n" .
                "Equipo de DaycoPrint";
        //enviarCorreoSMTP($fromeMail, $email, $subject, $message,"");        
        enviarCorreo("no-responder@espacioseguroDayco.com", $email, $subject, $body, $altbody);

        //mostramos los datos recibidos
        $out = new stdClass;
        $out->email = $email;
        $out->altbody = $altbody;
        //lanzamos un codigo que todo salio bien
        header("HTTP/1.1 200");
        echo (json_encode($out));
        die();
    
    
?>
<?php
// seniat/api/invoices/sendcheck.php

    header("Content-Type:application/json");
    include("../../../settings/dbconn.php");
    include("../../../settings/utils.php");
    require '../../../hooks/PHPMailer5/PHPMailerAutoload.php';

?>
<?php
    $id = "login";
    if (isset($_REQUEST["id"]))
        $id = $_REQUEST["id"];
    $sid = "main";
    if (isset($_REQUEST["sid"]))
        $sid = $_REQUEST["sid"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./fonts/all.css" rel="stylesheet">
    <link href="./fonts/fontawesome.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/main.css">
    <link href="./img/favicon.png" rel="icon" type="image/png" rel="shortcut icon">
    <?php
        if (file_exists("./css/".$id.".css"))
            echo ('<link rel="stylesheet" href="./css/'.$id.'.css">');
        if (file_exists("./css/".$id."/".$sid.".css"))
            echo ('<link rel="stylesheet" href="./css/'.$id."/".$sid.'.css">');
    ?>
    <script type="text/javascript" src="./js/main.js"></script>
    <?php
        if (file_exists("./js/".$id.".js"))
            echo ('<script type="text/javascript" src="./js/'.$id.'.js"></script>');
        if (file_exists("./js/".$id."/".$sid.".js"))
            echo ('<script type="text/javascript" src="./js/'.$id.'/'.$sid.'.js"></script>');
    ?>
    <title>DaycoPrint - APP</title>
</head>
<body>
<?php
    if (file_exists("./content/".$id.".php"))
        include_once("./content/".$id.".php");
?>
<script type="text/javascript" src="./js/all.js"></script>

</body>
</html>
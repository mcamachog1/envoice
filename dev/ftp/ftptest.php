<?php
$message = "";
if(isset($_POST['submit'])){ //check if form was submitted
    include_once("../settings/dbconn.php");
    include_once("../settings/utils.php");

    $customerfolder=$_POST["client_id"];
    if (isset($_FILES["invoicesfile"]) and count($_FILES)>0){
      $filename = $_FILES["invoicesfile"]["name"];
      if (!$_FILES["invoicesfile"]["tmp_name"])
          badEnd("500", array(id=>0,msg=>"Archivo no enviado"));
      $type=$_FILES["invoicesfile"]["type"];
      switch ($_FILES["invoicesfile"]["type"]){
          case "text/plain":
              $ext = ".txt";
              break;
          case "text/csv":
              $ext = ".csv";
              break;
          default:
              badEnd("500", array(id=>1,msg=>"El formato del documento debe ser CSV o TXT"));
      }
      $ruta="../ftpfiles/$customerfolder/$filename";
      if(move_uploaded_file($_FILES["invoicesfile"]["tmp_name"], $ruta)){
        $message="Archivo copiado exitosamente";
      } else 
          badEnd("500", array(id=>2,msg=>"Error en funcion move_uploaded_file"));
    }
}    
?>

<html>
  <head>
    <title>Prueba ftp</title>
    <style>
      div {
        padding: 10px 10px;
        margin: 1px solid black;
        margin: 15px 15px;
      }
      .hide { position:absolute; top:-1px; left:-1px; width:1px; height:1px; }      
    </style>
  </head>
  <body>
    <div>
      <ol>
        <li>     
          <form
            action=""
            method="post"
            enctype="multipart/form-data"
            target="hiddenFrame">
          
            <p>Selecciona el archivo para colocarlo en el directorio del cliente 
            indicado y simular la transferencia por ftp:</p>
            <br>
            <input type="file" name="invoicesfile" />
            <br><br>
            <input
              type="text"
              name="client_id"
              placeholder="XXX-Codigo del cliente"
            />
            <br><br>
            <input type="submit" value="Upload File" name="submit" />
          </form>
        </li>

        <strong><?php echo $message; ?></strong><br>
        <li>
          <form action="ftp.php">
            <input type="submit" value="Procesar archivos" />
          </form>
        </li>
      </ul>
    </div>
  </body>
</html>
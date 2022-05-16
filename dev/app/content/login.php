<div class="bigContainer">
<div class="containerBox">
<div class="container" id="logoContainer">
    <div class="logoSect textCenter">
        <img class="logo" src="./img/logo.svg"/>
    </div>
</div>
<?php
    if (file_exists("./content/".$id."/".$sid.".php")){
        include_once("./content/".$id."/".$sid.".php");
    }
?>
</div>
<div class="footer textCenter" id="copyright">
    © 2022. Totalsoftware de Venezuela -  Todos los derechos Reservados 
</div>
</div>

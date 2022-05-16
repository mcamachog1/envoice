
<div class="container containerHeader">
    <div class="headerSect textCenter" id="header">
        <div class="logoSect"><div class="logoMenu logoHead" id="goToMain"></div></div>
       
    </div> 
    <div class="menuSect">
        <a class="itemMenu itemMenuDisabled" id="dashboard"><b>Dashboard</b></a>
        <a class="itemMenu itemMenuReady" id="clients"><b>Clientes</b></a>
        <a class="itemMenu itemMenuDisabled" id="operations"><b>Operaciones</b></a>
        <a class="itemMenu itemMenuReady" id="users"><b>Usuarios</b></a>
        <div class="leaveSect"><span id="usernameMenu"></span><img class="logoMenu" id="logOut" src="./img/logout.svg"></div>
    </div>
</div>

<div id="containerHome">
<?php
    if (file_exists("./content/".$id."/".$sid.".php")){
        include_once("./content/".$id."/".$sid.".php");
    }
?>
</div>

<div class="footer textCenter" id="copyright"><p>© 2022. Totalsoftware de Venezuela -  Todos los derechos Reservados </p></div>
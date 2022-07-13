
<div class="container containerHeader">
    <div class="headerSect textCenter" id="header">
        <div class="logoSect"><div class="logoMenu logoHead" id="goToMain"></div></div>
       
    </div> 
    <div class="menuSect">
        <a class="itemMenu itemMenuDisabled" id="dashboard"><b>Dashboard</b></a>
        <a class="itemMenu itemMenuReady" id="clients"><b>Clientes</b></a>
        <a class="itemMenu itemMenuReady" id="operations"><b>Operaciones</b></a>
        <a class="itemMenu itemMenuReady" id="users"><b>Usuarios</b></a>
        <div class="itemMenu itemSubMenuReady" id="config">
            <b>Configuración</b>
            <div class="subMenu">
                <a class="itemMenu itemMenuReady" id="audit">Auditoría</a>
                <a class="itemMenu itemMenuReady" id="seniatusers">Usuarios SENIAT</a>
            </div>
        </div>
        <div class="leaveSect"><span id="usernameMenu"></span><i style="margin-left:5px" id="logOut" class="fa-solid fa-right-from-bracket"></i></div>
    </div>
</div>

<div id="containerHome">
<?php
    if (file_exists("./content/".$id."/".$sid.".php")){
        include_once("./content/".$id."/".$sid.".php");
    }
?>
</div>
<div class="invviewer" id="invViewer">
    <div class="invvHeadTbl">
        <div class="invvCellLeft">
            <span id="backViewer"><i class="fa fa-arrow-left invVBtn"></i></span>
            <span id="invName">
                Fact. 000187
            </span>
        </div>
        <div class="invvCellCenter">
            <div class="invvStatusLbl penStatus">
                <div class="statusDscCell">
                    <span id="statusVDsc">Estatus</span>
                </div>
                <div class="statusIcCell">
                    <span ><i id="viewStatusBtn" class="invVBtn fa-solid fa-sort-down" ></i></span>
                </div>
                <div class="statusPopup" id="statusPopup">
                    <div class="statusPopTbl">Creada <span id="viewIssueDate"></span></div>
                    <div class="statusPopTbl">Enviada <span id="viewSentDate"></span></div>
                    <div class="statusPopTbl">Leída <span id="viewReadDate"></span></div>
                </div>
            </div>
        </div>
        <div class="invvCellRight">
            <i class="fa-solid fa-print invVBtn" id="printView"></i>
            <!--<i class="fa-solid fa-download invVBtn" style="padding-left:20px;position:relative;">
            <a class="downloadViewer" id="downloadView" download="fact.pdf"></a>-->
            </i>
        </div>
    </div>
    <div class="contentPage">
        <iframe class="frameView" id="frameView" name="theFrame"></iframe>
    </div>
</div>

<div class="footer textCenter" id="copyright"><p>© 2022. Totalsoftware de Venezuela -  Todos los derechos Reservados </p></div>

<div class="menuSect">
    <div class="leaveSect">
        <span class="usernameMenu" id="usernameMenu"></span>
        <i id="logOut" class="fa-solid fa-right-from-bracket"></i>
        <!--<img class="logoMenu" id="logOut" src="./img/logout.svg">-->
    </div>
</div>
<?php
    if (file_exists("./content/".$id."/".$sid.".php")){
        include_once("./content/".$id."/".$sid.".php");
    }
?>

<div class="footer textCenter" id="copyright"><p>© 2022. Totalsoftware de Venezuela -  Todos los derechos Reservados </p></div>
<div class="invviewer" id="invViewer">
    <div class="invvHeadTbl">
        <div class="invvCellLeft">
            <span id="backViewer"><i class="fa fa-arrow-left invVBtn"></i></span>
            <span id="invName">
                Fact. 000187
            </span>
        </div>
        <div class="invvCellCenter">
            
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
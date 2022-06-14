<div class="bigContainer">
    <div class="bigCntTbl">
        <div class="bigCntCell">
            <div class="logoOut"></div>
            <div class="containerBox">
                <div class="container" id="logoContainer">
                    <div class="logoSect textCenter">
                        <div class="logo"></div>
                    </div>
                </div>
                <?php
                    if (file_exists("./content/".$id."/".$sid.".php")){
                        include_once("./content/".$id."/".$sid.".php");
                    }
                ?>
            </div>
        </div>
    </div>
    <div class="footer textCenter" id="copyright">
        © 2022. Totalsoftware de Venezuela -  Todos los derechos Reservados 
    </div>
</div>

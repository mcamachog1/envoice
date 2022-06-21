<!DOCTYPE html>
<html lang="es">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="./fonts/all.css" rel="stylesheet">
        <link href="./fonts/fontawesome.css" rel="stylesheet">
        <link href="./img/favicon.png" rel="icon" type="image/png" rel="shortcut icon">
        <link rel="stylesheet" href="./css/view.css">
        <title>DaycoPrint - Ver</title>
    </head>
    <body>
        <div class="invviewer" id="invViewer">
            <div class="invvHeadTbl">
                <div class="invvCellLeft">
                    <span id="invName">
                        Fact. 000187
                    </span>
                </div>
                <div class="invvCellCenter">
                    <div class="invvStatusLbl penStatus">
                        <div class="statusDscCell">
                            <span id="statusVDsc">Por Enviar</span>
                        </div>
                        <div class="statusIcCell">
                            <span ><i id="viewStatusBtn" class="invVBtn fa-solid fa-sort-down" ></i></span>
                        </div>
                        <div class="statusPopup" id="statusPopup">
                            <div class="statusPopTbl">Creación <span id="viewIssueDate">28-02-2022</span></div>
                            <div class="statusPopTbl">Envío <span></span></div>
                            <div class="statusPopTbl">Leída <span></span></div>
                        </div>
                    </div>
                </div>
                <div class="invvCellRight">
                    <i class="fa-solid fa-print invVBtn" id="printView"></i>
                    </i>
                </div>
            </div>
            <div class="contentPage">  
                <iframe class="frameView" id="frameView" name="theFrame"></iframe>
            </div>
        </div>
        <script type="text/javascript" src="./js/view.js"></script>
    </body>
</html>

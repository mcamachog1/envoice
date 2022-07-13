
<div class="pageCnt">
    <div class="menuSect">
        <div class="leaveSect">
            <span id="usernameMenu"></span>
            <i id="logOut" class="fa-solid fa-right-from-bracket"></i>
            <!--<img class="logoMenu" id="logOut" src="./img/logout.svg">-->
        </div>
    </div>
    <div class="page pageList" id="pageList" style="display:block;opacity:1">
        <div class="container containerHeader">
            <div class="headerSect textCenter" id="header">
                <div class="logoSect"><img class="logoMenu" id="goToMain" src="./img/logo.svg"></div>
                <div id="searchFilters" class="searchFilters">
                    <div class="filtTblCnt">
                        <div class="filtSrchCell">
                            <input class="prdSearch" type="search" id="mySearch" name="Search" placeholder="Buscar">
                            <span id="iconSearch" class="iconSearch"><i class="fa fa-search" aria-hidden="true"></i></span>
                        </div>
                        <div class="filtSrchCell filtRegCell">
                            <select id="numofrecFilt">
                                <option value="10">Ver 10 Registros</option>
                                <option value="25" selected>Ver 25 Registros</option>
                                <option value="50">Ver 50 Registros</option>
                                <option value="100">Ver 100 Registros</option>
                            </select>
                        </div>
                        <div class="filtTblCell">
                            <div class="dropdown">
                                
                                <button id="filtersButtom" class="btnIcon dropbtn" >
                                    <div class="filtIcCell">
                                        <i class="fas fa-sliders-h icon"></i>
                                    </div>
                                    <div class="filtLblCell">
                                        Filtrar
                                    </div>
                                </button>
                                <div id="filterDropdown" class="dropdown-content">
                                <div class="nexoFiltLine"></div>
                                    <div class="cntFiltSect">
                                        <div class="stdBtn">
                                            <label>
                                                MOSTRAR
                                            </label>
                                            <select name="periodo" id="periodoSelect">
                                                <option value="">Periodo</option>
                                                <option value="0">Última semana</option>
                                                <option value="1">Últimos 15 días</option>
                                                <option value="2">Último mes</option>
                                                <option value="3">Mes anterior</option>
                                                <option value="4" selected>Este año</option>
                                                <option value="5">Personalizado</option>

                                            </select>
                                        </div>
                                        <div class="tblDateFil">
                                            <div class="tblDateCell tblDateCellL">
                                                <div class="stdBtn">
                                                    <label>
                                                        DESDE
                                                    </label>
                                                    <input type="date" name="desde" id="dateDesde">
                                                </div>
                                            </div>
                                            <div class="tblDateCell tblDateCellR">
                                                <div class="stdBtn">
                                                    <label>
                                                        HASTA
                                                    </label>
                                                    <input type="date" name="hasta" id="dateHasta">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="stdBtn">
                                            <label>
                                                ESTATUS
                                            </label>
                                            <div class="optStatTbl">
                                                <div class="boxFiltStatus"><input type="checkbox" id="allStatus" checked/></div>
                                                <div class="lblFiltStatus">Todos</div>
                                            </div>
                                            <div class="optStatTbl">
                                                <div class="boxFiltStatus"><input class="checkstatus" sid="2" type="checkbox" checked/></div>
                                                <div class="lblFiltStatus">Enviados</div>
                                            </div>
                                            <div class="optStatTbl">
                                                <div class="boxFiltStatus"><input class="checkstatus" sid="3" type="checkbox" checked /></div>
                                                <div class="lblFiltStatus">Leídos</div>
                                            </div>
                                            <div class="optStatTbl">
                                                <div class="boxFiltStatus"><input class="checkstatus" sid="1" type="checkbox" checked/></div>
                                                <div class="lblFiltStatus">Pendientes</div>
                                            </div>
                                        </div>
                                        <br/>
                                    </div>
                                    <div class="stdbtn btnFiltCnt">
                                        <input id="stdbtnCloseFilters" class="cancel stdbtnOutline stdbtnSubmitCreateSave" type="button" value="CERRAR">
                                        <input id="stdbtnSendFilters" class="stdbtnSubmit updateModal stdbtnSubmitCreateSave" type="button" value="APLICAR">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
        <div id="containerHome" class="containerHome">
            <div class="stdBtn">
                <div class="subHdTbl">
                    <div class="subHdCell subHdCellL">
                        <div id="loadButtom" class="btnIcon px-0"><i class="fas fa-file-upload icon"></i>Cargar</div> 
                        <div id="createButtom" class="btnIcon px-0"><i class="fas fa-file-medical icon"></i>Crear</div> 
                        <div id="downloadButton" class="btnIconSubh px-0">
                            <i class="icon fa-solid fa-download"></i>
                        </div>
                    </div>
                    <div class="subHdCell subHdCellR" id="buttonsCell">
                        <div class="btnLblSubh px-0">
                            <span id="invoicesQtySel"></span>
                        </div> 
                        <div id="sendButton" class="btnIconSubh px-0">
                            <i class="icon fa-solid fa-paper-plane"></i>
                        </div> 
                        <div id="deleteButton" class="btnIconSubhDis btnIconSubh px-0" style="margin-right:0">
                            <i class="icon fa-solid fa-trash-can"></i>
                        </div> 
                        <div class="divMark">
                            <input class="inptMark" id="markAllRspv" type="checkbox">
                        </div>
                    </div>
                </div>  
            </div>
            <div class="stdBtn">
                <div class=tableSection>
                    <table class="centerTable" id="centerTable">
                    </table>
                    <div class="respveTbl" id="responsiveTable">
                        <div class="respveRow" style="display:none">
                            <div class="detLine">
                                <div class="despLblCell">
                                    <div class="">Fecha</div>
                                </div>
                                <div class="despValCell">                                 
                                    <div class=""><span></span></div>
                                </div>   
                            </div>
                            <div class="detLine hideOpt">
                                <div class="despLblCell">
                                    <div class=""># Documento</div>
                                </div>
                                <div class="despValCell">                                 
                                    <div class=""><span></span></div>
                                </div>   
                            </div>
                            <div class="detLine">
                                <div class="despLblCell">
                                    <div class="">Control</div>
                                </div>
                                <div class="despValCell">                                 
                                    <div class=""><span></span></div>
                                </div>   
                            </div>
                            <div class="detLine">
                                <div class="despLblCell">
                                    <div class="">Cédula/RIF</div>
                                </div>
                                <div class="despValCell">                                 
                                    <div class=""><span></span></div>
                                </div>   
                            </div>
                            <div class="detLine hideOpt">
                                <div class="despLblCell">
                                    <div class="">Cliente</div>
                                </div>
                                <div class="despValCell">                                 
                                    <div class=""><span></span></div>
                                </div>   
                            </div>
                            <div class="detLine hideOpt">
                                <div class="despLblCell">
                                    <div class="">Monto (VES)</div>
                                </div>
                                <div class="despValCell">                                 
                                    <div class=""><span></span></div>
                                </div>   
                            </div>
                            <div class="detLine hideOpt">
                                <div class="despLblCell">
                                    <div class="">IVA (VES)</div>
                                </div>
                                <div class="despValCell">                                 
                                    <div class=""><span></span></div>
                                </div>   
                            </div>
                            <div class="detLine">
                                <div class="despLblCell">
                                    <div class="">Total (VES)</div>
                                </div>
                                <div class="despValCell">                                 
                                    <div class=""><span></span></div>
                                </div>   
                            </div>
                            <div class="detLine">
                                <div class="despLblCell">
                                    <div class="">Estatus</div>
                                </div>
                                <div class="despValCell">                                 
                                    <div class=""><span></span></div>
                                </div>   
                            </div>
                            <div class="divMark">
                                <input class="inptMark" type="checkbox">
                            </div>
                            <div class="eyeIcon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="arrowIcon">
                                <i class="fa-solid fa-caret-down"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pager" id="pagination"></div>
            </div>

        </div>
    </div>
    <div class="page pageFrm" id="pageFrm" >
        <div class="container containerHeader">
            <div class="headerSect textCenter" style="padding: 0px calc(5% + 10px)" id="header">
                <div class="logoSect"><img class="logoMenu" id="goToMain" src="./img/logo.svg"></div>
                <div id="searchFilters">
                    <div class="subTitFrm" id="subtitPage"></div>
                </div>
            </div> 
        </div>
        <div class="invoiceTop">
            <div class="topBackCell">
                <div class="topBackCnt">
                    <span id="backArrow">
                        <i class="fa fa-arrow-left backArrow"></i>
                    </span>
                    <span class="backLbl">Regresar</span>
                </div>
            </div>
            <div class="topTypeCell">
                <div class="topTypeCnt" id="topTypeCnt">
                    <div class="topNatCell typeSel" type="1" placeholder="V-0000000">Natural</div>
                    <div class="topJurCell" type="2" placeholder="J-0000000">Jurídico</div>
                </div>
            </div>
        </div>
        <div class="invoiceFrm">
            <div class="invoiceRow">
                <div class="invoiceCell cell25">
                    <div class="inptCnt">
                        <div class="inptLbl">CI / RIF</div>
                        <div class="inptFrmCnt">
                            <input class="inptFrm" placeholder="V-0000000" type="text" id="customid"/>
                        </div>
                    </div>
                </div>
                <div class="invoiceCell cell30">
                    <div class="inptCnt">
                        <div class="inptLbl">NOMBRE / RAZÓN SOCIAL</div>
                        <div class="inptFrmCnt">
                            <input class="inptFrm" placeholder="Agregar nombre / razón social" type="text" id="customname"/>
                        </div>
                    </div>
                </div>
                <div class="invoiceCell cell7Mid">
                    <div class="inptCnt">
                        <div class="inptLbl">NRO SERIE.</div>
                        <div class="inptFrmCnt">
                            <select class="inptFrm" placeholder="--" id="invoiceserie">
                                <option>AA-00</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="invoiceCell cell12Mid">
                    <div class="inptCnt">
                        <div class="inptLbl">TIPO</div>
                        <div class="inptFrmCnt">
                            <select class="inptFrm" id="invoicetype">
                                <option selected value="FAC">Factura</option>
                                <option value="NCR">Nota Crédito</option>
                                <option value="NDB">Nota Débito</option>
                            </select>
                        </div>
                    </div>
                </div>                
                <div class="invoiceCell cell12Mid" style="display:none">
                    <div class="inptCnt">
                        <div class="inptLbl">SOBRE FACTURA</div>
                        <div class="inptFrmCnt">
                            <input class="inptFrm" placeholder="AA-00-00000000" type="text" id="invoicerefcrtl"/>
                        </div>
                    </div>
                </div>
                <div class="invoiceCell cell12Mid">
                    <div class="inptCnt">
                        <div class="inptLbl"># DOCUMENTO</div>
                        <div class="inptFrmCnt">
                            <input class="inptFrm" placeholder="00000000" type="number" id="invoicenumber"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="invoiceRow">
                <div class="invoiceCell cell80">
                    <div class="inptCnt">
                        <div class="inptLbl">DIRECCION</div>
                        <div class="inptFrmCnt">
                            <input class="inptFrm" placeholder="Agregar dirección fiscal" id="customaddr"/>
                        </div>
                    </div>
                </div>
                <div class="invoiceCell cell20">
                    <div class="inptCnt">
                        <div class="inptLbl">EMISIÓN</div>
                        <div class="inptFrmCnt">
                            <input type="date" class="inptFrm" id="issuedate" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="invoiceRow">
                <div class="invoiceCell cell17Mid">
                    <div class="inptCnt">
                        <div class="inptLbl">TELÉFONO MÓVIL</div>
                        <div class="inptFrmCnt">
                            <input class="inptFrm" placeholder="0414 0001122" type="text" id="custommobile"/>
                        </div>
                    </div>
                </div>
                <div class="invoiceCell cell17Mid">
                    <div class="inptCnt">
                        <div class="inptLbl">TELÉFONO OFICINA <span class="lblOpt">(Opcional)</span></div>
                        <div class="inptFrmCnt">
                            <input class="inptFrm"  placeholder="0212 0001122" type="text" id="customphone"/>
                        </div>
                    </div>
                </div>
                <div class="invoiceCell cell30">
                    <div class="inptCnt">
                        <div class="inptLbl">CORREO ELECTRÓNICO</div>
                        <div class="inptFrmCnt">
                            <input class="inptFrm" placeholder="nombre@correo.com" type="text" id="custommail"/>
                        </div>
                    </div>
                </div>
                <div class="invoiceCell cell7Mid">
                    <div class="inptCnt">
                        <div class="inptLbl">TASA</div>
                        <div class="inptFrmCnt">
                            <input class="inptFrm inptNum" placeholder="0,00" type="text" id="taxrate" />
                        </div>
                    </div>
                </div>
                <div class="invoiceCell cell7Mid">
                    <div class="inptCnt">
                        <div class="inptLbl">MONEDA</div>
                        <div class="inptFrmCnt">
                            <input class="inptFrm taxcurrrate" id="taxcurrrate"/>
                        </div>
                    </div>
                </div>
                <div class="invoiceCell cell20">
                    <div class="inptCnt">
                        <div class="inptLbl">VENCIMIENTO<!-- <span class="lblOpt">(Opcional)</span>--></div>
                        <div class="inptFrmCnt">
                            <input type="date" class="inptFrm" id="duedate"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="invoiceRow">
                <div class="invoiceCell cell100">
                    <div class="inptCnt">
                        <div class="inptLbl">OBSERVACIONES</div>
                        <div class="inptFrmCnt">
                            <input class="inptFrm" placeholder="Agregar observaciones" type="text" id="observations"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="invoiceTbl">
            <div class="tblItems">
                <div class="itemsTblHead">
                    <div class="itemHead cell20">
                        Ref
                    </div>
                    <div class="itemHead cell25">
                        Descripción
                    </div>                  
                    <div class="itemHead cell7Mid itemHdNum">
                        IVA %
                    </div>
                    <div class="itemHead cell10 itemHdNum">
                        Cant. (VES)
                    </div>                    
                    <div class="itemHead cell10">
                        Unit.
                    </div>  
                    <div class="itemHead cell10 itemHdNum">
                        Precio (VES)
                    </div>
                    <div class="itemHead cell7Mid itemHdNum">
                        Desc. %
                    </div>
                    <div class="itemHead cell10 itemHdNum">
                        Total (VES)
                    </div>
                </div>
                <div class="itemsListCnt" style="border-bottom: 1px solid #D7D7D7;padding-bottom: 3px;">
                    <div class="itemsRow">
                        <div class="itemCell cell20">
                            <div class="inptFrmCnt">
                                <input class="inptFrm inptRef" placeholder="0000000000" id="inptAddRef" type="number"/>
                            </div>
                        </div>
                        <div class="itemCell cell25">
                            <div class="inptFrmCnt">
                                <input class="inptFrm inptDsc" placeholder="Descripción…" id="inptAddDsc" type="text" />
                            </div>
                        </div>                     
                        <div class="itemCell cell7Mid">
                            <div class="inptFrmCnt">
                                <input class="inptFrm inptNum inptIVA" placeholder="0,00" id="inptAddTax" type="text"/>
                            </div>
                        </div>
                        <div class="itemCell cell10">
                            <div class="inptFrmCnt">
                                <input class="inptFrm inptNum inptQty" id="inptAddQty" placeholder="0" type="number"/>
                            </div>
                        </div>                        
                        <div class="itemCell cell10">
                            <div class="inptFrmCnt">
                                <input class="inptFrm inptUnit" id="inptAddUnit" placeholder="" type="text"/>
                            </div>
                        </div>   
                        <div class="itemCell cell10">
                            <div class="inptFrmCnt">
                                <input class="inptFrm inptNum inptPrice" id="inptAddPrice" placeholder="0,00" type="text"/>
                            </div>
                        </div>
                        <div class="itemCell cell7Mid">
                            <div class="inptFrmCnt">
                                <input class="inptFrm inptNum inptDisc" id="inptAddDisc" placeholder="0,00" type="text"/>
                            </div>
                        </div>
                        <div class="itemCell cell10">
                            <div class="inptFrmCnt totTblCnt totCell">
                                <input class="inptFrm inptNum inptTot" id="inptTotAmo" placeholder="0,00" type="text" disabled/>
                            </div>
                        </div>                    
                        <div class="itemBtn" id="addItemDetails">
                            <i class="fa fa-plus"></i>
                        </div>
                    </div>
                    <div id="itemsList">

                    </div>
                </div>
                <div class="itemsTotsCnt">
                    <div class="itemTotTbl">
                        <div class="itemTotLbl">Sub-Total</div>
                        <div class="itemTotAmo">
                            <span class="bgTot" id="subTot">0,00</span>
                        </div>
                    </div>
                    <div class="itemTotTbl">
                        <div class="itemTotLbl">% Descuento General</div>
                        <div class="itemTotAmo" style="padding:0;font-size:100%;">
                            <div class="itemCell cell7Mid">
                                <div class="inptFrmCnt">
                                    <input class="inptFrm inptNum inptDisc" placeholder="0,00" type="text" id="discPct">
                                </div>
                            </div>
                            <div class="itemCell cell10">
                                <div class="inptFrmCnt">
                                    <input class="inptFrm inptNum" placeholder="0,00" type="text" id="discAmo">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="itemTotTbl">
                        <div class="itemTotLbl">IVA sobre </div>
                        <div class="itemTotAmo" style="padding:0;font-size:100%;">
                            <div class="itemCell cell7Mid">
                                <div class="inptFrmCnt totTblCnt totCell">
                                    <input class="inptFrm inptNum" placeholder="0,00" type="text" id="taxAmo" disabled/>
                                </div>
                            </div>
                            <div class="itemCell cell10">
                                <div class="inptFrmCnt totTblCnt totCell" style="background-color:#FFFFFF">
                                    <input class="inptFrm inptNum" placeholder="0,00" type="text" id="taxSum" disabled/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="itemTotTbl">
                        <div class="itemTotLbl" style="font-weight:bold">TOTAL</div>
                        <div class="itemTotAmo">
                            <span class="totAmo" id="totAmo">0,00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="invoiceBot">
            <div class="btnsCnt">
                <div class="btnCell">
                    <div class="btnClose btnA" id="closeFrm">
                        CERRAR
                    </div>
                </div>
                <div class="btnCell">
                    <div class="btnSave btnB" id="saveFrm">                    
                        CREAR
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer textCenter" id="copyright"><p>© 2022. Totalsoftware de Venezuela -  Todos los derechos Reservados </p></div>
</div>
<div class="popupCnt">
    <div class="popupCell">
        <div class="popupCard" id="uploadPopup">
            <div class="popupClose" popup="uploadPopup"><i class="fa fa-times"></i></div>
            <div class="popupTit">Cargar Documentos</div>
            <div class="popUpSect">
                <div class="popupDsc">Seleccione los documentos a importar desde:</div>            
                <div class="btnCell btnCellPop">
                    <div class="btnClose btnB" id="uplFile">
                        SELECCIONAR ARCHIVO .TXT
                        <input class="uplFileInv" id="inptUplFile" type="file" accept=".txt"/>
                    </div>
                </div>
            </div>
            <div class="popUpSect">
                <div class="popupDsc">Descargue un plantilla de ejemplo si lo necesita</div>            
                <div class="btnCell btnCellPop" style="margin-bottom:15px">
                    <div class="btnClose btnInvExamp" id="btnInvExamp">
                        <span class="iccsv"></span>PLANTILLA
                    </div>
                </div>
            </div>            
            <div class="popupBtns">
                <div class="btnsCntPop">
                    <div class="btnCell btnCellPop" style="margin-bottom:10px">
                        <div class="btnSave btnA" id="cancelUplInvc">                    
                            CANCELAR
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="popupCard" id="uploadCnfrPopup">
            <div class="popupClose" popup="uploadCnfrPopup"><i class="fa fa-times"></i></div>
            <div class="popupTit">Cargar Documentos</div>
            <div class="popUpSect">
                <div class="popupDsc">Su archivo se ha cargado exitosamente.<br><span id="numberInvc"></span><br>¿Está seguro que quiere procesar este archivo?</div>            
                <div class="btnCell btnCellPop">
                    <div class="uplNameCnt">
                        <div class="uplNameCell" id="uplName"></div>
                        <div class="uplTrashCell" id="uplTrashFile">
                            <i class="fa-solid fa-trash-can"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btnsCntPop">
                <div class="btnCell btnCellPop">
                    <div class="btnClose btnB" id="uplCnfrmBtn">
                        PROCESAR
                    </div>
                </div>
                <div class="btnCell btnCellPop" style="margin-bottom:10px">
                    <div class="btnSave btnA" id="cancelCnfrmUp">                    
                        CANCELAR
                    </div>
                </div>
            </div>
            
        </div>
        <div class="popupCard" id="errUplPopup">
            <div class="popupClose" popup="errUplPopup"><i class="fa fa-times"></i></div>
            <div class="popupTit">Cargar Documentos</div>
            <div class="popupErrSect">
                <div class="cntrLblProg">
                    <div class="progTblName">
                        <div class="progLblName" id="progErrName"></div>
                        <div class="progLblIcon">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </div>
                    </div>
                    <div class="progTblCount">
                        <div class="tblTotCount" id="totRegCount"></div>
                        <div class="tblErrCount" id="totErrCount"></div>
                    </div>
                </div>
                <div class="invoiceRow">
                    <div class="invoiceCell cell100">
                        <div class="inptCnt">
                            <div class="inptLbl">Error</div>
                            <div class="inptFrmCnt">
                                <select class="inptFrm" id="selErrUpl">
                                    <option></option>
                                </select>
                            </div>
                            <div class="lblListErrors">
                                <div class="tblBadItemHd">
                                    <div class="cellErrNro"># Doc.</div>
                                    <div class="cellErrName"></div>
                                </div>
                            </div>
                            <div class="listErrors" id="listItemsErr">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>            
            <div class="popupBtns">
            <div class="btnsCntPop">
                <div class="btnCell btnCellPop">
                    <div class="btnClose btnB" id="tryagainUpl">
                        INTENTAR DE NUEVO
                    </div>
                </div>
                <div class="btnCell btnCellPop" style="margin-bottom:10px">
                    <div class="btnSave btnA" id="cancelErrInvc">                    
                        CANCELAR
                    </div>
                </div>
            </div>
            </div>
        </div>
        <div class="popupCard" id="deletePopup">
            <div class="popupClose" popup="deletePopup"><i class="fa fa-times"></i></div>
            <div class="popupTit">Eliminar Documentos</div>
            <div class="popupDsc">¿Está seguro que desea eliminar los documentos seleccionados?</div>
            <div class="popupBtns">
            <div class="btnsCntPop">
                <div class="btnCell btnCellPop">
                    <div class="btnClose btnB" id="delInvc">
                        ELIMINAR
                    </div>
                </div>
                <div class="btnCell btnCellPop" style="margin-bottom:10px">
                    <div class="btnSave btnA" id="cancelDelInvc">                    
                        CANCELAR
                    </div>
                </div>
            </div>
            </div>
        </div>
        <div class="popupCard" id="sendPopup">
            <div class="popupClose" popup="sendPopup"><i class="fa fa-times"></i></div>
            <div class="popupTit">Enviar Documentos</div>
            <div class="popupDsc">¿Está seguro que desea enviar los <span id="sendNro"></span>por correo electrónico?</div>
            <div class="popupBtns">
            <div class="btnsCntPop">
                <div class="btnCell btnCellPop">
                    <div class="btnClose btnB" id="sendInvc">
                        ENVIAR
                    </div>
                </div>
                <div class="btnCell btnCellPop" style="margin-bottom:10px">
                    <div class="btnSave btnA" id="cancelSendInvc">                    
                        CANCELAR
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
<div class="bannerCnfrm" id="bannerMsg">
    <div class="bannerTbl">
        <div class="bannerIcCell"><i class="fa-solid fa-circle-check"></i></div>
        <div class="bannnerLblCell" id="lblMsg">Sus facturas se han enviado exitosamente</div>
    </div>
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
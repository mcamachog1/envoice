
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
                                                <option value="0" selected>??ltima semana</option>
                                                <option value="1">??ltimos 15 d??as</option>
                                                <option value="2">??ltimo mes</option>
                                                <option value="3">Mes anterior</option>
                                                <option value="4">Este a??o</option>
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
                    </div>
                    <div class="subHdCell subHdCellR" id="buttonsCell" style="display:none">
                        <div class="btnLblSubh px-0"><span id="invoicesQtySel">No hay</span>&nbsp;registros seleccionados</div> 
                        <div id="sendButton" class="btnIconSubhDis btnIconSubh px-0"><i class="icon fa-solid fa-paper-plane"></i></div> 
                        <div id="deleteButton" class="btnIconSubhDis btnIconSubh px-0" style="margin-right:0"><i class="icon fa-solid fa-trash-can"></i></div> 
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
                                    <div class="">Factura</div>
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
                                    <div class="">C??dula/RIF</div>
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
                                    <div class="">Monto</div>
                                </div>
                                <div class="despValCell">                                 
                                    <div class=""><span></span></div>
                                </div>   
                            </div>
                            <div class="detLine hideOpt">
                                <div class="despLblCell">
                                    <div class="">IVA</div>
                                </div>
                                <div class="despValCell">                                 
                                    <div class=""><span></span></div>
                                </div>   
                            </div>
                            <div class="detLine">
                                <div class="despLblCell">
                                    <div class="">Total</div>
                                </div>
                                <div class="despValCell">                                 
                                    <div class=""><span></span></div>
                                </div>   
                            </div>
                            <div class="detLine">
                                <div class="despLblCell">
                                    <div class="">Status</div>
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
                    <div class="topJurCell" type="2" placeholder="J-0000000">Jur??dico</div>
                </div>
            </div>
        </div>
        <div class="invoiceFrm">
            <div class="invoiceRow">
                <div class="invoiceCell cell30">
                    <div class="inptCnt">
                        <div class="inptLbl">CI / RIF</div>
                        <div class="inptFrmCnt">
                            <input class="inptFrm" placeholder="V-0000000" type="text" id="customid"/>
                        </div>
                    </div>
                </div>
                <div class="invoiceCell cell40">
                    <div class="inptCnt">
                        <div class="inptLbl">NOMBRE / RAZ??N SOCIAL</div>
                        <div class="inptFrmCnt">
                            <input class="inptFrm" placeholder="Agregar nombre / raz??n social" type="text" id="customname"/>
                        </div>
                    </div>
                </div>
                <div class="invoiceCell cell10">
                    <div class="inptCnt">
                        <div class="inptLbl">NRO SERIE.</div>
                        <div class="inptFrmCnt">
                            <input class="inptFrm" placeholder="--" id="invoiceserie"/>
                        </div>
                    </div>
                </div>
                <div class="invoiceCell cell20">
                    <div class="inptCnt">
                        <div class="inptLbl">FACTURA</div>
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
                            <input class="inptFrm" placeholder="Agregar direcci??n fiscal" id="customaddr"/>
                        </div>
                    </div>
                </div>
                <div class="invoiceCell cell20">
                    <div class="inptCnt">
                        <div class="inptLbl">EMISI??N</div>
                        <div class="inptFrmCnt">
                            <input type="date" class="inptFrm" id="issuedate" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="invoiceRow">
                <div class="invoiceCell cell17Mid">
                    <div class="inptCnt">
                        <div class="inptLbl">TEL??FONO M??VIL</div>
                        <div class="inptFrmCnt">
                            <input class="inptFrm" placeholder="0414 0001122" type="text" id="custommobile"/>
                        </div>
                    </div>
                </div>
                <div class="invoiceCell cell17Mid">
                    <div class="inptCnt">
                        <div class="inptLbl">TEL??FONO OFICINA <span class="lblOpt">(Opcional)</span></div>
                        <div class="inptFrmCnt">
                            <input class="inptFrm"  placeholder="0212 0001122" type="text" id="customphone"/>
                        </div>
                    </div>
                </div>
                <div class="invoiceCell cell30">
                    <div class="inptCnt">
                        <div class="inptLbl">CORREO ELECTR??NICO</div>
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
                    <div class="itemHead cell25">
                        Ref
                    </div>
                    <div class="itemHead cell30">
                        Descripci??n
                    </div>                    
                    <div class="itemHead cell7Mid itemHdNum">
                        IVA %
                    </div>
                    <div class="itemHead cell10 itemHdNum">
                        Cant.
                    </div>
                    <div class="itemHead cell10 itemHdNum">
                        Precio
                    </div>
                    <div class="itemHead cell7Mid itemHdNum">
                        Desc. %
                    </div>
                    <div class="itemHead cell10 itemHdNum">
                        Total
                    </div>
                </div>
                <div class="itemsListCnt" style="border-bottom: 1px solid #D7D7D7;padding-bottom: 3px;">
                    <div class="itemsRow">
                        <div class="itemCell cell25">
                            <div class="inptFrmCnt">
                                <input class="inptFrm inptRef" placeholder="0000000000" id="inptAddRef" type="number"/>
                            </div>
                        </div>
                        <div class="itemCell cell30">
                            <div class="inptFrmCnt">
                                <input class="inptFrm inptDsc" placeholder="Descripci??n???" id="inptAddDsc" type="text" />
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
    <div class="footer textCenter" id="copyright"><p>?? 2022. Totalsoftware de Venezuela -  Todos los derechos Reservados </p></div>
</div>
<div class="popupCnt">
    <div class="popupCell">
        <div class="popupCard" id="deletePopup">
            <div class="popupClose" popup="deletePopup"><i class="fa fa-times"></i></div>
            <div class="popupTit">Eliminar Facturas</div>
            <div class="popupDsc">??Est?? seguro que desea eliminar las facturas seleccionadas?</div>
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
        <div class="popupCard">
            <div class="popupClose" popup="sendPopup"><i class="fa fa-times"></i></div>
            <div class="popupTit">Enviar Facturas</div>
            <div class="popupDsc">??Est?? seguro que desea enviar las facturas seleccionadas por correo electr??nico?</div>
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
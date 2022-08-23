
<div class="pageCnt">
    <div class="menuSect">
        <div class="menuTbl">
            <div class="cellMenu" style="width:40%" id="menu">Facturas</div> 
            <div class="cellMenu cellSel" id="book">Libro de Ventas</div>  
        </div>
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
                                            <div class="optStatTbl">
                                                <div class="boxFiltStatus"><input class="checkstatus" sid="4" type="checkbox" checked/></div>
                                                <div class="lblFiltStatus">Anulados</div>
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
                    <div class="subHdCell subHdCellL" style="width:70%">
                        <div class="subTit">Comprobación del Libro de Ventas</div>
                        <div class="periodSubTit">Período: <span id="periodDates">01/04/2017 al 30/04/2017<span></div>
                    </div>
                    <div class="subHdCell subHdCellR" id="buttonsCell">                        
                        <div id="downloadButton" style="margin:0;" class="btnIcon px-0"><i class="fas fa-file-download icon"></i>Descargar</div> 
                    </div>
                </div>  
            </div>
            <div class="stdBtn">
                <div class=tableSection>
                    <div class="centerTable">
                        <div class="hideSect"></div>
                        <div class="headCnt">
                            <div class="cellNo cell5">Nro</div>
                            <div class="cellDate cell10">Fecha Oper.</div>
                            <div class="cellRif cell10">Cédula/RIF</div>
                            <div class="cellName cell20">Nombre/Razón Social</div>
                            <div class="cellDetails cell55">
                                <div class="blockTblCnt" id="subTblInf">
                                    <div class="subtblHeadCnt">
                                        <div class="cellTbl">Factura</div>
                                        <div class="cellTbl cellTblRight" style="min-width:110px">Control</div>
                                        <div class="cellTbl">N. Débito</div>
                                        <div class="cellTbl">N. Crédito</div>
                                        <div class="cellTbl">Transacción</div>
                                        <div class="cellTbl">Fac. Afectada</div>
                                        <div class="cellTbl">
                                            <div class="subTblCnt">Total</div>
                                            <div class="cellTblCnt cell220px bgCellOpt1">
                                                <div class="cellEle cellTblRight cell50">Base Imponible</div>
                                                <div class="cellEle cellTblRight cell50 boderLeft">Monto Total</div>
                                            </div>
                                        </div>
                                        <div class="cellTbl">
                                            <div class="subTblCnt">Exentos</div>
                                            <div class="cellTblCnt cell285px">
                                                <div class="cellEle cellTblRight cell40">Base Imponible</div>
                                                <div class="cellEle cell20 boderLeft">%</div>
                                                <div class="cellEle cellTblRight cell40 boderLeft">Monto Total</div>
                                            </div>
                                        </div>
                                        <div class="cellTbl">
                                            <div class="subTblCnt">Exonerados o No Sujetos</div>
                                            <div class="cellTblCnt cell285px bgCellOpt1">
                                                <div class="cellEle cellTblRight cell40">Base Imponible</div>
                                                <div class="cellEle cell20 boderLeft">%</div>
                                                <div class="cellEle cellTblRight cell40 boderLeft">Monto Total</div>
                                            </div>
                                        </div>
                                        <div class="cellTbl">
                                            <div class="subTblCnt">Percibidos</div>
                                            <div class="cellTblCnt cell285px">
                                                <div class="cellEle cellTblRight cell40">Base Imponible</div>
                                                <div class="cellEle cell20 boderLeft">%</div>
                                                <div class="cellEle cellTblRight cell40 boderLeft">Monto Total</div>
                                            </div>
                                        </div>
                                        <div class="cellTbl">
                                            <div class="subTblCnt">Alícuota General</div>
                                            <div class="cellTblCnt cell285px bgCellOpt1">
                                                <div class="cellEle cellTblRight cell40">Base Imponible</div>
                                                <div class="cellEle cell20 boderLeft">%</div>
                                                <div class="cellEle cellTblRight cell40 boderLeft">Monto Total</div>
                                            </div>
                                        </div>
                                        <div class="cellTbl">
                                            <div class="subTblCnt">Alícuota Reducida</div>
                                            <div class="cellTblCnt cell285px">
                                                <div class="cellEle cellTblRight cell40">Base Imponible</div>
                                                <div class="cellEle cell20 boderLeft">%</div>
                                                <div class="cellEle cellTblRight cell40 boderLeft">Monto Total</div>
                                            </div>
                                        </div>
                                        <div class="cellTbl">
                                            <div class="subTblCnt">Alícuota Adicional</div>
                                            <div class="cellTblCnt cell285px bgCellOpt1">
                                                <div class="cellEle cellTblRight cell40">Base Imponible</div>
                                                <div class="cellEle cell20 boderLeft">%</div>
                                                <div class="cellEle cellTblRight cell40 boderLeft">Monto Total</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="regCnt" id="centerSubTable">
                                        <div class="subtblRow" style="display:none">
                                            <div class="cellTbl"></div>
                                            <div class="cellTbl cellTblRight" style="min-width:110px"></div>
                                            <div class="cellTbl"></div>
                                            <div class="cellTbl"></div>
                                            <div class="cellTbl"></div>
                                            <div class="cellTbl"></div>
                                            <div class="cellTbl bgCellOpt1">
                                                <div class="cellTblCnt cell220px">
                                                    <div class="cellEle cellTblRight cell50">1.000,00</div>
                                                    <div class="cellEle cellTblRight cell50 boderLeft">1.100,50</div>
                                                </div>
                                            </div>
                                            <div class="cellTbl">
                                                <div class="cellTblCnt cell285px">
                                                    <div class="cellEle cellTblRight cell40">950,00</div>                                                    
                                                    <div class="cellEle cell20 boderLeft">12</div>
                                                    <div class="cellEle cellTblRight cell40 boderLeft">1.150,25</div>
                                                </div>
                                            </div>
                                            <div class="cellTbl bgCellOpt1">
                                                <div class="cellTblCnt cell285px">
                                                    <div class="cellEle cellTblRight cell40">800,00</div>                                                    
                                                    <div class="cellEle cell20 boderLeft">16</div>
                                                    <div class="cellEle cellTblRight cell40 boderLeft">1.200,50</div>
                                                </div>
                                            </div>
                                            <div class="cellTbl">
                                                <div class="cellTblCnt cell285px">
                                                    <div class="cellEle cellTblRight cell40">1.200,00</div>                                                    
                                                    <div class="cellEle cell20 boderLeft">18</div>
                                                    <div class="cellEle cellTblRight cell40 boderLeft">1.300,50</div>
                                                </div>
                                            </div>
                                            <div class="cellTbl bgCellOpt1">
                                                <div class="cellTblCnt cell285px">
                                                    <div class="cellEle cellTblRight cell40">1.200,00</div>                                                    
                                                    <div class="cellEle cell20 boderLeft">18</div>
                                                    <div class="cellEle cellTblRight cell40 boderLeft">1.300,50</div>
                                                </div>
                                            </div>
                                            <div class="cellTbl">
                                                <div class="cellTblCnt cell285px">
                                                    <div class="cellEle cellTblRight cell40">1.200,00</div>                                                    
                                                    <div class="cellEle cell20 boderLeft">18</div>
                                                    <div class="cellEle cellTblRight cell40 boderLeft">1.300,50</div>
                                                </div>
                                            </div>
                                            <div class="cellTbl bgCellOpt1">
                                                <div class="cellTblCnt cell285px">
                                                    <div class="cellEle cellTblRight cell40">1.200,00</div>                                                    
                                                    <div class="cellEle cell20 boderLeft">18</div>
                                                    <div class="cellEle cellTblRight cell40 boderLeft">1.300,50</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="regCnt" id="centerTable"></div>
                    </div>
                </div>
                <div class="pager" id="pagination"></div>
            </div>
            <div class="stdBtn" style="padding-top:50px">
                <div class=tableSection>
                    <div class="centerTable bottomTbl">
                        <div class="headCnt">
                            <div class="cellNo" style="color:transparent;">-</div>
                            <div class="cellTbl">
                                <div class="subTblCnt">Débito Fiscal</div>
                                <div class="cellTblCnt cell220px bgCellOpt1">
                                    <div class="cellEle cellTblRight cell50">Base Imponible</div>
                                    <div class="cellEle cellTblRight cell50 boderLeft">IVA</div>
                                </div>
                            </div>
                            <div class="cellTbl">
                                <div class="subTblCnt">Crédito Fiscal</div>
                                <div class="cellTblCnt cell220px">
                                    <div class="cellEle cellTblRight cell50">Base Imponible</div>
                                    <div class="cellEle cellTblRight cell50 boderLeft">IVA</div>
                                </div>
                            </div>
                            <div class="cellTbl">
                                <div class="subTblCnt">Total</div>
                                <div class="cellTblCnt cell220px bgCellOpt1">
                                    <div class="cellEle cellTblRight cell50">Base Imponible</div>
                                    <div class="cellEle cellTblRight cell50 boderLeft">IVA</div>
                                </div>
                            </div>
                        </div>
                        <div class="regCnt" id="botCenterTable">
                            <div class="subtblRow bottomTblRow" id="notax">
                                <div class="cellNo">Total ventas no gravadas, no sujetas y/o sin derecho débito fiscal</div>
                                <div class="cellTbl bgCellOpt1" >
                                    <div class="cellTblCnt cell220px">
                                        <div class="cellEle cellTblRight cell50">1.000,00</div>
                                        <div class="cellEle cellTblRight cell50 boderLeft">1.100,50</div>
                                    </div>
                                </div>
                                <div class="cellTbl">
                                    <div class="cellTblCnt cell220px">
                                        <div class="cellEle cellTblRight cell50">1.000,00</div>
                                        <div class="cellEle cellTblRight cell50 boderLeft">1.100,50</div>
                                    </div>
                                </div>
                                <div class="cellTbl bgCellOpt1">
                                    <div class="cellTblCnt cell220px">
                                        <div class="cellEle cellTblRight cell50">1.000,00</div>
                                        <div class="cellEle cellTblRight cell50 boderLeft">1.100,50</div>
                                    </div>
                                </div>
                            </div>
                            <div class="subtblRow bottomTblRow" id="generaltax">
                                <div class="cellNo">Total ventas internas afectadas sólo alícuota general </div>
                                <div class="cellTbl bgCellOpt1">
                                    <div class="cellTblCnt cell220px">
                                        <div class="cellEle cellTblRight cell50">1.000,00</div>
                                        <div class="cellEle cellTblRight cell50 boderLeft">1.100,50</div>
                                    </div>
                                </div>
                                <div class="cellTbl">
                                    <div class="cellTblCnt cell220px">
                                        <div class="cellEle cellTblRight cell50">1.000,00</div>
                                        <div class="cellEle cellTblRight cell50 boderLeft">1.100,50</div>
                                    </div>
                                </div>
                                <div class="cellTbl bgCellOpt1">
                                    <div class="cellTblCnt cell220px">
                                        <div class="cellEle cellTblRight cell50">1.000,00</div>
                                        <div class="cellEle cellTblRight cell50 boderLeft">1.100,50</div>
                                    </div>
                                </div>
                            </div>
                            <div class="subtblRow bottomTblRow" id="addedtax">
                                <div class="cellNo">Total ventas internas afectadas sólo alícuota adicional</div>
                                <div class="cellTbl bgCellOpt1">
                                    <div class="cellTblCnt cell220px">
                                        <div class="cellEle cellTblRight cell50">1.000,00</div>
                                        <div class="cellEle cellTblRight cell50 boderLeft">1.100,50</div>
                                    </div>
                                </div>
                                <div class="cellTbl">
                                    <div class="cellTblCnt cell220px">
                                        <div class="cellEle cellTblRight cell50">1.000,00</div>
                                        <div class="cellEle cellTblRight cell50 boderLeft">1.100,50</div>
                                    </div>
                                </div>
                                <div class="cellTbl bgCellOpt1">
                                    <div class="cellTblCnt cell220px">
                                        <div class="cellEle cellTblRight cell50">1.000,00</div>
                                        <div class="cellEle cellTblRight cell50 boderLeft">1.100,50</div>
                                    </div>
                                </div>
                            </div>
                            <div class="subtblRow bottomTblRow" id="reducedtax">
                                <div class="cellNo">Total ventas internas afectadas sólo alícuota reducida</div>
                                <div class="cellTbl bgCellOpt1">
                                    <div class="cellTblCnt cell220px">
                                        <div class="cellEle cellTblRight cell50">1.000,00</div>
                                        <div class="cellEle cellTblRight cell50 boderLeft">1.100,50</div>
                                    </div>
                                </div>
                                <div class="cellTbl">
                                    <div class="cellTblCnt cell220px">
                                        <div class="cellEle cellTblRight cell50">1.000,00</div>
                                        <div class="cellEle cellTblRight cell50 boderLeft">1.100,50</div>
                                    </div>
                                </div>
                                <div class="cellTbl bgCellOpt1">
                                    <div class="cellTblCnt cell220px">
                                        <div class="cellEle cellTblRight cell50">1.000,00</div>
                                        <div class="cellEle cellTblRight cell50 boderLeft">1.100,50</div>
                                    </div>
                                </div>
                            </div>
                            <div class="subtblRow bottomTblRow totRow" id="totalstax">
                                <div class="cellNo">Totales</div>
                                <div class="cellTbl bgCellOpt1">
                                    <div class="cellTblCnt cell220px">
                                        <div class="cellEle cellTblRight cell50">1.000,00</div>
                                        <div class="cellEle cellTblRight cell50 boderLeft">1.100,50</div>
                                    </div>
                                </div>
                                <div class="cellTbl">
                                    <div class="cellTblCnt cell220px">
                                        <div class="cellEle cellTblRight cell50">1.000,00</div>
                                        <div class="cellEle cellTblRight cell50 boderLeft">1.100,50</div>
                                    </div>
                                </div>
                                <div class="cellTbl bgCellOpt1">
                                    <div class="cellTblCnt cell220px">
                                        <div class="cellEle cellTblRight cell50">1.000,00</div>
                                        <div class="cellEle cellTblRight cell50 boderLeft">1.100,50</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer textCenter" id="copyright"><p>© 2022. Totalsoftware de Venezuela -  Todos los derechos Reservados </p></div>
</div>
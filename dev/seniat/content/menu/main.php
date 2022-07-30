
<div class="pageCnt">
    <div class="page pageList" id="pageList" style="display:block;opacity:1">
        <div class="container containerHeader">
            <div class="headerSect textCenter" id="header">
                <div class="logoSect"><img class="logoMenu" id="goToMain" src="./img/logo.svg"></div>
                <div class="logoSectSnt"><div class="logoSeniat"></div></div>
            </div> 
        </div>
        <div id="containerHome" class="containerHome">
            <div class="stdBtn">
                <div class="subHdTbl">
                    <div class="subHdCell cell35">
                        <div class="inptCnt">
                            <select id="customersList">
                                <option selected value="">Seleccionar Contribuyente</option>
                            </select>
                        </div>   
                    </div>
                    <div class="subHdCell cell20">
                        <div class="inptCnt">
                            <input type="text" id="mySearch" placeholder="Buscar"/>
                        </div>   
                    </div>
                    <div class="subHdCell cell10">
                        <div class="inptCnt">
                            <select id="periodoSelect">
                                <option selected value="">Periodo</option>
                                <option value="0">Última semana</option>
                                <option value="1">Últimos 15 días</option>
                                <option value="2">Último mes</option>
                                <option value="3">Mes anterior</option>
                                <option value="4" selected>Este año</option>
                                <option value="5">Personalizado</option>                  
                            </select>
                        </div>   
                    </div>
                    <div class="subHdCell cell15">
                        <div class="inptCnt">
                            <input type="date" id="dateDesde"/>
                        </div>   
                    </div>
                    <div class="subHdCell cell15">
                        <div class="inptCnt">
                            <input type="date" id="dateHasta"/>
                        </div>   
                    </div>
                    <div class="subHdCell cell5" style="padding-right:0">
                        <div class="inptCnt inptDownload" id="downloadRep">
                            <i class="fa-solid fa-download"></i>
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
                                    <div class="">Monto (Bs.)</div>
                                </div>
                                <div class="despValCell">                                 
                                    <div class=""><span></span></div>
                                </div>   
                            </div>
                            <div class="detLine hideOpt">
                                <div class="despLblCell">
                                    <div class="">IVA (Bs.)</div>
                                </div>
                                <div class="despValCell">                                 
                                    <div class=""><span></span></div>
                                </div>   
                            </div>
                            <div class="detLine">
                                <div class="despLblCell">
                                    <div class="">Total (Bs.)</div>
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
                <div class="pager" id="pagination" style="display:none">
                    <div class="pagerCell">
                        <div class="pagerTbl">
                            <div class="pagCell pagLbl">Página</div>
                            <div class="pagCell">
                                <div class="pagInptCnt">
                                    <input type="number" id="actPag" value='1' />
                                </div>
                            </div>
                            <div class="pagCell pagLbl" id="totPagsLbl">de 30</div>
                            <div class="pagCell">
                                <div class="pagArrowsCnt">
                                    <div class="btnPagLeft" id="btnPagLeft"><i class="fa-solid fa-angle-left"></i></div>
                                    <div class="btnPagRight" id="btnPagRight"><i class="fa-solid fa-angle-right"></i></div>
                                </div>
                            </div>
                            <div class="pagCell pagLblBold">
                                <div class="totDocs" id="totDocs">385 Documentos</div>
                            </div>    
                        </div>
                    </div>
                    <div class="pagerCell">
                        <div class="totsTblCnt">
                            <div class="pagCell pagLblBold">                            
                                <div class="amoBox">
                                    Base Imponible: Bs. <span id="baseAmo">400</span>
                                </div>                            
                            </div>
                            <div class="pagCell pagLblBold">
                                <div class="amoBox">
                                    IVA: Bs. <span id="impAmo">200</span>
                                </div>     
                            </div>
                            <div class="pagCell pagLblBold">
                                <div class="amoBox">
                                    Total: Bs. <span id="totAmo">600</span>
                                </div>     
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>    
</div>
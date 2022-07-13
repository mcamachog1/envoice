<div class="container page" id="homeCenter">
    <div class="centerSect">
        
        <div class="stdBtn">
            <div class="subHdTbl">
                <div class="subHdCell cell20" style="padding-left:0">
                    <div class="inptCnt">
                        <select id="modulsList">
                            <option selected value="">Seleccionar Módulo</option>
                        </select>
                    </div>   
                </div>
                <div class="subHdCell cell25">
                    <div class="inptCnt">
                        <select id="usersList">
                            <option selected value="">Seleccionar Usuario</option>
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
                <div class="subHdCell cell25">
                    <div class="inptCnt">
                        <input type="text" id="mySearch" placeholder="Buscar"/>
                    </div>   
                </div>
            </div>  
        </div>
        <div class="stdBtn">
            <div class=tableSection>
                <table class="centerTable" id="centerTable">
                </table>
            </div>
            <!--
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
                                Base Imponible: <span id="baseAmo">400</span>
                            </div>                            
                        </div>
                        <div class="pagCell pagLblBold">
                            <div class="amoBox">
                                IVA: <span id="impAmo">200</span>
                            </div>     
                        </div>
                        <div class="pagCell pagLblBold">
                            <div class="amoBox">
                                Total: <span id="totAmo">600</span>
                            </div>     
                        </div>
                    </div>
                </div>
            </div>-->
            <div class="pager" id="pagination"></div>
        </div>
    </div>
</div>



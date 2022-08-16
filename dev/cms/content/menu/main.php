<div class="container page" id="homeCenter">
    <div class="headTbl">
        <div class="titCell cell25">
            <span class="stdText textLg"><b>Análisis de Datos</b></span>
        </div>
        <div class="dateCell cell25">
            <div class="inptCnt">
                <select id="modulsList">
                    <option selected value="">Seleccionar Módulo</option>
                </select>
            </div>
        </div>
        <div class="userCell cell50">
            <div class="inptCnt">
                <select id="usersList">
                    <option selected value="">Seleccionar Usuario</option>
                </select>
            </div>
        </div>
    </div>
    <div class="subheadTbl">
        <div class="cellCnt cell25">
            <div class="cntBoxInf">
                <div class="tblBoxInf">
                    <div class="boxTit">
                        <div class="boxTitCell">Total de documentos cargados</div>
                    </div>
                    <div class="boxValCnt">
                        <div class="valCell cell80">434.679</div>
                        <div class="chgCell greenChg">+ 12%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="cellCnt cell25">
            <div class="cntBoxInf">
                <div class="tblBoxInf">
                    <div class="boxTit">
                        <div class="boxTitCell">Total de Documentos enviados</div>
                    </div>
                    <div class="boxValCnt">
                        <div class="valCell cell80">434.679</div>
                        <div class="chgCell redChg">- 20%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="cellCnt cell25">
            <div class="cntBoxInf">
                <div class="tblBoxInf">
                    <div class="boxTit">
                        <div class="boxTitCell">Acceso  de usuarios al sistema</div>
                    </div>
                    <div class="boxValCnt">
                        <div class="valCell cell80">34</div>
                        <div class="chgCell redChg">- 7%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="cellCnt cell25">
            <div class="cntBoxInf">
                <div class="tblBoxInf">
                    <div class="boxTit">
                        <div class="boxTitCell">Acceso de agentes SENIAT al sistema</div>
                    </div>
                    <div class="boxValCnt">
                        <div class="valCell cell80">13</div>
                        <div class="chgCell greenChg">+ 23%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="subheadTbl">
        <div class="cellCnt cell50">
            <div class="chartBoxInf">
                <div class="tblBoxInf">
                    <div class="boxTit chartTit">
                        <div class="boxTitCell">Estatus de documentos</div>
                    </div>
                    <div class="chartTblCnt"  id="statusDonut">
                        <div class="cellChart cell50">
                            <div class="donut-chart-block block"> 
                                <div class="donut-chart">
                                    <div class="borderDonut" >
                                        <div class="hideEle">
                                            <div class="lblEleBord"></div>
                                            <div class="lineEleBord"></div>
                                            <div class="hideBox"></div>
                                        </div>
                                        <div class="hideEleBot">
                                            <div class="lblEleBord"></div>
                                            <div class="lineEleBord"></div>
                                            <div class="hideBox"></div>
                                        </div>
                                    </div>
                                </div> 
                                <div class="leyDonut">
                                    <div class="leyDonCell cell50">
                                        <div class="leyTblDon">
                                            <div class="leyColCell">
                                                <div class="colorBoxLey"></div>
                                            </div>
                                            <div class="leyDscCell">
                                                <b>Enviadas</b>
                                                <div class="leyVal">(391.211)</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="leyDonCell cell50">
                                        <div class="leyTblDon">
                                            <div class="leyColCell">
                                                <div class="colorBoxLey"></div>
                                            </div>
                                            <div class="leyDscCell">
                                                <b>Pendientes</b>
                                                <div class="leyVal">(41.211)</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>   
                        </div>
                        <div class="cellChart cell50">
                            <div class="donut-chart-block block" style="border-left: 1px solid #E6E6E6;"> 
                                <div class="donut-chart" > 
                                    <div class="borderDonut" >
                                        <div class="hideEle">
                                            <div class="lblEleBord"></div>
                                            <div class="lineEleBord"></div>
                                            <div class="hideBox"></div>
                                        </div>
                                        <div class="hideEleBot">
                                            <div class="lblEleBord"></div>
                                            <div class="lineEleBord"></div>
                                            <div class="hideBox"></div>
                                        </div>
                                    </div>                                   
                                </div> 
                                <div class="leyDonut">
                                    <div class="leyDonCell cell50">
                                        <div class="leyTblDon">
                                            <div class="leyColCell">
                                                <div class="colorBoxLey"></div>
                                            </div>
                                            <div class="leyDscCell">
                                                <b>Leídas</b>
                                                <div class="leyVal">(391.211)</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="leyDonCell cell50">
                                        <div class="leyTblDon">
                                            <div class="leyColCell">
                                                <div class="colorBoxLey"></div>
                                            </div>
                                            <div class="leyDscCell">
                                                <b>Sin leer</b>
                                                <div class="leyVal">(41.211)</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>   
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="cellCnt cell50">
            <div class="chartBoxInf">
                <div class="tblBoxInf">
                    <div class="boxTit chartTit">
                        <div class="boxTitCell">Actividad usuarios en el sistema</div>
                    </div>
                    <div class="graphic" >
                        <div class="graphicCell">
                            <canvas class="graphicCanva" id="chartLine"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="subheadTbl">
        <div class="cellCnt cell50">
            <div class="chartBoxInf">
                <div class="tblBoxInf">
                    <div class="boxTit chartTit">
                        <div class="boxTitCell">Cantidad de correos electrónicos</div>
                    </div>
                    <div class="chartTblCnt" id="qtyMailsChart">
                        <div class="cellChart" style="padding-bottom: 8px;">                                 
                            <section class="grafico-barrasV">
                                <ul class="listBarrasV">
                                    
                                </ul>
                                <div class="lblFootTbl">
                                    <div class="lblDayCell">
                                        <span>Día 1</span>
                                    </div>
                                </div>                                 
                                <div class="ceroLine"></div>                      
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="cellCnt cell50" style="visibility:hidden;">
            <div class="chartBoxInf">
                <div class="tblBoxInf">
                    
                </div>
            </div>
        </div>
    </div>
</div>



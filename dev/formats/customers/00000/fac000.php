<?php 
    if($id=="undefined" || $id==NULL || $id==""){
        header("HTTP/1.1 400");
        echo (json_encode(array("msg"=>"Parametros obligatorios id,session")));
        die();
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style rel="stylesheet">   
        :root {
            --textcolor: #4A4A4A;
        }     
        @font-face {
            font-family: 'ArialMT Regular';
            font-style: normal;
            font-weight: normal;
            src: url('<?php echo($urlfonts) ?>fonts/ArialMT/arialmt.ttf') format('truetype');
        }
        @font-face {
            font-family: 'ArialMT Bold';
            font-style: normal;
            font-weight: normal;
            src: url('<?php echo($urlfonts) ?>fonts/ArialMT/ARIALBOLDMT.OTF');
        }
        .cell2Mid{
            width:2.5%;
        }
        .cell5{
            width:5%;
        }
        .cell7Mid{
            width: 7.5%;
        }
        .cell10{
            width: 10%;
        }
        .cell12Mid{
            width: 12.5%;
        }
        .cell15{
            width: 15%;
        }
        .cell17Mid{
            width: 17.5%;
        }
        .cell20{
            width: 10%;
        }
        .cell25 {
            width: 25%;
        }
        .cell30 {
            width: 40%;
        }
        .cell40 {
            width: 40%;
        }
        .cell80 {
            width: 80%;
        }
        .cell100 {
            width: 100%;
        }
        html{ padding:0;margin:0;height:100%;}
        body{ padding:0;margin:0;font-size:15px;height:100%;}
        .page{
            height:100%;
            width:100%;  
                      
        }
        .header{
            display: table;
            width: 100%;
            padding-top: 10px;
            text-align: center;
            margin-left: auto;
            margin-right: auto;
            font-size: 90%;
            padding-bottom: 10px;
            border-bottom: 1px solid #979797;
            height: 80px;
        }
            .headLogo{
                display:table-cell;
                width:80px;
                vertical-align:bottom;
                background-image:url('<?php echo($urllogo.$image); ?>');
                background-repeat:no-repeat;
                background-position:center bottom;
                background-size:contain;
            }
            .headLogo img{display: none;}
            @media print{
                .header{
                    padding-top:0;
                }
                .headLogo img{display:inline;width:80px;}
                body{font-size:14px;}
            }
            .headTit{
                display:table-cell;
                width:auto;
                vertical-align:top;
                color:var(--textcolor);
                font-family:'ArialMT Bold';
                text-align:left;
                padding-top: 10px;
                font-size:113%;
                padding-left: 5px;
            }
            .headTitNm{
                padding-bottom:2px;
            }
            .headDet{
                display:table-cell;
                width:20%;
                max-width:180px;
                vertical-align:middle;
            }
                .typeDet{
                    width:100%;
                    text-align:right;
                    padding-bottom:10px;
                    color:var(--textcolor);
                    font-family:'ArialMT Bold';
                    font-size:120%;
                }
                .headDetTbl{
                    display:table;
                    width:100%;
                    padding:1px 0;
                }
                .headDetLbl{
                    display:table-cell;
                    width:auto;
                    color:var(--textcolor);
                    font-family:'ArialMT Bold';
                    text-align:right;
                    font-size:90%;
                    vertical-align:middle;
                }
                .headDetVal{
                    display:table-cell;
                    width:100px;
                    text-align:right;
                    color:var(--textcolor);
                    font-family:'ArialMT Regular',Sans-Serif;
                    font-size:88%;
                    vertical-align:middle;
                }
        .content{
            width:100%;
            margin-left:auto;
            margin-right:auto;
            height:calc(95vh - 112px);
            padding-bottom: 10px;
            border-bottom: 1px solid #979797;
        }   
            .contHeadDet{
                padding-top:10px;
                padding-bottom:10px;
                border-bottom:1px solid #979797;
            }     
                .contHeadTbl .headDetLbl{                    
                    width:100px;                    
                    text-align:left;
                    font-size:85%;
                }   
                .contHeadTbl .headDetVal{                    
                    width:auto;
                    text-align:left;
                    font-size:80%;
                } 
            
            .itemsTblHead {
                display: table;
                width: calc(100% - 2px);
                color:#0033A0;
                border: 1px solid #979797;
                margin-left:auto;
                margin-right:auto;
                font-size:79%;
            }
                .itemHead {
                    padding: 7px 9px;
                    border-left: 1px solid #979797;
                    display: table-cell;
                    color:var(--textcolor);
                    font-family:'ArialMT Bold';
                }
            .itemsDetCnt{
                display:table;
                width:calc(100% - 2px);
                height:auto;         
                border: 1px solid #979797;
                border-top:none;
                overflow:hidden;
            }
                .itemsTblRow{
                    display: table;
                    width: 100%;
                    color:#0033A0;
                    border-left:none;
                    margin-left:auto;
                    margin-right:auto;
                    font-size:80%;
                    border-top:none;
                    border-bottom:none;
                }
                    .itemsDet{
                        padding: 7px 9px;
                        border-left: 1px solid #979797;
                        display: table-cell;
                        color:var(--textcolor);
                        font-family:'ArialMT Regular',Sans-Serif;
                    }

                    .totCell{
                        background-color: #F8FAFB;
                    }
                    .totCell .inptFrm{
                        color:#AEBECA;
                    }
                    .itemTotTbl {/*Coincide en formato con el row*/
                        display: table;
                        width: calc(100% - 1px);
                        font-size: 80%;
                        border-right: 1px solid #979797;
                        margin-left:auto;
                        margin-right:auto;
                    }
                    .itemTotLbl{
                        color:var(--textcolor);
                        font-family:'ArialMT Regular',Sans-Serif;
                        font-size:95%;
                        display:table-cell;
                        text-align: right;
                        padding-right: 5px;
                        width: auto;
                        vertical-align: middle;
                    }
                    .boldClass{
                        font-family:'ArialMT Bold';font-weight:bold;font-size:100%;
                    }
                    .itemTotAmo{
                        color: var(--textcolor);
                        font-family:'ArialMT Regular',Sans-Serif;
                        font-size:95%;
                        display:table-cell;
                        text-align:right;
                        vertical-align: middle;
                        padding: 9px 9px;
                        width: 12.5%;                        
                        border: 1px solid #979797;
                        border-top:none;
                        border-right:none;
                    }
        .footer {
            display: flex;
            width: 100%;
            height: calc(5vh - 20px);
            padding-bottom: 10px;
            padding-top: 10px;
            text-align: center;
            margin-left: auto;
            margin-right: auto;
            align-items: center;
            font-size: 55%;
            justify-content: space-around;
            font-family:'ArialMT Regular',Sans-Serif;
            color:var(--textcolor);
        }

        .centerText{
            text-align:center;
        }
        .itemHdNum{
            text-align:right;
        }
    </style>
    <title>Invoice - View</title>
</head>
<body>
    <div class="page">        
        <div class="header">
            <div class="headLogo"><img src='<?php echo($urllogo.$image);?>' alt="Logo" /></div>
            <div class="headTit">
                <div class="headTitNm"><?php echo($commercename); ?></div>
                <div class="headDetCnt">
                    <div class="detNum headDetTbl contHeadTbl">
                        <div class="headDetVal"><?php echo($commercerif); ?></div>
                    </div>
                    <div class="detControl headDetTbl contHeadTbl">
                        <div class="headDetVal"><?php echo($commerceaddr); ?></div>
                    </div>
                    <div class="detDate headDetTbl contHeadTbl">
                        <div class="headDetVal"><?php echo($commercephn); ?></div>
                    </div>
                </div>
            </div>
            <div class="headDet">
                <div class="typeDet"><?php echo($type); ?></div>
                <div class="headDetCnt">
                    <div class="detNum headDetTbl">
                        <div class="headDetLbl">Número</div>
                        <div class="headDetVal"><?php echo($invoiceNum); ?></div>
                    </div>
                    <div class="detControl headDetTbl">
                        <div class="headDetLbl">Control</div>
                        <div class="headDetVal"><?php echo($invoiceCtrl); ?></div>
                    </div>
                    <div class="detDate headDetTbl">
                        <div class="headDetLbl">Fecha</div>
                        <div class="headDetVal"><?php echo($invoiceDate); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="contHeadDet">
                <div class="headDetCnt">
                    <div class="detNum headDetTbl contHeadTbl">
                        <div class="headDetLbl">Facturar A</div>
                        <div class="headDetVal"><?php echo($invoiceClient); ?></div>
                    </div>
                    <div class="detControl headDetTbl contHeadTbl">
                        <div class="headDetLbl">RIF</div>
                        <div class="headDetVal"><?php echo($customerRif); ?></div>
                    </div>
                    <div class="detDate headDetTbl contHeadTbl">
                        <div class="headDetLbl">Dirección</div>
                        <div class="headDetVal"><?php echo($customerAddr); ?></div>
                    </div>
                    <div class="detDate headDetTbl contHeadTbl">
                        <div class="headDetLbl">Teléfono</div>
                        <div class="headDetVal"><?php echo($customerPhn); ?> - <?php echo($customerPhn2); ?></div>
                    </div>                    
                    <div class="detDate headDetTbl contHeadTbl">
                        <div class="headDetLbl">Correo</div>
                        <div class="headDetVal"><?php echo($customerEmail); ?></div>
                    </div>  
                </div>
            </div>
            <div class="contHeadDet" style="border:none">
                <div class="headDetCnt">
                    <div class="detNum headDetTbl contHeadTbl">
                        <div class="headDetLbl">Condiciones</div>
                        <div class="headDetVal"><?php echo($conditions); ?></div>
                    </div>
                </div>
            </div>
            <div class="tblItemsCnt">
                <div class="itemsTblHead">
                    <div class="itemHead cell20" style="border-left:none">
                        Ref.
                    </div>
                    <div class="itemHead cell30">
                        Descripción
                    </div>                    
                    <div class="itemHead cell10 centerText">
                        IVA %
                    </div>
                    <div class="itemHead cell7Mid itemHdNum">
                        Cant.
                    </div>
                    <div class="itemHead cell10 itemHdNum">
                        Precio
                    </div>
                    <div class="itemHead cell10 centerText">
                        Desc. %
                    </div>
                    <div class="itemHead cell12Mid itemHdNum">
                        Total sin IVA
                    </div>
                </div>
                <div class="itemsDetCnt">
                    <?php 
                        for($i=0;$i<count($record->details);$i++){
                            echo('
                            <div class="itemsTblRow">
                                <div class="itemsDet cell20" style="border-left:none">
                                    '.$record->details[$i]->item->ref.'
                                </div>
                                <div class="itemsDet cell30">
                                '.$record->details[$i]->item->dsc.'
                                </div>                    
                                <div class="itemsDet cell10 centerText">
                                '.$record->details[$i]->tax->formatted.'
                                </div>
                                <div class="itemsDet cell7Mid itemHdNum">
                                '.$record->details[$i]->qty->formatted.'
                                </div>
                                <div class="itemsDet cell10 itemHdNum">
                                '.$record->details[$i]->unitprice->formatted.'
                                </div>
                                <div class="itemsDet cell10 centerText">
                                '.$record->details[$i]->discount->formatted.'
                                </div>
                                <div class="itemsDet cell12Mid itemHdNum">
                                '.$record->details[$i]->total->formatted.'
                                </div>
                            </div>
                            ');
                        }
                    ?>
                </div>
                <div class="itemsTotsCnt">
                    <div class="itemTotTbl">
                        <div class="itemTotLbl">Sub-Total</div>
                        <div class="itemTotAmo">
                            <span class="bgTot" id="subTot"><?php echo(($record->amounts->gross->number>0) ? $record->amounts->gross->formatted : '-'); ?></span>
                        </div>
                    </div>
                    <div class="itemTotTbl">
                        <div class="itemTotLbl">Descuento General</div>
                        <div class="itemTotAmo">
                            <span class="bgTot" ><?php echo(($record->amounts->discount->number>0) ? $record->amounts->discount->percentage : '-'); ?></span>
                        </div>
                    </div>
                    <div class="itemTotTbl">
                        <div class="itemTotLbl">IVA </div>
                        <div class="itemTotAmo">
                            <span class="bgTot"><?php echo(($record->amounts->tax->number>0) ? $record->amounts->tax->formatted : '-'); ?></span>
                        </div>
                    </div>
                    <div class="itemTotTbl">
                        <div class="itemTotLbl boldClass" >TOTAL</div>
                        <div class="itemTotAmo">
                            <span class="bgTot boldClass"><?php echo($record->amounts->total->formatted); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            De conformidad con lo establecido en la PSA SNAT/2022/000012 G.O. Nro. 42.329 del 17 de Marzo del 2022, hemos sido calificados responsables del impuesto a las Grandes Transacciones Financieras (IGTF) en calidad de Agentes de Percepción, con lo cual, si el pago de esta factura se realiza en moneda distinta a la de curso legal en el país, el monto así percibido estará sujeto a un 3%, que debe ser cancelado por el cliente conjuntamente con el pago de esta factura
        </div>
    </div>
    <script type="text/javascript">        
        //Esta funcion permite asignar el "espacio restante" a la sección de los items de la factura
        //Y así mantener su hegiht de ocupar el resto del espacio (min 100% si es más debería crecer)
        //OJO el calculo no es exacto debído a que una vez se abre la vista de imprimir las columnas tienen resize y los rows pueden alterar su tamaño
        //para esto se ocultó el overflow del contenedor en casode que el obejto de relleno se desborde por debajo en la vista de impresión
        function calcRealHeigh(){
            var padre = document.getElementsByClassName("content")[0];            
            var fillspace = (padre.getElementsByClassName("contHeadDet")[0].offsetHeight+
            padre.getElementsByClassName("contHeadDet")[1].offsetHeight);
            document.getElementsByClassName("tblItemsCnt")[0].style.height = "calc(98% - "+fillspace+"px)";
            var fillspace = (padre.getElementsByClassName("itemsDetCnt")[0].offsetHeight+
            padre.getElementsByClassName("itemsTotsCnt")[0].offsetHeight + 20);
            document.getElementsByClassName("itemsDetCnt")[0].style.height = "calc(100% - "+fillspace+"px)";
            sumAndFill();
        }

        //Esta función calcula el espacio restante entre los "items" y el contenedor de la factura y lo rellena con un item en blanco
        //que tiene el hegiht del espacio restante para mostrar las lineas de las demás columnas
        function sumAndFill(){
            var padre = document.getElementsByClassName("content")[0];
            var rows = padre.getElementsByClassName("itemsTblRow");
            if(rows.length>0){
                var height = 0;
                for(var i=0;i<rows.length;i++){//Se suma el espacio que ocupan los items
                    height += rows[i].offsetHeight;
                }
                var cntspace = document.getElementsByClassName("itemsDetCnt")[0].offsetHeight;
                //Se valída si hay algún espacio en blanco
                if(cntspace>height){
                    var nodo = padre.getElementsByClassName("itemsTblRow")[0].cloneNode(true);
                    var col = nodo.getElementsByClassName("itemsDet");
                    for(var i=0;i<col.length;i++){//Se blanquean los campos del que se clona
                        col[i].innerText = "";
                    }
                    nodo.style.height = "calc(100% - "+height+"px)";
                    padre.getElementsByClassName("itemsDetCnt")[0].appendChild(nodo);
                }                    
            }
        }
        window.onload = function(){
            calcRealHeigh();
        }
    </script>
</body>
</html>
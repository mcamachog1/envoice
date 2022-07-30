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
            --textcolor: #000000;
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
            width: 20%;
        }
        .cell25 {
            width: 25%;
        }
        .cell30 {
            width: 30%;
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
        html{ padding:0;margin:0; }
        body{ padding:0;margin:0;font-size:15px;}
        .page{
            height:100%;
            background-color:#FFFFFF;
            color:var(--textcolor);
            width:100%; 
            overflow-x:hidden;           
        }
        .header{
            display: table;
            width: 100%;
            text-align: center;
            margin-left: auto;
            margin-right: auto;
            font-size: 90%;
            padding-bottom: 10px;
            border-bottom: 1px solid #FFFFFF;
            height: 80px;
        }
            .headLogo{
                display:table-cell;
                width:90px;
                vertical-align:middle;
                background-image:url('<?php echo($urllogo.$image); ?>');
                background-repeat:no-repeat;
                background-position:center center;
                background-size:contain;
            }
            .headLogo img{display: none;}
            @media print{
                .header{
                    padding-top:0;
                }
                .headLogo img{display:inline;width:90px;}
                body{font-size:14px;}
            }
            .headTit{
                display:table-cell;
                width:auto;
                vertical-align:top;
                color:var(--textcolor);
                font-family:'ArialMT Bold';
                text-align:left;
                font-size:110%;
            }
            .headTitNm{
                padding-bottom:2px;
                padding-left: 5px;
            }
            .headCntLeft{
                max-width:150px;
                margin-left:auto;
                font-size:90%;
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
                    padding-bottom:5px;
                    color:var(--textcolor);
                    font-family:'ArialMT Bold';
                    font-size:95%;
                }
                .headDetTbl{
                    display:table;
                    width:100%;
                    padding:1px 0;
                    position:relative;
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
                .headDetLblTbl{
                    display: table !important;
                    width:100%;
                    color:var(--textcolor);
                    font-family:'ArialMT Bold';
                    font-size:95%;
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
                .headDetValTbl{
                    display: table !important;
                    width:100%;
                    font-size:120%;
                }
                
        .content{
            width:100%;
            margin-left:auto;
            margin-right:auto;
            height:calc(95vh - 112px);
            padding-bottom: 10px;
            border-bottom: 1px solid #FFFFFF;
        }   
            .contHeadDet{
                width: 100%;
                display: table;
                padding-top:10px;
                padding-bottom:10px;
                border-bottom:1px solid #FFFFFF;
            } 
            .contCellHead{
                display:table-cell;
                width:50%;
            }  
            
                .contHeadTbl .headDetLbl{                    
                    width:95px;                    
                    text-align:left;
                    font-size:80%;
                }   
                .contHeadTbl .headDetVal{                    
                    width:auto;
                    text-align:left;
                    font-size:78%;
                } 
                .contCellHeadR .headDetLbl{
                    text-align:right;
                    width:auto;
                }
                .contCellHeadR .headDetVal{                    
                    width:130px;
                    text-align:right;
                }
                
            .itemsTblHead {
                display: table;
                width: calc(100% + 16px);
                color: #0033A0;
                border: 1px solid #FFFFFF;
                margin-left: -9px;
                font-size: 79%;
            }
                .itemHead {
                    padding: 7px 9px;
                    border-left: 1px solid #FFFFFF;
                    display: table-cell;
                    color:var(--textcolor);
                    font-family:'ArialMT Bold';
                }
            .itemsDetCnt{
                display:table;
                width: calc(100% - 2px);
                height:auto;         
                border: 1px solid #FFFFFF;
                border-top:none;
                overflow:hidden;
            }
                .itemsTblRow{
                    display: table;
                    width: calc(100% + 18px);                    
                    margin-left:-9px;
                    color:#0033A0;
                    border-left:none;
                    margin-right:auto;
                    font-size:80%;
                    border-top:none;
                    border-bottom:none;
                }
                    .itemsDet{
                        padding: 7px 9px;
                        border-left: 1px solid #FFFFFF;
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
                        border-right: 1px solid #FFFFFF;
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
                        border: 1px solid #FFFFFF;
                        border-top:none;
                        border-right:none;
                    }
        .footer {
            display: flex;
            width: 100%;
            height: calc(5vh - 20px);
            padding-bottom: 5px;
            padding-top: 10px;
            text-align: center;
            margin-left: auto;
            margin-right: auto;
            align-items: center;
            font-size: 65%;
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

        .contHeadTbl .condVal{                    
            position: absolute;
            top: 2px;
            width: 120%;
        }
    </style>
    <title>Invoice - View</title>
</head>
<body>
    <div class="page">        
        <div class="header">
            <div class="headLogo"><img src='<?php echo($urllogo.$image);?>' alt="Logo" /></div>
            <div class="headTit">
                <div class="headTitNm headCntLeft"><?php echo($commercename); ?></div>
                <div class="headDetCnt headCntLeft">                    
                    <div class="detControl headDetTbl contHeadTbl">
                        <div class="headDetVal"><?php echo($commerceaddr); ?></div>
                    </div>                    
                    <div class="detDate headDetTbl contHeadTbl">
                        <div class="headDetVal">Tel: <?php echo($commercephn); ?></div>
                    </div>
                    <div class="detNum headDetTbl contHeadTbl">
                        <div class="headDetVal">RIF: <?php echo($commercerif); ?></div>
                    </div>
                </div>
            </div>
            <div class="headDet">
                <div class="typeDet"><?php /*echo($type);*/echo("Forma libre"); ?></div>
                <div class="headDetCnt">
                    <div class="detControl headDetTbl">
                        <div class="headDetLbl headDetLblTbl">N° de Control</div>
                        <div class="headDetVal headDetValTbl"><?php echo($invoiceCtrl); ?></div>
                    </div>                    
                    
                </div>
            </div>
        </div>
        <div class="content">
            <div class="contHeadDet">
                <div class="contCellHead">
                    <div class="headDetCnt">
                        <div class="detNum headDetTbl contHeadTbl">
                            <div class="headDetLbl">Cliente:</div>
                            <div class="headDetVal"><?php echo($invoiceClient); ?></div>
                        </div>
                        <div class="detControl headDetTbl contHeadTbl">
                            <div class="headDetLbl">RIF:</div>
                            <div class="headDetVal"><?php echo($customerRif); ?></div>
                        </div>
                        <div class="detDate headDetTbl contHeadTbl">
                            <div class="headDetLbl">Dirección:</div>
                            <div class="headDetVal"><?php echo($customerAddr); ?></div>
                        </div>
                        <div class="detDate headDetTbl contHeadTbl">
                            <div class="headDetLbl">Teléfono:</div>
                            <div class="headDetVal"><?php echo($customerPhn); ?> - <?php echo($customerPhn2); ?></div>
                        </div>
                        <div class="detDate headDetTbl contHeadTbl">
                            <div class="headDetLbl">Correo:</div>
                            <div class="headDetVal"><?php echo($customerEmail); ?></div>
                        </div>                         
                        <div class="detDate headDetTbl contHeadTbl">
                            <div class="headDetLbl">Observaciones:</div>
                            <div class="headDetVal condVal"><?php echo($conditions); ?></div>
                        </div>
                    </div>
                </div>
                <div class="contCellHead contCellHeadR">
                    <div class="headDetCnt">
                        <div class="detNum headDetTbl contHeadTbl">
                            <div class="headDetLbl">Factura N°:</div>
                            <div class="headDetVal"><?php echo($invoiceNum); ?></div>
                        </div>
                        <div class="detControl headDetTbl contHeadTbl">
                            <div class="headDetLbl">Fecha Emisión:</div>
                            <div class="headDetVal"><?php echo($invoiceDate); ?></div>
                        </div>
                        <div class="detDate headDetTbl contHeadTbl">
                            <div class="headDetLbl">Fecha Vencimiento:</div>
                            <div class="headDetVal"><?php echo($dueDate); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tblItemsCnt">
                <div class="itemsTblHead">   
                    <div class="itemHead cell20">
                        Cantidad
                    </div>                    
                    <div class="itemHead cell25">
                        Descripción
                    </div>                      
                    <div class="itemHead cell12Mid itemHdNum">
                        Unidad
                    </div>          
                    <div class="itemHead cell15 itemHdNum">
                        Precio
                    </div>
                    <div class="itemHead cell15 itemHdNum">
                        Descuento
                    </div>
                    <div class="itemHead cell12Mid itemHdNum">
                        SubTotal
                    </div>
                </div>
                <div class="itemsDetCnt">
                    <?php 
                        $taxamo = 0;
                        for($i=0;$i<count($record->details);$i++){
                            echo('
                            <div class="itemsTblRow">          
                                <div class="itemsDet cell20">
                                '.$record->details[$i]->qty->formatted.'
                                </div>                                
                                <div class="itemsDet cell25">
                                '.$record->details[$i]->item->dsc.'
                                </div>                                   
                                <div class="itemsDet cell12Mid itemHdNum">
                                '.$record->details[$i]->item->unit.'
                                </div>               
                                <div class="itemsDet cell15 itemHdNum">
                                Bs. '.$record->details[$i]->unitprice->formatted.'
                                </div>
                                <div class="itemsDet cell15 itemHdNum">
                                Bs. '.number_format(($record->details[$i]->qty->number*$record->details[$i]->unitprice->number)*($record->details[$i]->discount->number), 2, ",", ".").'
                                </div>
                                <div class="itemsDet cell12Mid itemHdNum">
                                Bs. '.$record->details[$i]->total->formatted.'
                                </div>
                            </div>
                            ');
                            $taxamo += $record->details[$i]->tax->number > 0 ? $record->details[$i]->total->number : 0;
                        }
                    ?>
                </div>
                <div class="itemsTotsCnt">
                    <div class="itemTotTbl">
                        <div class="itemTotLbl">Subtotal</div>
                        <div class="itemTotAmo">
                            <span class="bgTot" id="subTot">Bs. <?php echo(($record->amounts->gross->number>0) ? $record->amounts->gross->formatted : '-'); ?></span>
                        </div>
                    </div>
                    <!--
                    <div class="itemTotTbl">
                        <div class="itemTotLbl">Descuento General</div>
                        <div class="itemTotAmo">
                            <span class="bgTot" ><?php /* echo(($record->amounts->discount->number>0) ? $record->amounts->discount->percentage : '-'); */ ?></span>
                        </div>
                    </div>-->
                    <div class="itemTotTbl">
                        <div class="itemTotLbl">IVA sobre Bs. <span class="taxTotAmo"><?php echo(number_format($taxamo, 2, ",", "."))?></span></div>
                        <div class="itemTotAmo">
                            <span class="bgTot">Bs. <?php echo(($record->amounts->tax->number>0) ? $record->amounts->tax->formatted : '-'); ?></span>
                        </div>
                    </div>
                    <div class="itemTotTbl">
                        <div class="itemTotLbl boldClass" >TOTAL</div>
                        <div class="itemTotAmo">
                            <span class="bgTot boldClass">Bs. <?php echo($record->amounts->total->formatted); ?></span>
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
        window.onload = function(){
            //Esta funcion permite asignar el "espacio restante" a la sección de los items de la factura
            //Y así mantener su hegiht de ocupar el resto del espacio (min 100% si es más debería crecer)
            //OJO el calculo no es exacto debído a que una vez se abre la vista de imprimir las columnas tienen resize y los rows pueden alterar su tamaño
            //para esto se ocultó el overflow del contenedor en casode que el obejto de relleno se desborde por debajo en la vista de impresión
            function calcRealHeigh(){
                var padre = document.getElementsByClassName("content")[0];   
                var prevHeight = padre.previousElementSibling.offsetHeight+padre.nextElementSibling.offsetHeight+5;      
                padre.style.height = "calc(97vh - "+prevHeight+"px)";   
                var fillspace = (padre.getElementsByClassName("contHeadDet")[0].offsetHeight);
                document.getElementsByClassName("tblItemsCnt")[0].style.height = "calc(100% - "+fillspace+"px)";
                var fillspace = (padre.getElementsByClassName("itemsDetCnt")[0].offsetHeight+
                padre.getElementsByClassName("itemsTotsCnt")[0].offsetHeight + 20);
                document.getElementsByClassName("itemsDetCnt")[0].style.height = "calc(100% - "+fillspace+"px)";
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
            calcRealHeigh();
        }
    </script>
</body>
</html>
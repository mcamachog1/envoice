<div class="container page contFrm" id="cntFrm">
    
    <input type=text id="idUpdateSecurity" class="noShowD" name="id" value=0>
    <div class="centerSect">
        <div class="headAddFrm">
            <div class="titCellFrm">Crear Cliente</div>
            <div class="btnsCellFrm">
                <div class="textLeft btnsLeftCnt">
                    <input id="stdbtnCancel" class="stdbtnSubmit cancel stdbtnSubmitCreateSave" type="button" value="CANCELAR"><input id="stdbtnSubmit" class="stdbtnSubmit updateModal stdbtnSubmitCreateSave" type="button" value="GUARDAR">
                </div>                
                <div class="inptCnt" style="padding-top:5px;text-align:right;">
                    <div class="">
                        <div class="noShowD" id="btnMessageGeneral"></div>          
                    </div>
                    <div class="msgErrInpt" style="text-align:right"></div>          
                </div>
            </div>
        </div>
        <div class="contSect taxData">
            <div class="subTitSect">Datos Fiscales</div>
            <div class="taxFrm">
                <div class="taxSwitch">
                    <div class="switchNew">
                        <div class="invoiceCell statusCell">
                            <div class="inptLbl">ESTATUS</div>
                        </div>
                        <div class="switchCell">                 
                            <div class="switchCont">
                                <div class="switchBox" id="switchStatus">
                                    <div class="switchBal"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="taxFirst">
                    <div class="taxFirstCell">
                        <div class="invoiceRow">
                            <div class="invoiceCell cell100">
                                <div class="inptCnt">
                                    <div class="inptLbl">RAZÓN SOCIAL</div>
                                    <div class="inptFrmCnt">
                                        <input class="inptFrm noEmpty" autocomplete="off" id="razonSocialClient" placeholder="Agregar nombre / razón social" type="text" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoiceRow">
                            <div class="invoiceCell cell100">
                                <div class="inptCnt">
                                    <div class="inptLbl">DIRECCIÓN FISCAL</div>
                                    <div class="inptFrmCnt">
                                        <input class="inptFrm noEmpty" autocomplete="off" id="direccionFiscalClient" placeholder="Agregar dirección fiscal" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoiceRow">
                            <div class="invoiceCell cell33">
                                <div class="inptCnt">
                                    <div class="inptLbl">RIF</div>
                                    <div class="inptFrmCnt">
                                        <input class="inptFrm noEmpty" autocomplete="off" id="rifClient" placeholder="J000000000" />
                                    </div>
                                </div>
                            </div>
                            <div class="invoiceCell cell33">
                                <div class="inptCnt">
                                    <div class="inptLbl">TELÉFONO</div>
                                    <div class="inptFrmCnt">
                                        <input class="inptFrm noEmpty" autocomplete="off" placeholder="0414 0001122" type="text" id="tlfClient"/>
                                    </div>
                                </div>
                            </div>
                            <div class="invoiceCell cell33">
                                <div class="inptCnt">
                                    <div class="inptLbl">TELÉFONO ADICIONAL <span>(opcional)</span></div>
                                    <div class="inptFrmCnt">
                                        <input class="inptFrm " autocomplete="off" placeholder="0414 0001122" type="text" id="tlfClientAd"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="taxFirstCell">
                        <div class="stdSect mTop0">
                            <form class="accFormLogoImg" method="post" enctype="multipart/form-data"><input name="accLogoImg" type="file" id="accLogoImg"/></form>
                            <p class="inptLbl">LOGOTIPO</p>
                            <div class="accLogo divAspect" id="accLogo">
                                <div id ="innerFileText">
                                    <i class="fas fa-cloud-upload-alt nubeIcon"></i>
                                    Arrastra y suelta una imagen para cargar el emblema que aparecerá en la factura
                                </div>
                                <div id="innerFile">
                                    <div class="divIconFile" id="refreshFile">
                                        <i id="refreshFileIcon" class="fas fa-sync"></i>
                                    </div>
                                    <div class="divIconFile" id="removeFile">
                                        <i id="removeFileIcon" class='fas fa-trash-alt'></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="invoiceTbl">
                    <div class="tblItems">
                        <div class="itemsListCnt" style="border-bottom: 1px solid #D7D7D7;padding-bottom: 3px;">
                            <div class="itemsRow">
                                <div class="itemCell cell15">
                                    <div class="inptLbl lblSubTbl">SERIE</div>
                                    <div class="inptFrmCnt">
                                        <input class="inptFrm newSerie" id="newSerie" placeholder="AA" autocomplete="off" type="text" style="text-align:center"/>
                                    </div>
                                    <div class="lineRight">-</div>
                                </div>
                                <div class="itemCell cell15">
                                    <div class="inptLbl lblSubTbl">CONTROL</div>
                                    <div class="inptFrmCnt">
                                        <input class="inptFrm newControl" id="newControl" placeholder="00" autocomplete="off" type="number" min=0 style="text-align:center"/>
                                    </div>
                                    <div class="lineRight">-</div>
                                </div>                        
                                <div class="itemCell cell35">
                                    <div class="inptLbl lblSubTbl">NÚMERO DE INICIO</div>
                                    <div class="inptFrmCnt">
                                        <input class="inptFrm inptNum newInitnum" id="newInitnum" autocomplete="off" placeholder="00000000" type="number"/>
                                    </div>
                                </div>
                                <div class="itemCell cell35">
                                    <div class="inptLbl lblSubTbl" style="text-align:left">IDENTIFICADOR <span>(opcional)</span></div>
                                    <div class="inptFrmCnt">
                                        <input class="inptFrm inptNum newIdname" id="newIdname" autocomplete="off" placeholder="Agregar Identificador" type="text"/>
                                    </div>
                                </div>              
                                <div class="itemBtn" id="addItemDetails">
                                    <i class="fa fa-plus"></i>
                                </div>
                            </div>
                            <div id="itemsList">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="contSect accessData">            
            <div class="subTitSect">Datos de Acceso</div>
            <div class="accessSect">
                <div class="invoiceRow">
                    <div class="invoiceCell cell100" style="padding-right:0;">
                        <div class="inptCnt">
                            <div class="inptLbl">CONTACTO</div>
                            <div class="inptFrmCnt">
                                <input class="inptFrm noEmpty" placeholder="Nombre y Apellido" autocomplete="off" id="contactoClient" type="text" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="invoiceRow">
                    <div class="invoiceCell cell100" style="padding-right:0;">
                        <div class="inptCnt">
                            <div class="inptLbl">CORREO ELECTRÓNICO</div>
                            <div class="inptFrmCnt">
                                <input class="inptFrm noEmpty" id="emailClient" autocomplete="off" placeholder="nombre@correo.com" type="text" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="invoiceRow">
                    <div class="invoiceCell cell25">
                        <div class="inptCnt">
                            <div class="inptLbl">USUARIO FTP</div>
                            <div class="inptFrmCnt">
                                <input class="inptFrm" id="userFtpClient" autocomplete="new-user" placeholder="Nombre de usuario" type="text" />
                            </div>
                        </div>
                    </div>
                    <div class="invoiceCell cell25">
                        <div class="inptCnt">
                            <div class="inptLbl">CLAVE FTP</div>
                            <div class="inptFrmCnt">
                                <input class="inptFrm" id="claveFtpClient" autocomplete="new-password" placeholder="Clave" type="password" />
                            </div>
                        </div>
                    </div>
                    <div class="invoiceCell cell50">
                        <div class="inptCnt" style="visibility:hidden;">
                            <div class="inptLbl">-</div>
                            <div class="inptFrmCnt">
                                <input class="inptFrm"  />
                            </div>
                        </div>
                    </div>
                </div>
                        
            </div>
        </div>
    </div>
</div>

<div class="container page" id="homeCenter">
    <div class="centerSect">
        <div class="headerCon">
            <div class="paddingTitle headerCol">
                <span class="stdText textLg"><b>Clientes</b></span>
            </div>
            <div class="headerCol textRight" style="width: auto;text-align: right;">
                <input class="prdSearch" type="search" id="mySearch" name="Search" placeholder="Buscar">
                <span id="iconSearch"><i class="fa fa-search" aria-hidden="true"></i></span>
            </div>

            <div class="headerCol textRight" style="width:190px";>
                <input id="stdbtnCreate" class="stdBtn stdbtnSubmit" type="button" value="CREAR">
            </div>
        </div>
        
        <div class=tableSection>
            <table id="centerTable">
            </table>
        </div>
        <div class="pager" id="pagination"></div>
    </div>
</div>

<div id="detailsClient" class="modal">
    <div class="modalContent">
        <span id="closeModalDetails" class="close">&times;</span>
        <div class="titleArea">
            <h1 class="title">Detalle Cliente</h1>
        </div>
        <div id="imgClientDetail"></div>
        <p id="razonSocialClientDetail"></p>
        <p id="rifClientDetail"></p>
        <p id="direccionFiscalClientDetail"></p>
        <p id="tlfClientDetail"></p>
        <hr/>
        <p id="contactoClientDetail"></p>
        <p id="emailClientDetail"></p>
        <p id="ftpClientDetail"></p>
    </div>
</div>
<!--
<div id="editClient" class="modal">
    <div class="modalContent" id="modalContent">
        <span id="closeModalEdit" class="close">&times;</span>
        <div class="titleArea">
            <h1 class="title" id="modalTitle"></h1>
        </div>
        <div id="tabs">
            <div class="tab blueborder" id="tabPrincipal">
                Datos Fiscales
            </div>
            <div class="tab">
                Datos de Acceso
            </div>
            <label class="switch">
                <input type="checkbox" id="checkClient">
                <span class="slider round" id="checkStatus"><span class="estatusLabel">ESTATUS</span></span> 
            </label>
            <form id="updateForm" class="myForm">
                <!-- TAb de datos fiscales --
                <div class="tabContent">    
                    <div class="stdSect">
                        <p class="stdLabel">RAZÓN SOCIAL </p>
                        <input class="formElement noEmpty" type="text" id="razonSocialClient" name="razonSocialClient" placeholder="Nombre y Apellido" autocomplete=off>
                        <div class="noShowD btnMessage" id="btnMessageRazonSocialClient">X</div>
                    </div>
                    <div class="stdSect mBot0">
                        <p class="stdLabel">DIRECCIÓN FISCAL</p>
                        <input class="formElement noEmpty" type="text" id="direccionFiscalClient" name="direccionFiscalClient" placeholder="Agregue dirección" autocomplete=off>
                        <div class="noShowD btnMessage" id="btnMessageDireccionFiscalClient">X</div>           
                    </div>
                    <div class="stdSect width50">
                        <div class="stdSect mTop0">
                            <p class="stdLabel">RIF</p>
                            <input class="formElement noEmpty" type="text" id="rifClient" name="rifClient" maxlength="12" placeholder="J-00000000" autocomplete=off>
                            <div class="noShowD btnMessage" id="btnMessageRifClient">X</div>
                        </div>
                        <div class="stdSect">
                            <p class="stdLabel">TELÉFONO</p>
                            <input class="formElement noEmpty" type="text" id="tlfClient" maxlength="14" name="tlfClient" placeholder="(0000)000-0000" autocomplete=off
                            >
                            <!-- data-format="(***) ***-****" data-mask="(###) ###-####"
                            <div class="noShowD btnMessage" id="btnMessageTlfClient">X</div>
                        </div>
                        <div class="stdSect">
                            <p class="stdLabel">NUM. CONTROL SENIAT</p>
                            <input class="formElement noEmpty" type="text" id="serieClient" name="serieClient" maxlength="2" placeholder="00" autocomplete=off>
                            -
                            <input class="formElement noEmpty" type="text" id="numSeniatClient" name="numSeniatClient" maxlength="8" placeholder="00000000" autocomplete=off>
                            <div class="noShowD btnMessage" id="btnMessageNumSeniatClient">X</div>
                        </div>
                    </div>
                    <div class="stdSect width50 mLeft50">
                        <div class="stdSect mTop0">
                            <form class="accFormLogoImg" method="post" enctype="multipart/form-data"><input name="accLogoImg" type="file" id="accLogoImg"/></form>
                            <p class="stdLabel">LOGOTIPO</p>
                            <div class="accLogo divAspect" id="accLogo">
                                <div id ="innerFileText">
                                    <i class="fas fa-cloud-upload-alt nubeIcon"></i>
                                    Arrastra y suelta una imagen para cargar el emblema que aparecerá en la factura
                                </div>
                                <div id="innerFile">
                                    <div class="divIconFile" id="refreshFile">
                                        <i id="refreshFileIcon" class="fas fa-sync"></i>
                                    </div>
                                    <div class="divIconFile" id="removeFile">
                                        <i id="removeFileIcon" class='fas fa-trash-alt'></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="stdSect width50 mLeft50">

                    </div> -
                </div>
                <!-- Tab de Datos de Acceso --
                <div class="tabContent">
                    <div class="stdSect">
                            <p class="stdLabel">CONTACTO</p>
                            <input class="formElement noEmpty" type="text" id="contactoClient" name="contactoClient" placeholder="Nombre y Apellido" autocomplete=off>
                            <div class="noShowD btnMessage" id="btnMessageContactoClient">X</div>
                        </div>
                        <div class="stdSect mBot0">
                            <p class="stdLabel">CORREO ELECTRÓNICO</p>
                            <input class="formElement noEmpty" type="text" id="emailClient" name="emailClient" placeholder="nombre@correo.com" autocomplete=off>
                            <div class="noShowD btnMessage" id="btnMessageEmailClient">X</div>           
                        </div>
                        <div class="stdSect width50">
                            <div class="stdSect mTop0">
                                <p class="stdLabel">USUARIO FTP (Opcional)</p>
                                <input class="formElement" type="text" id="userFtpClient" name="userFtpClient" placeholder="Nombre de Usuario" autocomplete=off>
                                <div class="noShowD btnMessage" id="btnMessageUserFtpClient">X</div>
                            </div>
                        </div>
                        <div class="stdSect width50 mLeft50">
                            <div class="stdSect">
                                <p class="stdLabel">CLAVE FTP (Opcional)</p>
                                <input class="formElement" type="text" id="claveFtpClient" name="claveFtpClient" placeholder="●●●●●" autocomplete=off>
                                <div class="noShowD btnMessage" id="btnMessageClaveFtpClient">X</div>
                            </div>
                        </div>
                    </div>
                <div class="noShowD btnMessage" id="btnMessageGeneral">X</div>           
                <input type=text id="idUpdateSecurity" class="noShowD" name="id" value=0>
                <div class="textLeft">
                    <input id="stdbtnCancel" class="stdbtnSubmit cancel stdbtnSubmitCreateSave" type="button" value="CANCELAR">
                    <input id="stdbtnSubmit" class="stdbtnSubmit updateModal stdbtnSubmitCreateSave" type="button" value="GUARDAR">
                </div>
            </form>
        </div>
        <div class="whiteSpace"></div>
    </div>
</div>-->
<div id="myModalDelete" class="modal">
    <div class="modalContent">
        <span id="closeModalDelete" class="close">&times;</span>
        <div class="titleArea">
            <h1 class="title">Eliminar Cliente</h1>
        </div>
        <p>¿Está seguro que desea eliminar este cliente?</p>
        <div class="clientArea" style="margin-bottom:0">
            <span id="clientToDelete"></span>
        </div>
        <input type=text id="idDeleteSecurity" class="noShowD" name="id" value=0>
        <div class="btnMessage noShowD" id="btnMessageGenDel" style="display: none;"></div>
        <div class="stdbtn" style="margin-top:25px">
            <input id="stdbtnYes" class="stdbtnSubmit updateModal stdbtnSubmitCreateSave" type="button" value="CONFIRMAR">
            <input id="stdbtnNo" class="cancel stdbtnOutline stdbtnSubmitCreateSave" type="button" value="CANCELAR">
        </div>
    </div>
</div>

<div id="confirmModal" class="modal">
    <div class="modalContent">
        <span id="closeModalConfirm" class="close">&times;</span>
        <p id="modalTitleConfirm">¿Está seguro que desea cancelar sin guardar los cambios realizados?</p>
        <div class="stdbtn ">
            <input id="stdbtnYesConfirm" class="stdbtnSubmit updateModal stdbtnSubmitCreateSave" type="button" value="CONFIRMAR">
            <input id="stdbtnNoConfirm" class="cancel stdbtnOutline stdbtnSubmitCreateSave" type="button" value="CANCELAR">
        </div>
    </div>
</div>

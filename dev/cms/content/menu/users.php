<div class="container" id="homeCenter">
    <div class="centerSect">
        <div class="headerCon">
        <div class="paddingTitle headerCol">
                <span class="stdText textLg"><b>Usuarios</b></span>
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

<div id="editUser" class="modal">
    <div class="modalContent" id="modalContent">
        <span id="closeModalEdit" class="close">&times;</span>
        <div class="titleArea">
            <h1 class="title fontMontserrat" id="modalTitle"></h1>
        </div>
        <form id="updateForm" class="myForm">
            <div class="stdSect">
                <label class="switch">
                    <input type="checkbox" id="checkClient">
                    <span class="slider round" id="checkStatus"><span class="estatusLabel">ESTATUS</span></span> 
                </label>
            </div>
            <div class="stdSect">
                <p class="stdLabel">NOMBRE</p>
                <input class="formElement" type="text" id="nameUser" name="nameUser" placeholder="Nombre y Apellido" autocomplete=off>
                <div class="noShowD btnMessage" id="btnMessageUser">X</div>

            </div>
            <div class="stdSect">
                <p class="stdLabel">CORREO ELECTRÓNICO</p>
                <input class="formElement" type="text" id="emailUser" name="emailUser" placeholder="nombre@correo.com" autocomplete=off>
                <div class="noShowD btnMessage" id="btnMessageEmail">X</div>           
            </div>

            <div class="noShowD btnMessage" id="btnMessage">X</div>           
            <input type=text id="idUpdateSecurity" class="noShowD" name="id" value=0>
            <div class="stdbtn">
                <input id="stdbtnSubmit" class="stdbtnSubmit updateModal stdbtnSubmitCreateSave" type="button" value="GUARDAR">
                <input id="stdbtnCancel" class="stdbtnOutline cancel stdbtnSubmitCreateSave" type="button" value="CANCELAR">
            </div>
        </form>
    </div>
</div>
<div id="myModalDelete" class="modal">
    <div class="modalContent">
        <span id="closeModalDelete" class="close">&times;</span>
        <div class="titleArea">
            <h1 class="title">Eliminar Usuario</h1>
        </div>
        <p>¿Está seguro que desea eliminar este usuario?</p>
        <div class="userArea">
            <span id="userToDelete"></span>
        </div>
        <input type=text id="idDeleteSecurity" class="noShowD" name="id" value=0>
        <div class="stdbtn">
            <input id="stdbtnYes" class="stdbtnSubmit updateModal stdbtnSubmitCreateSave" type="button" value="CONFIRMAR">
            <input id="stdbtnNo" class="cancel stdbtnOutline stdbtnSubmitCreateSave" type="button" value="CANCELAR">
        </div>
    </div>
</div>

<div id="confirmModal" class="modal">
    <div class="modalContent">
        <span id="closeModalConfirm" class="close">&times;</span>
        <p id="modalTitleConfirm">¿Está seguro que desea cancelar sin guardar los cambios realizado?</p>
        <div class="stdbtn ">
            <input id="stdbtnYesConfirm" class="stdbtnSubmit updateModal stdbtnSubmitCreateSave" type="button" value="CONFIRMAR">
            <input id="stdbtnNoConfirm" class="cancel stdbtnOutline stdbtnSubmitCreateSave" type="button" value="CANCELAR">
        </div>
    </div>
</div>
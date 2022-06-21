var sessid = getParameterByName("sessid");
function usersDraw(rsp){
    var conts = document.getElementsByClassName("blockUsr");
    for(var i=0;i<conts.length;i++){
        if(rsp[i]!==null && rsp[i]!==undefined){
            conts[i].getElementsByTagName("input")[0].value = rsp[i].id;//El 0 es el usuario
            conts[i].getElementsByTagName("input")[1].value = rsp[i].value;//El 1 es el correo
            var trash = conts[i].getElementsByClassName("blankData")[0];     
            if(rsp[i].id !== "" || rsp[i].value != ""){
                trash.classList.add("isSet");
            }        
        }
    }
    //Se muestra la pantalla
    showPage("cntFrm");
}
function usersEntry() {
    var par = {};
    par.sessionid = sessid;
    //Si es exitoso 
    var onsucces = function(status, respText) {
        var rsp;
        if(respText!="")rsp = JSON.parse(respText);
        switch (status) {
            case 200: 
              console.log(rsp);
              usersDraw(rsp.seniatusers);
            break;
            case 400:
            break;
            case 401:
              gotoPage("login", "error", "");
            break;
            case 500:
            break;
            default:
            break;
        }
    }

    callWS("GET", "preferences/getseniatusers", par, onsucces,"");
    return;
}
function usersSave() {
    var par = {};
    var conts = document.getElementsByClassName("blockUsr");
    var users = "",emails = "";    
    var error = false;
    for(var i=0;i<conts.length;i++){
        if(conts[i].getElementsByTagName("input")[0].value!=="" && 
        conts[i].getElementsByTagName("input")[1].value!==""){
            users = users+conts[i].getElementsByTagName("input")[0].value+"-";//El 0 es el nombre            
            emails = emails+conts[i].getElementsByTagName("input")[1].value+"-";//El 1 es el email
        }
        //SE validan todos los correos
        if(conts[i].getElementsByTagName("input")[1].value !== "" && conts[i].getElementsByTagName("input")[0].value == ""){
            error = true;
            inptError(conts[i].getElementsByTagName("input")[0],"El nombre no puede estar vacío");   
        }else if(conts[i].getElementsByTagName("input")[0].value !== "" && conts[i].getElementsByTagName("input")[1].value == "") {
            error = true;
            inptError(conts[i].getElementsByTagName("input")[1],"El email no puede estar vacío");   
        }else if(conts[i].getElementsByTagName("input")[1].value!==""){
            if(!isEmail(conts[i].getElementsByTagName("input")[1].value)) {
                error = true;
                inptError(conts[i].getElementsByTagName("input")[1],"Ingrese un correo con el formato ejemplo@ejemplo.com");   
            }
        }        
    }
    if(error){
        setTimeout(function(){            
            var conts = document.getElementsByClassName("blockUsr");
            for(var i=0;i<conts.length;i++){      
                removeErr(conts[i].getElementsByTagName("input")[0]);      
                removeErr(conts[i].getElementsByTagName("input")[1]);
            }
        },6000);   
        return;//Dejamos que recorra todos los emails para que se valide el error en todos, si alguno falla aquí se termina el proceso
    }
    if(users!=="")users = users.substring(0, users.length - 1);
    if(emails!=="")emails = emails.substring(0, emails.length - 1);
    par.usernames = users;
    par.emails = emails;
    par.sessionid = sessid;
    //Si es exitoso 
    var onsucces = function(status, respText) {
        var rsp;
        if(respText!="")rsp = JSON.parse(respText);
        switch (status) {
            case 200: 
                var msg = document.getElementById("btnMessageGeneral");
                msg.parentElement.parentElement.classList.add("goodMsg");
                inptError(msg,"Los cambios fueron realizados con éxito");
                setTimeout(function(){
                    var msg = document.getElementById("btnMessageGeneral");
                    msg.parentElement.parentElement.classList.remove("goodMsg");
                    removeErr(msg);
                },6000);
            break;
            case 400:
                var msg = document.getElementById("btnMessageGeneral");
                if(rsp.msg != undefined && rsp.msg != ""){        
                    inptError(msg,rsp.msg);
                }else{
                    inptError(msg,"Error inesperado, por favor intente nuevamente");
                }
                setTimeout(function(){
                    removeErr(document.getElementById("btnMessageGeneral"));
                },6000);
            break;
            case 401:
              gotoPage("login", "error", "");
              break;
            case 500:
              break;
            default:
            break;
        }
    }

    callWS("GET", "preferences/setseniatusers", par, onsucces,"");
    return;
}

function clearUsers(){
    var conts = document.getElementsByClassName("blockUsr");
    for(var i=0;i<conts.length;i++){            
        var trash = conts[i].getElementsByClassName("blankData")[0];        
        trash.dispatchEvent(new Event("click"));
    }
}

function init() {
    //Cargar información de los usuarios
    usersEntry();

    //Evento onchange de email y usuario para rellenar papelera
    var conts = document.getElementsByClassName("blockUsr");
    for(var i=0;i<conts.length;i++){  
        var users = conts[i].getElementsByTagName("input")[0];
        users.addEventListener("change",function(){
            var cnt = this.parentElement.parentElement.parentElement.parentElement.parentElement;
            var trash = cnt.getElementsByClassName("blankData")[0];  
            if(this.value !== "" || cnt.getElementsByTagName("input")[0].value !== ""){
                trash.classList.add("isSet");
            }else{
                trash.classList.add("isSet");
            }
        });

        var email = conts[i].getElementsByTagName("input")[1];
        email.addEventListener("change",function(){
            var cnt = this.parentElement.parentElement.parentElement.parentElement.parentElement;
            var trash = cnt.getElementsByClassName("blankData")[0];  
            if(this.value !== "" || cnt.getElementsByTagName("input")[0].value !== ""){
                trash.classList.add("isSet");
            }else{
                trash.classList.add("isSet");
            }
        });

        var trash = conts[i].getElementsByClassName("blankData")[0];        
        trash.addEventListener("click",function(){
            var cont = this.parentElement;
            var trash = cont.getElementsByClassName("blankData")[0];     
            //Si el usuario o la clave tienen valor se blanquea
            if( cont.getElementsByTagName("input")[0].value !== "" ||
                cont.getElementsByTagName("input")[1].value !== ""
              ){ 
                trash.classList.remove("isSet");
                cont.getElementsByTagName("input")[0].value = "";
                cont.getElementsByTagName("input")[1].value = "";
            }
        });        
    }
    //Guardar cambios
    document.getElementById("stdbtnSubmit").addEventListener("click",function(){
        showModal("myModalDelete","closeModalDelete", "");
    });
    document.getElementById("stdbtnCancel").addEventListener("click",function(){
        clearUsers();
        usersEntry();
        //showModal("myModalDelete","closeModalDelete", "");
    });    
    //Confirmar blanqueo
    document.getElementById("stdbtnYes").addEventListener("click",function(){
        usersSave();
        closeModal("myModalDelete");
    });
    document.getElementById("stdbtnNo").addEventListener("click",function(){
        closeModal("myModalDelete");
    });
}

function showModal(myModalId,close,msg){
  if(msg !== ""){
    if(document.getElementsByClassName("title").length>0)
      document.getElementsByClassName("title")[0].innerText = msg;
  }
  var modal = document.getElementById(myModalId);
  var close = document.getElementById(close);

  modal.style.display = "flex";
  close.onclick = function() {
      modal.style.display = "none";
  }
}
function closeModal(myModalId){
    var modal = document.getElementById(myModalId);
    modal.style.display = "none";
}
function isEmail(email) {
    let re =
      /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
    return re.test(email);
  }
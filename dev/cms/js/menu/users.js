const numofrec = 8;

var sessid = getParameterByName('sessid');
function mySearchClean(table){
    table.forEach(function (element) {
        var padre = element.parentNode;
    	padre.removeChild(element);
    });   
}
function loadUsers(filter="",offset=0,order=-1,numrecords=numofrec){
    var par = {};
    if(filter !== "" && filter !== undefined && filter !== null)
        par.filter = filter;
    par.offset = offset;
    par.order = order;
    par.numofrec = numrecords;
    par.sessionid = sessid;
    callWS("GET", "users/list", par, BCLoadUsers, offset );
    return;
}
function BCLoadUsers(status, respText, offset){
    var jsonResp;
    console.log('status', status)
    switch (status){
        case 200:
            jsonResp = JSON.parse(respText);
            console.log('jsonResp', jsonResp)
            drawUsers(jsonResp.records);
            drawPagination(offset, jsonResp.numofrecords);
            break;
        case 400:
            jsonResp = JSON.parse(respText);
            console.log(jsonResp);
            break;
        case 401:
            gotoPage("login","error","");
            jsonResp = JSON.parse(respText);
            console.log(jsonResp);
            break;
        case 500:
            jsonResp = JSON.parse(respText);
            console.log(jsonResp);
            break;
        default:
            jsonResp = JSON.parse(respText);
            console.log(jsonResp);
            break;
    }
}


function usersEntry(id){
    var par = {};
    par.id = parseInt(id);
    par.sessionid = sessid;
    callWS("GET", "users/entry", par, BCLoadUsersEntry);
    return;
}

function BCLoadUsersEntry(status, respText){
    var jsonResp;
    console.log('jsonResp', jsonResp);
    switch (status){
        case 200:
            jsonResp = (JSON.parse(respText)).entry;
            console.log(jsonResp);

            document.getElementById("nameUser").value=jsonResp.name;
            document.getElementById("emailUser").value=jsonResp.usr;
            let check =document.getElementById("checkClient").value;
            let status = jsonResp.status.id;
            if(check == "on"){
                if(status==0){
                    document.getElementById("checkClient").click();
                    document.getElementById("checkClient").value= "off";
                }
            }else{
                if(status==1){
                    document.getElementById("checkClient").click();
                    document.getElementById("checkClient").value= "on";
                }
            }
            // document.getElementById("statusUser").value=jsonResp.status.id;

            document.getElementById("idUpdateSecurity").value= jsonResp.id;
            showModal('editUser','closeModalEdit','Editar Usuario');
            
            break;
        case 400:
            jsonResp = JSON.parse(respText);
            console.log(jsonResp);
            break;
        case 401:
            gotoPage("login","error","");
            jsonResp = JSON.parse(respText);
            console.log(jsonResp);
            break;
        case 500:
            jsonResp = JSON.parse(respText);
            console.log(jsonResp);
            break;
        default:
            jsonResp = JSON.parse(respText);
            console.log(jsonResp);
            break;
    }
}

// Esta funcion crea la tabla diamicamente segun la respuesta de user/list
function drawUsers(data){
    let cont;
    let btn;
    let table=document.getElementById("centerTable");
    table.innerHTML = "";
    
    
    // Headers
        let line = document.createElement("tr");
        
        let celda = document.createElement("th");
        celda.classList.add("thName");
        celda.innerHTML = "Nombre";
        line.appendChild(celda);
        
        celda = document.createElement("th");
        celda.classList.add("thEmail");
        celda.innerHTML = "Correo";
        line.appendChild(celda);

        celda = document.createElement("th");
        celda.classList.add("thStatus");
        celda.innerHTML = "Estatus";
        line.appendChild(celda);
        
        
        celda = document.createElement("th");
        celda.classList.add("thAction");
        line.appendChild(celda);
        
        table.appendChild(line);
    // End Headers
    
    for(navLink of data){
        line = document.createElement("tr");
        line.id = 'userLine';

        
        celda = document.createElement("td");
        celda.id="name-"+navLink.id;
        celda.innerHTML = '<span>'+navLink.name+'</span>';
        line.appendChild(celda);
        
        celda = document.createElement("td");
        celda.id="email-"+navLink.id;
        celda.classList.add("textLeft");
        celda.innerHTML = '<span>'+navLink.usr+'</span>';
        line.appendChild(celda);

        celda = document.createElement("td");
        celda.id="status-"+navLink.id;
        celda.classList.add("textRight");
        celda.innerHTML = '<span>'+navLink.status.dsc+'</span>';
        line.appendChild(celda);
    
        
        celda = document.createElement("td");
        
        celda.style.textAlign = "center";
        cont = document.createElement("span");
        if (true){
        // TODO: Revisar este if
        // if (sessionStorage.getItem("prd_mod")=="true"){
            cont.classList.add("userIcons");
                // editar
                btn = document.createElement("a");
                btn.setAttribute("editid", navLink.id);
                btn.classList.add("editIcon");
                btn.addEventListener("click", function(){
                    usersEntry(this.getAttribute("editid"));
                });
                btn.innerHTML = '<i class="fa-regular fa-pen-to-square"></i>';
                cont.appendChild(btn);
                // borrar

                if (navLink.id.toString() != sessionStorage.getItem("id")){
                    btn = document.createElement("a");
                    btn.setAttribute("delid", navLink.id);
                    btn.setAttribute("delnam", navLink.name);
                    btn.classList.add("editIcon");
                    btn.addEventListener("click", function(){
                        document.getElementById("idDeleteSecurity").value= this.getAttribute("delid");
                        document.getElementById("userToDelete").innerText = this.getAttribute("delnam");
                        showModal('myModalDelete',"closeModalDelete",'');
                    });
                    btn.innerHTML = '<i class="fa-regular fa-trash-can"></i>';
                    cont.appendChild(btn);
                }
        }
        celda.appendChild(cont);
        
        line.appendChild(celda);

        table.appendChild(line);
    }
    
}

// Esta funcion crea la paginacion de la tabla
function drawPagination(offset, numofrecords, recordsbypage=numofrec){
    const numofpagers = 2;
    var actpag = parseInt((offset/recordsbypage)+1);
    var lastpag = parseInt((numofrecords/recordsbypage)+0.9);
    var inp = document.getElementById("pagination");
    inp.innerHTML = "";

    // no se muestra nada si no hay paginas
    if (lastpag <= 1) {
        document.getElementById('pagination').style.display='none';
        return;
    }
    document.getElementById('pagination').style.display='inherit';

    // dibujar primera y anterior
    var o = document.createElement("span");
    o.classList.add("back");
    o.innerHTML = '<i class="fa fa-angle-left"></i><i class="fa fa-angle-left"></i>';
    if (actpag > 1){
        o.classList.add('colorBlue');
    }
    o.addEventListener("click", function(){loadUsers(document.getElementById("mySearch").value)});
    inp.appendChild(o);
    o = document.createElement("span");
    o.classList.add("back");
    o.innerHTML = '<i class="fa fa-angle-left"></i>';
    if (actpag > 1){
        o.classList.add('colorBlue');
        o.addEventListener("click", function(){loadUsers(document.getElementById("mySearch").value, (actpag-2)*recordsbypage)});
    }
    inp.appendChild(o);
    
    // calcular primera
    var min = 1;
    if (actpag>numofpagers/2)
        min = parseInt(actpag - numofpagers/2);
    if (min < 1) min = 1;
    
    // calcular ultima
    var max = lastpag+1;
    if (max > min + numofpagers)
        max = min + numofpagers;
    
    if (min != 1){
        o = document.createElement("span");
        o.classList.add("number");
        o.innerHTML = "...";
        inp.appendChild(o);
    }

    
    for (var i=min; i< max; i++){
        o = document.createElement("span");
        o.classList.add("number");
        if (actpag == i)
            o.classList.add("pagSel");
        o.innerHTML = i;
        o.addEventListener("click", function(){loadUsers(document.getElementById("mySearch").value, (1*this.innerHTML-1)*recordsbypage)});
        inp.appendChild(o);
    }
    
    
    if (max != lastpag+1){
        o = document.createElement("span");
        o.classList.add("number");
        o.innerHTML = "...";
        o.setAttribute("title", (lastpag) + " Pags");
        inp.appendChild(o);
    }
    o = document.createElement("span");
    o.classList.add("next");
    o.innerHTML = '<i class="fa fa-angle-right"></i>';
    if (actpag < parseInt(lastpag)){
        o.classList.add('colorBlue');
        o.addEventListener("click", function(){loadUsers(document.getElementById("mySearch").value, (actpag)*recordsbypage)});
    }
    inp.appendChild(o);
    o = document.createElement("span");
    o.classList.add("next");
    o.innerHTML = '<i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i>';
    if (actpag < parseInt(lastpag)){
        o.classList.add('colorBlue');
    }
    o.addEventListener("click", function(){loadUsers(document.getElementById("mySearch").value, (lastpag-1)*recordsbypage)});
    inp.appendChild(o);
}

function showModal(myModalId,close,msg){
    if(msg !== "")
        document.getElementById("modalTitle").innerText = msg;
    var modal = document.getElementById(myModalId);
    var close = document.getElementById(close);

    modal.style.display = "block";
    close.onclick = function() {
        modal.style.display = "none";
    }
}


function showMsg(inp, msg, id='none', wait=4000){
    inp.innerText = msg;
    inp.style.display = "inherit";
    inp.classList.remove('noShowD');
    if (id!='none') document.getElementById(id).classList.add('inputError');
    setTimeout(function () {
      inp.style.display = "none";
    inp.classList.add('noShowD');
      if (id!='none') document.getElementById(id).classList.remove('inputError');
    }, 4000);
}

function clearFormUser(){
    document.getElementById("nameUser").value="";
    document.getElementById("emailUser").value="";
    // document.getElementById("statusUser").value="";
    document
    .getElementById("stdbtnSubmit").disabled=false;
    let check =document.getElementById("checkClient").value;
    if(check == "off"){
          document.getElementById("checkClient").click();
          document.getElementById("checkClient").value= "on";
    }
}

function usersUpdate(){
    document.getElementById("stdbtnSubmit").disabled=true;
    let error = false;
    let name = document.getElementById("nameUser").value;
    let email = document.getElementById("emailUser").value;
    let status = document.getElementById("checkClient").value=="on" ? 1 : 0;
    var id = document.getElementById("idUpdateSecurity").value;


    let messageUser = document.getElementById("btnMessageUser");
    let messageEmail = document.getElementById("btnMessageEmail");
    let messageStatus = document.getElementById("btnMessageStatus");

    let emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

    // Validamos el formulario
    if(!name.length){
        showMsg(messageUser, "Ingrese un nombre","nameUser");
        error = true;
    }
    if(!emailRegex.test(email)||!email.length){
        showMsg(messageEmail, "Ingrese un correo con formato nombre@correo.com","emailUser");
        error = true;
    }

    if(error){
        document.getElementById("stdbtnSubmit").disabled=false;
        return;
    }

 
    
    var par = {};
    par.id = id;
    par.name = name;
    par.usr = email;
    par.status = status;
    par.sessionid = sessid;

    callWS("POST", "users/update", par, BCLoadUsersUpdate);
    return;
}

function BCLoadUsersUpdate(status, respText){
    var jsonResp;
    console.log("ESTATUS:",status);
    console.log("respText",respText)
    switch (status){
        case 200:
            jsonResp = JSON.parse(respText);
            console.log('jsonResp', jsonResp);
            loadUsers();
            clearFormUser();
            document.getElementById('editUser').style.display = "none";
            break;
        case 304: 
            console.log("error 304, no se pudo modificar");
            break;
        case 400:
            jsonResp = JSON.parse(respText);
            var message=document.getElementById("btnMessage");
            showMsg(message, "Error 400");
            break;
        case 401:
            gotoPage("login","error","");
            jsonResp = JSON.parse(respText);
            console.log(jsonResp);
            break;
        case 409:
            jsonResp = JSON.parse(respText);
            var messageEmail=document.getElementById("btnMessageEmail");
            showMsg(messageEmail, "Correo electrónico ya utilizado","emailUser");
            document.getElementById("stdbtnSubmit").disabled=false;
            break;
        case 500:
            jsonResp = JSON.parse(respText);
            console.log(jsonResp);
            break;
        default:
            jsonResp = JSON.parse(respText);
            console.log(jsonResp);
            break;
    }
}

function usersDelete(id){
    var par = {};
    par.id = parseInt(id);
    par.sessionid = sessid;
    callWS("GET", "users/delete", par, BCLoadUsersDelete);
    return;
}

function BCLoadUsersDelete(status, respText){
    var jsonResp;
    switch (status){
        case 200:
            jsonResp = JSON.parse(respText);
            loadUsers();
            document.getElementById('myModalDelete').style.display = "none";
            break;
        case 400:
            jsonResp = JSON.parse(respText);
            console.log(jsonResp);
            break;
        case 401:
            gotoPage("login","error","");
            jsonResp = JSON.parse(respText);
            console.log(jsonResp);
            break;
        case 500:
            jsonResp = JSON.parse(respText);
            console.log(jsonResp);
            break;
        default:
            jsonResp = JSON.parse(respText);
            console.log(jsonResp);
            break;
    }
}


function init(){

    // Cargamos los usuarios a mostrar en la tabla
    loadUsers();    
    // eventos del listado
    // busqueda
   // Cargamos los usuarios 
    var search = document.getElementById("mySearch");
    console.log('search', search)
    search.addEventListener("focus", function(event) {
        document.getElementById("iconSearch").style.visibility ="hidden";
    });
    search.addEventListener("focusout", function(event) {
        if(search.value === ""){
            document.getElementById("iconSearch").style.visibility ="visible";
        }
    });
    search.addEventListener("keyup", function(event) {
        console.log('search', search.value)
        if (event.keyCode === 13) {
            event.preventDefault();
            mySearchClean( document.querySelectorAll('[id="myUserLine"]'));
            loadUsers(search.value);
        }
    });

    search.addEventListener("search", function(event) {
        event.preventDefault();
        mySearchClean( document.querySelectorAll('[id="myUserLine"]'));
        loadUsers(); 
      });
    // if (sessionStorage.getItem("prd_mod")=="true"){
    //     // boton nuevo
        document.getElementById("stdbtnCreate").addEventListener("click", function(){
            clearFormUser();
            showModal('editUser','closeModalEdit','Crear Usuario'); 
        });

    // eventos del modal
    // botones

    document.getElementById("stdbtnCancel").addEventListener("click", function(){
        document.getElementById('editUser').style.display = "none";
        showModal('confirmModal','closeModalConfirm',"")
    });

    document.getElementById("stdbtnNoConfirm").addEventListener("click", function(){
        document.getElementById('confirmModal').style.display = "none";
        document.getElementById('editUser').style.display = "block";
    });

    document.getElementById("stdbtnYesConfirm").addEventListener("click", function(){
        document.getElementById('confirmModal').style.display = "none";
        document.getElementById('editUser').style.display = "none";
    });

    
    
    document.getElementById("stdbtnNo").addEventListener("click", function(){
        document.getElementById('myModalDelete').style.display = "none";
    });

    document.getElementById("stdbtnYes").addEventListener("click", function(){
        var id = document.getElementById('idDeleteSecurity').value;
        usersDelete(id);
    });
    
    document.getElementById("stdbtnSubmit").addEventListener("click", function(){
        usersUpdate();
    });


    // Para controlar el switch de estatus

    document.getElementById("checkClient").value = "off";
    document.getElementById("checkStatus").addEventListener("click", function(e){
        let status = document.getElementById("checkClient").value
        console.log("status",status);
        if (status == "off"){
            document.getElementById("checkClient").value="on";
        }else{
            document.getElementById("checkClient").value="off";
        }
    })

}

function clearFileInput(id) 
{ 
    document.getElementById(id).value = ""; 
}

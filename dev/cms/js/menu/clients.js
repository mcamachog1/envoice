const numofrec = 8;
let drop = false;
let fileToSend = "";

window.addEventListener("keydown", function(e) {
  if(["ArrowDown"].indexOf(e.code) > -1) {
      e.preventDefault();
  }
}, false);

var sessid = getParameterByName("sessid");
function mySearchClean(table) {
  table.forEach(function (element) {
    var padre = element.parentNode;
    padre.removeChild(element);
  });
}

function formatRif(rif) {
  console.log('rifFormat', rif)
  let letras = ["J","E","V","P","G"];
  let primeraLetra = rif.charAt(0).toUpperCase();
  let numeros = rif.slice(1)
  console.log('numeros', numeros)
  if(letras.includes(primeraLetra)){
      return primeraLetra + "-" + numeros;
  }

  return "";

}

function formatTlf(tlf) {
  if (isNum(tlf)){
    return `(${tlf.slice(0, 4)})${tlf.slice(4, 7)}-${tlf.slice(7)}`;
  }else{
    return "";
  }
}

function formatControl(control){
 let len = control.length;
 let numControlAux = 0;
 for (let i = 0; i<=len; i++){
  if(control[i]!=0){
    numControlAux = i;
    console.log('numControlAux', numControlAux)
    i = len+1;
  }
 }

 let num = "00000000";
 num += control.slice(numControlAux)
 let final = num.slice(len-numControlAux)
 return final;
}

function loadClients(
  filter = "",
  offset = 0,
  order = -1,
  numrecords = numofrec
) {
  var par = {};
  if (filter !== "" && filter !== undefined && filter !== null)
    par.filter = filter;
  par.offset = offset;
  par.order = order;
  par.numofrec = numrecords;
  par.sessionid = sessid;
  callWS("GET", "customers/list", par, BCLoadClients, offset);
  return;
}
function BCLoadClients(status, respText, offset) {
  var jsonResp;
  switch (status) {
    case 200:
      jsonResp = JSON.parse(respText);
      console.log("jsonResp", jsonResp);
      drawClients(jsonResp.records);
      drawPagination(offset, jsonResp.numofrecords);
      break;
    case 400:
      jsonResp = JSON.parse(respText);
      console.log(jsonResp);
      break;
    case 401:
      gotoPage("login", "error", "");
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

function generarLetra(){
	var letras = ["a","b","c","d","e","f","0","1","2","3","4","5","6","7","8","9"];
	var numero = (Math.random()*15).toFixed(0);
	return letras[numero];
}

function codigoHEX(){
	var cod = "";
	for(var i=0;i<6;i++){
		cod = cod + generarLetra() ;
	}
	return "#" + cod;
}

function clientsEntry(id, view) {
  clearFormClient();
  var par = {};
  console.log("id", id);
  par.id = parseInt(id);
  par.sessionid = sessid;
  callWS("GET", "customers/entry", par, BCLoadClientsEntry, view);
  return;
}

function BCLoadClientsEntry(status, respText, view) {
  var jsonResp;
  jsonResp = JSON.parse(respText).entry;
  switch (status) {
    case 200:
      jsonResp = JSON.parse(respText).entry;
      if (view == "consulta") {
        console.log("jsonResp:", jsonResp);
        document.getElementById(
          "razonSocialClientDetail"
        ).innerHTML = `<b>${jsonResp.name}</b>`;
        document.getElementById("rifClientDetail").innerHTML = formatRif(
          jsonResp.rif
        );
        document.getElementById("direccionFiscalClientDetail").innerHTML =
          jsonResp.address;
        document.getElementById("tlfClientDetail").innerHTML = formatTlf(
          jsonResp.phone
        );
        document.getElementById(
          "contactoClientDetail"
        ).innerHTML = `<b>${jsonResp.contact.name}</b>`;
        document.getElementById("emailClientDetail").innerHTML =
          jsonResp.contact.email;
        if (jsonResp.ftp.usr != "")
          document.getElementById("ftpClientDetail").innerHTML =
            jsonResp.ftp.usr + " - " + jsonResp.ftp.pwd;
        if (jsonResp.image){
            document.getElementById("imgClientDetail").style.backgroundImage = `url('${jsonResp.image}?x=${codigoHEX()}')`
            document.getElementById("imgClientDetail").style.display = "inherit";
        }else{
            document.getElementById("imgClientDetail").style.display = "none";
        }
        showModal("detailsClient","closeModalDetails", "Detalle Cliente");
      } else {
        document.getElementById("razonSocialClient").value = jsonResp.name;
        document.getElementById("rifClient").value = formatRif(jsonResp.rif);
        document.getElementById("direccionFiscalClient").value = jsonResp.address;
        document.getElementById("tlfClient").value = formatTlf(jsonResp.phone);
        /*
        document.getElementById("serieClient").value = jsonResp.seniat.serie;
        document.getElementById("numSeniatClient").value = jsonResp.seniat.control.initial;
 
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
        }*/

        //Se blanquea la linea de "agregar" control de facturas
        var inptsAdd = document.getElementById("addItemDetails").parentElement.getElementsByTagName("input");
        for(var x=0;x<inptsAdd.length;x++){
          inptsAdd[x].value = "";
          removeErr(inptsAdd[x]);
        }
        drawItemsDet(jsonResp.seniat);
        let status = jsonResp.status.id;
        if(status==0){   
          //Blanquear status
          document.getElementById("switchStatus").classList.remove("active");
          document.getElementById("switchStatus").children[0].classList.remove("balActive");    
        }else{
          //Blanquear status
          document.getElementById("switchStatus").classList.add("active");
          document.getElementById("switchStatus").children[0].classList.add("balActive");
        }
        


        document.getElementById("contactoClient").value = jsonResp.contact.name;
        document.getElementById("emailClient").value = jsonResp.contact.email;
        document.getElementById("userFtpClient").value = jsonResp.ftp.usr;
        document.getElementById("claveFtpClient").value = jsonResp.ftp.pwd;

        document.getElementById("idUpdateSecurity").value = jsonResp.id;
        if (jsonResp.image){
            document.getElementById("innerFile").style.backgroundImage =
            "url('" + jsonResp.image + "')";
            document.getElementById("innerFile").style.display = "inherit";
            document.getElementById("innerFileText").style.display = "none";
        }else{
            document.getElementById("innerFile").style.backgroundImage ="";
            document.getElementById("innerFile").style.display = "none";
            document.getElementById("innerFileText").style.display = "inherit";
        }
        //showModal("editClient","closeModalEdit", "Detalle Cliente");
        showPage("cntFrm");
      }
 
      break;
    case 400:
      jsonResp = JSON.parse(respText);
      console.log(jsonResp);
      break;
    case 401:
      gotoPage("login", "error", "");
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
function setValueByClass(classname,val,section = document,attr=""){
  section.getElementsByClassName(classname)[0].value = val;
  if(attr!=""){
    for (let i in attr) {
      section.getElementsByClassName(classname)[0].setAttribute(i,attr[i]);
    }
  }
}
function drawItemsDet(details){
  document.getElementById("itemsList").innerHTML = "";        
  var clone = document.getElementById("addItemDetails").parentElement.cloneNode(true);    
  for(var i=0;i<details.length;i++){        
    var itemLine;
    itemLine = clone.cloneNode(true);

    //Se blanquea la linea de "agregar" luego que se inserta el registro
    var inptsAdd = itemLine.getElementsByTagName("input");
    for(var x=0;x<inptsAdd.length;x++){
      inptsAdd[x].value = "";
      inptsAdd[x].setAttribute("datanumber","");      
      inptsAdd[x].removeAttribute("id");
      inptsAdd[x].setAttribute("disabled","");  
    }

    //Setear valores
    setValueByClass("newSerie",details[i].serie,itemLine);
    setValueByClass("newControl",details[i].prefix,itemLine);
    setValueByClass("newInitnum",details[i].control.initial,itemLine);
    //setValueByClass("newIdname",details[i].unitprice.formatted,itemLine);

    //Evento de eliminar la linea 
    if(parseFloat(details[i].prefix+""+details[i].control.initial)==parseFloat(details[i].control.next)){
      itemLine.getElementsByClassName("itemBtn")[0].addEventListener("click",function(){
        this.parentElement.parentElement.removeChild(this.parentElement);
      });
      itemLine.getElementsByClassName("itemBtn")[0].style.visibility = "";
    }else{
      itemLine.getElementsByClassName("itemBtn")[0].style.visibility = "hidden";
    }
    //Se eliminan los labels
    var inptsLbl = itemLine.getElementsByClassName("inptLbl");
    for(var x=(inptsLbl.length-1);inptsLbl.length>0;x=(inptsLbl.length-1)){
      if(inptsLbl[x].parentElement.getElementsByClassName("lineRight").length>0){
        inptsLbl[x].parentElement.getElementsByClassName("lineRight")[0].style.top = "5px";
      }
      inptsLbl[x].parentElement.removeChild(inptsLbl[x]);
    }
    itemLine.getElementsByClassName("itemBtn")[0].style.top = "5px";

    document.getElementById("itemsList").appendChild(itemLine);
    //Actualizar btn por un menos y evento de eliminar la linea    
    itemLine.getElementsByClassName("itemBtn")[0].children[0].classList.add("fa-minus");
    itemLine.getElementsByClassName("itemBtn")[0].children[0].classList.remove("fa-plus");     
      
  }      
}
// Esta funcion crea la tabla diamicamente segun la respuesta de user/list
function drawClients(data) {
  let cont;
  let btn;
  let table = document.getElementById("centerTable");
  table.innerHTML = "";

  // Headers
  let line = document.createElement("tr");

  let celda = document.createElement("th");
  celda.classList.add("thName");
  celda.innerHTML = "Nombre";
  line.appendChild(celda);

  celda = document.createElement("th");
  celda.classList.add("thContact");
  celda.innerHTML = "Contacto";
  line.appendChild(celda);

  celda = document.createElement("th");
  celda.classList.add("thTlf");
  celda.innerHTML = "Teléfono";
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

  for (navLink of data) {
    line = document.createElement("tr");
    line.id = "userLine";

    celda = document.createElement("td");
    celda.id = "name-" + navLink.id;
    celda.innerHTML =
      '<span class="pointer" id="spanName-' +
      navLink.id +
      '">' +
      navLink.name +
      "</span>";
    line.appendChild(celda);
    // Agregamos el click para consultar los datos dando click
    celda.addEventListener("click", function (event) {
      let id = event.target.id.split("-");
      clientsEntry(id[1], "consulta");
    });

    celda = document.createElement("td");
    celda.id = "contact-" + navLink.id;
    celda.classList.add("textLeft");
    celda.innerHTML = "<span>" + navLink.contact.name + "</span>";
    line.appendChild(celda);

    celda = document.createElement("td");
    celda.id = "tlf-" + navLink.id;
    celda.classList.add("textLeft");
    celda.innerHTML = "<span>" + formatTlf(navLink.phone) + "</span>";
    line.appendChild(celda);

    celda = document.createElement("td");
    celda.id = "email-" + navLink.id;
    celda.classList.add("textLeft");
    celda.innerHTML = "<span>" + navLink.contact.email + "</span>";
    line.appendChild(celda);

    celda = document.createElement("td");
    celda.id = "status-" + navLink.id;
    celda.classList.add("textRight");
    celda.innerHTML = "<span>" + navLink.status.dsc + "</span>";
    line.appendChild(celda);

    celda = document.createElement("td");
    celda.style.textAlign = "center";
    cont = document.createElement("span");
    if (true) {
      // TODO: Revisar este if
      // if (sessionStorage.getItem("prd_mod")=="true"){
      cont.classList.add("userIcons");
      // editar
      btn = document.createElement("a");
      btn.setAttribute("editid", navLink.id);
      btn.classList.add("editIcon");
      btn.addEventListener("click", function () {
        clientsEntry(this.getAttribute("editid"), "editar");
      });
      btn.innerHTML = '<i class="fa-regular fa-pen-to-square"></i>';
      cont.appendChild(btn);
      // borrar
      btn = document.createElement("a");
      btn.setAttribute("delid", navLink.id);
      btn.setAttribute("delnam", navLink.name);
      btn.classList.add("editIcon");
      btn.addEventListener("click", function () {
        document.getElementById("idDeleteSecurity").value =
          this.getAttribute("delid");
        document.getElementById("clientToDelete").innerText =
          this.getAttribute("delnam");
        showModal("myModalDelete","closeModalDelete", "");
      });
      btn.innerHTML = '<i class="fa-regular fa-trash-can"></i>';
      cont.appendChild(btn);
    }
    celda.appendChild(cont);

    line.appendChild(celda);

    table.appendChild(line);
  }
}

// Esta funcion crea la paginacion de la tabla
function drawPagination(offset, numofrecords, recordsbypage = numofrec) {
  const numofpagers = 2;
  var actpag = parseInt(offset / recordsbypage + 1);
  var lastpag = parseInt(numofrecords / recordsbypage + 0.9);
  var inp = document.getElementById("pagination");
  inp.innerHTML = "";

  // no se muestra nada si no hay paginas
  if (lastpag <= 1) {
    document.getElementById("pagination").style.display = "none";
    return;
  }
  document.getElementById("pagination").style.display = "inherit";

  // dibujar primera y anterior
  var o = document.createElement("span");
  o.classList.add("back");
  o.innerHTML =
    '<i class="fa fa-angle-left"></i><i class="fa fa-angle-left"></i>';
  if (actpag > 1) {
    o.classList.add("colorBlue");
  }
  o.addEventListener("click", function () {
    loadClients(document.getElementById("mySearch").value);
  });
  inp.appendChild(o);
  o = document.createElement("span");
  o.classList.add("back");
  o.innerHTML = '<i class="fa fa-angle-left"></i>';
  if (actpag > 1) {
    o.classList.add("colorBlue");
    o.addEventListener("click", function () {
      loadClients(
        document.getElementById("mySearch").value,
        (actpag - 2) * recordsbypage
      );
    });
  }
  inp.appendChild(o);

  // calcular primera
  var min = 1;
  if (actpag > numofpagers / 2) min = parseInt(actpag - numofpagers / 2);
  if (min < 1) min = 1;

  // calcular ultima
  var max = lastpag + 1;
  if (max > min + numofpagers) max = min + numofpagers;

  if (min != 1) {
    o = document.createElement("span");
    o.classList.add("number");
    o.innerHTML = "...";
    inp.appendChild(o);
  }

  for (var i = min; i < max; i++) {
    o = document.createElement("span");
    o.classList.add("number");
    if (actpag == i) o.classList.add("pagSel");
    o.innerHTML = i;
    o.addEventListener("click", function () {
      loadClients(
        document.getElementById("mySearch").value,
        (1 * this.innerHTML - 1) * recordsbypage
      );
    });
    inp.appendChild(o);
  }

  if (max != lastpag + 1) {
    o = document.createElement("span");
    o.classList.add("number");
    o.innerHTML = "...";
    o.setAttribute("title", lastpag + " Pags");
    inp.appendChild(o);
  }
  o = document.createElement("span");
  o.classList.add("next");
  o.innerHTML = '<i class="fa fa-angle-right"></i>';
  if (actpag < parseInt(lastpag)) {
    o.classList.add("colorBlue");
    o.addEventListener("click", function () {
      loadClients(
        document.getElementById("mySearch").value,
        actpag * recordsbypage
      );
    });
  }
  inp.appendChild(o);
  o = document.createElement("span");
  o.classList.add("next");
  o.innerHTML =
    '<i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i>';
  if (actpag < parseInt(lastpag)) {
    o.classList.add("colorBlue");
  }
  o.addEventListener("click", function () {
    loadClients(
      document.getElementById("mySearch").value,
      (lastpag - 1) * recordsbypage
    );
  });
  inp.appendChild(o);
}

// function showModal(myModalId, msg) {
//   if (msg !== "") document.getElementById("modalTitle").innerText = msg;
//   var modal = document.getElementById(myModalId);
//   modal.style.display = "block";
//   document.querySelector("body").style.overflow="hidden";
//   window.onclick = function (event) {
//     if (event.target == modal) {
//       modal.style.display = "none";
//       document.querySelector("body").style.overflow="scroll";
//     }
//   };
// }

function showModal(myModalId,close,msg){
  if(msg !== ""){
    if(document.getElementsByClassName("title").length>0)
      document.getElementsByClassName("title")[0].innerText = msg;
  }
  var modal = document.getElementById(myModalId);
  var close = document.getElementById(close);

  modal.style.display = "block";
  close.onclick = function() {
      modal.style.display = "none";
  }
}



function showMsg(inp, msg, id = "none", wait = 4000) {
  inp.innerText = msg;
  inp.style.display = "inherit";
  inp.classList.remove("noShowD");
  if (id != "none" && id != null)
    document.getElementById(id).classList.add("inputError");
  setTimeout(function () {
    inp.style.display = "none";
    inp.classList.add("noShowD");
    if (id != "none" && id != null)
      document.getElementById(id).classList.remove("inputError");
  }, 5000);
}

function clearFormClient() {
  document.getElementById("stdbtnSubmit").disabled=false;
  document.getElementById("razonSocialClient").value = "";
  removeErr(document.getElementById("razonSocialClient"));
  document.getElementById("rifClient").value = "";
  removeErr(document.getElementById("rifClient"));
  document.getElementById("direccionFiscalClient").value = "";
  removeErr(document.getElementById("direccionFiscalClient"));
  document.getElementById("tlfClient").value = "";
  removeErr(document.getElementById("tlfClient"));
  document.getElementById("contactoClient").value = "";
  removeErr(document.getElementById("contactoClient"));
  document.getElementById("emailClient").value = "";
  removeErr(document.getElementById("emailClient"));
  document.getElementById("userFtpClient").value = "";
  document.getElementById("claveFtpClient").value = "";
  removeErr(document.getElementById("btnMessageGeneral"));
  /*
  document.getElementById("serieClient").value = "";
  document.getElementById("numSeniatClient").value = "";
  */
  document.getElementById("idUpdateSecurity").value = "0";
  /*
  let check =document.getElementById("checkClient").value;
  if(check == "off"){
        document.getElementById("checkClient").click();
        document.getElementById("checkClient").value= "on";
  }*/
  //Se blanquea la linea de "agregar" control de facturas
  var inptsAdd = document.getElementById("addItemDetails").parentElement.getElementsByTagName("input");
  for(var x=0;x<inptsAdd.length;x++){
    inptsAdd[x].value = "";
    removeErr(inptsAdd[x]);
  }
  document.getElementById("itemsList").innerHTML = "";
  //Blanquear status
  document.getElementById("switchStatus").classList.remove("active");
  document.getElementById("switchStatus").children[0].classList.remove("balActive");

  // seteamos la primera pestaña del modal otra vez
  //document.getElementById("tabPrincipal").click();
  // quitamos el archivo
  document.getElementById("accLogoImg").value = "";
  fileToSend = "";
  document.getElementById("innerFile").style.backgroundImage = "none";
  document.getElementById("innerFile").style.display = "none";
  document.getElementById("innerFileText").style.display = "inherit";
}

function isRIF(rif) {
  let re = /^[JGVEPM][-][0-9]{4,9}$/;
  //var re = /^[0-9]{4,9}$/;
  return re.test(rif);
}

function isPhone(phone) {
  let re = /^[(][0-9]{4}[)][0-9]{3}[-][0-9]{4}$/;
  return re.test(phone);
}

function isEmail(email) {
  let re =
    /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
  return re.test(email);
}

function isNum(num){
  let re = /([0-9])+$/;
  return re.test(num);
}

function isAlphaNum(num){
  let re = /([0-9A-Za-z])+$/;
  return re.test(num);
}

function capitalizeFirstLetter(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}

function clientsUpdate() {
  document.getElementById("stdbtnSubmit").disabled=true;
  let error = false;

  // Datos Fiscales
  let razonSocial = document.getElementById("razonSocialClient").value;
  let direccion = document.getElementById("direccionFiscalClient").value;
  let rif = document.getElementById("rifClient").value;
  let tlf = document.getElementById("tlfClient").value;
  /*
  let serie = document.getElementById("serieClient").value;
  let numSeniat = document.getElementById("numSeniatClient").value;
*/
  var listCnt = document.getElementById("itemsList");
  var rows = listCnt.getElementsByClassName("itemsRow");  
  var serie = "",control = "";
  for(var i=0;i<rows.length;i++){
    serie += rows[i].getElementsByClassName("newSerie")[0].value;    
    control += rows[i].getElementsByClassName("newControl")[0].value+""+rows[i].getElementsByClassName("newInitnum")[0].value;   
    if((i+1)!=rows.length){
      serie += "-";
      control += "-";
    }
  }
  let numSeniat = control;

  //Datos de Acceso
  let contacto = document.getElementById("contactoClient").value;
  let email = document.getElementById("emailClient").value;
  let userFtp = document.getElementById("userFtpClient").value;
  let claveFtp = document.getElementById("claveFtpClient").value;

  let status = document.getElementById("switchStatus").classList.contains("active") ? 1 : 0;

  // Para el id del cliente
  let id = document.getElementById("idUpdateSecurity").value;

  // divs de mensajes
  let messageRazonSocialClient = document.getElementById("btnMessageRazonSocialClient");
  let messageDireccionFiscalClient = document.getElementById("btnMessageDireccionFiscalClient");
  let messageRifClient = document.getElementById("btnMessageRifClient");
  let messageTlfClient = document.getElementById("btnMessageTlfClient");
  let messageNumSeniatClient = document.getElementById("btnMessageNumSeniatClient");

  let messageContactoClient = document.getElementById("btnMessageContactoClient");
  let messageEmailClient = document.getElementById("btnMessageEmailClient");
  let messageUserFtpClient = document.getElementById("btnMessageUserFtpClient");
  let messageClaveFtpClient = document.getElementById("btnMessageClaveFtpClient");

  let mensajesVacios = {
    razonSocialClient: "Ingrese la razón social",
    direccionFiscalClient: "Ingrese la dirección fiscal",
    rifClient: "Ingrese el RIF",
    tlfClient: "Ingrese el teléfono",/*
    serieClient: "Ingrese el núm de serie del Seniat",
    numSeniatClient: "Ingrese el núm de control del Seniat",*/
    contactoClient: "Ingrese el nombre de contacto",
    emailClient: "Ingrese el correo electrónico con el que accederá en la aplicación",
  };


  // Validamos que los campos no esten vacios
  let noEmptyFields = document.getElementsByClassName("noEmpty");
  for (item of noEmptyFields) {
    if (!item.value.length) {
      error = true;
      let capitalize = capitalizeFirstLetter(item.id);
      let msg = document.getElementById("btnMessage" + capitalize);
      if (item.id == "serieClient") {
        msg = document.getElementById("btnMessageNumSeniatClient");
        //showMsg(msg, mensajesVacios[item.id], "numSeniatClient");
      } else {
        //showMsg(msg, mensajesVacios[item.id], item.id);
        inptError(document.getElementById(item.id),mensajesVacios[item.id]);
      }
    }
  }
  //Comprobamos primero si hubo error en los campos vacíos
  if (error) {
    document.getElementById("stdbtnSubmit").disabled=false;
    return;
  }else{
    removeErr(document.getElementById("razonSocialClient"));
    removeErr(document.getElementById("rifClient")); 
    removeErr(document.getElementById("direccionFiscalClient"));
    removeErr(document.getElementById("tlfClient"));
    removeErr(document.getElementById("contactoClient"));
    removeErr(document.getElementById("emailClient"));
    removeErr(document.getElementById("btnMessageGeneral"));
  }

  // validamos rif
  let rifValid = isRIF(rif);
  if (!rifValid) {
    error = true;
    inptError(document.getElementById("rifClient"),"Ingrese un RIF con el formato X-0000000");
    /*
    showMsg(
      messageRifClient,
      "Ingrese un RIF con el formato X-0000000",
      "rifClient"
    );*/
  }

  // validamos el telefono
  let tlfValid = isPhone(tlf);
  if (!tlfValid) {
    error = true;
    inptError(document.getElementById("tlfClient"),"Ingrese un teléfono con el formato (0000)000-0000");
    /*
    showMsg(
      messageTlfClient,
      "Ingrese un teléfono con el formato (0000)000-0000",
      "tlfClient"
    );*/
  }

  // validamos el email
  let emailValid = isEmail(email);
  if (!emailValid) {
    error = true;
    inptError(document.getElementById("emailClient"),"Ingrese un correo con el formato ejemplo@ejemplo.com");
    /*
    showMsg(
      messageEmailClient,
      "Ingrese un correo con el formato ejemplo@ejemplo.com",
      "emailClient"
    );*/
  }

  if (error) {
    //msg = document.getElementById("btnMessageGeneral");
    //inptError(document.getElementById("stdbtnSubmit"),"Debe completar todos los campos requeridos en ambas pestañas");
    /*
    showMsg(
      msg,
      "Debe completar todos los campos requeridos en ambas pestañas"
    );*/
    document.getElementById("stdbtnSubmit").disabled=false;


    return;
  }

  var list = document.getElementById("itemsList");
  var rows = list.getElementsByClassName("itemsRow");
  if(rows.length<=0){    
    inptError(document.getElementById("newSerie"),"Ingrese un número de control de factura");
    inptError(document.getElementById("newInitnum"),"");
    inptError(document.getElementById("newControl"),"");
    document.getElementById("newSerie").parentElement.parentElement.getElementsByClassName("msgErrInpt")[0].classList.add("errAbs");
    document.getElementById("stdbtnSubmit").disabled=false;
    return;
  }  

  let par = {};
  par.name = razonSocial;
  par.address = direccion;
  par.rif = rif;
  par.phone = tlf;
  par.serie = serie;
  par.control = numSeniat;
  par.contactname = contacto;
  par.contactemail = email;
  par.ftpusr = userFtp;
  par.ftppwd = claveFtp;
  par.status = status;
  par.id = id;
  par.sessionid = sessid;
  if (fileToSend != "") {
    par["IMAGEN"] = fileToSend;
  }

  callWS("POST", "customers/update", par, BCLoadClientsUpdate);
  return;
}

function BCLoadClientsUpdate(status, respText) {
  var jsonResp;
  console.log("STATUS:", status);
  console.log(JSON.parse(respText));
  switch (status) {
    case 200:
      jsonResp = JSON.parse(respText);
      loadClients();
      clearFormClient();
      showPage("homeCenter");
      break;
    case 400:
      jsonResp = JSON.parse(respText);
      //var message = document.getElementById("btnMessage");
      var msg = document.getElementById("btnMessageGeneral");
      if(jsonResp.msg != undefined && jsonResp.msg != ""){        
        inptError(msg,jsonResp.msg);
      }else{
        inptError(msg,"Error inesperado, por favor intente nuevamente");
      }
      setTimeout(function(){
        removeErr(document.getElementById("btnMessageGeneral"));
      },5000);
      document.getElementById("stdbtnSubmit").disabled=false;
      //showMsg(message, "Error 400");
      break;
    case 401:
      gotoPage("login", "error", "");
      jsonResp = JSON.parse(respText);
      console.log(jsonResp);
      break;
    case 409:
        jsonResp = JSON.parse(respText);
        // jsonResp = "correo";
        if (jsonResp.msg === "rif"){
          inptError(document.getElementById("rifClient"),"RIF ya utilizado");
          /*          
          let messageRifClient = document.getElementById("btnMessageRifClient");
          showMsg(
            messageRifClient,
            "RIF ya utilizado",
            "rifClient"
          );
          msg = document.getElementById("btnMessageGeneral");
          showMsg(
            msg,
            "Rif ya utilizado"
          );*/  
        }else if(jsonResp.msg === "correo"){
          inptError(document.getElementById("emailClient"),"Correo electrónico ya utilizado");
          /*
          let messageEmailClient = document.getElementById("btnMessageEmailClient");
          showMsg(
            messageEmailClient,
            "Correo electrónico ya utilizado",
            "emailClient"
          );
          msg = document.getElementById("btnMessageGeneral");
          showMsg(
            msg,
            "Correo electrónico ya utilizado"
          );*/
        }
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

function clientsDelete(id) {
  var par = {};
  par.id = parseInt(id);
  par.sessionid = sessid;
  callWS("GET", "customers/delete", par, BCLoadClientsDelete);
  return;
}

function BCLoadClientsDelete(status, respText) {
  var jsonResp;
  switch (status) {
    case 200:
      jsonResp = JSON.parse(respText);
      loadClients();
      document.getElementById("myModalDelete").style.display = "none";
      document.querySelector("body").style.overflow="scroll";

      break;
    case 400:
      jsonResp = JSON.parse(respText);
      console.log(jsonResp);      
      msg = document.getElementById("btnMessageGenDel");
      showMsg(
        msg,
        "El cliente tiene facturas asociadas que no permiten eliminarlo"
      );
      break;
    case 401:
      gotoPage("login", "error", "");
      jsonResp = JSON.parse(respText);
      console.log(jsonResp);
      break;
    case 500:
      jsonResp = JSON.parse(respText);
      console.log(jsonResp);
      break;
    default:
      console.log(status);
      console.log(respText);
      if(respText!==""){
        jsonResp = JSON.parse(respText);
        console.log(jsonResp);
      }
      break;
  }
}

// Funcion para las tabs

var tab;
var tabContent;

function hideTabsContent(a) {
  for (var i = a; i < tabContent.length; i++) {
    tabContent[i].classList.remove("show");
    tabContent[i].classList.add("hide");
    tab[i].classList.remove("blueborder");
  }
}

function showTabsContent(b) {
  if (tabContent[b].classList.contains("hide")) {
    hideTabsContent(0);
    tab[b].classList.add("blueborder");
    tabContent[b].classList.remove("hide");
    tabContent[b].classList.add("show");
  }
}


function loadFile(file) {
  var reader = new FileReader();
  reader.onload = function (e) {
    document.getElementById("innerFile").style.backgroundImage =
      "url('" + e.target.result + "')";
    document.getElementById("innerFile").style.display = "inherit";
    document.getElementById("innerFileText").style.display = "none";
  };
  reader.readAsDataURL(file); // convert to base64 string
  fileToSend = file;
}

function showFiles(file) {
  const docType = file.type;
  const validExt = ["image/jpg", "image/jpeg", "image/png"];
  if (validExt.includes(docType)) {
    loadFile(file);
  } else {
    console.log("Formato invaido");
  }
}

function clearFileInput(id) {
  document.getElementById(id).value = "";
}


function init() {

    // Cargamos los usuarios a mostrar en la tabla
    loadClients();
    showPage("homeCenter");
    // eventos del listado
    // busqueda
    // Cargamos los usuarios
    var search = document.getElementById("mySearch");
    search.addEventListener("focus", function (event) {
      document.getElementById("iconSearch").style.visibility = "hidden";
    });
    search.addEventListener("focusout", function (event) {
      if (search.value === "") {
        document.getElementById("iconSearch").style.visibility = "visible";
      }
    });
    search.addEventListener("keyup", function (event) {
      if (event.keyCode === 13) {
        event.preventDefault();
        mySearchClean(document.querySelectorAll('[id="myClientLine"]'));
        loadClients(search.value);
      }
    });
  
    search.addEventListener("search", function (event) {
      event.preventDefault();
      mySearchClean(document.querySelectorAll('[id="myClientLine"]'));
      loadClients();
    });
    // if (sessionStorage.getItem("prd_mod")=="true"){
    //     // boton nuevo
    document
      .getElementById("stdbtnCreate")
      .addEventListener("click", function () {
        clearFormClient();
        //showModal("editClient","closeModalEdit", "Crear Usuario");
        showPage("cntFrm");
      });
  
  
    document.getElementById("stdbtnNo").addEventListener("click", function () {
      document.getElementById("myModalDelete").style.display = "none";
    });
  
    document.getElementById("stdbtnYes").addEventListener("click", function () {
      var id = document.getElementById("idDeleteSecurity").value;
      clientsDelete(id);
    });
  
    document
      .getElementById("stdbtnSubmit")
      .addEventListener("click", function () {
        clientsUpdate();
    });

    document.getElementById("stdbtnCancel").addEventListener("click", function(){
        //document.getElementById('editClient').style.display = "none";
        //showPage("homeCenter");
        showModal('confirmModal','closeModalConfirm',"")
    });

    document.getElementById("stdbtnNoConfirm").addEventListener("click", function(){
        document.getElementById('confirmModal').style.display = "none";
        //document.getElementById('editClient').style.display = "block";
        
    });

    document.getElementById("stdbtnYesConfirm").addEventListener("click", function(){
        document.getElementById('confirmModal').style.display = "none";
        //document.getElementById('editClient').style.display = "none";
        showPage("homeCenter");
    });
  
  
    // Tabs
  /*
    tabContent = document.getElementsByClassName("tabContent");
    tab = document.getElementsByClassName("tab");
    hideTabsContent(1);
  
    document.getElementById("tabs").addEventListener("click", function (event) {
      var target = event.target;
      if (target.className == "tab") {
        for (var i = 0; i < tab.length; i++) {
          if (target == tab[i]) {
            showTabsContent(i);
            break;
          }
        }
      }
    });*/
  
    // PAra las mask del tlf
  
    document.getElementById("tlfClient").addEventListener("keypress",function(e){
      let tlf = document.getElementById("tlfClient").value;
      tlf = tlf.replaceAll("(","");
      tlf = tlf.replaceAll(")","");
      tlf = tlf.replaceAll("-","");
      let format = formatTlf(tlf);
      document.getElementById("tlfClient").value = format;
    })

    // Para el mask del rif

    document.getElementById("rifClient").addEventListener("keypress",function(e){
      let rif = document.getElementById("rifClient").value;
      rif = rif.replaceAll("-","");
      let format = formatRif(rif);
      document.getElementById("rifClient").value = format;
    })

    // Para el num de serie 
    /*
    document.getElementById("serieClient").addEventListener("keypress",function(e){
      e.preventDefault();
      let serie = document.getElementById("serieClient").value
      if(isAlphaNum(e.key) && serie.length<2){
        serie = serie + e.key.toLocaleUpperCase();
        document.getElementById("serieClient").value = serie;
      }
    })

    document.getElementById("numSeniatClient").addEventListener("keypress",function(e){
      e.preventDefault();
      let num = document.getElementById("numSeniatClient").value;
      if (isAlphaNum(e.key)){
        num += e.key;
        let format = formatControl(num);
        document.getElementById("numSeniatClient").value = format;
      }
    })*/
  
    // para el file
  
    document.getElementById("accLogo").addEventListener("click", function (e) {
      document
        .querySelector("#refreshFileIcon path")
        .setAttribute("id", "refreshFilePath");
      document
        .querySelector("#removeFileIcon path")
        .setAttribute("id", "removeFilePath");
      if (
        e.target.id == "removeFile" ||
        e.target.id == "removeFileIcon" ||
        e.target.id == "removeFilePath"
      ) {
        document.getElementById("accLogoImg").value = "";
        fileToSend = "";
        document.getElementById("innerFile").style.backgroundImage = "none";
        document.getElementById("innerFile").style.display = "none";
        document.getElementById("innerFileText").style.display = "inherit";
      } else {
        document.getElementById("accLogoImg").click();
      }
    });
  
    document
      .getElementById("accLogo")
      .addEventListener("dragenter", function (e) {
        e.preventDefault();
      });
  
    document.getElementById("accLogo").addEventListener("dragover", function (e) {
      e.preventDefault();
    });
  
    document.getElementById("accLogo").addEventListener("drop", function (e) {
      e.preventDefault();
      let dt = e.dataTransfer;
      let files = dt.files;
      drop = true;
      showFiles(files[0]);
    });
  
    document.getElementById("accLogoImg").addEventListener("change", function () {
      if (this.files && this.files[0]) {
        showFiles(this.files[0]);
      }
    });

    // Para controlar el switch de estatus
    /*
    document.getElementById("checkClient").value = "off";
    document.getElementById("checkStatus").addEventListener("click", function(e){
        let status = document.getElementById("checkClient").value
        console.log("status",status);
        if (status == "off"){
            document.getElementById("checkClient").value="on";
        }else{
            document.getElementById("checkClient").value="off";
        }
    })*/
    
    //Status del switch cliente 
    document.getElementById("switchStatus").addEventListener("click",function(){
      if(this.classList.contains("active")){
          this.classList.remove("active");
          this.firstElementChild.classList.remove("balActive");
      }else{
          this.classList.add("active");
          this.firstElementChild.classList.add("balActive");
      } 
  });

  //Evento del botón tabla inferior para añadir lineas de items details
  document.getElementById("addItemDetails").addEventListener("click",function(){
    addItemDet();
  });
  //Función que clona la linea actual con datos cargados y la inserta cómo nueva linea
  function addItemDet(){
    //Validación de datos
    var serie = "",control = "",initnum = "",idname = "";
    serie = document.getElementById("newSerie").value;
    control = document.getElementById("newControl").value;
    initnum = document.getElementById("newInitnum").value;
    idname = document.getElementById("newIdname").value;
    /*
    var failserie = false;
    if(serie==""||serie==null)failserie = true;*/
    var failcontrol = false;
    if(control==""||control==null)failcontrol = true;
    var failinitnum = false;
    if(initnum==""||initnum==null)failinitnum = true;
    if(failcontrol || failinitnum){
      /*
      if(failserie){
        inptError(document.getElementById("newSerie"),"Ingrese una serie");
      }*/
      if(failcontrol){
        inptError(document.getElementById("newControl"),"Ingrese número de control");
      }
      if(failinitnum){
        inptError(document.getElementById("newInitnum"),"Ingrese número de inicio");
      }
      return false;
    }else{
      removeErr(document.getElementById("newSerie"));
      removeErr(document.getElementById("newControl"))
      removeErr(document.getElementById("newInitnum"));
    }
    //Validar que no exista una serie + nro de control ya existente
    var list = document.getElementById("itemsList");
    var rows = list.getElementsByClassName("itemsRow");
    var newcontrol = document.getElementById("newSerie").value+"-"+document.getElementById("newControl").value;
    for(var i=0;i<rows.length;i++){
      var nrocntrol = rows[i].getElementsByClassName("newSerie")[0].value+"-"+rows[i].getElementsByClassName("newControl")[0].value;
      if(nrocntrol == newcontrol){
        inptError(document.getElementById("newSerie"),"Serie y Control ya existe");
        document.getElementById("newSerie").parentElement.parentElement.getElementsByClassName("msgErrInpt")[0].classList.add("errAbs");
        inptError(document.getElementById("newControl"));
        return false;
      }
    }

    var itemLine = document.getElementById("addItemDetails").parentElement.cloneNode(true);
    /*
    var fields = ["inptPrice","inptDisc","inptIVA","inptTot"]
    var format = ['amount','percent','percent','amount'];
    formatFields2(fields,format,2,itemLine);
    */
    //Se quita el id del origen
    var inptsAdd = itemLine.getElementsByTagName("input");
    for(var x=0;x<inptsAdd.length;x++){
      inptsAdd[x].removeAttribute("id");
      inptsAdd[x].setAttribute("disabled","");
    }
    //Se elimina el div del label porque no se muestra en las lineas
    var inptsLbl = itemLine.getElementsByClassName("inptLbl");
    for(var x=(inptsLbl.length-1);inptsLbl.length>0;x=(inptsLbl.length-1)){
      if(inptsLbl[x].parentElement.getElementsByClassName("lineRight").length>0){
        inptsLbl[x].parentElement.getElementsByClassName("lineRight")[0].style.top = "5px";
      }
      inptsLbl[x].parentElement.removeChild(inptsLbl[x]);
    }
    itemLine.getElementsByClassName("itemBtn")[0].style.top = "5px";
    //Evento de eliminar la linea 
    itemLine.getElementsByClassName("itemBtn")[0].addEventListener("click",function(){
      this.parentElement.parentElement.removeChild(this.parentElement);
      //recalcTotals();
    });

    
    var items = document.getElementById("itemsList").children;
    if(items.length<=0)document.getElementById("itemsList").appendChild(itemLine);  
    else document.getElementById("itemsList").insertBefore(itemLine,items[0]);
    
    //Se blanquea la linea de "agregar" luego que se inserta el registro
    var inptsAdd = document.getElementById("addItemDetails").parentElement.getElementsByTagName("input");
    for(var x=0;x<inptsAdd.length;x++){
      inptsAdd[x].value = "";
    }

    

    //Actualizar btn por un menos y evento de eliminar la linea    
    itemLine.getElementsByClassName("itemBtn")[0].children[0].classList.add("fa-minus");
    itemLine.getElementsByClassName("itemBtn")[0].children[0].classList.remove("fa-plus");
  }

  document.getElementById("newInitnum").addEventListener("keyup",function(){
    let padToFour = number => number <= 99999999 ? `0000000${number}`.slice(-8) : number.slice(0,8);
    this.value = padToFour(parseFloat(this.value).toFixed(0));
  });
  document.getElementById("newControl").addEventListener("keyup",function(){
    let padToFour = number => number <= 99 ? `00${number}`.slice(-2) : number.slice(0,2);
    this.value = padToFour(parseFloat(this.value).toFixed(0));
  });
  document.getElementById("newSerie").addEventListener("keyup",function(){
    this.value = (this.value).toUpperCase();
  }); 
  }
  
var sessid = getParameterByName('sessid');
function logOut() {
  var par = {};
  par.sessionid = getParameterByName("sessid");
  var success = function(status, respText) {
      var jsonResp;
      if(respText!="")jsonResp = JSON.parse(respText);
      switch (status) {
          case 200:
            // ir a pagina de login
            gotoPage("login", "main", "");
          break;
          case 400:
            console.log(jsonResp);
          break;
          case 401:
            gotoPage("login", "main", "");
          break;
          case 500:
            console.log(jsonResp);
          break;
          default:
            console.log(jsonResp);
          break;
      }
  }
  callWS("GET", "security/logout", par, success);
}
function loadAudit(offset = 0,filter="",order=-1,numrecords=10){
  var par = {};
  if(filter !== "" && filter !== undefined && filter !== null)
      par.filter = filter;
  else if(document.getElementById("mySearch").value!==""){
      par.filter = document.getElementById("mySearch").value;
  }
  
  let now = new Date();
  now = now.toISOString().split('T')[0];

  let desde = document.getElementById('dateDesde').value;
  let hasta = document.getElementById('dateHasta').value;

  par.datefrom = desde == "" ? now : desde;
  par.dateto = hasta == "" ? now : hasta;

  par.offset = offset;
  par.order = order;
  par.numofrec = numrecords;
  par.customerid = document.getElementById("usersList").value;
  if(par.customerid=="")par.customerid=-1;
  par.sessionid = getParameterByName("sessid");

  par.user = document.getElementById("usersList").value;
  par.module = document.getElementById("modulsList").value;

  var success = function(status, respText, offset){
    var jsonResp;
    if(respText!="")jsonResp = JSON.parse(respText);
    switch (status){
        case 200:
            console.log(jsonResp);            
            drawPagination(offset, jsonResp.numofrecords);
            drawInvoices(jsonResp);            
        break;
        case 400:
        break;
        case 401:
            gotoPage("login","main",{});
        break;
        case 500:
        break;
        default:
        break;
    }
  }
  callWS("GET", "audit/list", par, success, offset );
  return;
}
// Esta funcion crea la tabla diamicamente segun la respuesta de user/list
function drawInvoices(data){
    let cont;
    let btn;
    let table=document.getElementById("centerTable");
    table.innerHTML = "";
    
    var fulldata = data;
    /*
    document.getElementById("baseAmo").innerText = fulldata.totals.taxbase.formatted;
    document.getElementById("impAmo").innerText = fulldata.totals.tax.formatted;
    document.getElementById("totAmo").innerText = fulldata.totals.total.formatted;
    //Total de documentos
    document.getElementById("totDocs").innerText = fulldata.numofrecords+" Documentos";
    //Total de documentos
    var regperpage = 10;
    var tot = Math.ceil(fulldata.numofrecords/regperpage);
    document.getElementById("totPagsLbl").innerText = "de "+tot;

    var inptpage = document.getElementById("actPag");
    inptpage.setAttribute("max",tot);
    if(tot==1)inptpage.setAttribute("disabled","");
    else inptpage.removeAttribute("disabled");    
    */
    if(fulldata.records.length<=0){
      document.getElementById("pagination").style.display = "none";
    }else{
      document.getElementById("pagination").style.display = "";
    }
    var data = data.records;
    // Headers
        let line = document.createElement("tr");
        line.style.height = "32px";
        celda = document.createElement("th");
        celda.classList.add("thDate","cell20");
        celda.innerHTML = "Módulo";
        line.appendChild(celda);

        celda = document.createElement("th");
        celda.classList.add("thDate","cell30");
        celda.innerHTML = "Usuario";
        line.appendChild(celda);
        
        celda = document.createElement("th");
        celda.classList.add("thRef","cell20");
        celda.innerHTML = "Fecha";
        line.appendChild(celda);

        celda = document.createElement("th");
        celda.classList.add("thControl","cell30");
        celda.innerHTML = "Descripción";
        line.appendChild(celda);
        
        table.appendChild(line);
    // End Headers

    for(navLink of data){
        line = document.createElement("tr");
        line.classList.add('userLine');
        line.id = navLink.id;
        
        celda = document.createElement("td");
        celda.classList.add("thDate");
        celda.id="date-"+navLink.id;
        celda.innerHTML = '<span>'+navLink.issuedate.formatted+'</span>';
        line.appendChild(celda);

        celda = document.createElement("td");
        celda.style.textAlign = "right";
        celda.id="mount-"+navLink.id;
        celda.innerHTML = '<span>'+navLink.amounts.gross.formatted+'</span>';
        line.appendChild(celda);

        celda = document.createElement("td");
        celda.id="impto-"+navLink.id;
        celda.style.textAlign = "right";
        celda.innerHTML = '<span>'+navLink.amounts.tax.formatted+'</span>';
        line.appendChild(celda);

        celda = document.createElement("td");
        celda.id="total-"+navLink.id;
        celda.style.textAlign = "right";
        celda.style.fontWeight = "bold";
        celda.innerHTML = '<span>'+navLink.amounts.total.formatted+'</span>';
        line.appendChild(celda);
        
        table.appendChild(line);
    }

}  
function loadModules(){
  var par = {};  
  par.sessionid = getParameterByName("sessid");

  var success = function(status, respText){
    var jsonResp;
    if(respText!="")jsonResp = JSON.parse(respText);
    switch (status){
        case 200:     
            var select = document.getElementById("modulsList");
            var id = 'id';
            var dsc = 'dsc';  
            var first = 'Seleccione un Módulo';
            var newArr = [];
            debugger;
            for(var i=0;i<jsonResp.records.length;i++){
                var ele = {};
                ele.id = jsonResp.records[i].application+"-*";
                ele.dsc = jsonResp.records[i].application+" - Todos";
                ele.platform = jsonResp.records[i].application;
                newArr.push(ele);
              for(var x=0;x<jsonResp.records[i].modules;x++){
                var ele = {};
                ele.id = jsonResp.records[i].application+"-"+jsonResp.records[i].modules[x];
                ele.dsc = jsonResp.records[i].application+" - "+jsonResp.records[i].modules[x];
                ele.platform = jsonResp.records[i].application;
                newArr.push(ele);
              }
            }     
            drawSelectCustom(newArr, select, id, dsc, first, "");
        break;
        case 400:
        break;
        case 401:
            gotoPage("login","main",{});
        break;
        case 500:
        break;
        default:
        break;
    }
  }

  callWS("GET", "audit/getmodules", par, success);
  return;
}
function loadUsers(platform,module){
  var par = {};  
  par.sessionid = getParameterByName("sessid");
  par.app = platform;
  par.module = module;

  var success = function(status, respText){
    var jsonResp;
    if(respText!="")jsonResp = JSON.parse(respText);
    switch (status){
        case 200:     
            var select = document.getElementById("usersList");
            var id = 'id';
            var dsc = 'name';  
            var first = 'Seleccione un Usuario';     
            drawSelectCustom(jsonResp.records, select, id, dsc, first, "");
        break;
        case 400:
        break;
        case 401:
            gotoPage("login","main",{});
        break;
        case 500:
        break;
        default:
        break;
    }
  }

  callWS("GET", "audit/getusers", par, success);
  return;
}
//Esta función nos permitirá cambiar entre el "formaulario creación" y la "lista", o cualquier otra pantalla que se agregue
//id - Recibe la pantalla
function showPage(id){
    //Oculta todas las pantallas primero opacidad luego quito el display para hacer transición suave
    var pages = document.getElementsByClassName("page");
    for (var i = 0; i < pages.length; i++) {
      pages[i].style.opacity = "";
    }    
    var lastpage = id;
    setTimeout(function(){
      for (var i = 0; i < pages.length; i++) {
        pages[i].style.display = "";
      }      
      //Muestra la pagina solicitada
      document.getElementById(lastpage).style.display = "block";  
      setTimeout(function(){        
        document.getElementById(lastpage).style.opacity = "1";
      },100);  
    },400);
}
function drawPagination(offset, numofrecords, recordsbypage = 10) {
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
    loadAudit();
  });
  inp.appendChild(o);
  o = document.createElement("span");
  o.classList.add("back");
  o.innerHTML = '<i class="fa fa-angle-left"></i>';
  if (actpag > 1) {
    o.classList.add("colorBlue");
    o.addEventListener("click", function () {
      loadAudit((actpag - 2) * recordsbypage);
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
      loadAudit((1 * this.innerHTML - 1) * recordsbypage);
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
      loadAudit(actpag * recordsbypage);
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
    loadAudit((lastpag - 1) * recordsbypage);
  });
  inp.appendChild(o);
}
function init() {

    // Cargamos los usuarios a mostrar en la tabla
    document.getElementById("usernameMenu").innerText = sessionStorage.getItem("username");

    //Evento de salir
    document.getElementById("logOut").addEventListener("click", logOut);

    
    // Filtros PERIODO Y FECHAS    
    /*
    let period = document.getElementById('periodoSelect');
    period.addEventListener('change',function(e){
        document.getElementById("actPag").value = 1;
        const now = new Date();
        let fechas = [0,0]
        if(e.target.value == 0 ){
          fechas = getLastWeeksDates(0,now.getDay()) 
        }else if (e.target.value == 1){
          fechas = getLastWeeksDates(15-now.getDay(),now.getDay()) 
        }else if(e.target.value == 2){
          fechas = getLastWeeksDates(0,now.getDate()-1) 
        }else if(e.target.value == 3){
          fechas[0] = obtenerFechaInicioDeMes(now.getMonth()-2)
          fechas[1] = obtenerFechaFinDeMes(now.getMonth()-2)
          console.log('fechas', fechas)
        }else if(e.target.value==4){
          fechas[0] = new Date(now.getFullYear(), 0, 1);
          fechas[1] = now;
        }else if(e.target.value == 5){
          fechas[0]=now;
          fechas[1]=now;
        }
        desde.value = fechas[0].toISOString().split('T')[0];
        hasta.value =  fechas[1].toISOString().split('T')[0];
        document.getElementById("actPag").value = 1;
        loadAudit();
    });*/
    
    let desde = document.getElementById('dateDesde');
    let hasta = document.getElementById('dateHasta');
    desde.addEventListener('change',function(){
      loadAudit();
    });
    hasta.addEventListener('change',function(){
      loadAudit();
    });    
    
    document.getElementById("modulsList").addEventListener("change",function(){  
      var platform = this.options[this.selectedIndex].getAttribute("platform");
      var module = this.options[this.selectedIndex].getAttribute("value");
      loadUsers(platform,module);
    });
    /*
    document.getElementById("btnPagRight").addEventListener("click",function(){
      var actpag = document.getElementById("actPag");
      if(actpag.getAttribute("max")>=(parseFloat(actpag.value)+1)){
        actpag.value = parseFloat(actpag.value)+1;        
        loadAudit();
      }
    });
    document.getElementById("btnPagLeft").addEventListener("click",function(){
      var actpag = document.getElementById("actPag");
      if(actpag.value>1){
        actpag.value = parseFloat(actpag.value)-1;        
        loadAudit();
      }
    });
    document.getElementById("actPag").addEventListener("change",function(){
      if(parseFloat(this.value) < 1){
        this.value = 1;
      }else if(parseFloat(this.value) > parseFloat(this.getAttribute("max"))){
        this.value = this.getAttribute("max");
      }
      loadAudit();
    });*/
    //Evento de la busqueda
    document.getElementById("mySearch").addEventListener("change",function(){
      loadAudit(this.value);
    });

    loadModules();
    loadAudit();
    
    showPage("homeCenter");
};
 

function getLastWeeksDates(x,day) {
  const now = new Date();
  let start = new Date(now.getFullYear(), now.getMonth(), now.getDate() - x - day);
  let end = now;
  return [start,end];
}

const obtenerFechaInicioDeMes = (mes) => {
  const fechaInicio = new Date();
  // Iniciar en este año, este mes, en el día 1
  return new Date(fechaInicio.getFullYear(), mes + 1, 1);
};

const obtenerFechaFinDeMes = (mes) => {
  const fechaFin = new Date();
  // Iniciar en este año, el siguiente mes, en el día 0 (así que así nos regresamos un día)
  return new Date(fechaFin.getFullYear(), mes + 2, 0);
};
function formatRefctr(valor){  
  var cleaned = ('' + valor.toUpperCase()).replace(/[^a-zA-Z0-9]/g, '');
  if (cleaned.length){
      var prefijo, area, numero;
    
      if(cleaned.length>=12){
        cleaned = cleaned.substr(0,12);
      }

      if(cleaned.length>8){                
        numero = cleaned.substr((cleaned.length-8),8);
        if(Math.abs(cleaned.length-8)>0){
          if(Math.abs(cleaned.length-8)>2){            
            area = cleaned.substr((Math.abs(cleaned.length-8)-2),2);
            prefijo = cleaned.substr(0,(Math.abs(cleaned.length-8)-2));
          }else{            
            area = cleaned.substr(0,Math.abs(cleaned.length-8));            
            prefijo = "";
          }
        }
        var fullnum = "";
        if(prefijo!="")
          fullnum = (prefijo+"-"+area+"-"+numero);
        else if(area!="")
          fullnum = (area+"-"+numero);
        else if(numero!="")
          fullnum = (numero);


        return fullnum;
      }else{
        return cleaned;
      }
  }else{
      return("");
  }
}
function waitOn(){
  /*WAIT on*/
  var wait = document.createElement("div");
  wait.classList.add("waitScreen");
  wait.classList.add("textCenter");
  wait.setAttribute("id", "waitScreen");
  var spin = document.createElement("div");
  spin.classList.add("spinChar");
  spin.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i>';
  wait.appendChild(spin);
  setTimeout(function(){    
    var waitScreen = document.getElementById("waitScreen");
    waitScreen.style.opacity = "1";
  },300);  
  document.body.appendChild(wait);
}
function waitOff(){
  setTimeout(function(){        
    var waitScreen = document.getElementById("waitScreen");
    waitScreen.style.opacity = "";
    setTimeout(function(){
      var waitScreen = document.getElementById("waitScreen");
      document.body.removeChild(waitScreen);
      WS_waitscreen = false;
    },300);
  },500);
}
/*****
 * El parametro 1 debe ser el arreglo con los registros (opciones)
 * El parametro 2 debe ser el elemento (select) donde se insertaran las opciones * 
 * El parametro 3 corresponde al valor a ser leído cómo value * 
 * El parametro 4 corresponde al valor a ser leído cómo dsc *
 * El parametro 5 es opcional crea una primera opcion con value=0 se puede pasar en blanco, null o undefined si no se desea
 * El parametro 6 es una opci贸n selecionada
 * ****/
 function drawSelectCustom(rsp, select, id, dsc, first="", selected="") {
  select.innerHTML = "";
  var opt;
  if (first !== "" && first !== null && first !== undefined) {
      opt = document.createElement("option");
      opt.setAttribute("value", "");
      opt.innerHTML = first;
      select.appendChild(opt);
  }
  for (var i = 0; i < rsp.length; i++) {
      opt = document.createElement("option");
      opt.setAttribute("value", rsp[i][id]);
      opt.setAttribute("platform", rsp[i]['platform']);
      if (rsp[i][id] == selected) opt.setAttribute("selected", true);
      opt.innerHTML = rsp[i][dsc];
      select.appendChild(opt);
  }
}

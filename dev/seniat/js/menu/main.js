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
function downloadReport(){
  var par = {};
  if(document.getElementById("mySearch").value!==""){
    par.filter = document.getElementById("mySearch").value;
  }
  
  let now = new Date();
  now = now.toISOString().split('T')[0];

  let desde = document.getElementById('dateDesde').value;
  let hasta = document.getElementById('dateHasta').value;

  par.datefrom = desde == "" ? now : desde;
  par.dateto = hasta == "" ? now : hasta;
  var actpag = document.getElementById("actPag").value;
  var offset = (actpag*10)-10;
  par.status = "1-2-3";
  par.offset = offset;
  par.order = -1;
  par.numofrec =  10;
  par.customerid = document.getElementById("customersList").value;
  if(par.customerid=="")par.customerid=-1;      
  par.sessionid = getParameterByName("sessid");
  var parsedPars = Object.keys(par).map(function (k) {
    return encodeURIComponent(k) + "=" + encodeURIComponent(par[k]);
  })
  .join("&");
  download("documentos.csv",globalurl+"/api/invoices/listcsv.php?" + parsedPars);
}
function loadInvoices(filter="",order=-1,numrecords=10){
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

  par.status = "1-2-3";
  var actpag = document.getElementById("actPag").value;
  var offset = (actpag*numrecords)-numrecords;
  par.offset = offset;
  par.order = order;
  par.numofrec = numrecords;
  par.customerid = document.getElementById("customersList").value;
  if(par.customerid=="")par.customerid=-1;
  par.sessionid = getParameterByName("sessid");

  var success = function(status, respText, offset){
    var jsonResp;
    if(respText!="")jsonResp = JSON.parse(respText);
    switch (status){
        case 200:
          console.log(jsonResp);
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

  callWS("GET", "invoices/list", par, success, offset );
  return;
}
/*
function drawInvoicesPhone(rsp){
var tbl = document.getElementById("responsiveTable");
var ele = tbl.children[0].cloneNode(true);
if(rsp.length>0){      
  tbl.innerHTML = "";
  for(var i=0;i<rsp.length;i++){
    var clone = ele.cloneNode(true);      
    clone.style.display = "";
    var navLink = rsp[i];

    var arrow = clone.getElementsByClassName("arrowIcon")[0];
    arrow.addEventListener("click",function(){
      var cnt = this.parentElement;
      var eleHides = cnt.getElementsByClassName("hideOpt");
      if(!this.getAttribute("open")){
        for(var i=0;i<eleHides.length;i++){
          eleHides[i].style.display = "table-row";
        }
        this.setAttribute("open",true);
        this.innerHTML = '<i class="fa-solid fa-caret-up"></i>';
      }else{
        for(var i=0;i<eleHides.length;i++){
          eleHides[i].style.display = "";
        }
        this.removeAttribute("open");
        this.innerHTML = '<i class="fa-solid fa-caret-down"></i>';
      }
    });

    var mark = clone.getElementsByClassName("inptMark")[0];
    mark.setAttribute("invcid",navLink.id);
    mark.addEventListener("click",function(){
      var btnsCnt = document.getElementById("buttonsCell");
      var marks = document.getElementsByClassName("inptMark");
      var selected = 0;
      for(var i=0;i<marks.length;i++){
        if(marks[i].checked){
          selected++;
        }
      }
      var qtyCnt = document.getElementById("invoicesQtySel");
      if(selected>0){
        btnsCnt.getElementsByClassName("btnIconSubh")[0].classList.remove("btnIconSubhDis");
        btnsCnt.getElementsByClassName("btnIconSubh")[1].classList.remove("btnIconSubhDis");
        qtyCnt.innerText = selected;
      }else{
        btnsCnt.getElementsByClassName("btnIconSubh")[0].classList.add("btnIconSubhDis");
        btnsCnt.getElementsByClassName("btnIconSubh")[1].classList.add("btnIconSubhDis");
        qtyCnt.innerText = "No hay";
      }
    });
    
    var celda = clone.getElementsByClassName("despValCell")[0].children[0];
    celda.id="date-"+navLink.id;
    celda.innerHTML = '<span>'+navLink.issuedate.formatted+'</span>';

    var celda = clone.getElementsByClassName("despValCell")[1].children[0];
    celda.id="ref-"+navLink.id;
    celda.innerHTML = '<span>'+navLink.refnumber+'</span>';

    var celda = clone.getElementsByClassName("despValCell")[2].children[0];
    celda.id="control-"+navLink.id;
    celda.innerHTML = '<span>'+formatRefctr(navLink.ctrnumber)+'</span>';
    

    var celda = clone.getElementsByClassName("despValCell")[3].children[0];
    celda.id="ci-"+navLink.id;
    celda.innerHTML = '<span>'+navLink.client.rif+'</span>';

    var celda = clone.getElementsByClassName("despValCell")[4].children[0];
    celda.id="name-"+navLink.id;
    celda.innerHTML = '<span>'+navLink.client.name+'</span>';

    var celda = clone.getElementsByClassName("despValCell")[5].children[0];
    celda.id="mount-"+navLink.id;
    celda.innerHTML = '<span>'+navLink.amounts.gross.formatted+'</span>';

    var celda = clone.getElementsByClassName("despValCell")[6].children[0];
    celda.id="impto-"+navLink.id;
    celda.innerHTML = '<span>'+navLink.amounts.tax.formatted+'</span>';

    var celda = clone.getElementsByClassName("despValCell")[7].children[0];
    celda.id="total-"+navLink.id;
    celda.style.fontWeight = "bold";
    celda.innerHTML = '<span>'+navLink.amounts.total.formatted+'</span>';

    var celda = clone.getElementsByClassName("despValCell")[8].children[0];
    celda.id="status-"+navLink.id;
    celda.innerHTML = '<span class="status-color-'+navLink.status.id+'">'+navLink.status.dsc+'</span>';

    tbl.appendChild(clone);
  }      
}else{
  var clone = ele.cloneNode(true);
  clone.style.display = "none";
  tbl.innerHTML = "";
  tbl.appendChild(clone);
}
}
*/
// Esta funcion crea la tabla diamicamente segun la respuesta de user/list
function drawInvoices(data){
    let cont;
    let btn;
    let table=document.getElementById("centerTable");
    table.innerHTML = "";
    
    var fulldata = data;
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

    if(fulldata.records.length<=0){
      document.getElementById("pagination").style.display = "none";
    }else{
      document.getElementById("pagination").style.display = "";
    }
    var data = data.records;
    // Headers
        let line = document.createElement("tr");
        
        celda = document.createElement("th");
        celda.classList.add("thDate","cell7Mid");
        celda.innerHTML = "Fecha";
        line.appendChild(celda);

        celda = document.createElement("th");
        celda.classList.add("thDate","cell7Mid");
        celda.innerHTML = "Tipo";
        line.appendChild(celda);
        
        celda = document.createElement("th");
        celda.classList.add("thRef","cell7Mid");
        celda.innerHTML = "Número";
        line.appendChild(celda);

        celda = document.createElement("th");
        celda.classList.add("thControl","cell12Mid");
        celda.innerHTML = "Control";
        line.appendChild(celda);
        
        celda = document.createElement("th");
        celda.classList.add("thCI","cell10");
        celda.innerHTML = "Cédula/RIF";
        line.appendChild(celda);

        celda = document.createElement("th");
        celda.classList.add("thName","cell15");
        celda.innerHTML = "Cliente";
        line.appendChild(celda);

        celda = document.createElement("th");
        celda.classList.add("thMount","cell7Mid");
        celda.innerHTML = "Monto";
        line.appendChild(celda);
        
        celda = document.createElement("th");
        celda.classList.add("thImpto","cell7Mid");
        celda.innerHTML = "IVA";
        line.appendChild(celda);

        celda = document.createElement("th");
        celda.classList.add("thTotal","cell7Mid");
        celda.innerHTML = "Total";
        line.appendChild(celda);

        celda = document.createElement("th");
        celda.classList.add("thStatus","cell7Mid");
        celda.innerHTML = "Estatus";
        line.appendChild(celda);
        
        celda = document.createElement("th");
        celda.classList.add("thAction","cell2Mid");
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
        celda.classList.add("thDate");
        celda.innerHTML = navLink.type.name;
        line.appendChild(celda);

        celda = document.createElement("td");
        celda.id="ref-"+navLink.id;
        
        celda.style.textAlign = "right";
        celda.innerHTML = '<span>'+navLink.refnumber+'</span>';
        line.appendChild(celda);
        celda = document.createElement("td");
        celda.id="control-"+navLink.id;        
        celda.style.textAlign = "right";
        celda.innerHTML = '<span>'+formatRefctr(navLink.ctrnumber)+'</span>';
        line.appendChild(celda);
        

        celda = document.createElement("td");
        celda.classList.add("thCI");
        celda.id="ci-"+navLink.id;        
        celda.style.textAlign = "right";
        celda.innerHTML = '<span>'+navLink.client.rif+'</span>';
        line.appendChild(celda);

        celda = document.createElement("td");
        celda.classList.add("thName");
        celda.id="name-"+navLink.id;
        celda.innerHTML = '<span>'+navLink.client.name+'</span>';
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

        celda = document.createElement("td");
        celda.classList.add("thStatus");
        celda.id="status-"+navLink.id;
        celda.innerHTML = '<span class="status-color-'+navLink.status.id+'">'+navLink.status.dsc+'</span>';
        line.appendChild(celda);

        
        celda = document.createElement("td");
        cont = document.createElement("span");
        celda.classList.add("thAction");
        if (true){
        // TODO: Revisar este if
        // if (sessionStorage.getItem("prd_mod")=="true"){
            cont.classList.add("userIcons");
                // editar
                btn = document.createElement("a");
                btn.setAttribute("eyeid", navLink.id);
                btn.classList.add("eyeIcon");
                btn.addEventListener("click", function(){                  
                  invoiceEntry(this.getAttribute("eyeid"),true);
                });
                btn.innerHTML = '<i class="fas fa-eye"></i>';
                cont.appendChild(btn);

          
        }
        celda.appendChild(cont);
        
        line.appendChild(celda);

        table.appendChild(line);
    }

}  
function loadCustomers(filter="",offset=0,order=2,numrecords=100000){
  var par = {};  
  par.filter = "";
  par.status = "1"
  par.offset = offset;
  par.order = order;
  par.numofrec = numrecords;
  par.sessionid = getParameterByName("sessid");

  var success = function(status, respText, offset){
    var jsonResp;
    if(respText!="")jsonResp = JSON.parse(respText);
    switch (status){
        case 200:     
            var select = document.getElementById("customersList");
            var id = 'id';
            var dsc = 'name';  
            var first = 'Seleccione un Contribuyente';     
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

  callWS("GET", "customers/list", par, success, offset );
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
  //Se llama el servicio del entry
  function invoiceEntry(id,view=false){
    var par = {};
    par.invoiceid = id;
    par.sessionid = sessid;
    par.customerid = document.getElementById("customersList").value;
    if(par.customerid=="")par.customerid=-1;
    var succes = function(status, respText){
      var resp = JSON.parse(respText);
      switch (status){
          case 200:
            console.log(resp);
            viewDocument(resp.entry);
          break;
          case 400:
              console.log(resp);
              break;
          case 401:
              console.log(resp);
              break;
          case 500:
              console.log(resp);
              break;
          default:
              console.log(resp);
              break;
      }
    }
    callWS("GET", "invoices/entry", par, succes, "");
    return;
  }
//Función para visualizar el documento en el iframe previsualización
function viewDocument(rsp){
    var lblPrev = "";
    var type = rsp.header.type.id;
    var nro = rsp.header.refnumber;
    var id = rsp.header.id;    
    var sessid = getParameterByName("sessid");
    document.getElementById("viewIssueDate").innerText = rsp.header.issuedate.formatted;
    if(type=='FAC'){
      lblPrev = "Factura "+nro;
    }else if(type=='NCR'){
      lblPrev = "Nota Crédito "+nro;
    }else if(type=='NDB'){
      lblPrev = "Nota Débito "+nro;
    }
    document.getElementById("invName").innerText = lblPrev;
    waitOn();
    setTimeout(function(){    
      //Se actualiza el frame
      var frame = document.getElementById("frameView");
      customerid = document.getElementById("customersList").value;
      if(customerid=="")par.customerid=-1;
      frame.setAttribute("src","./api/invoices/show.php?id="+id+"&sessionid="+sessid+"&customerid="+customerid);
      showViewer();
      frame.onload = function(){
        setTimeout(function(){        
          var frame = document.getElementById("frameView");
          frame.style.opacity = "1";
          waitOff();
        },200);
      };
    },200);   

}
//Muestra el popup de visualización de la factura 
function showViewer(){
  var ele = document.getElementById("invViewer");
  ele.style.display = "block";
  setTimeout(function(){      
    ele.style.opacity = "1";
  },300);
}
//Oculta el popup de visualización de la factura 
function closeViewer(){
  var ele = document.getElementById("invViewer");
  ele.style.opacity = "";
  setTimeout(function(){      
    ele.style.display = "";
  },300);
}
window.onload = function () {
    document.getElementById("usernameMenu").innerText = sessionStorage.getItem("username");

    //Evento para cancelar visualizar invoice
    document.getElementById("backViewer").addEventListener("click",function(){
      closeViewer();
      document.getElementById("frameView").style.opacity = "";
    });
    //Evento de imprimir la pantalla del viewer
    document.getElementById("printView").addEventListener("click",function(){
      window.frames[0].print();
    });

    //Evento de salir
    document.getElementById("logOut").addEventListener("click", logOut);

    //Flecha para ver fechas con status de la factura
    document.getElementById("viewStatusBtn").addEventListener("click",function(){
      var tbl = document.getElementById("statusPopup");
      if(tbl.style.display == ""){
        tbl.style.display = "table";
        setTimeout(function(){
          document.getElementById("statusPopup").style.opacity = "1";
        },100);
      }else{
        tbl.style.opacity = "";
        setTimeout(function(){
          document.getElementById("statusPopup").style.display = "";
        },300);
      }
    });
    
    
    // Filtros PERIODO Y FECHAS
    let desde = document.getElementById('dateDesde');
    let hasta = document.getElementById('dateHasta');
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
        
        loadInvoices();
    });
    desde.addEventListener('change',function(){
      document.getElementById("actPag").value = 1;
      document.getElementById('periodoSelect').value="5";    
      loadInvoices();
    });
    hasta.addEventListener('change',function(){
      document.getElementById("actPag").value = 1;
      document.getElementById('periodoSelect').value="5";    
      loadInvoices();
    });    
    document.getElementById('periodoSelect').dispatchEvent(new Event('change'));
    
    document.getElementById("customersList").addEventListener("change",function(){      
      var desde = document.getElementById('dateDesde');
      var period = document.getElementById('periodoSelect');      
      var hasta = document.getElementById('dateHasta');
      var search = document.getElementById("mySearch");
      if(this.value == ""){
        desde.setAttribute("disabled","");
        hasta.setAttribute("disabled","");
        period.setAttribute("disabled","");
        search.setAttribute("disabled","");
      }else{
        desde.removeAttribute("disabled","");
        hasta.removeAttribute("disabled","");
        period.removeAttribute("disabled","");
        search.removeAttribute("disabled","");
      }
      loadInvoices();
    });
    var search = document.getElementById("mySearch");
    desde.setAttribute("disabled","");
    hasta.setAttribute("disabled","");
    period.setAttribute("disabled","");
    search.setAttribute("disabled","");

    document.getElementById("btnPagRight").addEventListener("click",function(){
      var actpag = document.getElementById("actPag");
      if(actpag.getAttribute("max")>=(parseFloat(actpag.value)+1)){
        actpag.value = parseFloat(actpag.value)+1;        
        loadInvoices();
      }
    });
    document.getElementById("btnPagLeft").addEventListener("click",function(){
      var actpag = document.getElementById("actPag");
      if(actpag.value>1){
        actpag.value = parseFloat(actpag.value)-1;        
        loadInvoices();
      }
    });
    document.getElementById("actPag").addEventListener("change",function(){
      if(parseFloat(this.value) < 1){
        this.value = 1;
      }else if(parseFloat(this.value) > parseFloat(this.getAttribute("max"))){
        this.value = this.getAttribute("max");
      }
      loadInvoices();
    });
    //Evento de la busqueda
    document.getElementById("mySearch").addEventListener("change",function(){
      loadInvoices(this.value);
    });

    //Descargar
    document.getElementById("downloadRep").addEventListener("click",function(){
      downloadReport();
    });

    loadCustomers();
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
      if(id.indexOf("err")>-1){
          opt.setAttribute("errid", id);
      }
      if (rsp[i][id] == selected) opt.setAttribute("selected", true);
      opt.innerHTML = rsp[i][dsc] + " - " + rsp[i]['rif'];
      select.appendChild(opt);
  }
}

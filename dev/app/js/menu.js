var sessid = getParameterByName('sessid');
window.addEventListener("keydown", function(e) {
  if(["ArrowDown"].indexOf(e.code) > -1) {
      e.preventDefault();
  }
}, false);

window.onload = function () {
  var menuCells = document.getElementsByClassName("cellMenu");
  for(var i=0;i<menuCells.length;i++){
    menuCells[i].addEventListener("click",function(){
      var sessid;
      sessid = getParameterByName("sessid");    
      gotoPage(this.getAttribute("id"), "main", { sessid: sessid });
    });
  }
   
  //Descargar
  document.getElementById("downloadButton").addEventListener("click",function(){
    downloadReport();
  });

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
    var checks = document.getElementsByClassName("checkstatus");
    var status = "";
    for(var i=0;i<checks.length;i++){
      if(checks[i].checked){
          status += checks[i].getAttribute("sid")+"-";
      }
    }
    if(status!=="")par.status = status.substring(0, status.length - 1);
    var actpag = document.getElementsByClassName("pagSel");
    if(actpag.length>0){
      actpag = parseFloat(actpag[0].innerHTML-1);
      par.offset = (actpag)*document.getElementById("numofrecFilt").value;
    }else{
      par.offset = 0;
    }
    par.order = -1;
    par.numofrec = document.getElementById("numofrecFilt").value;
    par.sessionid = getParameterByName("sessid");
    var parsedPars = Object.keys(par).map(function (k) {
      return encodeURIComponent(k) + "=" + encodeURIComponent(par[k]);
    })
    .join("&");
    download("documentos.csv",globalurl+"/api/invoices/listcsv.php?" + parsedPars);
}

  init();
  myMenu();


  document.getElementById("logOut").addEventListener("click", logOut);

  function myMenu() {
    document.getElementById("usernameMenu").innerText = sessionStorage.getItem("username");
    var sessid;
    sessid = getParameterByName("sessid");
    // TODO: Pendiente descomentar
    if (sessid === "" || sessid === null) {
      gotoPage("login", "main", "");
    }
    // TODO volver a poner itemMenu cuando esten listos
    var a = Array.from(document.getElementsByClassName("itemMenuReady"));
    a.forEach(function (e, i, o) {
      // TODO: pendiente descomentar
      // if (sessionStorage.getItem(e.id) == "true")
      if(true){
        e.addEventListener("click", function () {
          gotoPage("menu", e.id, { sessid: sessid });
        });

      } else e.style.color = "#666";
    });
  }

  function logOut() {
    var par = {};
    par.sessionid = getParameterByName("sessid");
    callWS("GET", "security/logout", par, respLogout);
    return;
  }

  function respLogout(status, respText) {
    var jsonResp;
    switch (status) {
      case 200:
        // ir a pagina de login
        gotoPage("login", "main", "");
        break;
      case 400:
        jsonResp = JSON.parse(respText);
        console.log(jsonResp);
        break;
      case 401:
        jsonResp = JSON.parse(respText);
        gotoPage("login", "main", "");
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

  function loadInvoices(filter="",offset=0,order=-1){
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
      var checks = document.getElementsByClassName("checkstatus");
      var status = "";
      for(var i=0;i<checks.length;i++){
        if(checks[i].checked){
            status += checks[i].getAttribute("sid")+"-";
        }
      }
      if(status!=="")par.status = status.substring(0, status.length - 1);
      par.offset = offset;
      par.order = order;
      par.numofrec = document.getElementById("numofrecFilt").value
      par.sessionid = sessid;
      callWS("GET", "invoices/list", par, BCLoadInvoices, offset );
      return;
  }
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
        if(navLink.status.id==1){
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
              qtyCnt.innerText = selected+" documentos seleccionados";
            }else{
              btnsCnt.getElementsByClassName("btnIconSubh")[0].classList.remove("btnIconSubhDis");
              btnsCnt.getElementsByClassName("btnIconSubh")[1].classList.add("btnIconSubhDis");
              qtyCnt.innerText = "";
            }
          });
        }else{
          mark.setAttribute("disabled","");
        }
        
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
  function BCLoadInvoices(status, respText, offset){
      var jsonResp;
      console.log('status', status)
      switch (status){
          case 200:
              jsonResp = JSON.parse(respText);
              console.log('jsonResp', jsonResp);
              
              drawInvoices(jsonResp.records);
              drawInvoicesPhone(jsonResp.records)
              drawPagination(offset, jsonResp.numofrecords);
              
              break;
          case 400:
              jsonResp = JSON.parse(respText);
              console.log(jsonResp);
              break;
          case 401:
              // gotoPage("login","error","");
              jsonResp = JSON.parse(respText);
              gotoPage("login","main",{});
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

  function fileValidation(file){
    var fileInput = file;
    var filePath = fileInput.value;
    var allowedExtensions = /(.txt)$/i;
    if(!allowedExtensions.exec(filePath)){
        inptError(file,'Por favor cargue un archivo solo con la extensión .txt');
        setTimeout(function(){
          removeErr(document.getElementById("inptUplFile"));
        },5000);
        fileInput.value = '';
        return false;
    }else{
        //Image preview
        checkFile(file.files[0]);
    }
    
  }

  // Esta funcion crea la tabla diamicamente segun la respuesta de user/list
  function drawInvoices(data){
    let cont;
    let btn;
    let table=document.getElementById("centerTable");
    table.innerHTML = "";
    
    
    // Headers
        let line = document.createElement("tr");

        let celda = document.createElement("th");
        celda.classList.add("thMark","cell2Mid");
        let mark = document.createElement("input");
        mark.addEventListener("click",function(){
          var btnsCnt = document.getElementById("buttonsCell");
          var tbl = document.getElementById("centerTable");
          var marks = tbl.getElementsByClassName("inptMark");
          var selected = 0;
          if(this.checked){
            for(var i=0;i<marks.length;i++){
              if(!marks[i].disabled){                
                selected++;
                marks[i].checked = true;
              }
            }
          }else{
            for(var i=0;i<marks.length;i++){
              marks[i].checked = false;
            }
            selected = 0;
          }
          var qtyCnt = document.getElementById("invoicesQtySel");
          if(selected>0){
            btnsCnt.getElementsByClassName("btnIconSubh")[0].classList.remove("btnIconSubhDis");
            btnsCnt.getElementsByClassName("btnIconSubh")[1].classList.remove("btnIconSubhDis");
            qtyCnt.innerText = selected+" documentos seleccionados";
          }else{
            btnsCnt.getElementsByClassName("btnIconSubh")[0].classList.remove("btnIconSubhDis");
            btnsCnt.getElementsByClassName("btnIconSubh")[1].classList.add("btnIconSubhDis");
            qtyCnt.innerText = "";
          }
        });
        mark.setAttribute("type","checkbox");
        celda.appendChild(mark);
        line.appendChild(celda);
        
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
        celda.innerHTML = "Monto (Bs.)";
        line.appendChild(celda);
        
        celda = document.createElement("th");
        celda.classList.add("thImpto","cell7Mid");
        celda.innerHTML = "IVA (Bs.)";
        line.appendChild(celda);

        celda = document.createElement("th");
        celda.classList.add("thTotal","cell7Mid");
        celda.innerHTML = "Total (Bs.)";
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
        if(navLink.status.id==1 && navLink.manualload){
          line.addEventListener("click",function(e){     
            //Se valida que no se esté marcando el campo del icono "ver factura" o el checkmark "escoger".
            if((e.target.classList.contains("inptMark") || e.target.classList.contains("thMark")) ||
            (e.target.classList.contains("userIcons") || e.target.classList.contains("thAction") || e.target.classList.contains("eyeIcon"))){
              return false;
            }else if(e.target.tagName=='svg'||e.target.tagName=='path'){//Porque el icono de fontawesom se convierte en svg y hereda el evento del row
              return false;
            }
            invoiceEntry(this.getAttribute("id"));
          });
        }else{
          line.classList.add("lineDis");
        }
        celda = document.createElement("td");
        celda.classList.add("thMark");
        celda.id="mark-"+navLink.id;
        let mark = document.createElement("input");
        mark.classList.add("inptMark");
        mark.setAttribute("type","checkbox");
        mark.setAttribute("invcid",navLink.id);
        
        if(navLink.status.id==1){
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
            qtyCnt.innerText = selected+" documentos seleccionados";
          }else{
            btnsCnt.getElementsByClassName("btnIconSubh")[0].classList.remove("btnIconSubhDis");
            btnsCnt.getElementsByClassName("btnIconSubh")[1].classList.add("btnIconSubhDis");
            qtyCnt.innerText = "";
          }
        });
        }else{
            mark.setAttribute("disabled","");
        }
        celda.appendChild(mark);
        line.appendChild(celda);
        
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
      qtyCnt.innerText = selected+" documentos seleccionados";
    }else{
      btnsCnt.getElementsByClassName("btnIconSubh")[0].classList.remove("btnIconSubhDis");
      btnsCnt.getElementsByClassName("btnIconSubh")[1].classList.add("btnIconSubhDis");
      qtyCnt.innerText = "";
    }
  }


  // Esta funcion crea la paginacion de la tabla
  function drawPagination(offset, numofrecords, recordsbypage=document.getElementById("numofrecFilt").value){
    const numofpagers = 2;
    var actpag = parseInt((offset/recordsbypage)+1);
    var lastpag = Math.ceil((numofrecords/recordsbypage));
    var inp = document.getElementById("pagination");
    inp.innerHTML = "";

    // no se muestra nada si no hay paginas
    if (lastpag <= 1) {
        document.getElementById('pagination').style.display='none';
        return;
    }
    document.getElementById('pagination').style.display='table';

    // dibujar primera y anterior
    var o = document.createElement("span");
    o.classList.add("back");
    o.innerHTML = '<i class="fa fa-angle-left"></i><i class="fa fa-angle-left"></i>';
    if (actpag > 1){
        o.classList.add('colorBlue');
    }
    o.addEventListener("click", function(){loadInvoices(document.getElementById("mySearch").value)});
    inp.appendChild(o);
    o = document.createElement("span");
    o.classList.add("back");
    o.innerHTML = '<i class="fa fa-angle-left"></i>';
    if (actpag > 1){
        o.classList.add('colorBlue');
        o.addEventListener("click", function(){loadInvoices(document.getElementById("mySearch").value, (actpag-2)*recordsbypage)});
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
        o.addEventListener("click", function(){loadInvoices(document.getElementById("mySearch").value, (1*this.innerHTML-1)*recordsbypage)});
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
        o.addEventListener("click", function(){loadInvoices(document.getElementById("mySearch").value, (actpag)*recordsbypage)});
    }
    inp.appendChild(o);
    o = document.createElement("span");
    o.classList.add("next");
    o.innerHTML = '<i class="fa fa-angle-right"></i><i class="fa fa-angle-right"></i>';
    if (actpag < parseInt(lastpag)){
        o.classList.add('colorBlue');
    }
    o.addEventListener("click", function(){loadInvoices(document.getElementById("mySearch").value, (lastpag-1)*recordsbypage)});
    inp.appendChild(o);
  }


  // Manejamos el estatus del boton de filtros

  document.getElementById('filtersButtom').addEventListener('click', function(){
    document.getElementById("filterDropdown").classList.toggle("show");
    if(document.getElementById("filterDropdown").classList.contains("show")){      
      this.classList.add("btnIconAct");
    }else{      
      this.classList.remove("btnIconAct");
    }
  })


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

  // Cerramos los filtros al darle al cerrar
  document.getElementById('stdbtnCloseFilters').addEventListener('click',function(){
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
        document.getElementById('filtersButtom').classList.remove("btnIconAct");
      }
    }
  })

  document.getElementById('stdbtnSendFilters').addEventListener('click',function(){
    loadInvoices();
    document.getElementById("stdbtnCloseFilters").dispatchEvent(new Event("click"));
  })  

  // Filtros
  let desde = document.getElementById('dateDesde');
  let hasta = document.getElementById('dateHasta');
  document.getElementById('periodoSelect').addEventListener('change',function(e){
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
  })
  document.getElementById('periodoSelect').dispatchEvent(new Event('change'));
  loadInvoices();

  desde.addEventListener('change',function(){
    console.log("Cambio en desde")
    document.getElementById('periodoSelect').value="5";
  })


  hasta.addEventListener('change',function(){
    console.log("Cambio en hasta")
    document.getElementById('periodoSelect').value="5";
  })

  


  //NUEVA SECCIÓN DESARROLLO DE AGREGAR FACTURAS EN MISMO MÓDULO
  invoiceControls();
  //RESPETO LA IDENTACIÖN DEL DOCUMENTO PERO CREO QUE DEBE SER a 4 espacios

  //Botón lista crear
  document.getElementById("createButtom").addEventListener("click",function(){
    blankAll();
    document.getElementById("subtitPage").innerText = "Nuevo Documento";
    document.getElementById("saveFrm").innerText = "CREAR";
    showPage("pageFrm");    
    document.getElementById("logOut").parentElement.style.right = "calc(5% + 10px)";    
  });

  //Botones del formulario cerrar y guardar  
  document.getElementById("backArrow").addEventListener("click",function(){
    showPage("pageList");
    document.getElementById("logOut").parentElement.style.right = "";
  });
  document.getElementById("closeFrm").addEventListener("click",function(){
    showPage("pageList");
    document.getElementById("logOut").parentElement.style.right = "";
  });
  document.getElementById("saveFrm").addEventListener("click",function(){
    var id = this.getAttribute("invcid");
    if(id!==null&&id!==0&&id!=="")invoiceUpdt(id);
    else invoiceUpdt(0);
  });
  
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

  //Evento del botón tabla inferior para añadir lineas de items details
  document.getElementById("addItemDetails").addEventListener("click",addItemDet);
  //Función que clona la linea actual con datos cargados y la inserta cómo nueva linea
  function addItemDet(){
    //Validación de datos
    var ref = "",dsc = "",qty = "",price = "";
    ref = document.getElementById("inptAddRef").value;
    dsc = document.getElementById("inptAddDsc").value;
    qty = document.getElementById("inptAddQty").value;
    price = document.getElementById("inptAddPrice").getAttribute("datanumber");
    var failref = false;
    if(ref==""||ref==null)failref = true;
    var faildsc = false;
    if(dsc==""||dsc==null)faildsc = true;
    var failqty = false;
    if(qty==""||qty==null)failqty = true;
    var failprice = false;
    if(price==""||price==null)failprice = true;
    if(failref || faildsc || failqty || failprice){
      if(failref){
        inptError(document.getElementById("inptAddRef"),"Ingrese una referencia");
      }
      if(faildsc){
        inptError(document.getElementById("inptAddDsc"),"Ingrese una descripción");
      }
      if(failqty){
        inptError(document.getElementById("inptAddQty"),"Ingrese la cantidad");
      }
      if(failprice){
        inptError(document.getElementById("inptAddPrice"),"Ingrese el precio");
      }


      //alert("faltan campos obligatorios");
      return false;
    }else{
      removeErr(document.getElementById("inptAddRef"));
      removeErr(document.getElementById("inptAddDsc"));
      removeErr(document.getElementById("inptAddQty"));
      removeErr(document.getElementById("inptAddPrice"));
    }
    

    var itemLine = document.getElementById("addItemDetails").parentElement.cloneNode(true);

    var fields = ["inptPrice","inptDisc","inptIVA","inptTot"]
    var format = ['amount','percent','percent','amount'];
    formatFields2(fields,format,2,itemLine);

    //Evento de eliminar la linea 
    itemLine.getElementsByClassName("itemBtn")[0].addEventListener("click",function(){
      this.parentElement.parentElement.removeChild(this.parentElement);
      recalcTotals();
    });

    //cantidad x precio x (1-%desc)
    itemLine.getElementsByClassName("inptPrice")[0].addEventListener("keyup",function(){
      recalcTotLine(this.parentElement.parentElement.parentElement);
    });
    itemLine.getElementsByClassName("inptQty")[0].addEventListener("keyup",function(){
      recalcTotLine(this.parentElement.parentElement.parentElement);
    });
    itemLine.getElementsByClassName("inptIVA")[0].addEventListener("keyup",function(){
      recalcTotLine(this.parentElement.parentElement.parentElement);
    });
    itemLine.getElementsByClassName("inptDisc")[0].addEventListener("keyup",function(){
      recalcTotLine(this.parentElement.parentElement.parentElement);
    });
    itemLine.getElementsByClassName("inptCheck")[0].addEventListener("click",function(){
      var itemLine = this.parentElement.parentElement.parentElement;
      if(this.checked){
        itemLine.getElementsByClassName("inptIVA")[0].value = "0,00";
        itemLine.getElementsByClassName("inptIVA")[0].setAttribute("datanumber",0);
        itemLine.getElementsByClassName("inptIVA")[0].setAttribute("disabled","");
        recalcTotAdd();
      }else{
        itemLine.getElementsByClassName("inptIVA")[0].value = "";
        itemLine.getElementsByClassName("inptIVA")[0].setAttribute("datanumber","");
        itemLine.getElementsByClassName("inptIVA")[0].removeAttribute("disabled");
      }
      recalcTotLine(this.parentElement.parentElement.parentElement);
    });
    
    var items = document.getElementById("itemsList").children;
    if(items.length<=0)document.getElementById("itemsList").appendChild(itemLine);  
    else document.getElementById("itemsList").insertBefore(itemLine,items[0]);
    
    //Se blanquea la linea de "agregar" luego que se inserta el registro
    var inptsAdd = document.getElementById("addItemDetails").parentElement.getElementsByTagName("input");
    for(var x=0;x<inptsAdd.length;x++){
      inptsAdd[x].value = "";
      inptsAdd[x].checked = false;
      inptsAdd[x].setAttribute("datanumber","");
      inptsAdd[x].removeAttribute("disabled");
    }
    if(document.getElementById("invoiceexent").checked){
      document.getElementById("inptAddCheck").setAttribute("disabled","");
      document.getElementById("inptAddTax").setAttribute("disabled","");
    }

    //Actualizar btn por un menos y evento de eliminar la linea    
    itemLine.getElementsByClassName("itemBtn")[0].children[0].classList.add("fa-minus");
    itemLine.getElementsByClassName("itemBtn")[0].children[0].classList.remove("fa-plus");

    recalcTotals();
  }
  function recalcTotals(){
    //Se suman los totatl
    var totals = document.getElementById("itemsList").getElementsByClassName("inptTot");    
    var taxinpt = document.getElementById("itemsList").getElementsByClassName("inptIVA");
    var subt = 0;
    var taxamo = 0;
    var taxsum = 0;
    for(var i=0;i<totals.length;i++){
      if(totals[i].getAttribute("datanumber")!=null && totals[i].getAttribute("datanumber")!=""){
        subt = subt+parseFloat(totals[i].getAttribute("datanumber"));
      }

      if((totals[i].getAttribute("datanumber")!=null && totals[i].getAttribute("datanumber")!="") && 
      (taxinpt[i].getAttribute("datanumber")!=null && taxinpt[i].getAttribute("datanumber")!="")
      ){
        if((taxinpt[i]).getAttribute("datanumber")>0){
          taxamo = taxamo+parseFloat(totals[i].getAttribute("datanumber"));
          taxsum = taxsum+(parseFloat(totals[i].getAttribute("datanumber"))*(taxinpt[i].getAttribute("datanumber")/100));
        }
      }
    }
    document.getElementById("subTot").setAttribute("datanumber",subt);
    document.getElementById("subTot").innerText = number_format(subt,2);
    document.getElementById("taxAmo").value = number_format(taxamo,2);
    document.getElementById("taxAmo").setAttribute("datanumber",taxamo);
    document.getElementById("taxSum").value = number_format(taxsum,2);
    document.getElementById("taxSum").setAttribute("datanumber",taxsum);
    var tot = subt+taxsum;
    document.getElementById("totAmo").innerText = number_format(tot,2);
    var discamo = 0;
    if(document.getElementById("discPct").type=='text')
      discamo = document.getElementById("discPct").getAttribute("datanumber");    
    else
      discamo = document.getElementById("discPct").value;    

      //var dicval = (discamo/100)*document.getElementById("subTot").getAttribute("datanumber");
    //document.getElementById("discAmo").setAttribute("datanumber",dicval);
    //document.getElementById("discAmo").dispatchEvent(new Event("keyup"));

    var subt = document.getElementById("subTot").getAttribute("datanumber");
    var taxval =  document.getElementById("taxSum").getAttribute("datanumber");
    
    if(discamo!==""&&discamo!==null&&!isNaN(discamo)){
      document.getElementById("totAmo").innerText = number_format(((parseFloat(subt)*(1-(parseFloat(discamo)/100)))+parseFloat(taxval)),2);
    }
  }
  //Si se cambia el porcentaje de descuento se recalcula el monto de descuento y el total
  document.getElementById("discPct").addEventListener("keyup",function(){
    var discamo = (this.value/100)*document.getElementById("subTot").getAttribute("datanumber");
    document.getElementById("discAmo").setAttribute("datanumber",discamo);
    document.getElementById("discAmo").value = number_format(discamo,2);
  });
  
  document.getElementById("discPct").addEventListener("change",function(){
    setTimeout(function(){      
      recalcTotals();
    },200);
  });
  
  //Si se cambia el monto se recalcula el %
  document.getElementById("discAmo").addEventListener("keyup",function(){
    var subt = document.getElementById("subTot").getAttribute("datanumber");    
    document.getElementById("discPct").setAttribute("datanumber",((this.value/subt)*100));
    document.getElementById("discPct").value = number_format((this.value/subt)*100,2)+"%";
  });
  
  document.getElementById("discAmo").addEventListener("change",function(){
    setTimeout(function(){      
      recalcTotals();
    },200);
  });


  var fields = ['inptAddPrice','inptAddTax','inptAddDisc','discPct','discAmo','taxrate','custommail',
                'custommobile','customphone'];
  var format = ['amount','percent','percent','percent','amount','amount','email',
                'phone','phone'];
  formatFields2(fields,format,2);
  //Evento a los campos que se afectan en el agregar item a la factura para que recalculen 
  function recalcTotAdd(){
    var qty =  document.getElementById("inptAddQty").value;
    var discount = document.getElementById("inptAddDisc");
    if(discount.type=='number'){
      discount = discount.value;
    }else{
      discount = discount.getAttribute("datanumber");
    }
    var price = document.getElementById("inptAddPrice");
    if(price.type=='number'){
      price = price.value;
    }else{
      price = price.getAttribute("datanumber");
    }
    var tot = 0;
    if((qty!==""&&qty!==null)&&(price!==""&&price!==null)){
      tot = qty*price;
      if(discount!=="" && !isNaN(discount) && discount!=null){
        tot = (qty*price)-((qty*price)*(discount/100));
      }
    }
    document.getElementById("inptTotAmo").setAttribute("datanumber",tot);
    document.getElementById("inptTotAmo").value = number_format(tot,2);
    recalcTotals();
  }

    //Función que recalcula el total de la linea para los campos que se afectan al modificar el item de una factura 
    function recalcTotLine(cnt){      
      var qty =  cnt.getElementsByClassName("inptQty")[0].value;
      var discount =  cnt.getElementsByClassName("inptDisc")[0];
      if(discount.type=='number'){
        discount = discount.value;
      }else{
        discount = discount.getAttribute("datanumber");
      }
      var price = cnt.getElementsByClassName("inptPrice")[0];
      if(price.type=='number'){
        price = price.value;
      }else{
        price = price.getAttribute("datanumber");
      }
      var tot = 0;
      if((qty!==""&&qty!==null)&&(price!==""&&price!==null)){
        tot = qty*price;
        if(discount!=="" && !isNaN(discount) && discount!=null){
          tot = (qty*price)-((qty*price)*(discount/100));
        }
      }
      cnt.getElementsByClassName("inptTot")[0].setAttribute("datanumber",tot);
      cnt.getElementsByClassName("inptTot")[0].value = number_format(tot,2);
      recalcTotals();
    }
  //cantidad x precio x (1-%desc)
  document.getElementById("inptAddPrice").addEventListener("keyup",function(){
    recalcTotAdd();
  });
  document.getElementById("inptAddQty").addEventListener("keyup",function(){
    recalcTotAdd();
  });
  document.getElementById("inptAddTax").addEventListener("keyup",function(){
    recalcTotAdd();
  });
  document.getElementById("inptAddCheck").addEventListener("click",function(){
    if(this.checked){
      document.getElementById("inptAddTax").value = "0,00";
      document.getElementById("inptAddTax").setAttribute("datanumber",0);
      document.getElementById("inptAddTax").setAttribute("disabled","");
      recalcTotAdd();
    }else{
      document.getElementById("inptAddTax").value = "";
      document.getElementById("inptAddTax").setAttribute("datanumber","");
      document.getElementById("inptAddTax").removeAttribute("disabled");
    }
  });
  document.getElementById("inptAddDisc").addEventListener("keyup",function(){
    recalcTotAdd();
  });
  function isEmpty(ele,msg=""){
    if(ele.value == "" && (ele.getAttribute("datanumber")==""||ele.getAttribute("datanumber")==null)){
      //Está vacío:
      if(msg!==""){//Si existe mensaje se muestra el error con este
        inptError(ele,msg);
      }else if(ele.parentElement.parentElement.getElementsByClassName("inptLbl").length>0){//Si existe un label
        var msg = "El campo "/*+(ele.parentElement.parentElement.getElementsByClassName("inptLbl")[0].innerText).toLowerCase()*/+"es obligatorio";
        inptError(ele,msg);
      }
      return true;
    }
    return false;
  }
  function removeAllErr(){
    var inptserr = document.getElementsByClassName("inptErr");
    for(var i=(inptserr.length-1);i>=0;i=(inptserr.length-1)){
      var msgerrcnt = inptserr[i].parentElement;
      if(msgerrcnt.getElementsByClassName("msgErrInpt").length>0){
        msgerrcnt.removeChild(msgerrcnt.getElementsByClassName("msgErrInpt")[0]);
      }
      inptserr[i].classList.remove("inptErr");
    }
  }
  //Actualizar / crear una nueva factura
  function invoiceUpdt(id){
    var par = {};
    var falta = false;
    par.id = id;
    par.sessionid = sessid;
    par.issuedate = getValue("issuedate");
    //Al poner la función isEmpty delante en cada IF se valida antes de salir por campo "falta"
    //Y el isEmpty colorea el campo si está vacío 

    if(isEmpty(document.getElementById("issuedate")) && !falta)falta = true;
    par.duedate = getValue("duedate");
    if(isEmpty(document.getElementById("duedate")) && !falta)falta = true;
    par.refnumber = getValue("invoicenumber");
    if(isEmpty(document.getElementById("invoicenumber")) && !falta)falta = true;
    par.obs = getValue("observations");

    //#new
    par.seriecontrol = {};
    var invoiceserie = document.getElementById("invoiceserie").value;
    if(document.getElementById("invoiceserie").value == '-1'){
      inptError(document.getElementById("invoiceserie"),"El campo es obligatorio");
      falta = true;
    }
    if(invoiceserie.split("-").length>1){
      par.seriecontrol.serie = invoiceserie.split("-")[0].trim();
      par.seriecontrol.control = invoiceserie.split("-")[1].trim();
    }else if(invoiceserie.split("-").length>0){
      par.seriecontrol.serie = "";
      par.seriecontrol.control = invoiceserie.split("-")[0].trim();
    }
    //#new
    if(isEmpty(document.getElementById("invoicetype")) && !falta)falta = true;
    par.type = getValue("invoicetype");
    
    var srinvoice = document.getElementById("invoicerefcrtl").value;
    var srinvoiceArr = srinvoice.split("-");
    par.ctrref = {};
    if(srinvoice.length<11 && par.type!=='FAC'){
      inptError(document.getElementById("invoicerefcrtl"),"El campo debe tener almenos 10 dígitos");
      falta = true;
    }else if(par.type != "FAC" || srinvoice !== ""){
      if(srinvoiceArr.length==2){
        par.ctrref.serie = "";
        par.ctrref.control = srinvoiceArr[0];
        par.ctrref.number = srinvoiceArr[1];
      }else if(srinvoiceArr.length==3){
        par.ctrref.serie = srinvoiceArr[0];
        par.ctrref.control = srinvoiceArr[1];
        par.ctrref.number = srinvoiceArr[2];
      }else{
        inptError(document.getElementById("invoicerefcrtl"),"El campo debe tener almenos 10 dígitos");
        falta = true;
      }
    }
    

    var client = {};
    client.rif = getValue("customid");
    if(isEmpty(document.getElementById("customid")) && !falta)falta = true;
    client.name = getValue("customname");
    if(isEmpty(document.getElementById("customname")) && !falta)falta = true;
    client.mobile = getValue("custommobile");
    if(isEmpty(document.getElementById("custommobile")) && !falta)falta = true;
    client.phone = getValue("customphone");
    client.address = getValue("customaddr");
    client.email = getValue("custommail");
    if(isEmpty(document.getElementById("custommail")) && !falta)falta = true;
    par.client = client;

    par.currencyrate = parseFloat(document.getElementById("taxrate").getAttribute("datanumber"));
    if(par.currencyrate==null||par.currencyrate==""||isNaN(par.currencyrate)){
      par.currencyrate = 0;
    } 

    if(isEmpty(document.getElementById("taxrate")) && !falta)falta = true;
    par.currency = getValue("taxcurrrate");
    par.discount = parseFloat(document.getElementById("discPct").getAttribute("datanumber"));

    par.details = [];
    var details = document.getElementById("itemsList").getElementsByClassName("itemsRow");
    for(var i=0;i<details.length;i++){
      var detail = {};
      detail.itemref = valueByClass("inptRef",details[i]);
      if(isEmpty(details[i].getElementsByClassName("inptRef")[0],"Ingrese una referencia") && !falta)falta = true;
      detail.itemdsc = valueByClass("inptDsc",details[i]);
      if(isEmpty(details[i].getElementsByClassName("inptDsc")[0],"Ingrese una descripción") && !falta)falta = true;
      detail.qty = parseFloat(valueByClass("inptQty",details[i]));
      if(isEmpty(details[i].getElementsByClassName("inptQty")[0],"Ingrese una cantidad") && !falta)falta = true;
      detail.unit = valueByClass("inptUnit",details[i]);
      if(isEmpty(details[i].getElementsByClassName("inptUnit")[0],"Ingrese una unidad") && !falta)falta = true;
      detail.unitprice = parseFloat(details[i].getElementsByClassName("inptPrice")[0].getAttribute("datanumber"));
      if(isEmpty(details[i].getElementsByClassName("inptPrice")[0],"Ingrese un precio") && !falta)falta = true;
      var tax = details[i].getElementsByClassName("inptIVA")[0].getAttribute("datanumber");
      var percibe = details[i].getElementsByClassName("inptCheck")[0].checked;
      var excento = document.getElementById("invoiceexent").checked;
      if(excento){
        tax = -2;
      }else if(percibe){
        tax = -1;
      }else if(tax==null||tax==""||isNaN(tax)){
        tax = 0;
      }
      detail.tax = parseFloat(tax);
      var discount = details[i].getElementsByClassName("inptDisc")[0].getAttribute("datanumber");
      if(discount==null||discount==""||isNaN(discount)){
        discount = 0;
      }      
      detail.discount = parseFloat(discount);      
      par.details.push(detail);
    }    
    if(details.length==0){
      document.getElementById("addItemDetails").dispatchEvent(new Event("click"));
      falta = true;
    }      
    if(falta){
      //alert("Faltan campos obligatorios");
      setTimeout(function(){
        var frm = document.getElementById("pageFrm");
        removeAllErr(frm);
      },5000);
      return false;
    }
    //Se validan luego los formatos de cada campo en particular
    if(!isEmail(document.getElementById("custommail").value)){      
      inptError(document.getElementById("custommail"),"Formato de Correo Inválido");
      falta = true;
    }else{      
      removeErr(document.getElementById("custommail"));
    }
    if(document.getElementsByClassName("typeSel")[0].getAttribute("type")=='2'){
      if(!isRIF(document.getElementById("customid").value)){      
        inptError(document.getElementById("customid"),"Formato de RIF Inválido");
      }else{      
        removeErr(document.getElementById("customid"));
      }
    }else{
      if(!isCI(document.getElementById("customid").value)){      
        inptError(document.getElementById("customid"),"Formato de Cédula Inválido");
        falta = true;
      }else{      
        removeErr(document.getElementById("customid"));
      }
    }
    if(!isPhone(document.getElementById("custommobile").value)){      
      inptError(document.getElementById("custommobile"),"Formato de Teléfono Inválido");
      falta = true;
    }else{      
      removeErr(document.getElementById("custommobile"));
    }
    if(par.client.phone!=""){
      if(!isPhone(document.getElementById("customphone").value)){      
        inptError(document.getElementById("customphone"),"Formato de Teléfono Inválido");
        falta = true;
      }else{      
        removeErr(document.getElementById("customphone"));
      }
    }  
    if(falta)return false;

    var succes = function(status, respText){
      var resp = JSON.parse(respText);
      switch (status){
          case 200:
            loadInvoices();
            showPage("pageList");
            document.getElementById("logOut").parentElement.style.right = "";
              break;
          case 400:
              console.log(resp);
              globErr(document.getElementById("globMsgErr"),resp.msg);
              break;
          case 401:
              console.log(resp);
              gotoPage("login","main",{});
              break;
          case 500:
            var msg = "";
            if(resp.msg!==""&&resp.msg!==undefined)msg = resp.msg;
              globErr(document.getElementById("globMsgErr"),msg);
              console.log(resp);
              break;
          default:
              console.log(resp);
              break;
      }
    }
    callWS("JSON", "invoices/update", par, succes, "");
    return;
  }
  //Set entry se arma el formulario según la respuesta del entry
  function setEntry(rsp){
    //Datos adicionales de la factura
    var header = rsp.header;
    setValue("issuedate",header.issuedate.date);
    setValue("duedate",header.duedate.date);
    setValue("invoiceserie","-1");
    setValue("invoicenumber",header.refnumber);
    setValue("observations",header.obs);
    document.getElementById("taxrate").setAttribute("datanumber",header.multicurrency.rate.number);
    setValue("taxrate",header.multicurrency.rate.formatted);
    setValue("taxcurrrate",header.multicurrency.currency);
    document.getElementById("discPct").setAttribute("datanumber",header.amounts.discount.number);
    setValue("discPct",header.amounts.discount.percentage);

    //NEW
    var fullctr = formatRefctr(header.ctrnumber);
    var ctrarr = fullctr.split("-");
    var seriecontrol = "";
    if(ctrarr.length>2){
      seriecontrol = ctrarr[0]+"-"+ctrarr[1];
    }else if(ctrarr.length>1){
      seriecontrol = ctrarr[0];
    }
    setValue("invoicetype",header.type.id);
    document.getElementById("invoicetype").dispatchEvent(new Event("change"));
    document.getElementById("invoiceserie").setAttribute("disabled","");  
    document.getElementById("invoiceserie").value = seriecontrol;
    
    //Se calcula el descuento a partir del subtotal
    var discamo = (header.amounts.discount.number/100)*header.amounts.gross.number;
    document.getElementById("discAmo").setAttribute("datanumber",discamo);
    document.getElementById("discAmo").value = number_format(discamo,2);

    //Datos del cliente
    var client = header.client;
    var cellsType = document.getElementById("topTypeCnt").getElementsByTagName("div");
    client.rif = formatRIF(client.rif);    
    setValue("customid", client.rif);
    if(isRIF(client.rif) || !client.rif.length){
      cellsType[1].classList.add("typeSel");
      cellsType[0].classList.remove("typeSel");
    }else if(isCI(client.rif) || !client.rif.length){
      cellsType[0].classList.add("typeSel");
      cellsType[1].classList.remove("typeSel");
    }
    setValue("customname", client.name);
    setValue("custommobile", client.mobile);
    setValue("customphone", client.phone);
    setValue("customaddr", client.address);
    setValue("custommail", client.email);

    setValue("invoicerefcrtl", header.ctrref);
    document.getElementById("invoicerefcrtl").dispatchEvent(new Event("keyup"));
    

    //Se pintan los items
    drawItemsDet(rsp.details);

    //Se recalculan los totales
    recalcTotals();


    //Luego de que se carga todo se muestra la página
    showPage("pageFrm");    
    document.getElementById("logOut").parentElement.style.right = "calc(5% + 10px)";

    //Seteo el botón de guarda
    document.getElementById("saveFrm").setAttribute("invcid",header.id);
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
      }
      var fields = ["inptPrice","inptDisc","inptIVA","inptTot"]
      var format = ['amount','percent','percent','amount'];
      formatFields2(fields,format,2,itemLine);

      //Setear valores
      setValueByClass("inptRef",details[i].item.ref,itemLine);
      setValueByClass("inptDsc",details[i].item.dsc,itemLine);
      setValueByClass("inptQty",details[i].qty.formatted,itemLine,{'datanumber':details[i].qty.number});
      setValueByClass("inptUnit",details[i].unit,itemLine);
      setValueByClass("inptPrice",details[i].unitprice.formatted,itemLine,{'datanumber':details[i].unitprice.number});
      debugger;
      if((details[0].tax.number*100)=='-2'){
        document.getElementById("invoiceexent").checked = true;
        setValueByClass("inptCheck",'',itemLine,{'disabled':''});
        setValueByClass("inptIVA",'0,00',itemLine,{'datanumber':(0),'disabled':''});
        document.getElementById("inptAddCheck").setAttribute("disabled","");
        document.getElementById("inptAddTax").setAttribute("disabled","");
      }else if((details[i].tax.number*100)=='-1'){
        setValueByClass("inptCheck",'',itemLine,{});
        itemLine.getElementsByClassName("inptCheck")[0].checked = true;
        setValueByClass("inptIVA",'0,00',itemLine,{'datanumber':(0),'disabled':''});
      }else{
        setValueByClass("inptIVA",details[i].tax.formatted,itemLine,{'datanumber':(details[i].tax.number*100)});
      }
      setValueByClass("inptDisc",details[i].discount.formatted,itemLine,{'datanumber':(details[i].discount.number*100)});
      setValueByClass("inptTot",details[i].total.formatted,itemLine,{'datanumber':details[i].total.number});

      //Evento de eliminar la linea 
      itemLine.getElementsByClassName("itemBtn")[0].addEventListener("click",function(){
        this.parentElement.parentElement.removeChild(this.parentElement);
        recalcTotals();
      });

      //cantidad x precio x (1-%desc)
      itemLine.getElementsByClassName("inptPrice")[0].addEventListener("keyup",function(){
        recalcTotLine(this.parentElement.parentElement.parentElement);
      });
      itemLine.getElementsByClassName("inptQty")[0].addEventListener("keyup",function(){
        recalcTotLine(this.parentElement.parentElement.parentElement);
      });
      itemLine.getElementsByClassName("inptCheck")[0].addEventListener("click",function(){
        var itemLine = this.parentElement.parentElement.parentElement;
        if(this.checked){
          itemLine.getElementsByClassName("inptIVA")[0].value = "0";
          itemLine.getElementsByClassName("inptIVA")[0].setAttribute("datanumber",0);
          itemLine.getElementsByClassName("inptIVA")[0].setAttribute("disabled","");
          recalcTotAdd();
        }else{
          itemLine.getElementsByClassName("inptIVA")[0].value = "";
          itemLine.getElementsByClassName("inptIVA")[0].setAttribute("datanumber","");
          itemLine.getElementsByClassName("inptIVA")[0].removeAttribute("disabled");
        }
        recalcTotLine(this.parentElement.parentElement.parentElement);
      });
      itemLine.getElementsByClassName("inptIVA")[0].addEventListener("keyup",function(){
        recalcTotLine(this.parentElement.parentElement.parentElement);
      });
      itemLine.getElementsByClassName("inptDisc")[0].addEventListener("keyup",function(){
        recalcTotLine(this.parentElement.parentElement.parentElement);
      });

      document.getElementById("itemsList").appendChild(itemLine);
      //Actualizar btn por un menos y evento de eliminar la linea    
      itemLine.getElementsByClassName("itemBtn")[0].children[0].classList.add("fa-minus");
      itemLine.getElementsByClassName("itemBtn")[0].children[0].classList.remove("fa-plus");
        
        
    }      
    recalcTotals();
  }

function blankAll(){
  //Se blanquea la linea de "agregar" luego que se inserta el registro
  var form = document.getElementById("pageFrm");
  var inpts = form.getElementsByTagName("input");
  for(var x=0;x<inpts.length;x++){
    inpts[x].value = "";
    inpts[x].removeAttribute("datanumber");
    inpts[x].removeAttribute("disabled");
    inpts[x].checked = false;
  }
  document.getElementById("itemsList").innerHTML = "";
  document.getElementById("subTot").innerHTML = "0,00";
  document.getElementById("subTot").removeAttribute("datanumber");
  document.getElementById("totAmo").innerHTML = "0,00";
  document.getElementById("totAmo").removeAttribute("datanumber");
  document.getElementById("saveFrm").removeAttribute("invcid");

  document.getElementById("invoiceserie").value = "-1";
  document.getElementById("invoicetype").value = "FAC";
  document.getElementById("invoicetype").dispatchEvent(new Event("change"));
  var frm = document.getElementById("pageFrm");
  removeAllErr(frm);

  document.getElementById("invoiceserie").removeAttribute("disabled");

  document.getElementById("taxrate").dispatchEvent(new Event("focusout"));

}

  //Se llama el servicio del entry
  function invoiceEntry(id,view=false){
    var par = {};
    par.id = id;
    par.sessionid = sessid;
    var succes = function(status, respText){
      var resp = JSON.parse(respText);
      switch (status){
          case 200:
            console.log(resp);
            if(!view){
              blankAll();
              document.getElementById("saveFrm").innerText = "GUARDAR";
              document.getElementById("subtitPage").innerText = "Editar Documento";
              setEntry(resp.entry);        
            }else{
              viewDocument(resp.entry);
            }    
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
  
  //Evento al botón de "enviar" para mostrar popup de confirmación
  document.getElementById("sendButton").addEventListener("click",function(){
    sendcheckInvoices();
  });
  //Evento al botón de "delete" para mostrar popup de eliminar confirmación
  document.getElementById("deleteButton").addEventListener("click",function(){
    showPopup("deletePopup");
  });
  //Actualizar / crear una nueva factura
  function deleteInvoices(){
    var par = {};
    var marks = document.getElementsByClassName("inptMark");    
    var ids = "";
    for(var i=0;i<marks.length;i++){
      if(marks[i].checked){
        if( marks[i].getAttribute("invcid")!=null&& marks[i].getAttribute("invcid")!="")
          ids += marks[i].getAttribute("invcid")+"-";
      }
    }
    if(ids!=="")ids = ids.substr(0,ids.length-1);
    par.id = ids;
    par.sessionid = sessid;
    var succes = function(status, respText){
      if(respText!="")var resp = JSON.parse(respText);
      switch (status){
          case 200:
            closePopup("deletePopup");
            loadInvoices();
          break;
          case 204:
            console.log("El registro no existe");
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
    callWS("DELETE", "invoices/delete", par, succes, "");
    return;
  }
  function sendcheckInvoices(){
    var par = {};
    var marks = document.getElementsByClassName("inptMark");    
    var ids = "";
    for(var i=0;i<marks.length;i++){
      if(marks[i].checked){
        if( marks[i].getAttribute("invcid")!=null&& marks[i].getAttribute("invcid")!="")
          ids += marks[i].getAttribute("invcid")+"-";
      }
    }
    if(ids!==""){
      ids = ids.substr(0,ids.length-1);
      par.invoiceids = ids;  
    }
    par.sessionid = sessid;
    var succes = function(status, respText){
      var resp = JSON.parse(respText);
      switch (status){
          case 200:
            if(ids!==""){
              document.getElementById("sendNro").innerText = resp.invoices.tosend+" documentos seleccionados "; 
            }else{
              document.getElementById("sendNro").innerText = resp.invoices.tosend+" documentos pendientes "; 
            }                    
            showPopup("sendPopup");
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
    callWS("GET", "invoices/sendcheck", par, succes, "");
    return;
  }
  function sendInvoices(){
    var par = {};
    var marks = document.getElementsByClassName("inptMark");    
    var ids = "";
    for(var i=0;i<marks.length;i++){
      if(marks[i].checked){
        if(marks[i].getAttribute("invcid")!=null&& marks[i].getAttribute("invcid")!="")
          ids += marks[i].getAttribute("invcid")+"-";
      }
    }
    if(ids!==""){
      ids = ids.substr(0,ids.length-1);
      par.invoiceids = ids;  
    }
    par.sessionid = sessid;
    var succes = function(status, respText){
      var resp = JSON.parse(respText);
      switch (status){
          case 200:
            closePopup("sendPopup");
            loadInvoices();          
            var msg = "Sus "+resp.invoices.sent+" documentos se han enviado exitosamente";
            showBanner(msg);
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
    callWS("GET", "invoices/send", par, succes, "");
    return;
  }
  function showBanner(msg){
    document.getElementById("lblMsg").innerText = msg;
    document.getElementById("bannerMsg").style.bottom = "0px";
    setTimeout(function(){
      hideBanner();
    },5000);
  }
  function hideBanner(){
    setTimeout(function(){      
      document.getElementById("lblMsg").innerText = "";
    },500);
    document.getElementById("bannerMsg").style.bottom = "";
  }
  
  //Botón de confirmar el enviar, ejecuta la llamada al servicio
  document.getElementById("sendInvc").addEventListener("click",function(){
    sendInvoices();
  });
  //Botón de confirmar el delete, ejecuta la llamada al servicio
  document.getElementById("delInvc").addEventListener("click",function(){
    deleteInvoices();
  });

  //Evento a mayuscula el simbolo de la tasa
  document.getElementById("taxcurrrate").addEventListener("keyup",function(){
    this.value = (this.value).toUpperCase();
  });

  //Evento ocultar/mostrar sobre factura según el tipo
  document.getElementById("invoicetype").addEventListener("change",function(){    
      var sbfac = document.getElementById("invoicerefcrtl").parentElement.parentElement.parentElement;
      if(this.value == "FAC"){
        sbfac.style.display = "none";
      }else{
        sbfac.style.display = "";
      }
  });
  //Evento bloquear ivas en items si está excento
  document.getElementById("invoiceexent").addEventListener("change",function(){    
    if(this.checked){
      var items = document.getElementsByClassName("itemsRow");
      for(var i=0;i<items.length;i++){ 
        items[i].getElementsByClassName("inptCheck")[0].checked = false;
        setValueByClass("inptCheck",'',items[i],{'disabled':''});
        setValueByClass("inptIVA",'0,00',items[i],{'datanumber':(0),'disabled':''});
      }  
      document.getElementById("inptAddCheck").setAttribute("disabled","");
      document.getElementById("inptAddTax").setAttribute("disabled","");
    }else{
      document.getElementById("inptAddCheck").removeAttribute("disabled");
      document.getElementById("inptAddTax").removeAttribute("disabled");
      var items = document.getElementsByClassName("itemsRow");
      for(var i=0;i<items.length;i++){        
        items[i].getElementsByClassName("inptCheck")[0].removeAttribute("disabled");
        items[i].getElementsByClassName("inptIVA")[0].removeAttribute("disabled");
      }
    }
  });
  

  //Recibe el id del "card" (data del popup a mostrar)
  function showPopup(popname){
    var ele = document.getElementById(popname);
    ele.style.display = "block";
    ele.parentElement.parentElement.style.display = "table";
  }
  function closePopup(popname){
    var ele = document.getElementById(popname);
    ele.style.display = "";
    ele.parentElement.parentElement.style.display = "";
  }
  //Cancelar "x" de popup's
  var popClose = document.getElementsByClassName("popupClose");
  for(var i=0;i<popClose.length;i++){
    popClose[i].addEventListener("click",function(){
      closePopup(this.getAttribute("data-popup"));
    });
  }

  //Muestra el popup de visualización de la factura 
  function showViewer(){
    var tbl = document.getElementById("statusPopup");
    tbl.style.opacity = "";
    setTimeout(function(){
      document.getElementById("statusPopup").style.display = "";
    },300);
    var ele = document.getElementById("invViewer");
    ele.style.display = "block";
    setTimeout(function(){      
      ele.style.opacity = "1";
      //window.scroll(0,0);
      document.body.style.overflow = "hidden";
    },300);
  }
  //Oculta el popup de visualización de la factura 
  function closeViewer(){
    var ele = document.getElementById("invViewer");
    ele.style.opacity = "";
    setTimeout(function(){      
      ele.style.display = "";
      document.body.style.overflow = "";
    },300);
  }
  //Evento para cancelar visualizar invoice
  document.getElementById("backViewer").addEventListener("click",function(){
    closeViewer();
    document.getElementById("frameView").style.opacity = "";
  });
  //Evento de imprimir la pantalla del viewer
  document.getElementById("printView").addEventListener("click",function(){
    window.frames[0].print();
  });
  //Cancelar delete evento del pboton del popup
  document.getElementById("cancelDelInvc").addEventListener("click",function(){
    closePopup("deletePopup");
  });
  //Cancelar enviar evento del pboton del popup
  document.getElementById("cancelSendInvc").addEventListener("click",function(){
    closePopup("sendPopup");
  });
  
  //Cancelar delete evento del pboton del popup
  document.getElementById("cancelUplInvc").addEventListener("click",function(){
    closePopup("uploadPopup");
    document.getElementById("inptUplFile").value = "";
  });
  //Cancelar delete evento del pboton del popup
  document.getElementById("cancelCnfrmUp").addEventListener("click",function(){
    closePopup("uploadCnfrPopup");
    document.getElementById("inptUplFile").value = "";
  });
  document.getElementById("uplTrashFile").addEventListener("click",function(){
    document.getElementById("inptUplFile").value = "";
    document.getElementById("uplName").innerText = "";
    closePopup("uploadCnfrPopup");
    showPopup("uploadPopup");
  });
  //Cancelar delete evento del pboton del popup
  document.getElementById("cancelErrInvc").addEventListener("click",function(){
    closePopup("errUplPopup");
    document.getElementById("inptUplFile").value = "";
  });
  

  //Evento si se cambia la fecha de vencimiento se valida que esta no sea menor que la de emisión
  document.getElementById("duedate").addEventListener("focusout",function(){
    var issuedate = document.getElementById("issuedate").value;
    if(issuedate!=""){
      var time1 = (new Date(issuedate)).getTime();
      var time2 = (new Date(this.value)).getTime();
      if(time1>time2){
        this.value = "";
        inptError(this,"La fecha de vencimiento no puede ser menor a la fecha de emisión");
      }else{
        removeErr(this);
        removeErr(document.getElementById("duedate"));
      }
    }
  });
  //Evento si se cambia la fecha de vencimiento se valida que esta no sea menor que la de emisión
  document.getElementById("issuedate").addEventListener("focusout",function(){
    var duedate = document.getElementById("duedate").value;
    if(duedate!=""){
      var time1 = (new Date(duedate)).getTime();
      var time2 = (new Date(this.value)).getTime();
      if(time1<time2){
        this.value = "";
        inptError(this,"La fecha de emisión no puede ser mayor a la fecha de vencimiento");
      }else{
        removeErr(this);
        removeErr(document.getElementById("duedate"));
      }
    }
  });
  //Evento que se marquen todas las casillas responsive
  var mark = document.getElementById("markAllRspv");
  mark.addEventListener("click",function(){
    var btnsCnt = document.getElementById("buttonsCell");
    var tbl = document.getElementById("responsiveTable");
    var marks = tbl.getElementsByClassName("inptMark");
    var selected = 0;
    if(this.checked){
      for(var i=0;i<marks.length;i++){
        if(marks[i].parentElement.parentElement.style.display!="none"){
          selected++;
          marks[i].checked = true;
        }
      }
    }else{
      for(var i=0;i<marks.length;i++){
        marks[i].checked = false;
      }
      selected = 0;
    }
    var qtyCnt = document.getElementById("invoicesQtySel");
    if(selected>0){
      btnsCnt.getElementsByClassName("btnIconSubh")[0].classList.remove("btnIconSubhDis");
      btnsCnt.getElementsByClassName("btnIconSubh")[1].classList.remove("btnIconSubhDis");
      qtyCnt.innerText = selected+" documentos seleccionados";
    }else{
      btnsCnt.getElementsByClassName("btnIconSubh")[0].classList.remove("btnIconSubhDis");
      btnsCnt.getElementsByClassName("btnIconSubh")[1].classList.add("btnIconSubhDis");
      qtyCnt.innerText = "";
    }
  });

  //Swiche de natural y jurídico
  var cells = document.getElementById("topTypeCnt").getElementsByTagName("div");
  for(var i=0;i<cells.length;i++){
    cells[i].addEventListener("click",function(){
      //Remover la clase de la otra
      document.getElementsByClassName("typeSel")[0].classList.remove("typeSel");
      this.classList.add("typeSel");
      //Marcar placeholder del campo rif/cedula según este campo
      
      var rif = document.getElementById("customid");
      rif.setAttribute("placeholder",this.getAttribute("placeholder"));
      var type = this.getAttribute("type");
      if(type==2){
        if(isRIF(rif.value) || !rif.value.length){
            removeErr(rif);
        }else{
            inptError(rif,"Formato de RIF Inválido");
        }
      }else{
        if(isCI(rif.value) || !rif.value.length){
            removeErr(rif);
        }else{
            inptError(rif,"Formato de Cédula Inválido");
        }
      }
    });
  }
  //Evento de campo RIF / Cedula
  var riffield = document.getElementById("customid");
  // definir formato de entrada
  riffield.addEventListener("keyup", function(e){                    
      this.value = formatRIF(this.value); 
  });
  // definir validacion
  riffield.addEventListener("change", function(){
    var type = document.getElementsByClassName("typeSel")[0].getAttribute("type");
    if(type==2){
      if(isRIF(this.value) || !this.value.length){
          removeErr(this);
      }else{
          inptError(this,"Formato de RIF Inválido");
      }
    }else{
      if(isCI(this.value) || !this.value.length){
          removeErr(this);
      }else{
          inptError(this,"Formato de Cédula Inválido");
      }
    }
  });
  //Evento de la busqueda
  document.getElementById("mySearch").addEventListener("change",function(){
    loadInvoices(this.value);
  });
  //Evento para mostrar popup de carga masiva. Boton encima de la tabla "cargar"
  document.getElementById("loadButtom").addEventListener("click",function(){
    showPopup("uploadPopup");
  });
  //Valida que el formato cargado sea .txt
  document.getElementById("inptUplFile").addEventListener("change",function(){
    fileValidation(this);
  });
  
  //Se valida el archivo previo a procesar su carga para determinar incidencias en su construcción
  function checkFile(file){
    var par = {};    
    par.sessionid = getParameterByName("sessid");
    par.invoicesfile = file;
    var succes = function(status, respText){
      var resp = "";
      if(respText!=="")resp = JSON.parse(respText);    
      switch (status){
          case 200:
            var errors = resp.errors;
            var newArr = [];
            for(var i=1;i<errors.length;i++){
              if(errors[i]['err'+i]>0){
                errors[i].lbl = ('err'+i);
                newArr.push(errors[i]);
              }
            }
            if(!(newArr.length>0)){
              //Evento para mostrar popup de carga masiva. Boton encima de la tabla "cargar"
              var fileInput = document.getElementById("inptUplFile");
              var filePath = fileInput.files[0].name;;
              if(errors[0]['err0']>1)lbl = ' registros cargados'; else lbl = ' registro cargado.';
              document.getElementById("numberInvc").innerHTML = errors[0]['err0']+lbl;
              document.getElementById("uplName").innerText = filePath;
              closePopup("uploadPopup");
              showPopup("uploadCnfrPopup");
            }else{
              var select = document.getElementById("selErrUpl");
              drawSelectErr(newArr,select,'lbl','errmsg');
              closePopup("uploadPopup");
              document.getElementById("selErrUpl").dispatchEvent(new Event("change"));
              showPopup("errUplPopup");
              //Se llena el nombre de error
              var fileInput = document.getElementById("inptUplFile");
              var filePath = fileInput.files[0].name;
              var tot = 0;
              for(var x=0;x<errors.length;x++){
                tot = tot + parseFloat(errors[x]['err'+x]);
              }
              var toterr = 0;
              for(var x=0;x<newArr.length;x++){
                toterr = toterr + parseFloat(newArr[x][newArr[x]['lbl']]);
              }
              var lblreg = tot+" Registros ";
              if(tot==1)lblreg = tot+" Registro ";
              document.getElementById("totRegCount").innerText = lblreg;
              document.getElementById("progErrName").innerText = filePath;
              var lblerr = toterr+' Errores';
              if(toterr==1)lblerr = toterr+' Error';
              document.getElementById("totErrCount").innerText = lblerr;
            }
          break;
          case 400:
              console.log(resp);
              var file = document.getElementById("inptUplFile");
              var msg = resp.msg;
              if(msg==""||msg==undefined||msg==null){
                msg = 'Ocurrió un error al procesar el archivo, por favor intente nuevamente';
              }
              inptError(file,msg);
              file.value = "";
              setTimeout(function(){
                removeErr(document.getElementById("inptUplFile"));
              },6000);
              break;
          case 401:
              console.log(resp);
              gotoPage("login","main",{});
              break;
          case 500:
            console.log(resp);
              var file = document.getElementById("inptUplFile");
              var msg = "";//resp.msg;
              if(msg==""||msg==undefined||msg==null){
                msg = 'Ocurrió un error al procesar el archivo, por favor intente nuevamente';
              }
              inptError(file,msg);
              file.value = "";
              setTimeout(function(){
                removeErr(document.getElementById("inptUplFile"));
              },6000);
              console.log(resp);
              break;
          default:
            console.log(resp);
              var file = document.getElementById("inptUplFile");
              var msg = resp.msg;
              if(msg==""||msg==undefined||msg==null){
                msg = 'Ocurrió un error al procesar el archivo, por favor intente nuevamente';
              }
              inptError(file,msg);
              file.value = "";
              setTimeout(function(){
                removeErr(document.getElementById("inptUplFile"));
              },6000);
              console.log(resp);
              break;
      }
    }
    callWS("POST", "invoices/uploadcheck", par, succes, "");
    return;
  }

  //Una vez que el archivo fue aprobado se autoriza el pase de la tabla temporal a la definitiva
  function approveUpload(){
    var par = {};    
    par.sessionid = getParameterByName("sessid");
    var succes = function(status, respText){
      var resp = "";
      if(respText!=="")resp = JSON.parse(respText);    
      switch (status){
          case 200:
            if(resp!=""){
              if(resp.recordsupdates>0){
                document.getElementById("inptUplFile").value = "";
                //Evento para mostrar popup de carga masiva. Boton encima de la tabla "cargar"
                closePopup("uploadCnfrPopup");
                loadInvoices();
              }
            }
          break;
          case 400:
              console.log(resp);
              var file = document.getElementById("uplTrashFile");
              inptError(file,'Ocurrió un error al procesar el archivo, por favor intente nuevamente');
              setTimeout(function(){
                removeErr(document.getElementById("uplTrashFile"));
              },6000);
              break;
          case 401:
              console.log(resp);
              gotoPage("login","main",{});
              break;
          case 500:
              console.log(resp);
              var file = document.getElementById("uplTrashFile");
              inptError(file,'Ocurrió un error al procesar el archivo, por favor intente nuevamente');
              setTimeout(function(){
                removeErr(document.getElementById("uplTrashFile"));
              },6000);
              break;
          default:
              var file = document.getElementById("uplTrashFile");
              inptError(file,'Ocurrió un error al procesar el archivo, por favor intente nuevamente');
              setTimeout(function(){
                removeErr(document.getElementById("uplTrashFile"));
              },6000);
              console.log(resp);
              break;
      }
    }
    callWS("POST", "invoices/uploadupdate", par, succes, "");
    return;
  }
  //Procesar la carga del archivo
  document.getElementById("uplCnfrmBtn").addEventListener("click",function(){
    approveUpload();
  });

  //Intentar de nuevo botón cierra popup de error y abre el de carga nuevamente
  document.getElementById("tryagainUpl").addEventListener("click",function(){
    document.getElementById("inptUplFile").value = "";
    closePopup("errUplPopup");
    showPopup("uploadPopup");
  });

  //Función para visualizar el documento en el iframe previsualización
  function viewDocument(rsp){
    var lblPrev = "";
    var type = rsp.header.type.id;
    var nro = rsp.header.refnumber;
    var id = rsp.header.id;    
    var sessid = getParameterByName("sessid");
    document.getElementById("viewIssueDate").innerText = rsp.header.issuedate.formatted;

    if(rsp.header.sentdate.formatted)
      document.getElementById("viewSentDate").innerText = rsp.header.sentdate.formatted;
    else
      document.getElementById("viewSentDate").innerText = "";

    if(rsp.header.viewdate.formatted)
      document.getElementById("viewReadDate").innerText = rsp.header.viewdate.formatted;
    else
      document.getElementById("viewReadDate").innerText = "";

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
      frame.setAttribute("src","./api/invoices/show.php?id="+id+"&sessionid="+sessid);
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
  //Se llama el servicio del entry
  function invoiceControls(id){
    var par = {};
    par.id = id;
    par.sessionid = sessid;
    var succes = function(status, respText){
      var resp = JSON.parse(respText);
      switch (status){
          case 200:
            console.log(resp);
            serieControl(resp.seriescontrol);
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
    callWS("GET", "invoices/info", par, succes, "");
    return;
  }
  //Se arma el select de la seriecontrol
  function serieControl(rsp) {
    var select = document.getElementById("invoiceserie");
    select.innerHTML = "";
    var opt;
    opt = document.createElement("option");
    opt.setAttribute("value", "-1");
    opt.innerHTML = "--";
    select.appendChild(opt);  
    for (var i = 0; i < rsp.length; i++) {
      opt = document.createElement("option");
      if(rsp[i]['serie']!="")
        val = rsp[i]['serie']+"-"+rsp[i]['control'];
      else  
        val = rsp[i]['control'];
      opt.setAttribute("value", val);    
      opt.innerHTML = val;
      select.appendChild(opt);
    }

    //Evento para fromatear el campo refcontrol
    document.getElementById("invoicerefcrtl").addEventListener("keyup",function(){
      this.value = formatRefctr(this.value);
    });
  }

  //Cargar los itemes que posean un error especifico 
  function loadBadItems(offset=0,order=-1,numrecords=999){
    var par = {};
    par.errorcode = (document.getElementById("selErrUpl").value).replace("err","");
    par.offset = offset;
    par.order = order;
    par.numofrec = numrecords;
    par.sessionid = getParameterByName("sessid");
    var succes = function(status, respText){
      var resp = "";
      if(respText!="")resp = JSON.parse(respText);
      switch (status){
          case 200:
            if(resp!==""){
              drawBadItems(resp.records);
            }
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
    callWS("GET", "invoices/uploaderrors", par, succes);
    return;
  }
  //Se pinta la lista de los items cargados por un error
  function drawBadItems(rsp){
    var tbl = document.getElementById("listItemsErr");
    tbl.innerHTML = "";
    for(var i=0;i<rsp.length;i++){
      var itemRow = document.createElement("div");
      itemRow.classList.add("tblBadItem");
        var cellId = document.createElement("div");
        cellId.classList.add("cellErrNro");
        cellId.innerHTML= rsp[i].refnumber;
        var cellName = document.createElement("div");
        cellName.classList.add("cellErrName");
        cellName.innerHTML= rsp[i].client.name;
      itemRow.appendChild(cellId);itemRow.appendChild(cellName);
      tbl.appendChild(itemRow);
    }
  }
  //al cambiar la lista de errores se refrescan los items
  document.getElementById("selErrUpl").addEventListener("change",function(){
    loadBadItems(0,-1,100);
  });
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
  //Evento descargar la plantilla
  document.getElementById("btnInvExamp").addEventListener("click", function () {
    var text = '../formats/plantilla.csv';
    var filename = "plantilla.csv";
    download(filename, text);
  }, false);

  //Evento de ver todos los status del filtro
  document.getElementById("allStatus").addEventListener("click",function(){
    var checks = document.getElementsByClassName("checkstatus");
    for(var i=0;i<checks.length;i++){dra
      if(this.checked){
        checks[i].checked = true;
      }else{
        checks[i].checked = false;
      }
    }
  });
  var checks = document.getElementsByClassName("checkstatus");
  for(var i=0;i<checks.length;i++){
    checks[i].addEventListener("click",function(){
      if(this.checked){
        document.getElementById("allStatus").checked = true;
        var checks = document.getElementsByClassName("checkstatus");
        for(var x=0;x<checks.length;x++){
          if(!checks[x].checked)document.getElementById("allStatus").checked = false;
        }
      }else{
        document.getElementById("allStatus").checked = false;
      }
    });
  }

  document.getElementById('numofrecFilt').addEventListener('change',function(){
    loadInvoices();
  });
};
 



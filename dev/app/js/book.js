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

      var fdate = "";
      fdarr =  (par.datefrom).split("-");
      fdate = fdarr[2]+"/"+fdarr[1]+"/"+fdarr[0];
      var sdate = "";
      sdarr =  (par.dateto).split("-");
      sdate = sdarr[2]+"/"+sdarr[1]+"/"+sdarr[0];
      document.getElementById("periodDates").innerHTML = fdate+" al "+sdate;
      par.offset = offset;
      par.order = order;
      par.numofrec = document.getElementById("numofrecFilt").value
      par.sessionid = sessid;
      
      //alert("Offset="+offset+"  Numofrec="+par.numofrec);
      callWS("GET", "reports/salesbook", par, BCLoadInvoices, offset );
      return;
  }
  function BCLoadInvoices(status, respText, offset){
      var jsonResp;
      console.log('status', status)
      switch (status){
          case 200:
              jsonResp = JSON.parse(respText);
              console.log('jsonResp', jsonResp);
              drawInvoices(jsonResp);
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

  // Esta funcion crea la tabla diamicamente segun la respuesta de user/list
  function drawInvoices(data){
    let cont;
    let btn;
    let table=document.getElementById("centerTable");    
    table.innerHTML = "";
    
    var subTable = document.getElementById("centerSubTable");
    var clone = subTable.firstElementChild.cloneNode(true);
    var rsp = data;
    data = rsp.records;
    if(data.length>0){   
      
      subTable.innerHTML = ""; 
      var cn = 0;  
      data.length;
      for(navLink of data){
        cn = cn+1;        
        line = document.createElement("tr");
        line.classList.add('userLine');
        line.id = navLink.id;

        celda = document.createElement("td");
        celda.classList.add("cellNo","cell5");
        celda.innerHTML = '<span>'+navLink.id+'</span>';
        line.appendChild(celda);
                
        celda = document.createElement("td");
        celda.classList.add("cellDate","cell10");
        celda.id="date-"+navLink.id;
        celda.innerHTML = '<span>'+navLink.issuedate.formatted+'</span>';
        line.appendChild(celda);

        celda = document.createElement("td");
        celda.classList.add("cellRif","cell10");
        celda.innerHTML = '<span>'+navLink.client.rif+'</span>';
        line.appendChild(celda);

        celda = document.createElement("td");
        celda.classList.add("cellRif","cell20");
        celda.innerHTML = '<span>'+navLink.client.name+'</span>';
        line.appendChild(celda);

        celda = document.createElement("td");
        celda.classList.add("cellRif","cell55");
        line.appendChild(celda);
    
        table.appendChild(line);

        var altclone = clone.cloneNode(true);
        altclone.style.display = "";
        //Sección sub interna del lado derecho. Se cambian los valores del clone y se reinserta          
        altclone.getElementsByClassName("cellTbl")[1].innerHTML = formatRefctr(navLink.ctrnumber);
        var type= navLink.type.id;
        if(type=='FAC'){
          altclone.getElementsByClassName("cellTbl")[0].innerHTML = navLink.refnumber;
          altclone.getElementsByClassName("cellTbl")[2].innerHTML = "";
          altclone.getElementsByClassName("cellTbl")[3].innerHTML =  "";
          altclone.getElementsByClassName("cellTbl")[5].innerHTML =  "";
        }else if(type=='NCR'){
          altclone.getElementsByClassName("cellTbl")[0].innerHTML = "";
          altclone.getElementsByClassName("cellTbl")[2].innerHTML = "";
          altclone.getElementsByClassName("cellTbl")[3].innerHTML =  navLink.refnumber;
          altclone.getElementsByClassName("cellTbl")[5].innerHTML =  formatRefctr(navLink.ctrref);
        }else if(type=='NDB'){
          altclone.getElementsByClassName("cellTbl")[0].innerHTML = "";
          altclone.getElementsByClassName("cellTbl")[2].innerHTML = navLink.refnumber;
          altclone.getElementsByClassName("cellTbl")[3].innerHTML =  "";
          altclone.getElementsByClassName("cellTbl")[5].innerHTML =  formatRefctr(navLink.ctrref);
        }

        altclone.getElementsByClassName("cellTbl")[4].innerHTML =  navLink.transactiontype;
        
        altclone.getElementsByClassName("cellTbl")[6].lastElementChild.children[0].innerHTML =  navLink.amounts.totals.taxbase.formatted;
        altclone.getElementsByClassName("cellTbl")[6].lastElementChild.children[1].innerHTML =  navLink.amounts.totals.total.formatted;

        const setthreecols = (ele,keyname) =>{
          ele.lastElementChild.children[0].innerHTML =  navLink.amounts[keyname].taxbase.formatted;
          ele.lastElementChild.children[1].innerHTML =  navLink.amounts[keyname].taxpct.number;
          ele.lastElementChild.children[2].innerHTML =  navLink.amounts[keyname].taxtotal.formatted;
        }
        var keys = ['exempt','notaxable','perceived','generaliva','reducediva','addediva'];
        for(var x = 7; x<13; x++){
          setthreecols(altclone.getElementsByClassName("cellTbl")[x],keys[x-7]);
        }
        
        subTable.appendChild(altclone);

        
      }
      if(cn==data.length){
        line = document.createElement("tr");
        line.classList.add('userLine');
        line.style.backgroundColor = "rgb(232, 232, 232,0.7)";

        celda = document.createElement("td");
        celda.classList.add("cellNo","cell5");
        line.appendChild(celda);
                
        celda = document.createElement("td");
        celda.classList.add("cellDate","cell10");
        line.appendChild(celda);

        celda = document.createElement("td");
        celda.classList.add("cellRif","cell10");
        line.appendChild(celda);

        celda = document.createElement("td");
        celda.classList.add("cellRif","cell20");
        line.appendChild(celda);

        celda = document.createElement("td");
        celda.classList.add("cellRif","cell55");
        line.appendChild(celda);
    
        table.appendChild(line);

        var altclone = clone.cloneNode(true);
        altclone.style.display = "";

        altclone.getElementsByClassName("cellTbl")[0].innerHTML = "";
        altclone.getElementsByClassName("cellTbl")[1].innerHTML = "";
        altclone.getElementsByClassName("cellTbl")[2].innerHTML =  "";
        altclone.getElementsByClassName("cellTbl")[3].innerHTML = "";
        altclone.getElementsByClassName("cellTbl")[4].innerHTML =  "";
        altclone.getElementsByClassName("cellTbl")[5].innerHTML = "";
      
        altclone.getElementsByClassName("cellTbl")[6].lastElementChild.children[0].innerHTML =  rsp.totals.totals.taxbase.formatted;
        altclone.getElementsByClassName("cellTbl")[6].lastElementChild.children[1].innerHTML =  rsp.totals.totals.total.formatted;

        const setthreecolsTot = (ele,keyname) =>{
          ele.lastElementChild.children[0].innerHTML =  rsp.totals[keyname].taxbase.formatted;
          ele.lastElementChild.children[1].innerHTML =  rsp.totals[keyname].taxpct.number;
          ele.lastElementChild.children[2].innerHTML =  rsp.totals[keyname].taxtotal.formatted;
        }
        var keys = ['exempt','notaxable','perceived','generaliva','reducediva','addediva'];
        for(var x = 7; x<13; x++){
          setthreecolsTot(altclone.getElementsByClassName("cellTbl")[x],keys[x-7]);
        }

        subTable.appendChild(altclone);
      }
      
    }else{
      subTable.innerHTML = ""; 
      var altclone = clone.cloneNode(true);
      altclone.style.display = "none";
      subTable.appendChild(altclone);
      var html = '';
      html += '<div class="blankTblCell">';
      html += '<div class="blankTblImg"></div>';
      html += '<div class="blankTblMsg">Seleccione un rango de fechas para mostrar información</div>';
      html += '</div>';
      table.innerHTML = html;
    }

    //Datos tabla inferior
    var debit = {'taxbase':0,'taxtotal':0},credit={'taxbase':0,'taxtotal':0},total={'taxbase':0,'taxtotal':0};
    const setlowtbl = (ele,row,keyname) =>{
      if(row=='totalstax'){
        switch(keyname){
          case 'debits':
            ele.lastElementChild.children[0].innerHTML = number_format(debit.taxbase,2);
            ele.lastElementChild.children[1].innerHTML =  number_format(debit.taxtotal,2);
          break;
          case 'credits':
            ele.lastElementChild.children[0].innerHTML = number_format(credit.taxbase,2);
            ele.lastElementChild.children[1].innerHTML =  number_format(credit.taxtotal,2);
          break;
          case 'totals':
            ele.lastElementChild.children[0].innerHTML = number_format(total.taxbase,2);
            ele.lastElementChild.children[1].innerHTML =  number_format(total.taxtotal,2);
          break;
        }
      }else{
        switch(row){
          case 'debits':
            debit.taxbase = debit.taxbase+rsp.resume[row][keyname].taxbase.number;
            debit.taxtotal = debit.taxtotal+rsp.resume[row][keyname].taxtotal.number;
          break;
          case 'credits':
            credit.taxbase = credit.taxbase+rsp.resume[row][keyname].taxbase.number;
            credit.taxtotal = credit.taxtotal+rsp.resume[row][keyname].taxtotal.number;
          break;
          case 'totals':
            total.taxbase = total.taxbase+rsp.resume[row][keyname].taxbase.number;
            total.taxtotal = total.taxtotal+rsp.resume[row][keyname].taxtotal.number;
          break;
        }
        ele.lastElementChild.children[0].innerHTML =  rsp.resume[row][keyname].taxbase.formatted;
        ele.lastElementChild.children[1].innerHTML =  rsp.resume[row][keyname].taxtotal.formatted;
      }
    }
    var keys = ['debits','credits','totals'];
    for(let key in rsp.resume){
      for(var x = 0; x<3; x++){
        setlowtbl(document.getElementById(key).getElementsByClassName("cellTbl")[x],key,keys[x]);
      }
    }
    for(var x = 0; x<3; x++){
      setlowtbl(document.getElementById("totalstax").getElementsByClassName("cellTbl")[x],"totalstax",keys[x]);
    }
    
    
    document.getElementById("subTblInf").style.height = document.getElementsByClassName("centerTable")[0].offsetHeight+5+"px";
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

  //RESPETO LA IDENTACIÖN DEL DOCUMENTO PERO CREO QUE DEBE SER a 4 espacios
  
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
  
  //Evento de la busqueda
  document.getElementById("mySearch").addEventListener("change",function(){
    loadInvoices(this.value);
  });

  //Evento de ver todos los status del filtro
  document.getElementById("allStatus").addEventListener("click",function(){
    var checks = document.getElementsByClassName("checkstatus");
    for(var i=0;i<checks.length;i++){
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
 



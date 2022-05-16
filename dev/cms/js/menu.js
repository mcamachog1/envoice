
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
  function inptError(ele,msg=""){
    ele.parentElement.classList.add("inptErr");
    if(msg!=""){
      if(ele.parentElement.parentElement.getElementsByClassName("msgErrInpt").length==0){
        var msgerr = document.createElement("div");
        msgerr.classList.add("msgErrInpt");
        msgerr.innerHTML = msg;
        ele.parentElement.parentElement.appendChild(msgerr);
      }else{
        ele.parentElement.parentElement.getElementsByClassName("msgErrInpt")[0].innerHTML = msg;
      }
    }
  }
  function removeErr(ele){
    ele.parentElement.classList.remove("inptErr");
    if(ele.parentElement.parentElement.getElementsByClassName("msgErrInpt").length>0){
      ele.parentElement.parentElement.removeChild(ele.parentElement.parentElement.getElementsByClassName("msgErrInpt")[0]);
    }
  }
  

window.onload = function () {
    init();
    myMenu();

    document.getElementById("logOut").addEventListener("click", logOut);
  
    function myMenu() {
      document.getElementById("usernameMenu").innerText =
        sessionStorage.getItem("username");
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
    //Close onLoad
  };


  
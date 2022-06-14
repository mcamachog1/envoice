function myFunction() {
  if (window.event.keyCode === 13 && !window.event.shiftKey) {
    sendform();
  }
}
function contrasena(show, hide, element, type) {
  document.getElementById(show).style.visibility = "hidden";
  document.getElementById(hide).style.visibility = "visible";
  document.getElementById(element).type = type;
}

function sendform() {
  let user = document.getElementById("user");
  let pass = document.getElementById("password");
  if (checkform(user, pass)) {
    let par = {};
    par.usr = user.value;
    par.pwd = MD5(pass.value);
    callWS("GET", "security/login", par, respLogin);
    return;
  }
}

function respLogin(status, respText) {
  let jsonResp;
  let badLoginUser = document.getElementById("btnMessageUser");
  let badLoginPwd = document.getElementById("btnMessagePwd");
  switch (status) {
    case 200:
      // ir a pagina del home
      jsonResp = JSON.parse(respText);
      console.log(jsonResp);
      document.getElementById("stdbtnSubmit").classList.add("noShowD");
      document.getElementById("showButtonD").classList.remove("noShowD");
      sessionStorage.setItem("username", jsonResp.name);
      sessionStorage.setItem("id", jsonResp.id);
      // sessionStorage.setItem("dashboard", jsonResp.privileges.dashboard);
      gotoPage("menu", "main", { sessid: jsonResp.sessionid });
      break;
    case 400:
      var msg = "Error en la llamada del servicio.";
      jsonResp = JSON.parse(respText);
      if(jsonResp.msg!=="")msg = jsonResp.msg;
      badLoginPwd.innerText = msg;
      badLoginPwd.style.display = "inherit";
      badLoginPwd.classList.remove('noShowD')
      document.getElementById('user').classList.add('badPwd')
      document.getElementById('password').classList.add('badPwd');
      setTimeout(function () {
        badLoginPwd.style.display = "none";
        badLoginPwd.classList.add('noShowD')
        document.getElementById('user').classList.remove('badPwd')
        document.getElementById('password').classList.remove('badPwd');
      }, 5000);
      break;
    case 401:
      badLoginPwd.innerText = "Inténtelo nuevamente. Usuario/Contraseña inválidos.";
      badLoginPwd.style.display = "inherit";
      badLoginPwd.classList.remove('noShowD')
      document.getElementById('user').classList.add('badPwd')
      document.getElementById('password').classList.add('badPwd');

      setTimeout(function () {
        badLoginPwd.style.display = "none";
        badLoginPwd.classList.add('noShowD')
        document.getElementById('user').classList.remove('badPwd')
        document.getElementById('password').classList.remove('badPwd');
      }, 5000);
      break;
    case 500:
      badLoginPwd.innerText = "Error Interno.";
      badLoginPwd.style.display = "inherit";
      badLoginPwd.classList.remove('noShowD')
      document.getElementById('user').classList.add('badPwd')
      document.getElementById('password').classList.add('badPwd');
      setTimeout(function () {
        badLoginPwd.style.display = "none";
        badLoginPwd.classList.add('noShowD')
        document.getElementById('user').classList.remove('badPwd')
        document.getElementById('password').classList.remove('badPwd');
      }, 4000);
      break;
    default:
      jsonResp = JSON.parse(respText);
      console.log(jsonResp);
      // document.getElementById("alerta").innerHTML = ' <div class="stdalert">'+jsonResp.msg+'</div>';
      break;
  }
}

function checkform(user, pass) {
  let badLoginUser = document.getElementById("btnMessageUser");
  let badLoginPwd = document.getElementById("btnMessagePwd");
  let error = true;
  let re = /^([\da-z_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;
  // Validamos el usuario
  if (
    user.value === null ||
    user.value.length === 0 ||
    /^\s+$/.test(user.value)
  ) {
    user.select();
    badLoginUser.innerText = "Ingrese su usuario.";
    badLoginUser.style.display = "inherit";
    badLoginUser.classList.remove('noShowD');
    document.getElementById('user').classList.add('badPwd');
    setTimeout(function () {
      badLoginUser.style.display = "none";
    badLoginUser.classList.add('noShowD');
      document.getElementById('user').classList.remove('badPwd');
    }, 4000);
    error = false;
  } 
  // Validamos la password
  if (
    pass.value === null ||
    pass.value.length === 0 ||
    /^\s+$/.test(pass.value)
  ) {
    pass.select();
    badLoginPwd.innerText = "Ingrese su clave.";
    badLoginPwd.style.display = "inherit";
    badLoginPwd.classList.remove('noShowD');
    
    document.getElementById('password').classList.add('badPwd');
    setTimeout(function () {
      badLoginPwd.style.display = "none";
      badLoginPwd.classList.add('noShowD');
      document.getElementById('password').classList.remove('badPwd');
    }, 4000);
    error = false;
    //}else if(!isOkPass(pass.value)){
    //    document.getElementById("alerta").innerHTML = ' <div class="stdalert">La contraseña debe contener letras mayúsculas y minúsculas, números, símbolos y tener al menos 8 caracteres</div>'
    //    pass.select();
    //    return false;
  }

  return error;
}

function init() {
  //Loginal presionar enter
  window.onkeypress = function () {
    myFunction();
  };
  document
    .getElementById("stdbtnSubmit")
    .addEventListener("click", function () {
      sendform();
    });

  document.getElementById("lostPass").addEventListener("click", function () {
    gotoPage("login", "forgotpwd", "");
  });

  document.getElementById("showPwd").addEventListener("click", function () {
    contrasena("showPwd", "hidePwd", "password", "text");
  });
  document.getElementById("hidePwd").addEventListener("click", function () {
    contrasena("hidePwd", "showPwd", "password", "password");
  });
}
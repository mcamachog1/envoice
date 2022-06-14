function contrasena(show, hide, element, type) {
  document.getElementById(show).style.visibility = "hidden";
  document.getElementById(hide).style.visibility = "visible";
  document.getElementById(element).type = type;
}

function myRecover(url) {
  var pwd = document.getElementById("newPassword").value;
  var confirmpwd = document.getElementById("confirmPassword").value;
  var par = {};


  var badRecover = document.getElementById("btnChangeMessage");
  if (pwd.length < 8) {
    badRecover.innerText = "La clave debe contener mínimo 8 caracteres.";
    badRecover.style.visibility = "visible";
    document.getElementById('newPassword').classList.add('badPwd')
    document.getElementById('confirmPassword').classList.add('badPwd');
    setTimeout(function () {
      badRecover.style.visibility = "hidden";
      document.getElementById('newPassword').classList.remove('badPwd')
      document.getElementById('confirmPassword').classList.remove('badPwd');
    }, 5000);
  } else if (pwd != confirmpwd) {
    document.getElementById("confirmPassword").classList.add("badPwd");
    document.getElementById("confirmPassword").focus();
    badRecover.innerText = "Contraseñas no coinciden";
    badRecover.style.visibility = "visible";
    setTimeout(function () {
      badRecover.style.visibility = "hidden";
      document.getElementById("confirmPassword").classList.remove("badPwd");
    }, 6000);
  } else if (pwd.length === 0 || confirmpwd.length === 0) {
    badRecover.innerText = "Introduzca una clave.";
    badRecover.style.visibility = "visible";
    setTimeout(function () {
      badRecover.style.visibility = "hidden";
    }, 4000);
  } else {
    par.hash = url[url.length - 2].split("&")[0];
    par.pwd = MD5(pwd);
    callWS("GET", "security/recover", par, respRecover);
    return;
  } 
}

function respRecover(status, respText) {
  var jsonResp;
  var badRecover = document.getElementById("btnChangeMessage");
  switch (status) {
    case 200:
      // ir a pagina del home
      jsonResp = JSON.parse(respText);
      console.log(jsonResp);
      document.getElementById("stdbtnChange").classList.add("noShowD");
      document.getElementById("showButtonD").classList.remove("noShowD");
      badRecover.innerHTML =
        '<span style="color:#359A4A"><i class="far fa-check-circle" style="font-size: 16px;"></i><br>Usted ha cambiado su contraseña exitosamente.</span>';
      badRecover.style.visibility = "visible";
      setTimeout(function () {
        badRecover.style.visibility = "hidden";
        sessionStorage.removeItem("emailRecover");
        gotoPage("login", "main", "");
      }, 5000);
      break;
    case 401:
      badRecover.innerText = "Enlace inválido.";
      badRecover.style.visibility = "visible";
      setTimeout(function () {
        badRecover.style.visibility = "hidden";
      }, 4000);
      break;
    case 500:
      badRecover.innerText = "Error Interno.";
      badRecover.style.visibility = "visible";
      setTimeout(function () {
        badRecover.style.visibility = "hidden";
      }, 4000);
      break;
    default:
      jsonResp = JSON.parse(respText);
      console.log(jsonResp);
      break;
  }
}

//Contraseña entre 8 y 32 caracteres con may, min, car y num
function validar_clave(contrasenna) {
  if (contrasenna.length >= 8 && contrasenna.length <= 32) {
    var mayuscula = false;
    var minuscula = false;
    var numero = false;
    var caracter_raro = false;
    for (var i = 0; i < contrasenna.length; i++) {
      if (contrasenna.charCodeAt(i) >= 65 && contrasenna.charCodeAt(i) <= 90) {
        mayuscula = true;
      } else if (
        contrasenna.charCodeAt(i) >= 97 &&
        contrasenna.charCodeAt(i) <= 122
      ) {
        minuscula = true;
      } else if (
        contrasenna.charCodeAt(i) >= 48 &&
        contrasenna.charCodeAt(i) <= 57
      ) {
        numero = true;
      } else {
        caracter_raro = true;
      }
    }
    if (
      mayuscula == true &&
      minuscula == true &&
      caracter_raro == true &&
      numero == true
    ) {
      return true;
    }
  }
  return false;
}

function init() {
  var url = window.location.search.split("=");
  document.getElementById("showPwd").addEventListener("click", function () {
    contrasena("showPwd", "hidePwd", "newPassword", "text");
  });
  document.getElementById("hidePwd").addEventListener("click", function () {
    contrasena("hidePwd", "showPwd", "newPassword", "password");
  });

  document
    .getElementById("showPwdConfirm")
    .addEventListener("click", function () {
      contrasena("showPwdConfirm", "hidePwdConfirm", "confirmPassword", "text");
    });
  document
    .getElementById("hidePwdConfirm")
    .addEventListener("click", function () {
      contrasena(
        "hidePwdConfirm",
        "showPwdConfirm",
        "confirmPassword",
        "password"
      );
    });
  localStorage.removeItem("emailRecover");

  document.getElementById("hidePwd").style.visibility = "hidden";
  document.getElementById("hidePwdConfirm").style.visibility = "hidden";

  document
    .getElementById("stdbtnChange")
    .addEventListener("click", function () {
      myRecover(url);
      //gotoPage('login','main','');
    });
  document.getElementById("emailPwd").innerText = url[url.length - 1];
}

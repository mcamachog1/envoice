function myFunction() {
  if (window.event.keyCode === 13 && !window.event.shiftKey) {
    sendEmail();
  }
}

function sendEmail() {
  var email = document.getElementById("email");
  if (checkEmail(email)) {
    var par = {};
    par.email = email.value;
    callWS("GET", "security/forgot", par, respForgot);
    return;
  }
}

function respForgot(status, respText) {
  var jsonResp;
  var badForgotEmail = document.getElementById("btnMessageEmail");
  switch (status) {
    case 200:
      // ir a pagina del home
      // jsonResp = JSON.parse(respText);
      //console.log(jsonResp);
      document.getElementById("hideForMessage").classList.add("noShowD");
      document.getElementById("showForMessage").classList.remove("noShowD");
      document.getElementById("showForMessage").classList.add("show");
      break;
    case 400:
        badForgotEmail.innerText = "No tenemos ningún usuario registrado con ese correo electrónico.";
        badForgotEmail.style.display = "inherit";
        badForgotEmail.classList.remove('noShowD')
        document.getElementById('email').classList.add('badPwd')
        setTimeout(function () {
          badForgotEmail.style.display = "none";
          badForgotEmail.classList.add('noShowD')
          document.getElementById('email').classList.remove('badPwd')
        }, 4000);
    break;
    case 401:
      badForgotEmail.innerText =
        "No tenemos ningún usuario registrado con ese correo electrónico.";
      badForgotEmail.style.display = "inherit";
      badForgotEmail.classList.remove('noShowD')
      document.getElementById('email').classList.add('badPwd')
      setTimeout(function () {
        badForgotEmail.style.display = "none";
        badForgotEmail.classList.add('noShowD')
        document.getElementById('email').classList.remove('badPwd')
      }, 4000);
      break;
    case 500:
      badForgotEmail.innerText = "Error Interno.";
      badForgotEmail.style.visibility = "visible";
      setTimeout(function () {
        badForgotEmail.style.display = "none";
        badForgotEmail.classList.add('noShowD')
        document.getElementById('email').classList.remove('badPwd')
      }, 4000);
      break;
    default:
      jsonResp = JSON.parse(respText);
      console.log(jsonResp);
      break;
  }
}

function checkEmail(email) {
  var badForgotEmail = document.getElementById("btnMessageEmail");
  var re = /^([\da-z_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;
  if (
    email.value === null ||
    email.value.length === 0 ||
    /^\s+$/.test(email.value)
  ) {
    email.select();
    badForgotEmail.innerText = "Ingrese su correo electrónico.";
    badForgotEmail.style.display = "inherit";
    badForgotEmail.classList.remove('noShowD')
    document.getElementById('email').classList.add('badPwd')
    setTimeout(function () {
      badForgotEmail.style.display = "none";
      badForgotEmail.classList.add('noShowD')
      document.getElementById('email').classList.remove('badPwd')
    }, 4000);
    return false;
    // validar campo email
  } else if (!re.exec(email.value)) {
    email.select();
    email.classList.add("badPwd");
    badForgotEmail.innerText = "Ingrese una dirección de correo electrónico válida.";
    badForgotEmail.style.display = "inherit";
    badForgotEmail.classList.remove('noShowD')
    document.getElementById('email').classList.add('badPwd')
    setTimeout(function () {
      badForgotEmail.style.display = "none";
      badForgotEmail.classList.add('noShowD')
      document.getElementById('email').classList.remove('badPwd')
      email.classList.remove("badPwd");
    }, 5000);
    return false;
  }

  return true;
}

function init() {
  window.onkeypress = function () {
    myFunction();
  };
  document
    .getElementById("stdbtnSubmit")
    .addEventListener("click", function () {
      sendEmail();
      // TODO: Volver a poner este sendEmail
      // respForgot();
    });
  document.getElementById("stdbtnAgree").addEventListener("click", function () {
    gotoPage("login", "main", "");
  });

  document.getElementById("stdbtnCancelar").addEventListener("click", function () {
    gotoPage("login", "main", "");
  });
}

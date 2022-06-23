var globalurl = ".";
var globalurlAlt = "../app";

function gotoPage(id, sid, params) {
    esconderHTML(true);
    var url = globalurl;
    var parsedPars = Object.keys(params)
      .map(function (k) {
        return encodeURIComponent(k) + "=" + encodeURIComponent(params[k]);
      })
      .join("&");
    if (parsedPars.length) parsedPars = "&" + parsedPars;
    location.href = url + "?id=" + id + "&sid=" + sid + parsedPars;
}

function esconderHTML(hide) {
    var html = document.getElementsByTagName("html")[0];
    if (html !== null)
        if (!hide) html.setAttribute("style", "opacity:1");
        else html.removeAttribute("style");
}
/*
function download(service) {
    var url = globalurl + "/api/" + service;
    window.open(url);
}*/
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
      results = regex.exec(location.search);
    return results === null
      ? ""
      : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function getMonday(d) {
    d = new Date(d);
    var day = d.getDay(),
      diff = d.getDate() - day + (day == 0 ? -6 : 1); // adjust when day is sunday
    return new Date(d.setDate(diff));
}

function pad(number, length) {
    var str = "" + number;
    while (str.length < length) {
      str = "0" + str;
    }
    return str;
}

function getValue(id){
    return document.getElementById(id).value;
}
function valueByClass(classname,section = document){
    return section.getElementsByClassName(classname)[0].value;
}
function setValue(id,val){
    document.getElementById(id).value = val;
}
function setValueByClass(classname,val,section = document,attr=""){
    section.getElementsByClassName(classname)[0].value = val;
    if(attr!=""){
        for (let i in attr) {
            section.getElementsByClassName(classname)[0].setAttribute(i,attr[i]);
        }
    }
}

// SW loginmain
var WS_waitscreen = false;
var WS_wstimeout;
function callWS(type, service, params, response, extra = "") {
    var url = globalurl + "/api/" + service + ".php";
    waitOn();
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4) {
            var rsp = "";
            if (this.responseText !== "") {
                rsp = JSON.parse(this.responseText);
                response(this.status, this.responseText, extra);
            } else {
                response(this.status, this.responseText, extra);
            }
            waitOff();
        }
    };
    switch (type) {
        case "POST":
            xhttp.open("POST", url, true);
            var formdata = new FormData();
            for (var key in params) {
              formdata.append(key, params[key]);
            }
            xhttp.send(formdata);
        break; 
        case "JSON":
            xhttp.open("POST", url, true);
            var data = JSON.stringify(params);
            xhttp.send(data);
        break;    
        case "GET":
        case "DELETE":
            var parsedPars = Object.keys(params).map(function (k) {
                return encodeURIComponent(k) + "=" + encodeURIComponent(params[k]);
              })
              .join("&");
            xhttp.open(type.toUpperCase(), url + "?" + parsedPars, true);
            xhttp.send();
        break;
    }
}
function callWSAlt(type, service, params, response, extra = "") {
    var url = globalurlAlt + "/api/" + service + ".php";
    waitOn();
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4) {
            var rsp = "";
            if (this.responseText !== "") {
                rsp = JSON.parse(this.responseText);
                response(this.status, this.responseText, extra);
            } else {
                response(this.status, this.responseText, extra);
            }
            waitOff();
        }
    };
    switch (type) {
        case "POST":
            xhttp.open("POST", url, true);
            var formdata = new FormData();
            for (var key in params) {
              formdata.append(key, params[key]);
            }
            xhttp.send(formdata);
        break; 
        case "JSON":
            xhttp.open("POST", url, true);
            var data = JSON.stringify(params);
            xhttp.send(data);
        break;    
        case "GET":
        case "DELETE":
            var parsedPars = Object.keys(params).map(function (k) {
                return encodeURIComponent(k) + "=" + encodeURIComponent(params[k]);
              })
              .join("&");
            xhttp.open(type.toUpperCase(), url + "?" + parsedPars, true);
            xhttp.send();
        break;
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
/****************
 *   DESDE AQUI
 * 
 */
 function moveCursorToEnd(el) {
    var data = el.getAttribute('datanumber');
    if(!isNaN(data)){
        el.value = ''; 
        el.value = data;
    }
}
/*AJUSTE PARA CAER SIEMPRE CON CURSOR A LA DERECHA AGREGADO-PROBAR*/
function formatPercent(ele,decimCoin){
  ele.addEventListener("keyup",function(e){
      var newval = formatCurrency(this.getAttribute("decim"),this.value);
      if(newval!="")this.value = newval;

      this.setAttribute("dataNumber",parseFloat(newval).noExponents());
  });
  ele.addEventListener("focusin",function(){
      var val = this.getAttribute("dataNumber");
      this.value = parseFloat(val).noExponents();
      this.setAttribute("type","number");
      eletmp=this;
      setTimeout(function(){
          eletmp.dispatchEvent(new Event("click"));
      },100);
  });
  ele.setAttribute("decim",decimCoin);
  ele.addEventListener("focusout",function(){
      this.setAttribute("dataNumber",parseFloat(this.value).noExponents());
      this.value = "";
      this.setAttribute("type","text");
      this.value = number_format(this.getAttribute("dataNumber"),this.getAttribute("decim"))+"%";
  });
  ele.addEventListener("click",function(){
      moveCursorToEnd(this); 
  });
}   
/*AJUSTE PARA CAER SIEMPRE CON CURSOR A LA DERECHA AGREGADO-PROBAR*/
function formatAmount(ele,decimCoin){
    ele.addEventListener("keyup",function(e){
        var newval = formatCurrency(this.getAttribute("decim"),this.value);
        if(newval!="")this.value = newval;

        this.setAttribute("dataNumber",parseFloat(newval).noExponents());
    });
    ele.addEventListener("focusin",function(){
        var val = this.getAttribute("dataNumber");
        this.value = parseFloat(val).noExponents();
        this.setAttribute("type","number");
        eletmp=this;
        setTimeout(function(){
            eletmp.dispatchEvent(new Event("click"));
        },100);
    });
    ele.setAttribute("decim",decimCoin);
    ele.addEventListener("focusout",function(){
        this.setAttribute("dataNumber",parseFloat(this.value).noExponents());
        this.value = "";
        this.setAttribute("type","text");
        this.value = number_format(this.getAttribute("dataNumber"),this.getAttribute("decim"));
    });
    ele.addEventListener("click",function(){
        moveCursorToEnd(this); 
    });
}     
function formatCurrency (fractionDigits, number) {
    var ln = window.navigator.language;
    var decim = "";
    maxDig = false;
    var num = "";
    //
    if(number.split(".").length>1){
        num = number.split(".")[0];
        decim = number.split(".")[1];
        if(decim.length>=fractionDigits){
            decim = decim.substr(0,fractionDigits);
            maxDig = true;
        }
    }else if(number.split(".").length>0){
        num = number.split(".")[0];
    }else if(number.split(",").length>1){
        num = number.split(",")[0];
        decim = number.split(",")[1];
        if(decim.length>=fractionDigits){
            decim = decim.substr(0,fractionDigits);
            maxDig = true;
        }
    }else if(number.split(",").length>1){
        num = number.split(",")[0];
    }
    var formatted="";
    if(decim!=="")formatted=num+"."+decim;
    else formatted = num;
    if(maxDig == true && (number.split(".").length==2 || number.split(",").length==2))return formatted;
    else return "";
}
/* Funcion que se aplica a tipos numericos para eliminar los exponentes */
Number.prototype.noExponents= function(){
    var data= String(this).split(/[eE]/);
    if(data.length== 1) return data[0]; 

    var  z= '', sign= this<0? '-':'',
    str= data[0].replace('.', ''),
    mag= Number(data[1])+ 1;

    if(mag<0){
        z= sign + '0.';
        while(mag++) z += '0';
        return z + str.replace(/^\-/,'');
    }
    mag -= str.length;  
    while(mag--) z += '0';
    return str + z;
}              
//Formateo de amount real;
function number_format(amount, decimals) {
    amount += ''; // por si pasan un numero en vez de un string
    amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto

    decimals = decimals || 0; // por si la variable no fue fue pasada

    // si no es un numero o es igual a cero retorno el mismo cero
    if (isNaN(amount) || amount === 0) 
        return parseFloat(0).toFixed(decimals);

    // si es mayor o menor que cero retorno el valor formateado como numero
    amount = '' + amount.toFixed(decimals);

    var amount_parts = amount.split('.'),
        regexp = /(\d+)(\d{3})/;

    while (regexp.test(amount_parts[0]))
        amount_parts[0] = amount_parts[0].replace(regexp, '$1' + '.' + '$2');

    return amount_parts.join(',');
}
function isEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
function isRIF(rif){
    var re = /^[JG][0-9]{9}$/;
    return re.test(rif);
}
function isCI(rif){
    var re = /^[VEP][0-9]{4,9}$/;
    return re.test(rif);
}
function isPhone(phone){
    var re = /^[0-9]{4} [0-9]{7}$/;
    return re.test(phone);
}
function MaysPrimera(string){
    return string.capitalize(true);
}

String.prototype.capitalize = function(lower) {
    var out = (lower ? this.toLowerCase() : this).replace(/(?:^|\s)\S/g, function(a) { return a.toUpperCase(); });
    // colocar excepciones
    out = out.replace(/C\.a\./g, "C.A.");
    out = out.replace(/S\.a\./g, "S.A.");
    out = out.replace(/ De /g, " de ");
    out = out.replace(/ La /g, " la ");
    out = out.replace(/ Es /g, " es ");
    out = out.replace(/ El /g, " el ");
    out = out.replace(/ En /g, " en ");
    out = out.replace(/ Las /g, " las ");
    out = out.replace(/ Los /g, " los ");
    out = out.replace(/ Una /g, " una ");
    out = out.replace(/ Por /g, " por ");
    out = out.replace(/ Con /g, " con ");
    out = out.replace(/ Sin /g, " sin ");
    out = out.replace(/ Un /g, " un ");
    out = out.replace(/ Y /g, " y ");
    out = out.replace(/Ii/g, "II");
    out = out.replace(/IIi/g, "III");
    out = out.replace(/Iv/g, "IV");
    out = out.replace(/Vi/g, "VI");
    out = out.replace(/Vii/g, "VII");
    out = out.replace(/Viii/g, "VIII");
  
    return (out);
};
function formatRIF(valor){
    let padToFour = number => number <= 999999999 ? `00000000${number}`.slice(-9) : number.slice(0,9);
    var cleaned = (valor.replace(/[.,\-, ,\/]/g,"").toUpperCase());
    if (cleaned.length){
        var area, numero;
            area = cleaned.substr(0,1);
            numero = cleaned.substr(1);

        if(numero!=""&&parseFloat(numero)>0)
          return(area+""+padToFour(numero));
        else
          return(area);
  }else{
      return("");
  }
}
function formatEmail(valor){
    return(valor.toLowerCase());
}
  
function formatPhone(valor){  
    var cleaned = ('' + valor).replace(/\D/g, '');
    if (cleaned.length){
        var prefijo, area, numero;
        area = cleaned.substr(0,4);
        numero = cleaned.substr(4,7);
        if(numero!="")
          return(area+" "+numero);
        else
          return(area);
    }else{
        return("");
    }
}
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
function setTextFormat(o){
    o.addEventListener("keyup", function(e){
          var cursorStart = e.target.selectionStart,
              cursorEnd = e.target.selectionEnd;
          this.value = MaysPrimera(this.value);
          e.target.setSelectionRange(cursorStart, cursorEnd);
    });
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
function download(filename, textInput) {
    var element = document.createElement('a');
    element.setAttribute('href', textInput);
    element.setAttribute('download', filename);
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
}

// Ej formatFields({"campo1", "campo2"}, {"rif","phone"})
function formatFields2(campos, formato,optional="",cnt=document){
  var ele;
  for (var i=0; i<campos.length; i++){
      ele = document.getElementById(campos[i]);
      if(ele == null || ele == undefined)ele = cnt.getElementsByClassName(campos[i])[0];
      if (ele !== null){
        switch (formato[i]){
            case "text":
                setTextFormat(ele);
            break;
            case "rif":
                // definir formato de entrada
                ele.addEventListener("keyup", function(e){                  
                    this.value = formatRIF(this.value); 
                });
                // definir validacion
                ele.addEventListener("change", function(){
                    if(isRIF(this.value) || !this.value.length){
                        removeErr(this);
                    }else{
                        inptError(this,"Formato de RIF/Cédula Inválido");
                    }
                });
            break;
            case "email":
                ele.addEventListener("keyup", function(e){
                    var cursorStart = e.target.selectionStart,
                        cursorEnd = e.target.selectionEnd;
                    this.value = formatEmail(this.value); 
                    e.target.setSelectionRange(cursorStart, cursorEnd);
                });
                ele.addEventListener("change", function(){
                    if(isEmail(this.value) || !this.value.length){
                        removeErr(this);
                    }else{
                        inptError(this,"Formato de Correo Inválido");
                    }
                });
            break;
            case "phone":
                ele.addEventListener("keyup", function(e){
                    var codigo = e.which || e.keyCode;
                    if(codigo !== 8){
                        this.value = formatPhone(this.value); 
                    }
                });
                ele.addEventListener("change", function(){
                    if(isPhone(this.value) || !this.value.length){
                        removeErr(this);
                    }else{
                        inptError(this,"Formato de Teléfono Inválido");
                    }
                });
            break;
            case "amount":
                formatAmount(ele,optional);
            break;
            case "percent":
                formatPercent(ele,optional);
            break;
          }
      }else{
          console.log("Validando el campo '"+campos[i]+"' como '" + formato + "' que no existe");
      }
  }
}

/*****
 * El parametro 1 debe ser el arreglo con los registros (opciones)
 * El parametro 2 debe ser el elemento (select) donde se insertaran las opciones * 
 * El parametro 3 corresponde al valor a ser leído cómo value * 
 * El parametro 4 corresponde al valor a ser leído cómo dsc *
 * El parametro 5 es opcional crea una primera opcion con value=0 se puede pasar en blanco, null o undefined si no se desea
 * El parametro 6 es una opci贸n selecionada
 * ****/
function drawSelect(rsp, select, id, dsc, first="", selected="") {
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
        opt.innerHTML = rsp[i][dsc];
        select.appendChild(opt);
    }
}
function MD5(string) {
    function RotateLeft(lValue, iShiftBits) {
      return (lValue << iShiftBits) | (lValue >>> (32 - iShiftBits));
    }

    function AddUnsigned(lX, lY) {
      var lX4, lY4, lX8, lY8, lResult;
      lX8 = lX & 0x80000000;
      lY8 = lY & 0x80000000;
      lX4 = lX & 0x40000000;
      lY4 = lY & 0x40000000;
      lResult = (lX & 0x3fffffff) + (lY & 0x3fffffff);
      if (lX4 & lY4) {
        return lResult ^ 0x80000000 ^ lX8 ^ lY8;
      }
      if (lX4 | lY4) {
        if (lResult & 0x40000000) {
          return lResult ^ 0xc0000000 ^ lX8 ^ lY8;
        } else {
          return lResult ^ 0x40000000 ^ lX8 ^ lY8;
        }
      } else {
        return lResult ^ lX8 ^ lY8;
      }
    }

    function F(x, y, z) {
      return (x & y) | (~x & z);
    }
    function G(x, y, z) {
      return (x & z) | (y & ~z);
    }
    function H(x, y, z) {
      return x ^ y ^ z;
    }
    function I(x, y, z) {
      return y ^ (x | ~z);
    }

    function FF(a, b, c, d, x, s, ac) {
      a = AddUnsigned(a, AddUnsigned(AddUnsigned(F(b, c, d), x), ac));
      return AddUnsigned(RotateLeft(a, s), b);
    }

    function GG(a, b, c, d, x, s, ac) {
      a = AddUnsigned(a, AddUnsigned(AddUnsigned(G(b, c, d), x), ac));
      return AddUnsigned(RotateLeft(a, s), b);
    }

    function HH(a, b, c, d, x, s, ac) {
      a = AddUnsigned(a, AddUnsigned(AddUnsigned(H(b, c, d), x), ac));
      return AddUnsigned(RotateLeft(a, s), b);
    }

    function II(a, b, c, d, x, s, ac) {
      a = AddUnsigned(a, AddUnsigned(AddUnsigned(I(b, c, d), x), ac));
      return AddUnsigned(RotateLeft(a, s), b);
    }

    function ConvertToWordArray(string) {
      var lWordCount;
      var lMessageLength = string.length;
      var lNumberOfWords_temp1 = lMessageLength + 8;
      var lNumberOfWords_temp2 =
        (lNumberOfWords_temp1 - (lNumberOfWords_temp1 % 64)) / 64;
      var lNumberOfWords = (lNumberOfWords_temp2 + 1) * 16;
      var lWordArray = Array(lNumberOfWords - 1);
      var lBytePosition = 0;
      var lByteCount = 0;
      while (lByteCount < lMessageLength) {
        lWordCount = (lByteCount - (lByteCount % 4)) / 4;
        lBytePosition = (lByteCount % 4) * 8;
        lWordArray[lWordCount] =
          lWordArray[lWordCount] |
          (string.charCodeAt(lByteCount) << lBytePosition);
        lByteCount++;
      }
      lWordCount = (lByteCount - (lByteCount % 4)) / 4;
      lBytePosition = (lByteCount % 4) * 8;
      lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80 << lBytePosition);
      lWordArray[lNumberOfWords - 2] = lMessageLength << 3;
      lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29;
      return lWordArray;
    }

    function WordToHex(lValue) {
      var WordToHexValue = "",
        WordToHexValue_temp = "",
        lByte,
        lCount;
      for (lCount = 0; lCount <= 3; lCount++) {
        lByte = (lValue >>> (lCount * 8)) & 255;
        WordToHexValue_temp = "0" + lByte.toString(16);
        WordToHexValue =
          WordToHexValue +
          WordToHexValue_temp.substr(WordToHexValue_temp.length - 2, 2);
      }
      return WordToHexValue;
    }

    function Utf8Encode(string) {
      string = string.replace(/\r\n/g, "\n");
      var utftext = "";

      for (var n = 0; n < string.length; n++) {
        var c = string.charCodeAt(n);

        if (c < 128) {
          utftext += String.fromCharCode(c);
        } else if (c > 127 && c < 2048) {
          utftext += String.fromCharCode((c >> 6) | 192);
          utftext += String.fromCharCode((c & 63) | 128);
        } else {
          utftext += String.fromCharCode((c >> 12) | 224);
          utftext += String.fromCharCode(((c >> 6) & 63) | 128);
          utftext += String.fromCharCode((c & 63) | 128);
        }
      }

      return utftext;
    }

    var x = Array();
    var k, AA, BB, CC, DD, a, b, c, d;
    var S11 = 7,
      S12 = 12,
      S13 = 17,
      S14 = 22;
    var S21 = 5,
      S22 = 9,
      S23 = 14,
      S24 = 20;
    var S31 = 4,
      S32 = 11,
      S33 = 16,
      S34 = 23;
    var S41 = 6,
      S42 = 10,
      S43 = 15,
      S44 = 21;

    string = Utf8Encode(string);

    x = ConvertToWordArray(string);

    a = 0x67452301;
    b = 0xefcdab89;
    c = 0x98badcfe;
    d = 0x10325476;

    for (k = 0; k < x.length; k += 16) {
      AA = a;
      BB = b;
      CC = c;
      DD = d;
      a = FF(a, b, c, d, x[k + 0], S11, 0xd76aa478);
      d = FF(d, a, b, c, x[k + 1], S12, 0xe8c7b756);
      c = FF(c, d, a, b, x[k + 2], S13, 0x242070db);
      b = FF(b, c, d, a, x[k + 3], S14, 0xc1bdceee);
      a = FF(a, b, c, d, x[k + 4], S11, 0xf57c0faf);
      d = FF(d, a, b, c, x[k + 5], S12, 0x4787c62a);
      c = FF(c, d, a, b, x[k + 6], S13, 0xa8304613);
      b = FF(b, c, d, a, x[k + 7], S14, 0xfd469501);
      a = FF(a, b, c, d, x[k + 8], S11, 0x698098d8);
      d = FF(d, a, b, c, x[k + 9], S12, 0x8b44f7af);
      c = FF(c, d, a, b, x[k + 10], S13, 0xffff5bb1);
      b = FF(b, c, d, a, x[k + 11], S14, 0x895cd7be);
      a = FF(a, b, c, d, x[k + 12], S11, 0x6b901122);
      d = FF(d, a, b, c, x[k + 13], S12, 0xfd987193);
      c = FF(c, d, a, b, x[k + 14], S13, 0xa679438e);
      b = FF(b, c, d, a, x[k + 15], S14, 0x49b40821);
      a = GG(a, b, c, d, x[k + 1], S21, 0xf61e2562);
      d = GG(d, a, b, c, x[k + 6], S22, 0xc040b340);
      c = GG(c, d, a, b, x[k + 11], S23, 0x265e5a51);
      b = GG(b, c, d, a, x[k + 0], S24, 0xe9b6c7aa);
      a = GG(a, b, c, d, x[k + 5], S21, 0xd62f105d);
      d = GG(d, a, b, c, x[k + 10], S22, 0x2441453);
      c = GG(c, d, a, b, x[k + 15], S23, 0xd8a1e681);
      b = GG(b, c, d, a, x[k + 4], S24, 0xe7d3fbc8);
      a = GG(a, b, c, d, x[k + 9], S21, 0x21e1cde6);
      d = GG(d, a, b, c, x[k + 14], S22, 0xc33707d6);
      c = GG(c, d, a, b, x[k + 3], S23, 0xf4d50d87);
      b = GG(b, c, d, a, x[k + 8], S24, 0x455a14ed);
      a = GG(a, b, c, d, x[k + 13], S21, 0xa9e3e905);
      d = GG(d, a, b, c, x[k + 2], S22, 0xfcefa3f8);
      c = GG(c, d, a, b, x[k + 7], S23, 0x676f02d9);
      b = GG(b, c, d, a, x[k + 12], S24, 0x8d2a4c8a);
      a = HH(a, b, c, d, x[k + 5], S31, 0xfffa3942);
      d = HH(d, a, b, c, x[k + 8], S32, 0x8771f681);
      c = HH(c, d, a, b, x[k + 11], S33, 0x6d9d6122);
      b = HH(b, c, d, a, x[k + 14], S34, 0xfde5380c);
      a = HH(a, b, c, d, x[k + 1], S31, 0xa4beea44);
      d = HH(d, a, b, c, x[k + 4], S32, 0x4bdecfa9);
      c = HH(c, d, a, b, x[k + 7], S33, 0xf6bb4b60);
      b = HH(b, c, d, a, x[k + 10], S34, 0xbebfbc70);
      a = HH(a, b, c, d, x[k + 13], S31, 0x289b7ec6);
      d = HH(d, a, b, c, x[k + 0], S32, 0xeaa127fa);
      c = HH(c, d, a, b, x[k + 3], S33, 0xd4ef3085);
      b = HH(b, c, d, a, x[k + 6], S34, 0x4881d05);
      a = HH(a, b, c, d, x[k + 9], S31, 0xd9d4d039);
      d = HH(d, a, b, c, x[k + 12], S32, 0xe6db99e5);
      c = HH(c, d, a, b, x[k + 15], S33, 0x1fa27cf8);
      b = HH(b, c, d, a, x[k + 2], S34, 0xc4ac5665);
      a = II(a, b, c, d, x[k + 0], S41, 0xf4292244);
      d = II(d, a, b, c, x[k + 7], S42, 0x432aff97);
      c = II(c, d, a, b, x[k + 14], S43, 0xab9423a7);
      b = II(b, c, d, a, x[k + 5], S44, 0xfc93a039);
      a = II(a, b, c, d, x[k + 12], S41, 0x655b59c3);
      d = II(d, a, b, c, x[k + 3], S42, 0x8f0ccc92);
      c = II(c, d, a, b, x[k + 10], S43, 0xffeff47d);
      b = II(b, c, d, a, x[k + 1], S44, 0x85845dd1);
      a = II(a, b, c, d, x[k + 8], S41, 0x6fa87e4f);
      d = II(d, a, b, c, x[k + 15], S42, 0xfe2ce6e0);
      c = II(c, d, a, b, x[k + 6], S43, 0xa3014314);
      b = II(b, c, d, a, x[k + 13], S44, 0x4e0811a1);
      a = II(a, b, c, d, x[k + 4], S41, 0xf7537e82);
      d = II(d, a, b, c, x[k + 11], S42, 0xbd3af235);
      c = II(c, d, a, b, x[k + 2], S43, 0x2ad7d2bb);
      b = II(b, c, d, a, x[k + 9], S44, 0xeb86d391);
      a = AddUnsigned(a, AA);
      b = AddUnsigned(b, BB);
      c = AddUnsigned(c, CC);
      d = AddUnsigned(d, DD);
    }

    var temp = WordToHex(a) + WordToHex(b) + WordToHex(c) + WordToHex(d);

    return temp.toLowerCase();
}

window.onload = function(){
    init();
};


/****************
 *   DESDE AQUI
 * 
 */
function isEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
function isRIF(rif){
    var re = /^[JGVEPM][0-9]{4,9}$/;
    //var re = /^[0-9]{4,9}$/;
    return re.test(rif);
}
function isPhone(phone){
    var re = /^[+][0-9]{1,2} [0-9]{3} [0-9]{7}$/;
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
    return(valor.replace(/[.,\-, ,\/]/g,"").toUpperCase());
}
function formatEmail(valor){
    return(valor.toLowerCase());
}
function puntito(donde,caracter,campo)
{
    var decimales = false
    dec = 2;
    if (dec != 0){decimales = true}
    
    pat = /[\*,\+,\(,\),\?,\\,\$,\[,\],\^]/
    valor = donde.value
    largo = valor.length
    crtr = true
    
    if(isNaN(caracter) || pat.test(caracter) == true){
    	if (pat.test(caracter)==true){caracter = "\\" + caracter}
    	carcter = new RegExp(caracter,"g")
    	valor = valor.replace(carcter,"")
    	donde.value = valor
    	crtr = false
    }else{
    	var nums = new Array()
    	cont = 0
    	for(m=0;m<largo;m++){
    		if(valor.charAt(m) == "." || valor.charAt(m) == " " || valor.charAt(m) == ","){
    		    continue;
	 		}else{
    			nums[cont] = valor.charAt(m)
    			cont++
    		}
    	}
    }
    if(decimales == true) {
    	ctdd = eval(1 + dec);
    	nmrs = 1
    }else {
        ctdd = 1; nmrs = 3
    }
    var cad1="",cad2="",cad3="",tres=0;
    if(nums.length > nmrs && crtr == true){
    	for (k=nums.length-ctdd;k>=0;k--){
    		cad1 = nums[k]
    		cad2 = cad1 + cad2
    		tres++
    		if((tres%3) == 0){
    			if(k!=0){
    				cad2 = "." + cad2
    			}
    		}
    	}
    	for (dd = dec; dd > 0; dd--){
    	    cad3 += nums[nums.length-dd] 
    	}
        if(decimales == true){cad2 += "," + cad3}
    }else{
        if(nums.length>0)cad2 = nums[0];
    }
    return(cad2);
    
    donde.focus();
}
function formatAmount(valor, moneda){
    //valor = (parseInt(parseFloat(valor.replace(/\D/g,''))*100)/100).toString();
    //if (isNaN(v)) v=0.00;
    //var valor = (parseInt(v*100)).toString();
    //var n = parseInt((parseFloat(valor.replace(/\D/g,'.'))).toString().replace(/\D/g,''))/100;
    
    // corregir cuando vienen m��s de dos decimales
    //if (valor.search(/\D/) > -1)
    //    valor = valor.substr(0,valor.search(/\D/)+4);
    
    var num = parseFloat(valor.replace(/\D/g,''))/100;
    if(!isNaN(num)){
        num = num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
        num = num.split('').reverse().join('').replace(/^[\.]/,'');
        n = num;
    }
    if (isNaN(n)) n=0.00;
    /*
    var monedadsc = "";
    if (moneda=="VBS")
        monedadsc = "Bs ";
    if (moneda=="USD")
        monedadsc = "$ ";
    */
    //var out = n.toLocaleString('es');
    return(n.toLocaleString('es', {"minimumFractionDigits":2}));
}

function formatPhone(valor){
    
    var cleaned = ('' + valor).replace(/\D/g, '');
    if (cleaned.length){
        var prefijo, area, numero;
        if (cleaned[1]=="1"){
            prefijo = "1";
            area = cleaned.substr(1,3);
            numero = cleaned.substr(4);
        }else{
            prefijo = cleaned.substr(0,2);
            area = cleaned.substr(2,3);
            numero = cleaned.substr(5);
        }
        return("+"+prefijo+" "+area+" "+numero);
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

// Ej formatFields({"campo1", "campo2"}, {"rif","phone"})
//Sidney no pongas esta funcion en comentarios por favor ya que la uso en contratantes y servicios
function formatFields2(campos, formato){
    var e;
    for (var i=0; i<campos.length; i++){
        e = document.getElementById(campos[i]);
        if (e !== null){
            switch (formato[i]){
                case "text":
                    setTextFormat(document.getElementById(campos[i]));
                    break;
                case "rif":
                    // definir formato de entrada
                    document.getElementById(campos[i]).addEventListener("keyup", function(e){
                        var cursorStart = e.target.selectionStart,
                            cursorEnd = e.target.selectionEnd;
                        this.value = formatRIF(this.value); 
                        e.target.setSelectionRange(cursorStart, cursorEnd);
                    });
                    // definir validacion
                    document.getElementById(campos[i]).addEventListener("change", function(){
                        if(isRIF(this.value) || !this.value.length){
                            showError(this, "",this.id);
                        }else{
                            //showError(this, "Formato de RIF/Cedula Invalido");
                            showError(this, "Formato de RIF/Cédula Inválido",this.id);
                            //alert("Formato de RIF/Cédula Inválido");
                        }
                    });
                    break;
                case "email":
                    document.getElementById(campos[i]).addEventListener("keyup", function(e){
                        var cursorStart = e.target.selectionStart,
                            cursorEnd = e.target.selectionEnd;
                        this.value = formatEmail(this.value); 
                        e.target.setSelectionRange(cursorStart, cursorEnd);
                    });
                    document.getElementById(campos[i]).addEventListener("change", function(){
                        if(isEmail(this.value) || !this.value.length){
                            showError(this, "",this.id);
                        }else{
                            showError(this, "Formato de Correo Inválido",this.id);
                            //alert("Formato de Correo Inválido");
                        }
                    });
                    break;
                case "phone":
                   // console.log("hello");
                    document.getElementById(campos[i]).addEventListener("keyup", function(e){
                        //var cursorStart = e.target.selectionStart,
                        //    cursorEnd = e.target.selectionEnd;
                        var codigo = e.which || e.keyCode;
                        if(codigo !== 8){
                            this.value = formatPhone(this.value); 
                        }
                        //e.target.setSelectionRange(cursorEnd, cursorEnd);
                    });
                    document.getElementById(campos[i]).addEventListener("change", function(){
                        if(isPhone(this.value) || !this.value.length){
                            showError(this, "",this.id);
                        }else{
                            showError(this, "Teléfono Inválido", this.id);
                           // alert("Teléfono Inválido");
                        }
                    });
                    break;
                case "amount":
                    document.getElementById(campos[i]).addEventListener("keyup", function(e){
                        //var cursorStart = e.target.selectionStart,
                        //    cursorEnd = e.target.selectionEnd;
                        var currency = this.getAttribute("data-currency");
                        //if(currency == "" || currency == undefined)currency = "VBS";
                        this.value = puntito(this, this.value.charAt(this.value.length-1));
                        var cnt;
                        if (this.getAttribute("data-cnt")===null)
                            cnt = 1;
                        else
                            cnt = parseInt(this.getAttribute("data-cnt"));
                        
                        if (cnt===0)    {
                            this.value = puntito(this,this.value.charAt(this.value.length-1));
                        }else{
                            this.setAttribute("data-amount", (parseFloat(this.value.replace(/\D/g,''))/100)/cnt);
                        }
                    });
            }
        }else{
            console.log("Validando el campo '"+campos[i]+"' como '" + formato + "' que no existe");
        }
    }
}
function showError(campo, msj,i){
    imagen = document.getElementById("myError-"+i);	
	if (imagen){
		padre = imagen.parentNode;
		padre.removeChild(imagen);
	}
    var newDiv = document.createElement("p"); 
        newDiv.appendChild(document.createTextNode(msj)); 
        newDiv.classList.add("myError");
        newDiv.id="myError-"+i;
        // añade el elemento creado y su contenido al DOM 
        campo.parentNode.insertBefore(newDiv, campo.nextSibling);   
}

function formatFields(campos, formato){
    campos.addEventListener("keyup", function(e){
        this.value = formatPhone(this.value); 
    });
    campos.addEventListener("change", function(){
        if(isPhone(this.value) || !this.value.length){
            }else{
            alert("Teléfono Inválido");
        }

    });
}
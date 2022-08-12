var inf = [
  {
      "label": {
          "short": "Jul 15",
          "long": "15 de Julio de 2022"
      },
      "values": {
          "nuevos": 10,
          "existente": 65,
          "baja": -20
      }
  },
  {
    "label": {
        "short": "Jul 16",
        "long": "16 de Julio de 2022"
    },
    "values": {
        "nuevos": 10,
        "existente": 35,
        "baja": -35
    }
  },
  {
    "label": {
        "short": "Jul 17",
        "long": "17 de Julio de 2022"
    },
    "values": {
        "nuevos": 28,
        "existente": 55,
        "baja": -13
    }
  },
  {
    "label": {
        "short": "Jul 18",
        "long": "18 de Julio de 2022"
    },
    "values": {
        "nuevos": 15,
        "existente": 45,
        "baja": -21
    }
  },
  {
    "label": {
        "short": "Jul 19",
        "long": "19 de Julio de 2022"
    },
    "values": {
        "nuevos": 1,
        "existente": 45,
        "baja": -5
    }
  },
  {
    "label": {
        "short": "Jul 20",
        "long": "20 de Julio de 2022"
    },
    "values": {
        "nuevos": 25,
        "existente": 55,
        "baja": -10
    }
  },
  {
    "label": {
        "short": "Jul 21",
        "long": "21 de Julio de 2022"
    },
    "values": {
        "nuevos": 10,
        "existente": 65,
        "baja": -20
    }
  },
  {
  "label": {
      "short": "Jul 22",
      "long": "22 de Julio de 2022"
  },
  "values": {
      "nuevos": 12,
      "existente": 50,
      "baja": -8
  }
  },
  {
  "label": {
      "short": "Jul 23",
      "long": "23 de Julio de 2022"
  },
  "values": {
      "nuevos": 23,
      "existente": 25,
      "baja": -1
  }
  },
];
function init(){
  var sessid = getParameterByName('sessid');
 // document.getElementById("goToPolicy").addEventListener("click", function(){
     // gotoPage("menu","policy",  {"sessid":sessid});
 // });
  chartBarsV(inf)
}
var colors = ['#F5A623','#D0021B','#1A3867','#A5E65D','#CC89E8','#D3D3D3'];
var actColor = [
  {id:2,color:'#7CC36A'},{id:3,color:'#0033A0'},{id:1,color:'#D3D3D3'}
];
function chartBarsV(inf){
  var clone = document.getElementById("qtyMailsChart");
  //Grafico barras
  var chartList = clone.getElementsByClassName("grafico-barrasV")[0].children[0];
  clone.getElementsByClassName("grafico-barrasV")[0].children[0].innerHTML = "";
  //Label días foot
  var chartLbls = clone.getElementsByClassName("grafico-barrasV")[0].children[1];
  clone.getElementsByClassName("grafico-barrasV")[0].children[1].innerHTML = "";
  
  var myArray = [];
  for(var i=0;i<inf.length;i++){
    var sum = 0;
    for(var x=0;x<3;x++){
      var lbl = "";
      switch(x+1){
        case 2:
          lbl = 'existente';          
          sum += Math.abs(inf[i].values[lbl]);
        break;
        case 3:
          lbl = 'nuevos';          
          sum += Math.abs(inf[i].values[lbl]);
        break;
        case 1:
          lbl = 'baja';
        break;
      }
      myArray.push(inf[i].values[lbl]);
    }    
    //Obtener que valor porcentual es.
    myArray.push(sum);
  }
  var maxval = Math.max.apply(null, myArray);
  var minval = Math.min.apply(null, myArray);
  var dif = maxval - minval;  
  var minprc = (Math.abs(minval)*100)/dif;
  var points = 6;
  //Label eje Y izquierda
  var barCell = document.createElement("div");
  barCell.classList.add("barCell");
      var bar = document.createElement("span");
      bar.classList.add("lblYCnt");
      var prevsize = 0;
      bar.innerHTML = "";
      for(var x=0;x<(points);x++){
          var barSectLbl = document.createElement("li");
          barSectLbl.classList.add("lblSect");
          barSectLbl.innerHTML = "";
              //Obtener que posición porcentual debe ocupar en la barra (label)
              var pct = ((100/(points-1))*x);
              if(pct==0){
                barSectLbl.style.bottom = "-7.5px";                
                barSectLbl.innerHTML = Math.round(minval);
              }else{
                barSectLbl.style.bottom = "calc("+((100/(points-1))*x)+"% - 7.5px)";                
                var lessPorcion = dif/(points-1);
                barSectLbl.innerHTML = Math.round(maxval-(lessPorcion*((points-1)-x)));
              }

          bar.appendChild(barSectLbl);
      }
  barCell.appendChild(bar);
  //Oculto para mantener tamaño
  var lblDay = document.createElement("span");
      lblDay.classList.add("lblDayCell")
      lblDay.innerHTML = "-"
      lblDay.style.visibility="hidden";
  barCell.appendChild(lblDay);
  chartList.appendChild(barCell);
  for(var i=0;i<inf.length;i++){
      //Bloque de la leyenda
      var leyCnt = document.createElement("div");
      leyCnt.classList.add("boxLeyCnt");
      var leyTit = document.createElement("div");
      leyTit.classList.add("leyTit");
      leyTit.innerHTML = inf[i].label.long;
      leyCnt.appendChild(leyTit);
      var leyInf = document.createElement("div");
      leyInf.innerHTML = "";
      //Pintar barras
      var barCell = document.createElement("div");
      barCell.classList.add("barCell");
      var bar = document.createElement("span");
      bar.classList.add("barra-fondoV");
      var prevsize = 0;
      for(var x=0;x<3;x++){
        var bot = false;
        var lbl = "";
        var sublbl = "";
        switch(x+1){
          case 2:
            lbl = 'existente';
            sublbl = 'existentes';
          break;
          case 3:
            lbl = 'nuevos';
            sublbl = 'nuevos';
          break;
          case 1:
            lbl = 'baja';
            sublbl = 'bajas';
            //Se pinta un bloque por la diferencia respecto al bajo más bajo
            var barVal = document.createElement("li");
            barVal.classList.add("barrasV");
            barVal.innerHTML = "";
            
            //Obtener que valor porcentual del contenido real
            var valprc = (Math.abs(inf[i].values[lbl])*100)/dif;
            var altprc = minprc-valprc; //Saco la diferencia del espacio restante respecto al mas bajo
            if(prevsize==0)barVal.style.bottom = "0"; //Ese espacio siempre se ocupa por el bloque oculto pos 0
            
            prevsize += altprc;//Se guarda el size para tener el proximo origen
            barVal.style.height =  "calc("+altprc+"%)";
            barVal.style.backgroundColor = "transparent";                
            bar.appendChild(barVal);

            
            bot = true;
          break;
        }
        var barVal = document.createElement("li");
        barVal.classList.add("barrasV","barVal");
        barVal.innerHTML = "";
        
         //Obtener que valor porcentual del contenido real
        var valprc = (Math.abs(inf[i].values[lbl])*100)/dif;
        if(prevsize==0)barVal.style.bottom = "0"; //Si es 0 se ubica en la base
        else{
          
          barVal.style.bottom = "calc("+prevsize+"%)"; //Se ubica desde el bloque anterior
        }
        prevsize += valprc; //Se suma este bloque al proximo origen
        if(bot){
          barVal.style.height =  "calc("+valprc+"% - 1px)";
          barVal.style.borderTop = "1px solid #E9E9E9";
        }
        else barVal.style.height =  "calc("+valprc+"%)";

        //Eventos para visualizar la leyenda
        barVal.addEventListener("mousemove", function(evt) {
          var output = this.parentElement.getElementsByClassName("boxLeyCnt")[0];
          var padre = this.parentElement;
          var vals = padre.getElementsByClassName("barVal");
          for(var i=0;i<vals.length;i++){
            vals[i].style.boxShadow = '0px 0px 10px 2px rgb(0 0 0 / 13%)';
          }
          var mousePos = oMousePos(this.parentElement, evt);
          marcarCoords(this, output, mousePos.x, mousePos.y)
        }, false);
        //Eventos para ocultar la leyenda
        barVal.addEventListener("mouseout", function(evt) {
          var output = this.parentElement.getElementsByClassName("boxLeyCnt")[0];
          var padre = this.parentElement;
          var vals = padre.getElementsByClassName("barVal");
          for(var i=0;i<vals.length;i++){
            vals[i].style.boxShadow = '';
          }
          limpiarCoords(this,output);
        }, false);
        
        var colorBg = "";
        //Se identifica el "color" del tipo actual que se está consultando
        for(var j=0;j<actColor.length;j++){
            if(actColor[j].id==(x+1)){
                colorBg = actColor[j].color;
            }
        }
        barVal.style.backgroundColor = colorBg;
        
        bar.appendChild(barVal);

        //Se agregar una linea a la leyenda con los valores correspondientes
        var rowLeyTbl = document.createElement("div");
        rowLeyTbl.classList.add("rowLeyTbl");
        var rowColor = document.createElement("div");
        var rowBall = document.createElement("div");
        rowBall.classList.add("rowBall");
        rowBall.style.backgroundColor = colorBg;
        rowColor.appendChild(rowBall);
        rowColor.classList.add("rowColor");
        rowLeyTbl.appendChild(rowColor);
        var rowNum = document.createElement("div");
        rowNum.classList.add("rowNum");
        rowNum.innerHTML = inf[i].values[lbl];
        rowLeyTbl.appendChild(rowNum);
        var rowDsc = document.createElement("div");
        rowDsc.classList.add("rowDsc");
        rowDsc.innerHTML = capitalizarPrimeraLetra(sublbl);
        rowLeyTbl.appendChild(rowDsc);
        leyInf.appendChild(rowLeyTbl);
      }
      //Reestablezco el orden le los elementos que se muestran en el label
      var newOrden = leyInf.cloneNode(true);
      newOrden.innerHTML = "";
      newOrden.appendChild(leyInf.children[2].cloneNode(true));
      newOrden.appendChild(leyInf.children[1].cloneNode(true));
      newOrden.appendChild(leyInf.children[0].cloneNode(true));
      leyCnt.appendChild(newOrden);
      bar.appendChild(leyCnt);
      barCell.appendChild(bar);
      
      var lblDay = document.createElement("span");
          lblDay.classList.add("lblDayCell")
          lblDay.innerHTML = inf[i].label.short;
      barCell.appendChild(lblDay);
      
      chartList.appendChild(barCell);
      
  }
  //Pinto la linea que atravieza el punto 0
  var line = document.getElementsByClassName("barrasV")[1].parentElement.getBoundingClientRect().top-document.getElementsByClassName("barrasV")[1].getBoundingClientRect().top;
  document.getElementsByClassName("ceroLine")[0].style.top = Math.abs(line)+"px";
  var lineLeft = chartList.getElementsByClassName("barCell")[0].offsetWidth;
  document.getElementsByClassName("ceroLine")[0].style.left = lineLeft+"px";
  document.getElementsByClassName("ceroLine")[0].style.width = "calc(100% - "+lineLeft+"px)";

  //Ajusto el resize del label izquierdo según su número más grande
  var labels = chartList.getElementsByClassName("lblSect");
  var bigW = 15;
  for(var i=0;i<labels.length;i++){
    var newW = labels[i].offsetWidth;
    if(newW>bigW)bigW = newW;
  }
  //Se inserta el width del más alto para todos los labels (alinear a la izquierda con el tit) numeros a la derecha
  chartList.getElementsByClassName("lblYCnt")[0].style.width = bigW+"px";
}

		function marcarCoords(testPosRaton,output, x, y) {
		  var cssString = "";
		  cssString += "top:" + (y) + "px;";
		  cssString += "left:" + (x + 10) + "px;";
		  cssString += "opacity:1;";
      cssString += "display:block;";
		  output.style.cssText = cssString;

		  testPosRaton.style.cursor = "pointer";
		}

		function limpiarCoords(testPosRaton,output) {
		  output.style.cssText = "";
		  testPosRaton.style.cursor = "default";
		}

		function oMousePos(element, evt) {
		  var ClientRect = element.getBoundingClientRect();
		  return { //objeto
		    x: Math.round(evt.clientX - ClientRect.left),
		    y: Math.round(evt.clientY - ClientRect.top)
		  }
		}
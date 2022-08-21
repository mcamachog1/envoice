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
var infStatus =  {
  "bysendstatus": {
      "sent": {
          "qty": {
              "number": 391211,
              "formatted": "391.211"
          },
          "pct": {
              "number": 77.50,
              "formatted": "77,50%"
          }
      },
      "notsent": {
          "qty": {
              "number": 43468,
              "formatted": "43.468"
          },
          "pct": {
              "number": 22.5,
              "formatted": "22,5%"
          }
      }
  },
  "byreadstatus": {
      "readed": {
          "qty": {
              "number": 381211,
              "formatted": "391.211"
          },
          "pct": {
              "number": 50,
              "formatted": "50%"
          }
      },
      "unreaded": {
          "qty": {
              "number": 53468,
              "formatted": "53.468"
          },
          "pct": {
              "number": 50,
              "formatted": "50%"
          }
      }
  }
}
var dataValues = [
  {
      "label": {
          "short": "Jul 25",
          "long": "25 de Julio de 2022"
      },
      "values": {
          "Simple TV": 42,
          "Telefónica Venezuela": 18,
          "Corporación Telemic": 5
      }
  },
  {
    "label": {
        "short": "Jul 26",
        "long": "26 de Julio de 2022"
    },
    "values": {
        "Simple TV": 13,
        "Telefónica Venezuela": 58,
        "Corporación Telemic": 75
    }
  },
  {
    "label": {
        "short": "Jul 27",
        "long": "27 de Julio de 2022"
    },
    "values": {
        "Simple TV": 42,
        "Telefónica Venezuela": 28,
        "Corporación Telemic": 25
    }
  },
  {
    "label": {
        "short": "Jul 28",
        "long": "28 de Julio de 2022"
    },
    "values": {
        "Simple TV": 32,
        "Telefónica Venezuela": 28,
        "Corporación Telemic": 55
    }
  },
];
var colors = ['#F5A623','#D0021B','#1A3867','#A5E65D','#CC89E8','#D3D3D3'];
var donutAColors = ["#5FB748",'#0033A0']; 
var donutBColors = ["#1890FF",'#D3D3D3']; 
var actColor = [
  {id:2,color:'#7CC36A'},
  {id:3,color:'#0033A0'},
  {id:1,color:'#D3D3D3'}
];
function init(){
  document.getElementById("customersList").addEventListener("change",function(){      
    loadReports();
  });
  document.getElementById("periodoSelect").addEventListener("change",function(){      
    loadReports();
  });
  loadCustomers();
}

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
  var minpoints = 2;
  var maxpoints = 9;
  var pointset = true;
  var range = maxval + Math.abs(minval);
  var fperiod = 0;
  var period = 0;
  while(pointset){
    fperiod = 1;
    for(var x=2;(x<=(range/minpoints)||(x==2));x++){
      var res = (range%x);
      period = range/x;
      if(res==0 && (period%1)==0){
        if(period<maxpoints){
          pointset = false;          
          break;
        }        
      }
    }
    if(pointset)range = range + fperiod;
  }

  
  //var dif = maxval - minval;  
  var minprc = (Math.abs(minval)*100)/range;

  points = period+1;
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
                barSectLbl.innerHTML = (minval).toFixed(0);
              }else{
                barSectLbl.style.bottom = "calc("+pct+"% - 7.5px)";                
                var lessPorcion = range/(points-1);
                barSectLbl.innerHTML = (minval+(lessPorcion*x)).toFixed(0);
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
            var valprc = (Math.abs(inf[i].values[lbl])*100)/range;
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
        var valprc = (Math.abs(inf[i].values[lbl])*100)/range;
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


function chartDonut(clone,tit,inf,colors){
  //Grafico barras
  var donutList = clone.getElementsByClassName("donut-chart-block")[0].children[0].getElementsByClassName("piesCnt")[0];
  //clone.getElementsByClassName("donut-chart-block")[0].children[0].innerHTML = "";
  
  var start = 0;
  var i = 0;
  var sum = 0;  
  var rotBord = "";
  //Necesito recorrerlo antes para ver si ambos vienen en 0 o solo 1
  var zero = 0;
  for (var key in inf) {
    if(inf[key].qty.number==0)zero++;
  }
  for (var key in inf) {
      //Pintar barras
      var slice = document.createElement("div");
      slice.classList.add("slice");
      slice.style.transform = "rotate("+start+"deg)";
          quesito = document.createElement("span");
          quesito.innerHTML = "";
          //Obtener que valor del circulo esporcentual es.
          var valPct = inf[key].pct.number;
          if(zero==2)valPct = 50;
          sum = sum+inf[key].qty.number;
          var valdeg = (valPct*360)/100;
          var maxDeg = 180;
          if(valdeg>maxDeg){
              slice.style.transform = "rotate("+(start)+"deg) translate3d(0,0,0)";
              quesito.style.transform = "rotate(0deg) translate3d(0,0,0)";
              quesito.style.backgroundColor = colors[i];
              slice.appendChild(quesito);
              
              var clonePie = slice.cloneNode(true);
              var dif = (valdeg-maxDeg);
              clonePie.style.transform = "rotate("+(start+maxDeg)+"deg) translate3d(0,0,0)";
              clonePie.children[0].style.transform = "rotate("+(dif-maxDeg)+"deg) translate3d(0,0,0)";
              clonePie.children[0].style.backgroundColor = colors[i]; 
          }else{
              var val = (parseFloat(valdeg-maxDeg)+1);              
              if(i==0){
                rotBord = ((val-(valdeg/2)));
              }else{
                rotBord = -((360-start)/2);
              }
              slice.style.transform = "rotate("+(start)+"deg) translate3d(0,0,0)";
              quesito.style.transform = "rotate("+val+"deg) translate3d(0,0,0)";
              quesito.style.backgroundColor = colors[i];
              slice.appendChild(quesito);
          }
          var size = 3;
          //Dependiendo del % que comprende el borde exterior del circulo se muestra más o menos
          if(valPct>50){
            size = 5;
          }else if(valPct > 15){
            size = 3;
          }else if(valPct > 10){
            size = 2;
          }else if(valPct > 5){
            size = 1;
          }else if(valPct >1){
            size = 0.5;
          }else{
            size = 0;
          }

          //Se inserta el label en el bloque al extremo correspondiente según lo determine el paso.
          if(i==0){
            clone.getElementsByClassName("borderDonut")[0].children[1].children[0].innerHTML = inf[key].pct.formatted;
            clone.getElementsByClassName("borderDonut")[0].children[1].children[2].style.top = -(20+size)+"px";
          }else{
            clone.getElementsByClassName("borderDonut")[0].children[0].children[0].innerHTML = inf[key].pct.formatted;
            clone.getElementsByClassName("borderDonut")[0].children[0].children[2].style.top = size+"px";
          }
  
          
          start += valdeg;
      
      donutList.appendChild(slice);
      //Agrega complemento de circulo
      if(valdeg>maxDeg)donutList.appendChild(clonePie);
        
      var eleVal = clone.getElementsByClassName("leyDonut")[0].getElementsByClassName("leyVal")[i];
      eleVal.innerHTML = "("+inf[key].qty.formatted+")";
      eleVal.parentElement.previousElementSibling.children[0].style.backgroundColor = colors[i];
      i++;
  }
  //Se Rota el circulo con los labels y el borde 
  clone.getElementsByClassName("borderDonut")[0].style.rotate = rotBord+"deg";
  //Se colorea el label, borde y la linea del top con el primer color
  clone.getElementsByClassName("borderDonut")[0].children[0].style.color = (colors[1]=='#D3D3D3' ? "#818285" : colors[1]);
  clone.getElementsByClassName("borderDonut")[0].style.borderTopColor = colors[1];                
  clone.getElementsByClassName("borderDonut")[0].children[0].children[1].style.backgroundColor = colors[1];
  //Se colorea el label, borde y la linea del top con el segundo color
  clone.getElementsByClassName("borderDonut")[0].style.borderBottomColor = colors[0];
  clone.getElementsByClassName("borderDonut")[0].children[1].style.color = (colors[0]=='#D3D3D3' ? "#818285" : colors[0]);
  clone.getElementsByClassName("borderDonut")[0].children[1].children[1].style.backgroundColor = colors[0];
      
  //Se orientan verticalmente de nuevo los lbls
  clone.getElementsByClassName("borderDonut")[0].children[0].children[0].style.rotate = (-(rotBord))+"deg"; 
  clone.getElementsByClassName("borderDonut")[0].children[1].children[0].style.rotate = (-(rotBord))+"deg"; 
  
  var donutIn = document.createElement("div");
  donutIn.classList.add("in");
    var donutInCell = document.createElement("div");
    donutInCell.classList.add("inCell");
      var inTit = document.createElement("div");
      inTit.classList.add("inTit");
      inTit.innerHTML = tit;
      donutInCell.appendChild(inTit);
      var inVal = document.createElement("div");
      inVal.classList.add("inVal");
      inVal.innerHTML = formatNumberES(sum,0);
      donutInCell.appendChild(inVal);
    donutIn.appendChild(donutInCell);
  clone.getElementsByClassName("donut-chart-block")[0].children[0].appendChild(donutIn);

}

function formatNumberES(n, d=0){
  n=new Intl.NumberFormat("es-ES").format(parseFloat(n).toFixed(d))
  if (d>0) {
      // Obtenemos la cantidad de decimales que tiene el numero
      const decimals=n.indexOf(",")>-1 ? n.length-1-n.indexOf(",") : 0;

      // añadimos los ceros necesios al numero
      n = (decimals==0) ? n+","+"0".repeat(d) : n+"0".repeat(d-decimals);
  }
  return n;
}

var lineColors = ["#0033A0","#2FC25B","#1890FF"];
var longLabel = [];
var customers = [];
var labels = [];
function plotChart(id,dataValues){  
  customers = [];
  labels = [];
  longLabel = [];
  dataValues.forEach(ele=>{
    labels.push(ele.label.short);
    longLabel.push(ele.label.long);
    var i = 0;
    for (var key in ele.values) {
      var nw = true;
      var line = {};
      line.label = key;
      line.data = [ele.values[key]];
      line.pointRadius= 1;
      line.pointHoverRadius = 4;
      line.borderColor =  lineColors[i];
      line.backgroundColor = lineColors[i];
      //Validamos si es nuevo
      customers.forEach(lbl=>{
        if(lbl.label == key)nw = false;
      });

      if(nw)customers.push(line);
      else{
        //Si no es ubicamos el valor solo dentro del data
        customers.forEach(customer=>{
          if(customer.label == key)customer.data.push(ele.values[key]);
        });
      }      
      i++;
    }
  });

  const data = {
    labels: labels,
    datasets: customers
  }

  var canva = document.getElementById(id);
  canva.width = canva.offsetWidth;
  canva.height = canva.offsetHeight;

  var config = {
    type: 'line',
    data: data,
    options: {
      responsive: false,
      scales: { 
        x:{
          ticks: {
            font:{size: 9.5,family:"'Lato'",weight:400},
            padding:7
          },
          grid:{
              display:true,                                            
              color:'transparent',    
              borderColor:'transparent'
          },          
        },
        y:{                    
          min:0,
          ticks: {
            font:{size: 9.5,family:"'Lato'",weight:400},
            precision:0
          },
          grid:{
            display:true,
            borderDash: [3, 3],
            color:"#E9E9E9",
            borderColor:'transparent',
          },          
        }
      },
      plugins: {
        legend: {
          display:false,
          position: 'top',
        },
        title: {
          display: false,
          text: ''
        },
        tooltip: {
          enabled: false,
          position: 'nearest',   
          external: externalTooltipHandler
        }
      }
    },
  };
  var ctx = document.getElementById(id).getContext('2d');
  if (window.grafica) {
    window.grafica.clear();
    window.grafica.destroy();
  }
  window.grafica = new Chart(ctx,config);

}

const getOrCreateTooltip = (chart) => {
  let tooltipEl = chart.canvas.parentNode.querySelector('div');

  if (!tooltipEl) {
    tooltipEl = document.createElement('div');
    tooltipEl.style.background = 'rgba(255, 255, 255, 0.95)';
    tooltipEl.style.borderRadius = '4px';
    tooltipEl.style.color = '#000000';
    tooltipEl.style.opacity = 1;
    tooltipEl.style.pointerEvents = 'none';
    tooltipEl.style.position = 'absolute';
    tooltipEl.style.transform = 'translate(-50%, 0)';
    tooltipEl.style.transition = 'all .1s ease';
    tooltipEl.style.width = "130px";
    tooltipEl.style.boxShadow = '0 2px 8px 0 rgba(0, 0, 0, 0.15)';

    const table = document.createElement('table');
    table.style.margin = '0px';

    tooltipEl.appendChild(table);
    chart.canvas.parentNode.appendChild(tooltipEl);
  }

  return tooltipEl;
};

const externalTooltipHandler = (context) => {
  // Tooltip Element
  const {chart, tooltip} = context;
  const tooltipEl = getOrCreateTooltip(chart);

  // Hide if no tooltip
  if (tooltip.opacity === 0) {
    tooltipEl.style.opacity = 0;
    return;
  }

  // Set Text
  if (tooltip.body) {
    const titleLines = tooltip.title || [];
    const bodyLines = tooltip.body.map(b => b.lines);

    const tableHead = document.createElement('thead');
    titleLines.forEach(title => {
      const tr = document.createElement('tr');
      tr.style.borderWidth = 0;

      const th = document.createElement('th');
      th.style.borderWidth = 0;
      const text = document.createTextNode(longLabel[labels.indexOf(title)]);

      th.appendChild(text);
      tr.appendChild(th);
      tableHead.appendChild(tr);
    });

    const tableBody = document.createElement('tbody');
    bodyLines.forEach((body, i) => {
      const colors = tooltip.labelColors[i];

      const span = document.createElement('span');
      span.style.background = colors.backgroundColor;
      span.style.borderColor = colors.borderColor;
      span.style.borderWidth = '2px';
      span.style.borderRadius = '100%';
      span.style.marginRight = '3px';
      span.style.height = '5px';
      span.style.width = '5px';
      span.style.display = 'inline-block';
      span.style.marginBottom = '2.5px';
      

      const tr = document.createElement('tr');
      tr.style.backgroundColor = 'inherit';
      tr.style.borderWidth = 0;

      const td = document.createElement('td');
      td.style.borderWidth = 0;
      var valArr = body[0].split(":");
      const val = document.createElement("strong");
      val.appendChild(document.createTextNode(valArr[1]+" "));
      const text = document.createTextNode(valArr[0]);

      td.appendChild(span);
      td.appendChild(val);
      td.appendChild(text);
      tr.appendChild(td);
      tableBody.appendChild(tr);
    });

    const tableRoot = tooltipEl.querySelector('table');

    // Remove old children
    while (tableRoot.firstChild) {
      tableRoot.firstChild.remove();
    }

    // Add new children
    tableRoot.appendChild(tableHead);
    tableRoot.appendChild(tableBody);
  }

  const {offsetLeft: positionX, offsetTop: positionY} = chart.canvas;

  // Display, position, and set styles for font
  tooltipEl.style.opacity = 1;
  tooltipEl.style.left = positionX + tooltip.caretX + 'px';
  tooltipEl.style.top = positionY + tooltip.caretY + 'px';
  tooltipEl.style.font = tooltip.options.bodyFont.string;
  tooltipEl.style.padding = tooltip.options.padding + 'px ' + tooltip.options.padding + 'px';
};
function blankCharts(){
  document.getElementById("chartLine").innerHTML = "";
  document.getElementById("qtyMailsChart").getElementsByClassName("listBarrasV")[0].innerHTML = "";
  document.getElementById("qtyMailsChart").getElementsByClassName("lblFootTbl")[0].innerHTML = "";
  document.getElementById("statusDonut").children[0].getElementsByClassName("piesCnt")[0].innerHTML = "";
  document.getElementById("statusDonut").children[1].getElementsByClassName("piesCnt")[0].innerHTML = "";
}
function drawReport(rsp){
  /*
  chartBarsV(inf);
  var donutLeft = document.getElementById("statusDonut").children[0];
  chartDonut(donutLeft,"Cargados",infStatus.bysendstatus,donutAColors);
  var donutLeft = document.getElementById("statusDonut").children[1];
  chartDonut(donutLeft,"Enviados",infStatus.byreadstatus,donutBColors);
  var chartLine = document.getElementById("chartLine").children[1];
  plotChart("chartLine",dataValues);*/
  console.log(rsp);
  //Total de documentos cargados
  document.getElementById("loadedTotal").innerText = rsp.documentsloaded.total.formatted;
  document.getElementById("loadedIncrement").innerText = rsp.documentsloaded.increment.formatted;

  //Total de Documentos enviados
  document.getElementById("sentTotal").innerText = rsp.documentssent.total.formatted;
  document.getElementById("sentIncrement").innerText = rsp.documentssent.increment.formatted;

  //Total de accesos de usuarios al sistema
  document.getElementById("accessTotal").innerText = rsp.logins.customers.total.formatted;
  document.getElementById("accessIncrement").innerText = rsp.logins.customers.increment.formatted;

  //Total de accesos de usuarios al sistema de SENIAT
  document.getElementById("seniatTotal").innerText = rsp.logins.seniat.total.formatted;
  document.getElementById("seniatIncrement").innerText = rsp.logins.seniat.increment.formatted;

  
  //Grafico de barras verticales
  chartBarsV(rsp.targets);
  //Gráficos de donas se pintan por separado
  var donutLeft = document.getElementById("statusDonut").children[0];
  chartDonut(donutLeft,"Cargados",rsp.documentsstatus.bysendstatus,donutAColors);
  var donutLeft = document.getElementById("statusDonut").children[1];
  //chartDonut(donutLeft,"Enviados",infStatus.byreadstatus,donutBColors);
  chartDonut(donutLeft,"Enviados",rsp.documentsstatus.byreadstatus,donutBColors);
  //Grafico de lineas
  plotChart("chartLine",rsp.customers);
}

function loadReports() {
  var par = {};
  var sessid = getParameterByName('sessid');
  let fechas = getDates(document.getElementById("periodoSelect").value);
  par.datefrom = fechas[0];
  par.dateto = fechas[1];
  par.customerid = document.getElementById("customersList").value;
  par.sessionid = sessid;
  let success = (status,response) =>{
    var rsp = (response != "") ? JSON.parse(response) : "";  
    switch(status){
      case 200:
        //Si es exitoso lo pintamos
        drawReport(rsp);
      break;
      default:
        console.log(status);
        console.log(rsp);
      break;
    }
  }
  callWS("GET", "reports/maindashboard", par, success);
  return;
}
function getDates(days) {
  let end = sumarDias(new Date(),1);
  let start = sumarDias(new Date(),-days);
  return [start.toISOString().split('T')[0],end.toISOString().split('T')[0]];
}
const sumarDias = (fecha, dias) =>{
  fecha.setDate(fecha.getDate() + dias);
  return fecha;
};

function loadCustomers(filter="",offset=0,order=2,numrecords=100000){
  var par = {};  
  par.filter = filter;
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
            var first = 'Todos';     
            drawSelectCustom(jsonResp.records, select, id, dsc, first, "");
            loadReports();
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
function drawSelectCustom(rsp, select, id, dsc, first="", selected="") {
  select.innerHTML = "";
  var opt;
  if (first !== "" && first !== null && first !== undefined) {
      opt = document.createElement("option");
      opt.setAttribute("value", "0");
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
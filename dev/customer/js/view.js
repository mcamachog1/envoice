//Muestra el popup de visualización de la factura 
function showViewer(){
    var ele = document.getElementById("invViewer");
    ele.style.display = "block";
    setTimeout(function(){      
      ele.style.opacity = "1";
    },300);
}
  //Oculta el popup de visualización de la factura 
function closeViewer(){
    var ele = document.getElementById("invViewer");
    ele.style.opacity = "";
    setTimeout(function(){      
      ele.style.display = "";
    },300);
}
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
      results = regex.exec(location.search);
    return results === null
      ? ""
      : decodeURIComponent(results[1].replace(/\+/g, " "));
}
window.onload = function(){
    var hash = getParameterByName("hash");
    var frame = document.getElementById("frameView");
    frame.setAttribute("src","./api/show.php?hash="+hash);
    showViewer();
    frame.onload = function(){
        setTimeout(function(){        
          var frame = document.getElementById("frameView");
          frame.style.opacity = "1";
        },300);
    };
}
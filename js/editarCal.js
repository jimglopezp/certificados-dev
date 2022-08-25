var casilla;
/*
var x=$(function(){
	$('[class=diaHabil]').dblclick(alerta);
	$('[class=diaFestivo]').dblclick(alerta);
	//$("table").attr("style=diaHabil").click(alerta);
})

function alerta(){
	alert("thi");
}*/

$(document).ready(function(){
    // Cuando el mouse se pone encima de un elemento con el class=text
    $(".diaHabil").dblclick(mostrarDiv);
    $(".diaFestivo").dblclick(mostrarDiv);
	
    /*  $("#flotante").dblclick(function(){
        $(this).fadeOut("slow");
    });
    */
	
    $("#flotante").click(function(){
        $(this).fadeOut("slow");
    });
    // Cuando el mouse sale del elemento con el class=text
    $(".text").mouseleave(function(event){
        // Escondemos el div flotante
        $("#flotante").hide();
    });
	
	$("span").click(function(event){
		//alert($("#forzarDia").attr("value"));
        //alert($(this).attr("id"));
		//alert("v="+$("#forzarDia").attr("value")+"&o="+$(this).attr("id"));
		var o = $(this).attr("id");
                var v = $("#forzarDia").attr("value");
		//$("#bas").load(BASE_URL,"v="+vv+"&o="+oo,
		$("#bas").load(BASE_URL,{v:v,o:o},
		function(){
			switch(o){
				case "opc1":
					//alert("poc1:"+casilla.attr("class"));
					//casilla.attr("class","diaFestivo");
					casilla.removeClass();
					casilla.addClass("diaFestivo");
					casilla.css("text-decoration","overline");
					//casilla.css("text-shadow","2px 2px #dddddd");
					casilla.css("font-style","italic");
				break;
					
				case "opc2":
					casilla.removeClass();
					casilla.addClass("diaHabil");
					casilla.css("font-size","75%");
					casilla.css("text-decoration","overline");
					casilla.css("font-style","italic");
					//casilla.addClass("diaHabil");
				break;
				case "opc3":
					casilla.removeClass();
					casilla.css("font-size","75%");
					casilla.css("font-style","italic");
					casilla.css("text-decoration","none");
					casilla.css("background-color","#FFFF00");
				break;
			}
			//alert($(this).attr("id"));
		}
		);
		//load("forzar.php",{ v:v,o:o } "v="+$("#forzarDia").attr("value")+"&o="+$(this).attr("id"));
    });
	
	function mostrarDiv(event){
        // Ponemos en el div flotante el contenido del attributo content del div
        // donde se encuentra el mouse (this)
        //$("#flotante").html($(this).attr("content"));
		$("#forzarDia").attr("value",($(this).attr("content")));
		casilla = $(this);
        // Posicionamos el div flotante y mo lostramos
        $("#flotante").css({left:event.pageX+5, top:event.pageY+5, display:"block"});
    }
});
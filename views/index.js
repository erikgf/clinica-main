/*
1.- pager

    inicializar: carga al DOM, crea el objeto y lo muestra


*/

var PAGE_NAME_INIT = "registro_atencion";
var PAGINAS_NAVEGACION = [];
var Pager = {
    inicializarPagina : function(pagina, parametros){
        $(".content").addClass("hide");
        var $ctn = $("#content-"+pagina),
            objPage,
            pageName;
    
        if ($ctn.length == 0){
            alert("Interfaz no existe. Consultar con administrador");
            return;
        };

        pageName = $ctn.data("name");

        if (pageName != ""){
            try{
                objPage = window["Pag"+pageName];
            } catch(e){
                console.error(pageName+" no ha sido declarada");
                return;
            }
        }
    
        if ($ctn.hasClass("data-loaded")){
            //$ctn.removeClass('hide');}
            if (typeof objPage.show !== 'function'){
                console.error(pageName+ " no tiene la función show() definida.");
                return;
            }
            objPage.show(parametros);
            $ctn = null;
            return;
        }

        $ctn.load('views/pages/frm-'+pagina+'.php', 
            function( response, status, xhr ) {
                if (status == "error"){
                    if (xhr.status == 404){
                        alert("Interfaz no existe. Consultar con administrador");
                        return;
                    }
                }

                if (typeof objPage.init !== 'function'){
                    console.error(pageName+ " no tiene la función init() definida.");
                    return;
                }
                
                objPage.init(parametros);
            }
        );
        
        $ctn = null;
        //$ctn.addClass('data-loaded');
        //$ctn.removeClass('hide');
    }
};


/*LO idea l sería reoger esta información desde el servior, acorde l usuario logueado.*/
window.addEventListener("load", function(event) {
    document.body.style.zoom = "95%";
    Pager.inicializarPagina(PAGE_NAME_INIT);
});
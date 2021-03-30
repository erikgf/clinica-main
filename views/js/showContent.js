function showContent(pagina, forzarReload = false){
    hideAll();
    var $ctn = $("#content-"+pagina);

    if ($ctn.length == 0){
        alert("Interfaz no existe. Consultar con administrador");
        /*Mostrar página no válida.*/
        return;
    };

    if ($ctn.hasClass("data-loaded")){
        $ctn.removeClass('hide');
        $ctn = null;
        return;
    }

/*
    if (!forzarReload){
        if ($ctn.hasClass("data-loaded")){
            $ctn.removeClass('hide');
            $ctn = null;
            return;
        }
    }
    const scriptList = $ctn.find("script[type='text/javascript']");
    if (scriptList.length){
        console.log(scriptList);
        const convertedNodeList = Array.from(scriptList);        
        convertedNodeList[0].parentNode.removeChild(convertedNodeList[0]);

        $ctn.empty();
    }
    */

    $ctn.load('views/pages/frm-'+pagina+'.php', 
        function( response, status, xhr ) {
            if (status == "error"){
                console.error(xhr.statusText);
                if (xhr.status == 404){
                    alert("Interfaz no existe. Consultar con administrador");
                    /*Mostrar página no válida.*/
                    return;
                }
            }
        }
    );
    $ctn.addClass('data-loaded');
    $ctn.removeClass('hide');
};

function hideAll(){
    $(".content").addClass("hide");
};
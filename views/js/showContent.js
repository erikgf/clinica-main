function showContent(pagina, forzarReload = false){
    hideAll();
    var $ctn = $("#content-"+pagina);

    if ($ctn.length == 0){
        alert("Interfaz no existe. Consultar con administrador");
        return;
    };

    if ($ctn.hasClass("data-loaded")){
        $ctn.removeClass('hide');
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
        }
    );
    $ctn.addClass('data-loaded');
    $ctn.removeClass('hide');
};

function hideAll(){
    $(".content").addClass("hide");
};
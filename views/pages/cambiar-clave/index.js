var CambiarClave = function() {
    var $txtAntiguaClave,
        $txtNuevaClave,
        $btnCambiarClave,
        $frm;

    this.setDOM = function(){

        $txtAntiguaClave = $("#txt-antiguaclave");
        $txtNuevaClave = $("#txt-nuevaclave");
        $btnCambiarClave = $("#btn-cambiarclave");

        $frm  = $("form");
    };  
    
    this.setEventos = function(){

        $(".ver-clave").on("mouseover", function(e){
            var $this = $(this);
            $this.parent().find("input").attr("type", "text");
        });

        $(".ver-clave").on("mouseout", function(e){
            var $this = $(this);
            $this.parent().find("input").attr("type", "password");
        });

        $btnCambiarClave.on("click", function(e){
            e.preventDefault();
            cambiarClave();
        });
    };

    var cambiarClave = function(){
        if (!Util.validarFormulario($frm)){
            return;
        };

        if ($txtNuevaClave.val().length < 6){
            toastr.error("Tu nueva clave debe tener al menos 6 caracteres.");
            return;
        }

        $.ajax({ 
            url: VARS.URL_CONTROLADOR+"usuario.controlador.php?op=cambiar_clave",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: {
               p_antigua_clave : $txtAntiguaClave.val(),
               p_nueva_clave : $txtNuevaClave.val()
            },
            success: function(datos){
                $frm[0].reset();
                toastr.success(datos.msj);
            },
            error: function (request) {
                toastr.error(request.responseText);
                return
            },
            cache: true
        });
    };


    this.setDOM();
    this.setEventos();
    return this;
};

$(document).ready(function(){
    objCambiarClave = new CambiarClave(); 
});



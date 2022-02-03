var InicioSesion = function(){
    var $txtUsuario,
        $txtClave,
        $frmSesion,
        $btnAcceder;

    this.setDOM = function(){
        $frmSesion = $("#frm-sesion");
        $txtUsuario = $("#txt-usuario");
        $txtClave = $("#txt-clave");
        $btnAcceder = $("#btn-acceder");
    };

    this.setEventos = function(){

        $frmSesion.on("submit", function(e){
            e.preventDefault();
            iniciarSesion();
        });

        
        setTimeout(function(){
            $txtUsuario.focus();    
        }, 1000);

    };

    var iniciandoSesion = false;
    var iniciarSesion = function(){
        if (iniciandoSesion == true){
            return;
        }

        iniciandoSesion = true;
        if ($btnAcceder){
            $btnAcceder.prop("disabled", true);
        }

        $.ajax({ 
            url: VARS.URL_CONTROLADOR+"usuario.controlador.php?op=iniciar_sesion",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: {
               p_nombre_usuario : $txtUsuario.val(),
               p_clave : $txtClave.val()
            },
            success: function(xhr){

                localStorage.setItem("cache_caja", "");

                var url_inicio = xhr.interfaz_inicio_sesion;
                window.location.href = "../"+url_inicio;                
            },
            error: function (request) {
                iniciandoSesion = false;
                if ($btnAcceder){
                    $btnAcceder.prop("disabled", false);
                }
                toastr.error(request.responseText);
                return;
            },
            cache: true
        });
    };

    this.setDOM();
    this.setEventos();
};



$(document).ready(function(){
   objInicioSesion = new InicioSesion();
});
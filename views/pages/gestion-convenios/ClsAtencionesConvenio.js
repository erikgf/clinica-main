var AtencionesConvenio = function(_template, _$tabla, _$tbody){
    var tplColaboradores,
        $tblColaboradores,
        $tbbColaboradores;
    
    this.setInit = function(){
        tplColaboradores  = _template;
        $tblColaboradores  = _$tabla;
        $tbbColaboradores  = _$tbody;

        this.setDOM();
        this.setEventos();

        //this.cargar();
        return this;
    };

    this.setDOM = function(){

    };

    this.setEventos = function () {
        var self = this;
    };

    /*
    this.leer = function(id){
        var self = this;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"colaborador.servicio.controlador.php?op=leer",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_colaborador : id
            },
            success: function(result){
                $mdl.modal("show");
                self.render(result);
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    this.render = function(data){
        $mdl.find(".modal-title").html("Editando Colaborador");

        $txtIdColaborador.val(data.id_colaborador);
        $txtIdTipoDocumento.val(data.id_tipo_documento);
        $txtNumeroDocumento.val(data.numero_documento);
        $txtNombres.val(data.nombres);
        $txtApellidoPaterno.val(data.apellido_paterno);
        $txtApellidoMaterno.val(data.apellido_materno);
        $correo.val(data.correo);
        $telefono.val(data.telefono);
        $idRol.val(data.id_rol);
        
        $btnEliminar.show();
    };

    this.anular = function(id){
        var self = this;

        if (!confirm("¿Está seguro de dar de baja este colaborador?")){
            return;
        }

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"colaborador.controlador.php?op=anular",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_colaborador : id
            },
            success: function(result){
                toastr.success(result.msj);
                self.cargar();

                $mdl.modal("hide");
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    this.cargar = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"colaborador.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                $tbbColaboradores.html(tplColaboradores(result));
                TABLA_COLABORADORES = $tblColaboradores.DataTable({
                    "ordering": false
                });
                
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };
    */

    return this.setInit();
};
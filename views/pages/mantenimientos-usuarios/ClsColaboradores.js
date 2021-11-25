var Colaborador = function(_template, _$tabla, _$tbody){
    var $mdl,   
        $txtIdColaborador,
        $txtIdTipoDocumento,
        $txtNumeroDocumento,
        $txtNombres,
        $txtApellidoPaterno,
        $txtApellidoMaterno,
        $correo,
        $telefono,
        $idRol,
        $txtLogin,
        $txtClave,
        $btnEliminar,
        $btnGuardar;

    var tplColaboradores,
        $tblColaboradores,
        $tbbColaboradores;
    
    this.setInit = function(){
        tplColaboradores  = _template;
        $tblColaboradores  = _$tabla;
        $tbbColaboradores  = _$tbody;

        this.setDOM();
        this.setEventos();

        this.cargar();
        return this;
    };

    this.setDOM = function(){
        $mdl = $("#mdl-colaborador");
        $txtIdColaborador = $("#txt-colaborador-seleccionado");
        $txtIdTipoDocumento = $("#txt-colaborador-tipodocumento");
        $txtNumeroDocumento = $("#txt-colaborador-numerodocumento");
        $txtNombres = $("#txt-colaborador-nombres");
        $txtApellidoPaterno = $("#txt-colaborador-apellidopaterno");
        $txtApellidoMaterno = $("#txt-colaborador-apellidomaterno");
        $correo  = $("#txt-colaborador-correo");
        $telefono  = $("#txt-colaborador-telefono");
        $idRol  = $("#txt-colaborador-idrol");
        $txtLogin = $("#txt-colaborador-login");
        $txtClave = $("#txt-colaborador-clave");

        $btnEliminar = $("#btn-colaborador-eliminar");
        $btnGuardar = $("#btn-colaborador-guardar");
    };

    this.setEventos = function () {
        var self = this;

        $btnEliminar.on("click", function () {
            self.anular(this.dataset.id);
        });
        
        $btnGuardar.on("click", function(e){
            self.guardar();
        });

        $mdl.on("hidden.bs.modal", function(e){
            $btnEliminar.hide();
            $mdl.find("form")[0].reset();
        });
    };

    this.nuevoRegistro = function(){
        $mdl.find("form")[0].reset();
        $mdl.modal("show");
        $mdl.find(".modal-title").html("Nuevo Colaborador");

        $txtIdColaborador.val("");
        
    };

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

    this.guardar = function(){
        var self = this;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"colaborador.controlador.php?op=guardar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_colaborador : $txtIdColaborador.val(),
                p_id_tipo_documento: $txtIdTipoDocumento.val(),
                p_numero_documento: $txtNumeroDocumento.val(),
                p_nombres: $txtNombres.val(),
                p_apellido_paterno: $txtApellidoPaterno.val(),
                p_apellido_materno: $txtApellidoMaterno.val(),
                p_correo: $correo.val(),
                p_telefono: $telefono.val(),
                p_id_rol: $idRol.val()
                
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

    return this.setInit();
};
var Colaborador = function(){
    var $mdl,   
        $txtIdColaborador,
        $txtIdTipoDocumento,
        $txtNumeroDocumento,
        $txtNombres,
        $txtApellidoPaterno,
        $txtApellidoMaterno,
        $txtCorreo,
        $txtTelefono,
        $txtRol,
        $chkAccesoSistema,
        $btnEliminar,
        $btnGuardar;

    var tplColaboradores,
        $tblColaboradores,
        $tbdColaboradores;

    var $overlayTabla, 
        $btnActualizar;

    var $mdlCambiarClave,
        $btnGuardarCambiarClave,
        $txtNombresCambiarClave,
        $txtClaveCambiarClave,
        $txtIdColaboradorCambiarClave;
    
    this.setInit = function(){
        this.getTemplates();
        return this;
    };

    this.getTemplates = function(){
        var $reqColaboradores =  $.get("template.colaboradores.php");
        var self = this;

        $.when($reqColaboradores)
            .done(function(resColaboradores){
                tplColaboradores = Handlebars.compile(resColaboradores);
                
                self.setDOM();
                self.setEventos();
                self.cargarRoles();
                self.listar();
            })
            .fail(function(error){
                console.error(error);
            });
    };

    this.setDOM = function(){
        $mdl = $("#mdl-colaborador");
        $txtIdColaborador = $("#txt-colaborador-seleccionado");
        $txtIdTipoDocumento = $("#txt-colaborador-tipodocumento");
        $txtNumeroDocumento = $("#txt-colaborador-numerodocumento");
        $txtNombres = $("#txt-colaborador-nombres");
        $txtApellidoPaterno = $("#txt-colaborador-apellidopaterno");
        $txtApellidoMaterno = $("#txt-colaborador-apellidomaterno");
        $txtCorreo  = $("#txt-colaborador-correo");
        $txtTelefono  = $("#txt-colaborador-telefono");
        $txtRol  = $("#txt-colaborador-idrol");
        $chkAccesoSistema = $("#chk-colaborador-accesosistema");

        $tblColaboradores = $("#tbl-colaboradores");
        $tbdColaboradores = $("#tbd-colaboradores");

        $btnEliminar = $("#btn-colaborador-eliminar");
        $btnGuardar = $("#btn-colaborador-guardar");

        $overlayTabla = $("#overlay-tbl-colaboradores");
        $btnActualizar =  $("#btn-actualizar-colaboradores");

        $mdlCambiarClave = $("#mdl-colaboradorcambiarclave");
        $txtNombresCambiarClave = $("#txt-colaboradorcambiarclave-nombres");
        $txtClaveCambiarClave = $("#txt-colaboradorcambiarclave-clave");
        $txtIdColaboradorCambiarClave = $("#txt-colaboradorcambiarclave-seleccionado");
        $btnGuardarCambiarClave = $("#btn-colaboradorcambiarclave-guardar");
    };

    this.setEventos = function () {
        var self = this;

        $btnActualizar.on("click", function(e){
            e.preventDefault();
            self.listar();
        });

        $("#btn-nuevocolaboradores").on("click", function(e){
            e.preventDefault();
            self.nuevoRegistro();
        });

        $btnEliminar.on("click", function () {
            self.anular($txtIdColaborador.val());
        });
        
        $btnGuardar.on("click", function(e){
            self.guardar();
        });

        $mdl.on("hidden.bs.modal", function(e){
            $btnEliminar.hide();
            $mdl.find("form")[0].reset();
        });

        $tbdColaboradores.on("click", ".btn-editar", function(e){
            e.preventDefault();
            self.leer(this.dataset.id);
        });

        $tbdColaboradores.on("click", ".btn-eliminar", function(e){
            e.preventDefault();
            self.anular(this.dataset.id);
        });

        $tbdColaboradores.on("click", ".btn-cambiarclave", function(e){
            e.preventDefault();
            self.leerCambiarClave(this.dataset.id, this.dataset.nombres);
        });

        $btnGuardarCambiarClave.on("click", function(e){
            self.cambiarClave();
        });
    
        $mdlCambiarClave.on("hide.bs.modal", function(e){
            $txtIdColaboradorCambiarClave.val("");
            $txtClaveCambiarClave.val("");
            $txtNombresCambiarClave.val("");
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
            url : VARS.URL_CONTROLADOR+"colaborador.controlador.php?op=leer",
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

    this.leerCambiarClave = function(id, nombres_apellidos){
        console.log(id, nombres_apellidos);

        $mdlCambiarClave.modal("show");
        $txtNombresCambiarClave.val(nombres_apellidos);
        $txtIdColaboradorCambiarClave.val(id);
    };

    this.render = function(data){
        $mdl.find(".modal-title").html("Editando Colaborador");

        $txtIdColaborador.val(data.id_colaborador);
        $txtIdTipoDocumento.val(data.id_tipo_documento);
        $txtNumeroDocumento.val(data.numero_documento);
        $txtNombres.val(data.nombres);
        $txtApellidoPaterno.val(data.apellido_paterno);
        $txtApellidoMaterno.val(data.apellido_materno);
        $txtCorreo.val(data.correo);
        $txtTelefono.val(data.telefono);
        $txtRol.val(data.id_rol);

        $chkAccesoSistema[0].checked = data.estado_acceso == 'A';
        
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
                self.listar();

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
                p_correo: $txtCorreo.val(),
                p_telefono: $txtTelefono.val(),
                p_id_rol: $txtRol.val(),
                p_estado_acceso: $chkAccesoSistema[0].checked ? "A" : "I"
            },
            success: function(result){
                toastr.success(result.msj);
                self.listar();
                
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

    this.cambiarClave = function(){
        if (!(confirm("¿Está seguro de realizar este cambio?"))){
            return;
        }

        var clave = $txtClaveCambiarClave.val().trim();
        if (clave.length < 6){
            toastr.error("La clave debe tener mínimo 6 caracteres.");
            return;
        }

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"colaborador.controlador.php?op=cambiar_clave",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_colaborador : $txtIdColaboradorCambiarClave.val(),
                p_clave : $txtClaveCambiarClave.val()
            },
            success: function(result){
                toastr.success(result.msj);
                $mdlCambiarClave.modal("hide");
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    TABLA_COLABORADORES  = null;
    this.listar = function(){
        $btnActualizar.prop("disabled", true);
        $overlayTabla.show();

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"colaborador.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                $btnActualizar.prop("disabled", false);
                $overlayTabla.hide();

                if (TABLA_COLABORADORES){
                    TABLA_COLABORADORES.destroy();
                }

                $tbdColaboradores.html(tplColaboradores(result));
                TABLA_COLABORADORES = $tblColaboradores.DataTable({
                    "ordering":true,
                    "pageLength": 10,
                    /*
                    "columns": [
                            { "width": "75px" },
                            null,
                            { "width": "135px" },
                            { "width": "115px" },
                            { "width": "115px" },
                            { "width": "115px" }
                          ]
                    */
                });
            },
            error: function (request) {
                $btnActualizar.prop("disabled", false);
                $overlayTabla.hide();
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    this.cargarRoles = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"rol.controlador.php?op=obtener_combo",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                new SelectComponente({$select : $txtRol, opcion_vacia: false}).render(result);
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
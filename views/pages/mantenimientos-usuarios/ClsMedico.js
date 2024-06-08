var TABLA_MEDICOS = null;
const Medico = function(){
    var $mdl,   
        $txtIdMedico,
        $txtIdTipoDocumento,
        $txtNumeroDocumento,
        $txtNombresRS,
        $chkAccesoSistema,
        $btnGuardar;

    var tplMedicos,
        $tblMedicos,
        $tbdMedicos;

    var $overlayTabla, 
        $btnActualizar;

    var $mdlCambiarClave,
        $btnGuardarCambiarClave,
        $txtNombresCambiarClave,
        $txtClaveCambiarClave,
        $txtIdMedicoCambiarClave;
    
    this.setInit = function(){
        this.getTemplates();
        return this;
    };

    this.getTemplates = function(){
        var $reqMedicos =  $.get("template.medicos.php");
        var self = this;

        $.when($reqMedicos)
            .done(function(resMedicos){
                tplMedicos = Handlebars.compile(resMedicos);
                
                self.setDOM();
                self.setEventos();
                self.listar();
            })
            .fail(function(error){
                console.error(error);
            });
    };

    this.setDOM = function(){
        $mdl = $("#mdl-medico");
        $txtIdMedico = $("#txt-medico-seleccionado");
        $txtIdTipoDocumento = $("#txt-medico-tipodocumento");
        $txtNumeroDocumento = $("#txt-medico-numerodocumento");
        $txtNombresRS = $("#txt-medico-nombres");
        $chkAccesoSistema = $("#chk-medico-accesosistema");

        $tblMedicos = $("#tbl-medicos");
        $tbdMedicos = $("#tbd-medicos");

        $btnGuardar = $("#btn-medico-guardar");

        $overlayTabla = $("#overlay-tbl-medicos");
        $btnActualizar =  $("#btn-actualizar-medicos");

        $mdlCambiarClave = $("#mdl-medicocambiarclave");
        $txtNombresCambiarClave = $("#txt-medicocambiarclave-nombres");
        $txtClaveCambiarClave = $("#txt-medicocambiarclave-clave");
        $txtIdMedicoCambiarClave = $("#txt-medicocambiarclave-seleccionado");
        $btnGuardarCambiarClave = $("#btn-medicocambiarclave-guardar");
    };

    this.setEventos = function () {
        var self = this;

        $btnActualizar.on("click", function(e){
            e.preventDefault();
            self.listar();
        });

        $btnGuardar.on("click", function(e){
            self.guardar();
        });

        $mdl.on("hidden.bs.modal", function(e){
            $mdl.find("form")[0].reset();
        });

        $tbdMedicos.on("click", ".btn-editar", function(e){
            e.preventDefault();
            self.leer(this.dataset.id);
        });

        $tbdMedicos.on("click", ".btn-cambiarclave", function(e){
            e.preventDefault();
            self.leerCambiarClave(this.dataset.id, this.dataset.nombres);
        });

        $btnGuardarCambiarClave.on("click", function(e){
            self.cambiarClave();
        });
    
        $mdlCambiarClave.on("hide.bs.modal", function(e){
            $txtIdMedicoCambiarClave.val("");
            $txtClaveCambiarClave.val("");
            $txtNombresCambiarClave.val("");
        });
    };

    this.leer = function(id){
        var self = this;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=leer_usuario",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_medico : id
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
        $mdlCambiarClave.modal("show");
        $txtNombresCambiarClave.val(nombres_apellidos);
        $txtIdMedicoCambiarClave.val(id);
    };

    this.render = function(data){
        $mdl.find(".modal-title").html("Editando Usuario / Medico");

        $txtIdMedico.val(data.id_medico);
        $txtIdTipoDocumento.val(data.id_tipo_documento);
        $txtNumeroDocumento.val(data.numero_documento);
        $txtNombresRS.val(data.nombres);
        $chkAccesoSistema[0].checked = data.estado_acceso == 'A';
    };

    this.guardar = function(){
        var self = this;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=guardar_usuario",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_medico : $txtIdMedico.val(),
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
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=cambiar_clave",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_medico : $txtIdMedicoCambiarClave.val(),
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

    this.listar = function(){
        $btnActualizar.prop("disabled", true);
        $overlayTabla.show();

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=listar_usuario",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                $btnActualizar.prop("disabled", false);
                $overlayTabla.hide();

                if (TABLA_MEDICOS){
                    TABLA_MEDICOS.destroy();
                }

                $tbdMedicos.html(tplMedicos(result));
                TABLA_MEDICOS = $tblMedicos.DataTable({
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

    return this.setInit();
};
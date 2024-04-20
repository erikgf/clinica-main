var TABLA_PROMOTORAS = null;
const Promotora = function(){
    var $mdl,   
        $txtIdPromotora,
        $txtIdTipoDocumento,
        $txtNumeroDocumento,
        $txtNombresRS,
        $chkAccesoSistema,
        $btnGuardar;

    var tplPromotoras,
        $tblPromotoras,
        $tbdPromotoras;

    var $overlayTabla, 
        $btnActualizar;

    var $mdlCambiarClave,
        $btnGuardarCambiarClave,
        $txtNombresCambiarClave,
        $txtClaveCambiarClave,
        $txtIdPromotoraCambiarClave;
    
    this.setInit = function(){
        this.getTemplates();
        return this;
    };

    this.getTemplates = function(){
        var $reqPromotoras =  $.get("template.promotoras.php");
        var self = this;

        $.when($reqPromotoras)
            .done(function(resPromotoras){
                tplPromotoras = Handlebars.compile(resPromotoras);
                
                self.setDOM();
                self.setEventos();
                self.listar();
            })
            .fail(function(error){
                console.error(error);
            });
    };

    this.setDOM = function(){
        $mdl = $("#mdl-promotora");
        $txtIdPromotora = $("#txt-promotora-seleccionado");
        $txtIdTipoDocumento = $("#txt-promotora-tipodocumento");
        $txtNumeroDocumento = $("#txt-promotora-numerodocumento");
        $txtNombresRS = $("#txt-promotora-nombres");
        $chkAccesoSistema = $("#chk-promotora-accesosistema");

        $tblPromotoras = $("#tbl-promotoras");
        $tbdPromotoras = $("#tbd-promotoras");

        $btnGuardar = $("#btn-promotora-guardar");

        $overlayTabla = $("#overlay-tbl-promotoras");
        $btnActualizar =  $("#btn-actualizar-promotoras");

        $mdlCambiarClave = $("#mdl-promotoracambiarclave");
        $txtNombresCambiarClave = $("#txt-promotoracambiarclave-nombres");
        $txtClaveCambiarClave = $("#txt-promotoracambiarclave-clave");
        $txtIdPromotoraCambiarClave = $("#txt-promotoracambiarclave-seleccionado");
        $btnGuardarCambiarClave = $("#btn-promotoracambiarclave-guardar");
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

        $tbdPromotoras.on("click", ".btn-editar", function(e){
            e.preventDefault();
            self.leer(this.dataset.id);
        });

        $tbdPromotoras.on("click", ".btn-cambiarclave", function(e){
            e.preventDefault();
            self.leerCambiarClave(this.dataset.id, this.dataset.nombres);
        });

        $btnGuardarCambiarClave.on("click", function(e){
            self.cambiarClave();
        });
    
        $mdlCambiarClave.on("hide.bs.modal", function(e){
            $txtIdPromotoraCambiarClave.val("");
            $txtClaveCambiarClave.val("");
            $txtNombresCambiarClave.val("");
        });
    };

    this.leer = function(id){
        var self = this;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"promotora.controlador.php?op=leer_usuario",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_promotora : id
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
        $txtIdPromotoraCambiarClave.val(id);
    };

    this.render = function(data){
        $mdl.find(".modal-title").html("Editando Usuario / Promotora");

        $txtIdPromotora.val(data.id_promotora);
        $txtIdTipoDocumento.val(data.id_tipo_documento);
        $txtNumeroDocumento.val(data.numero_documento);
        $txtNombresRS.val(data.nombres);
        $chkAccesoSistema[0].checked = data.estado_acceso == 'A';
    };

    this.guardar = function(){
        var self = this;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"promotora.controlador.php?op=guardar_usuario",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_promotora : $txtIdPromotora.val(),
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
            url : VARS.URL_CONTROLADOR+"promotora.controlador.php?op=cambiar_clave",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_promotora : $txtIdPromotoraCambiarClave.val(),
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
            url : VARS.URL_CONTROLADOR+"promotora.controlador.php?op=listar_usuario",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                $btnActualizar.prop("disabled", false);
                $overlayTabla.hide();

                if (TABLA_PROMOTORAS){
                    TABLA_PROMOTORAS.destroy();
                }

                $tbdPromotoras.html(tplPromotoras(result));
                TABLA_PROMOTORAS = $tblPromotoras.DataTable({
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
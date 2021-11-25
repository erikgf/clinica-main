var EmpresaConvenio = function(){
    var $mdl,   
        $txtIdEmpresaConvenio,
        $txtRazonSocial,
        $btnDarBaja,
        $btnDarAlta,
        $btnNuevo,
        $btnGuardar;

    var tplEmpresasConvenios,
        $tblEmpresasConvenios,
        $tbdEmpresasConvenios;
    var self = this;
    var TR_FILA = null;
    var TABLA_AJUSTADA = false;
    
    this.setInit = function(){
        this.setDOM();
        this.setEventos();

        this.getTemplates();
        return this;
    };

    this.getTemplates = function(){
        var $reqEmpresasConvenios =  $.get("template.empresas.convenio.php");

        $.when($reqEmpresasConvenios)
            .done(function(resEmpresasConvenios){
                tplEmpresasConvenios = Handlebars.compile(resEmpresasConvenios);
                self.listar();
            })
            .fail(function(error){
                console.error(error);
            });
    };


    this.setDOM = function(){
        $mdl = $("#mdl-empresaconvenio");
        $txtIdEmpresaConvenio = $("#txt-empresaconvenio-seleccionado");
        $txtNumeroDocumento = $("#txt-empresaconvenio-numerodocumento");
        $txtRazonSocial = $("#txt-empresaconvenio-razonsocial");
        $btnDarBaja = $("#btn-empresaconvenio-darbaja");
        $btnDarAlta = $("#btn-empresaconvenio-daralta");
        $btnGuardar = $("#btn-empresaconvenio-guardar");    
        
        $overlayTabla = $("#overlay-tbl-empresaconvenio");
        $btnActualizar  =  $("#btn-actualizar-empresaconvenio");
        $btnNuevo = $("#btn-nuevo-empresaconvenio");

        $tblEmpresasConvenios = $("#tbl-empresaconvenio");
        $tbdEmpresasConvenios  = $("#tbd-empresaconvenio");
    };

    this.setEventos = function () {
        var self = this;
        $btnActualizar.on("click", function(e){
            e.preventDefault();
            self.listar();
        });

        $btnDarBaja.on("click", function () {
            self.darBaja($txtIdEmpresaConvenio.val(), TR_FILA, 'I');
        });

        $btnDarAlta.on("click", function () {
            self.darBaja($txtIdEmpresaConvenio.val(), TR_FILA, 'A');
        });
        
        $btnGuardar.on("click", function(e){
            self.guardar();
        });

        $mdl.on("hidden.bs.modal", function(e){
            $btnDarBaja.hide();
            $mdl.find("form")[0].reset();
        });

        $btnNuevo.on("click", function(e){
            e.preventDefault();
            self.nuevoRegistro();
        });

        $tbdEmpresasConvenios.on("click", ".btn-editar", function (e) {
            e.preventDefault();
            self.leer(this.dataset.id, $(this).parents("tr"));
        });

        $tbdEmpresasConvenios.on("click", ".btn-darbaja", function (e) {
            e.preventDefault();
            self.darBaja(this.dataset.id, $(this).parents("tr"), 'I');
        });

        $tbdEmpresasConvenios.on("click", ".btn-daralta", function (e) {
            e.preventDefault();
            self.darBaja(this.dataset.id, $(this).parents("tr"), 'A');
        });

        $("#tab-empresaconvenio").on("shown.bs.tab", function(e){
            if (!TABLA_AJUSTADA){
                TABLA_EMPRESAS_CONVENIO.columns.adjust();
                TABLA_AJUSTADA = true;
            }
        });
    };

    this.nuevoRegistro = function(){
        $mdl.find("form")[0].reset();
        $mdl.modal("show");
        $mdl.find(".modal-title").html("Nueva Empresa de Convenio");

        $txtIdEmpresaConvenio.val("");
        TR_FILA = null;
    };

    this.leer = function(id, $tr_fila){
        var self = this;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"empresa.convenio.controlador.php?op=leer",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_empresa_convenio : id
            },
            success: function(result){
                $mdl.modal("show");
                self.render(result);

                TR_FILA = $tr_fila;
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    this.render = function(dataEmpresaConvenio){
        $mdl.find(".modal-title").html("Editando Empresa Convenio");

        $txtIdEmpresaConvenio.val(dataEmpresaConvenio.id_empresa_convenio);
        $txtNumeroDocumento.val(dataEmpresaConvenio.numero_documento);
        $txtRazonSocial.val(dataEmpresaConvenio.razon_social);

        if( dataEmpresaConvenio.estado == 'A'){
            $btnDarBaja.show();
            $btnDarAlta.hide();
        }else {
            $btnDarBaja.hide();
            $btnDarAlta.show();
        }
    };

    this.darBaja = function(idEmpresaConvenio, $tr_fila, estado){
        if (!confirm("¿Está seguro de actualizar este registro?")){
            return;
        }

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"empresa.convenio.controlador.php?op=dar_baja",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_empresa_convenio : idEmpresaConvenio,
                p_estado : estado
            },
            success: function(result){
                toastr.success(result.msj);
                var arr = [].slice.call($(tplEmpresasConvenios([result.registro])).find("td")),
                    dataNuevaFila = $.map(arr, function(item) {
                        return item.innerHTML;
                    });

                if (TABLA_EMPRESAS_CONVENIO){
                    if ($tr_fila){ 
                        TABLA_EMPRESAS_CONVENIO
                            .row($tr_fila)
                            .data(dataNuevaFila)
                            .draw();  
                    } else {
                        TABLA_EMPRESAS_CONVENIO.row.add(dataNuevaFila).draw(false);     
                    }
                }
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
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"empresa.convenio.controlador.php?op=guardar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_empresa_convenio : $txtIdEmpresaConvenio.val(),
                p_numero_documento : $txtNumeroDocumento.val(),
                p_razon_social : $txtRazonSocial.val()
            },
            success: function(result){
                toastr.success(result.msj);
                var arr = [].slice.call($(tplEmpresasConvenios([result.registro])).find("td")),
                    dataNuevaFila = $.map(arr, function(item) {
                        return item.innerHTML;
                    });

                if (TABLA_EMPRESAS_CONVENIO){
                    if (TR_FILA){ 
                        TABLA_EMPRESAS_CONVENIO
                            .row(TR_FILA)
                            .data(dataNuevaFila)
                            .draw();  
                    } else {
                        TABLA_EMPRESAS_CONVENIO.row.add(dataNuevaFila).draw(false);     
                    }
                }
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

    TABLA_EMPRESAS_CONVENIO  = null;
    this.listar = function(){
        $btnActualizar.prop("disabled", true);
        $overlayTabla.show();

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"empresa.convenio.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                $btnActualizar.prop("disabled", false);
                $overlayTabla.hide();

                if (TABLA_EMPRESAS_CONVENIO){
                    TABLA_EMPRESAS_CONVENIO.destroy();
                }

                $tbdEmpresasConvenios.html(tplEmpresasConvenios(result));
                TABLA_EMPRESAS_CONVENIO = $tblEmpresasConvenios.DataTable({
                    "ordering": false,
                     columns: [
                        { width: "75px "},
                        { width: "135px "},
                        null,
                        { width: "135px "},
                        { width: "135px "},
                        { width: "100px "}
                    ]
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

$(document).ready(function(){
    objEmpresaConvenio = new EmpresaConvenio();
});



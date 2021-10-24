var Servicio = function() {
    var $tblServicios,
        $tbbServicios,
        $btnActualizar;

    var $overlayTabla;

    var $txtFiltroTipoServicio;

    var tplServicios;
    var self = this;
    var TR_FILA = null;

    this.getTemplates = function(){
        var $reqServicios =  $.get("template.servicios.php");
        
        $.when($reqServicios)
            .done(function(resServicios){
                tplServicios = Handlebars.compile(resServicios);
                objServicioGeneral = new ServicioGeneral();
                self.listar();
            })
            .fail(function(error){
                console.error(error);
            });
    };

    this.setDOM = function(){
        $txtFiltroTipoServicio = $("#txt-filtro-tiposervicio");

        $tblServicios = $("#tbl-servicios");
        $tbbServicios  = $("#tbd-servicios");
        $overlayTabla = $("#overlay-tbl-servicios");
        $btnActualizar  =  $("#btn-actualizar-servicios");

    };
    
    this.setEventos = function(){
        $btnActualizar.on("click", function(e){
            e.preventDefault();
            self.listar();
        });

        $txtFiltroTipoServicio.on("change", function(e){
            e.preventDefault();
            self.listar();
        });

        $("#btn-nuevoservicio").on("click", function(e){
            e.preventDefault();
            objServicioGeneral.nuevoRegistro();
        });
        /*

        $("#btn-nuevoexamenlab").on("click", function(e){
            e.preventDefault();
            objExamenLab.nuevoRegistro();
        });

        $("#btn-nuevoperfillab").on("click", function(e){
            e.preventDefault();
            objPerfilLab.nuevoRegistro();
        });
        */

        $tbbServicios.on("click", ".btn-editar", function (e) {
            e.preventDefault();
            self.leer(this.dataset.id, this.dataset.idtiposervicio, $(this).parents("tr"));
        });

        $tbbServicios.on("click", ".btn-eliminar", function (e) {
            e.preventDefault();
            self.anular(this.dataset.id, $(this).parents("tr"));
        });
    };

    this.leer = function(id, id_tipo_servicio, $tr_fila){
        var self = this;

        switch(id_tipo_servicio){
            case "1":
                TR_FILA = $tr_fila;
                objServicioGeneral.leer(id);
            break;
        }

        return;

        if (TABLA_SERVICIOS){
            var _ = {
                "id": 999,
                "descripcion": "XXX",
                "id_tipo_servicio": "3",
                "tipo_servicio" : "PERFIL LAB.",
                "area_categoria": "LABORATORIO",
                "valor_venta" : "100.0000",
                "precio_venta": "118.00"
            };
            var arr = [].slice.call($(tplServicios([_])).find("td")),
                dataNuevaFila = $.map(arr, function(item) {
                    return item.innerHTML;
                });

            TABLA_SERVICIOS
                .row($tr_fila)
                .data(dataNuevaFila)
                .draw();    
        }
    };

    this.actualizarFilaTabla = function(dataRegistro){
        var arr = [].slice.call($(tplServicios([dataRegistro])).find("td")),
            dataFila = $.map(arr, function(item) {
                return item.innerHTML;
            });

        if (TABLA_SERVICIOS){
            if (TR_FILA){ 
                TABLA_SERVICIOS
                    .row(TR_FILA)
                    .data(dataFila)
                    .draw();  
            } else {
                TABLA_SERVICIOS.row.add(dataFila).draw(false);     
            }
        }

        TR_FILA = null;    
    };

    this.anular = function(id_servicio, $mdl_operando = null){
        if (!confirm("¿Está seguro de dar de baja este servicio?")){
            return;
        }
        var self = this;

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"servicio.controlador.php?op=anular",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_servicio : id_servicio
            },
            success: function(result){
                toastr.success(result.msj);
                if (TABLA_SERVICIOS){
                    TABLA_SERVICIOS
                        .row(TR_FILA)
                        .remove()
                        .draw();    
                }

                if ($mdl_operando){
                    $mdl_operando.modal("hide");
                }
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    TABLA_SERVICIOS  = null;
    this.listar = function(){
        $btnActualizar.prop("disabled", true);
        $overlayTabla.show();

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"servicio.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_filtro: $txtFiltroTipoServicio.val()
            },
            success: function(result){
                $btnActualizar.prop("disabled", false);
                $overlayTabla.hide();

                if (TABLA_SERVICIOS){
                    TABLA_SERVICIOS.destroy();
                }

                $tbbServicios.html(tplServicios(result));
                TABLA_SERVICIOS = $tblServicios.DataTable({
                    "ordering":true,
                    "pageLength": 25,
                    "columns": [
                            { "width": "75px" },
                            null,
                            { "width": "135px" },
                            { "width": "115px" },
                            { "width": "115px" },
                            { "width": "115px" }
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

    ARREGLO_TIPO_AFECTACION = null;
    var cargarTipoAfectacion = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"servicio.controlador.php?op=obtener_tipo_afectacion",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                ARREGLO_TIPO_AFECTACION = result;
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    ARREGLO_CATEGORIA_SERVICIO = null;
    var cargarCategoriaServicio = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"categoria.servicio.controlador.php?op=obtener",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_sin_laboratorio: "1"
            },
            success: function(result){
                ARREGLO_CATEGORIA_SERVICIO = result;
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    this.getTemplates();
    this.setDOM();
    this.setEventos();

    cargarTipoAfectacion();
    cargarCategoriaServicio();

    return this;
};


$(document).ready(function(){
    objServicio = new Servicio();
});



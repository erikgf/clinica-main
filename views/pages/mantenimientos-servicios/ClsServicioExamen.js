var ServicioExamen = function(){
    var $mdl,
        $frm,
        $txtIdServicio,
        $txtDescripcion,
        $txtComision,
        $txtIdTipoAfectacion,
        $txtPrecioVenta,
        $txtValorVenta,
        $txtIdMuestra,
        $txtIdSeccion,
        $tblExamenes,
        $btnEliminar,
        $btnGuardar;

    var COMBO_CONSTRUIDO = false,
        tplServiciosExamenFormulario;

    var CACHE_INFO_TABLAS = "";

    this.setInit = function(){
        this.getTemplates();
        return this;
    };

    this.getTemplates = function(){
        var $reqServiciosExamen =  $.get("template.servicios.examen.formulario.php");
        var self = this;

        $.when($reqServiciosExamen)
            .done(function(resServiciosExamen){
                tplServiciosExamenFormulario = Handlebars.compile(resServiciosExamen);
                    
                self.setDOM();
                self.setEventos();
                self.getMuestras();
                self.getSecciones();

                new PrecioVentaComponente({$txtPrecioVenta: $txtPrecioVenta, $txtValorVenta: $txtValorVenta, $txtIdTipoAfectacion : $txtIdTipoAfectacion});                
            })
            .fail(function(error){
                console.error(error);
            });
    };

    this.getMuestras = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.muestra.controlador.php?op=obtener_combo",
            type: "POST",
            dataType: 'json',
            delay: 250,
            success: function(result){
                new SelectComponente({$select : $txtIdMuestra, opcion_vacia: true}).render(result);
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    this.getSecciones = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.seccion.controlador.php?op=obtener_combo",
            type: "POST",
            dataType: 'json',
            delay: 250,
            success: function(result){
                new SelectComponente({$select : $txtIdSeccion, opcion_vacia: true}).render(result);
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    this.setDOM = function(){
        $mdl = $("#mdl-examenlaboratorio");
        $frm = $("#frm-examenlaboratorio");
      
        $txtIdServicio = $("#txt-examenlaboratorio-seleccionado");
        $txtDescripcion = $("#txt-examenlaboratorio-descripcion");
        $txtComision = $("#txt-examenlaboratorio-comision");
        $txtIdTipoAfectacion = $("#txt-examenlaboratorio-tipoafectacion");
        $txtPrecioVenta = $("#txt-examenlaboratorio-precioventa");
        $txtValorVenta = $("#txt-examenlaboratorio-valorventa");
        $txtIdMuestra = $("#txt-examenlaboratorio-muestra");
        $txtIdSeccion = $("#txt-examenlaboratorio-seccion");
        
        $tblExamenes = $("#tbl-examenlaboratorio-examenes");

        $btnEliminar = $("#btn-examenlaboratorio-eliminar");
        $btnGuardar = $("#btn-examenlaboratorio-guardar");
    };

    this.setEventos = function () {
        var self = this;

        $btnEliminar.on("click", function () {
            self.anular($txtIdServicio.val());
        });

        $btnGuardar.on("click", function(e){
            self.guardar();
        });

        $mdl.on("shown.bs.modal", function(e){
            if (!COMBO_CONSTRUIDO){
                new SelectComponente({$select : $txtIdTipoAfectacion, opcion_vacia: false}).render(ARREGLO_TIPO_AFECTACION);

                COMBO_CONSTRUIDO = true;
            }
        });

        $mdl.on("hidden.bs.modal", function(e){
            self.limpiarModal();
        });

        $tblExamenes.on("click", ".btn-agregarfila", function(e){
            e.preventDefault();
            self.agregarNuevaFila(null, false, $(this).parents("tr"));
        });

        $tblExamenes.on("click", ".btn-quitarfila", function(e){
            e.preventDefault();
            self.removerFila($(this).parents("tr"));
        });

        $tblExamenes.on("change", ".txt-nivel", function(e){
            e.preventDefault();
            ajustarSegunNivel($(this));
        });

    };

    this.nuevoRegistro = function(){
        $mdl.modal("show");
        $mdl.find(".modal-title").html("Nuevo Servicio Examen Lab.");

        this.limpiarModal();
    };

    this.limpiarModal = function(){
        $mdl.find("form")[0].reset();
        $btnEliminar.hide();
        $txtComision.val("0.00");
        $txtIdServicio.val("");

        $tblExamenes.find("select.select2").select2("destroy");
        $tblExamenes.find("tbody").empty();

        CACHE_INFO_TABLAS = "";
        this.agregarNuevaFila();        
    };

    this.agregarNuevaFila = function (datosFilas, render = false, $fila_madre = null) {
        if (datosFilas == null){
            datosFilas = [{
                nivel : 1,
                descripcion: "",
                abreviatura: "",
                unidad: "",
                valores_referenciales: "",
                metodo : "",
                eliminar: $tblExamenes.find("tbody tr").length > 0
            }];
        }

        if (!datosFilas.length){
            $tblExamenes.find("tbody").html(tplServiciosExamenFormulario(datosFilas));
            return;
        }

        if(datosFilas.length <= 1 && !render){
            var $fila = $(tplServiciosExamenFormulario(datosFilas));
            $fila.find(".txt-abreviatura").select2({
                ajax: { 
                    url : VARS.URL_CONTROLADOR+"lab.abreviatura.controlador.php?op=buscar_combo",
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            p_cadenabuscar: params.term
                        };
                    },
                    processResults: function (response) {
                        return {results: response};
                    }
                },
                minimumInputLength: 1,
                multiple:false,
                placeholder:"Seleccionar",
                tags: true,
                allowClear: true
            });

            $fila.find(".txt-unidad").select2({
                ajax: { 
                    url : VARS.URL_CONTROLADOR+"lab.unidad.controlador.php?op=buscar_combo",
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            p_cadenabuscar: params.term
                        };
                    },
                    processResults: function (response) {
                        return {results: response};
                    }
                },
                minimumInputLength: 1,
                multiple:false,
                placeholder:"Seleccionar",
                tags: true,
                allowClear: true
            });

            $fila.find(".txt-metodo").select2({
                ajax: { 
                    url : VARS.URL_CONTROLADOR+"lab.metodo.controlador.php?op=buscar_combo",
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            p_cadenabuscar: params.term
                        };
                    },
                    processResults: function (response) {
                        return {results: response};
                    }
                },
                minimumInputLength: 1,
                multiple:false,
                placeholder:"Seleccionar",
                tags: true,
                allowClear: true
            });

            if ($fila_madre == null){
                $tblExamenes.find("tbody").append($fila);
            } else {
                $fila_madre.after($fila);   
            }

            return;
        } else {

            datosFilas[0].eliminar = "0";
            $tblExamenes.find("tbody").html(tplServiciosExamenFormulario(datosFilas));
            $tblExamenes.find(".txt-abreviatura").select2({
                ajax: { 
                    url : VARS.URL_CONTROLADOR+"lab.abreviatura.controlador.php?op=buscar_combo",
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            p_cadenabuscar: params.term
                        };
                    },
                    processResults: function (response) {
                        return {results: response};
                    }
                },
                minimumInputLength: 1,
                multiple:false,
                placeholder:"Seleccionar",
                tags: true,
                allowClear: true
            });
            
            $tblExamenes.find(".txt-unidad").select2({
                ajax: { 
                    url : VARS.URL_CONTROLADOR+"lab.unidad.controlador.php?op=buscar_combo",
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            p_cadenabuscar: params.term
                        };
                    },
                    processResults: function (response) {
                        return {results: response};
                    }
                },
                minimumInputLength: 1,
                multiple:false,
                placeholder:"Seleccionar",
                tags: true,
                allowClear: true
            });

            $tblExamenes.find(".txt-metodo").select2({
                ajax: { 
                    url : VARS.URL_CONTROLADOR+"lab.metodo.controlador.php?op=buscar_combo",
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            p_cadenabuscar: params.term
                        };
                    },
                    processResults: function (response) {
                        return {results: response};
                    }
                },
                minimumInputLength: 1,
                multiple:false,
                placeholder:"Seleccionar",
                tags: true,
                allowClear: true
            });

            $tblExamenes.find("select.select2").each(function(i,o){
                var valor = this.dataset.val;
                if (!(valor == null || valor == "")){
                    $(this).append(new Option(valor, null, true, true)).trigger('change');    
                }
            });

            $tblExamenes.find(".txt-nivel").each(function(i,o){
                ajustarSegunNivel($(this));
            });

            CACHE_INFO_TABLAS  =obtenerCadenaModficacion();
        }
    };

    var obtenerCadenaModficacion = function(){
        var html = "";
        $tblExamenes.find("tbody tr:not(:first)").each(function(i,o){
            var $o = $(o);
            html += $o.find("input").val()+""+$o.find("textarea").val()+$o.find("select").val();
        });
        
        return html.trim();
    };

    this.removerFila = function($tr) {
        $tr.find("select.select2").select2("destroy");
        $tr.remove();
    };  

    this.leer = function(id, $tr_fila){
        var self = this;

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"servicio.controlador.php?op=leer_servicio_examen",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_servicio : id
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
        /*
            Se deberia cargar la cabecera +
            detalle
                El reg
        */
        $mdl.find(".modal-title").html("Editando Servicio Examen Lab.");

        $txtIdServicio.val(data.id_servicio);
        $txtDescripcion.val(data.descripcion);
        $txtComision.val(data.comision);
        $txtIdTipoAfectacion.val(data.idtipo_afectacion);
        $txtValorVenta.val(data.valor_venta);
        $txtPrecioVenta.val(data.precio_venta);
        $txtIdMuestra.val(data.id_lab_muestra);
        $txtIdSeccion.val(data.id_lab_seccion);

        this.agregarNuevaFila(data.detalle, true);

        $btnEliminar.show();
    };

    this.anular = function(id){
        objServicio.anular(id, $mdl);
    };

    this.guardar = function(){
        if(!Util.validarFormulario($frm)){
            toastr.error("No están todos los campos completados.");
            return;
        }

        var arregloDetalle = [];
        $tblExamenes.find("tbody tr").each(function(i,o){
            var $o = $(o);
            arregloDetalle.push({
                id_lab_examen : $o.data("id") ?? "",
                nivel  : $o.find(".txt-nivel").val(),
                descripcion: $o.find(".txt-descripcion").val(),
                abreviatura :$o.find(".txt-abreviatura option:selected").html(),
                unidad :$o.find(".txt-unidad option:selected").html(),
                valores_referenciales: $o.find(".txt-valoresreferenciales").val(),
                metodo :$o.find(".txt-metodo option:selected").html()
            });
        });

        if (!arregloDetalle.length){
            toastr.error("No se están enviando detalle del examen.");
            return;
        }

        var seModificoLaTablaExamenes = obtenerCadenaModficacion() != CACHE_INFO_TABLAS;

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"servicio.controlador.php?op=registrar_examen",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_servicio : $txtIdServicio.val(),
                p_descripcion : $txtDescripcion.val(),
                p_id_tipo_afectacion : $txtIdTipoAfectacion.val(),
                p_id_seccion : $txtIdSeccion.val(),
                p_id_muestra : $txtIdMuestra.val(),
                p_valor_venta : $txtValorVenta.val(),
                p_precio_venta : $txtPrecioVenta.val(),
                p_comision :  $txtComision.val(),
                p_detalle : JSON.stringify(arregloDetalle),
                p_se_modifico: seModificoLaTablaExamenes
            },
            success: function(result){
                toastr.success(result.msj);
                objServicio.actualizarFilaTabla(result.registro);
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

    var ajustarSegunNivel = function($selectNivel){
        var nivel = $selectNivel.val(),
            esNivelDescripcion = nivel == "99";

        var $tr = $selectNivel.parents("tr");

        $tr.find(".txt-descripcion").prop("disabled", esNivelDescripcion);
        $tr.find(".txt-unidad").prop("disabled", esNivelDescripcion);
        $tr.find(".txt-abreviatura").prop("disabled", esNivelDescripcion);
        $tr.find(".txt-metodo").prop("disabled", esNivelDescripcion);
    };

    return this.setInit();
};
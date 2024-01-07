var ExamenLaboratorio = function(){
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

        $btnExportarCSV = $("#btn-exportarcsv");
    };

    this.setEventos = function () {
        var self = this;

        $btnEliminar.on("click", function () {
            self.anular(this.dataset.id);
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
            self.agregarNuevaFila();
        });

        $tblExamenes.on("click", ".btn-quitarfila", function(e){
            e.preventDefault();
            self.removerFila($(this).parents("tr"));
        });

        $tblExamenes.on("change", ".txt-nivel", function(e){
            e.preventDefault();
            ajustarSegunNivel($(this));
        });

        $btnExportarCSV.on("click", ()=>{
            const exportadorCSV = new ExportadorCSV({fileName:"data_examenes_lab"});
            const data = generarDataDesdeTablaParaExportar();
            exportadorCSV.exportar(data);
        });

    };

    const generarDataDesdeTablaParaExportar = () => {
        const $tblBody = $tblExamenes.find("tbody tr").toArray();
        const mapeoColumnas  = {
            "tab": 0,
            "descripcion_examen": 1,
            "abrev": 2,
            "unidad": 3,
            "valores_referenciales": 4,
            "metodo": 5,
            "opc": 6
        };

        const nombreColumnas = [
            "Examen", "Abrev.", "Unidad", "Valores Referenciales", "Metodo"
        ];

        const filas = [];
        $tblBody.forEach(tr=>{
            let fila = [];
            [].slice.call(tr.children).forEach((td, j) => {
                let cadenaBlanco = "";
                if (j === mapeoColumnas.tab){
                    const tabVal =  parseInt($(td).find("select option:selected").text());
                    if (tabVal > 0){
                        for (let index = 0; index < tabVal - 1; index++) {
                            cadenaBlanco += "   ";
                        }    
                    } else {
                        cadenaBlanco = "            ";
                    }
                    
                }

                if (j === mapeoColumnas.descripcion_examen){
                    const valor =  `${cadenaBlanco}${($(td).find("input").val())}`;
                    fila.push(valor || "");
                    return;
                }

                if (j === mapeoColumnas.valores_referenciales){
                    const valor =  $(td).find("textarea").val();
                    fila.push(valor || "");
                    return;
                }

                if (j === mapeoColumnas.abrev || 
                    j === mapeoColumnas.unidad || 
                    j === mapeoColumnas.metodo){
                    const select2Data =  $(td).find("select").select2('data');
                    const valor = select2Data.length > 0 ? select2Data[0].text : "";
                    fila.push(valor);
                    return;
                }
            })

            filas.push(fila);
        });


        return {nombreColumnas, filas};
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

        this.agregarNuevaFila();        
    };

    this.agregarNuevaFila = function (datosFilas, render = false) {
        if (datosFilas == null){
            datosFilas = [{
                nivel : 0,
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
            $tblExamenes.find("tbody").append($fila);

            return;
        } else {
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
                    $(this).append(new Option(this.dataset.val, null, true, true)).trigger('change');    
                }
            });

        }
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
        var self = this;

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
                p_detalle : JSON.stringify(arregloDetalle)
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
var ServicioPerfilExamen = function(){
    var $mdl,
        $frm,
        $txtIdServicio,
        $txtDescripcion,
        $txtPrecioVenta,
        $txtValorVenta,
        $tblExamenes,
        $btnEliminar,
        $btnGuardar;

    var tplServiciosExamenFormulario;
    var NO_DEBO_ACTIVAR_EVENTO_SERVICIO_CHANGE = false;

    this.setInit = function(){
        this.getTemplates();
        return this;
    };

    this.getTemplates = function(){
        var $reqServiciosExamen =  $.get("template.servicios.perfil.examen.formulario.php");
        var self = this;

        $.when($reqServiciosExamen)
            .done(function(resServiciosExamen){
                tplServiciosExamenFormulario = Handlebars.compile(resServiciosExamen);
                    
                self.setDOM();
                self.setEventos();
            })
            .fail(function(error){
                console.error(error);
            });
    };

    this.setDOM = function(){
        $mdl = $("#mdl-perfilexamen");
        $frm = $("#frm-perfilexamen");
      
        $txtIdServicio = $("#txt-perfilexamen-seleccionado");
        $txtDescripcion = $("#txt-perfilexamen-descripcion");
        $txtPrecioVenta = $("#txt-perfilexamen-precioventa");
        $txtValorVenta = $("#txt-perfilexamen-valorventa");
        
        $tblExamenes = $("#tbl-perfilexamen-examenes");

        $btnEliminar = $("#btn-perfilexamen-eliminar");
        $btnGuardar = $("#btn-perfilexamen-guardar");
    };

    this.setEventos = function () {
        var self = this;

        $btnEliminar.on("click", function () {
            self.anular($txtIdServicio.val());
        });

        $btnGuardar.on("click", function(e){
            self.guardar();
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

        $tblExamenes.on("change", ".txt-servicio", function(e){
            e.preventDefault();
            if (NO_DEBO_ACTIVAR_EVENTO_SERVICIO_CHANGE){
                return;
            }
            setearPreciosFila($(this));
        }); 
    };

    this.nuevoRegistro = function(){
        $mdl.modal("show");
        $mdl.find(".modal-title").html("Nuevo Perfil Examen Lab.");

        this.limpiarModal();
    };

    this.limpiarModal = function(){
        $mdl.find("form")[0].reset();
        $btnEliminar.hide();
        $txtIdServicio.val("");

        $tblExamenes.find("select.select2").select2("destroy");
        $tblExamenes.find("tbody").empty();

        this.agregarNuevaFila();        
    };

    this.agregarNuevaFila = function (datosFilas, render = false) {
        if (datosFilas == null){
            datosFilas = [{
                id_servicio : "",
                nombre_servicio : "",
                precio_venta : "0.00",
                valor_venta : "0.00"
            }];
        }

        if (!datosFilas.length){
            $tblExamenes.find("tbody").html(tplServiciosExamenFormulario(datosFilas));
            return;
        }

        if(datosFilas.length <= 1 && !render){
            var $fila = $(tplServiciosExamenFormulario(datosFilas));
            $fila.find(".txt-servicio").select2({
                ajax: { 
                    url : VARS.URL_CONTROLADOR+"servicio.controlador.php?op=buscar_laboratorio_combo",
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
                width: "100%",
                allowClear: true
            });

            $tblExamenes.find("tbody").append($fila);
        } else {
            $tblExamenes.find("tbody").html(tplServiciosExamenFormulario(datosFilas));
            $tblExamenes.find(".txt-servicio").select2({
                ajax: { 
                    url : VARS.URL_CONTROLADOR+"servicio.controlador.php?op=buscar_laboratorio_combo",
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
                width: "100%",
                allowClear: true
            });

            NO_DEBO_ACTIVAR_EVENTO_SERVICIO_CHANGE = true;
            $tblExamenes.find("select.select2").each(function(i,o){
                var valor = o.dataset.val,
                    id = o.dataset.id;
                if (!(valor == null || valor == "")){
                    $(o).append(new Option(valor, id, true, true)).trigger('change');    
                }
            });

            NO_DEBO_ACTIVAR_EVENTO_SERVICIO_CHANGE = false;
        }
    };

    this.removerFila = function($tr) {
        if ($tr.siblings("tr").length <= 0){
            return;
        }
        $tr.find("select.select2").select2("destroy");
        $tr.remove();

        actualizarPrecios();
    };  

    this.leer = function(id, $tr_fila){
        var self = this;

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"servicio.controlador.php?op=leer_servicio_perfil_examen",
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
        $mdl.find(".modal-title").html("Editando Perfil Examen Lab.");

        $txtIdServicio.val(data.id_servicio);
        $txtDescripcion.val(data.descripcion);
        $txtValorVenta.val(data.valor_venta);
        $txtPrecioVenta.val(data.precio_venta);

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
            var $o = $(o),
                idServicio = $o.find(".txt-servicio").val();

            if (idServicio != ""){
                arregloDetalle.push(idServicio);
            }
        });

        if (!arregloDetalle.length){
            toastr.error("No se están enviando detalle del perfil.");
            return;
        }

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"servicio.controlador.php?op=registrar_perfil_examen",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_servicio : $txtIdServicio.val(),
                p_descripcion : $txtDescripcion.val(),
                p_precio_venta : $txtPrecioVenta.val(),
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

    var setearPreciosFila = function($select){
        var  $tr =  $select.parents("tr"),
             id_servicio = $select.val();

        var _setearPrecios = function($tr, valor_venta, precio_venta){
            $tr.find(".txt-valorventa").html(valor_venta);
            $tr.find(".txt-precioventa").html(precio_venta);
            actualizarPrecios();
        };

        if (id_servicio == "" || id_servicio == null){
            _setearPrecios($tr, "0.00", "0.00");
            return;
        }

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"servicio.controlador.php?op=obtener_precios_x_id",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: { 
                p_id_servicio : id_servicio
            },
            success: function(result){
                _setearPrecios($tr, parseFloat(result.valor_venta).toFixed(2), parseFloat(result.precio_venta).toFixed(2));
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    var  actualizarPrecios = function(){
        var totalPrecioVenta = 0.00;
        $tblExamenes.find("tbody tr .txt-precioventa").each(function(i,o){
            totalPrecioVenta = totalPrecioVenta + parseFloat(this.innerHTML);
        });

        $txtPrecioVenta.val(totalPrecioVenta.toFixed(2));
    };

    return this.setInit();
};
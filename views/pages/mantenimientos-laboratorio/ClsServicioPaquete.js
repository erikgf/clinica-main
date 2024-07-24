var ServicioPaquete = function(){
    var $mdl,
        $frm,
        $txtIdServicio,
        $txtDescripcion,
        $txtPrecioVenta,
        $txtValorVenta,
        $tblExamenes,
        $btnEliminar,
        $btnGuardar;

    var $lblTotal, $lblValorVenta;

    var tplServiciosExamenFormulario;
    var NO_DEBO_ACTIVAR_EVENTO_SERVICIO_CHANGE = false;

    const defaultExamenLaboratorio = {
        id_servicio : "",
        nombre_servicio : "",
        precio_venta : "0.00",
        valor_venta : "0.00"
    };

    this.setInit = function(){
        this.getTemplates();
        return this;
    };

    this.getTemplates = function(){
        var $reqServiciosExamen =  $.get("template.servicios.paquete.formulario.php");
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
        $mdl = $("#mdl-paquete");
        $frm = $("#frm-paquete");
      
        $txtIdServicio = $("#txt-paquete-seleccionado");
        $txtDescripcion = $("#txt-paquete-descripcion");
        $txtPrecioVenta = $("#txt-paquete-precioventa");
        $txtValorVenta = $("#txt-paquete-valorventa");
        
        $tblExamenes = $("#tbl-paquete-examenes");

        $btnEliminar = $("#btn-paquete-eliminar");
        $btnGuardar = $("#btn-paquete-guardar");


        $lblValorVenta = $("#lbl-paquete-valorventa");
        $lblTotal = $("#lbl-paquete-total");
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
            self.agregarNuevaFila(undefined, $(this).parents("tr"));
        });

        $tblExamenes.on("click", ".btn-quitarfila", function(e){
            e.preventDefault();
            self.removerFila($(this).parents("tr"));
        });

        $tblExamenes.on("click", ".btn-subirfila", function(e){
            e.preventDefault();
            self.moverFila($(this).parents("tr"), "up");
        });

        $tblExamenes.on("click", ".btn-bajarfila", function(e){
            e.preventDefault();
            self.moverFila($(this).parents("tr"), "down");
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
        $mdl.find(".modal-title").html("Nuevo Paquete");

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

    this.agregarNuevaFila = function (datosFilas = [defaultExamenLaboratorio], $posicion = null, render = false) {
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

            if (!Boolean($posicion)){
                $tblExamenes.find("tbody").append($fila);
            } else {
                $fila.insertAfter($posicion);
            }
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
            url : VARS.URL_CONTROLADOR+"servicio.controlador.php?op=leer_servicio_paquete",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_servicio : id
            },
            success: function(result){
                $mdl.modal("show");
                self.render(result);

                actualizarPrecios();
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
        $mdl.find(".modal-title").html("Editando Paquete");

        $txtIdServicio.val(data.id_servicio);
        $txtDescripcion.val(data.descripcion);
        $txtValorVenta.val(data.valor_venta);
        $txtPrecioVenta.val(data.precio_venta);

        this.agregarNuevaFila(data.detalle, null, true);

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
            toastr.error("No se están enviando los ítems del paquete.");
            return;
        }

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"servicio.controlador.php?op=registrar_paquete",
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
        let totalPrecioVenta = 0.00, totalValorventa = 0.00;

        $tblExamenes.find("tbody tr").each((i, tr) => {
            const $tr = $(tr);
            totalValorventa = totalValorventa + parseFloat($tr.find(".txt-valorventa").html());
            totalPrecioVenta = totalPrecioVenta + parseFloat($tr.find(".txt-precioventa").html());
        });

        $lblValorVenta.html(totalValorventa.toFixed(2));
        $lblTotal.html(totalPrecioVenta.toFixed(2));
    };

    this.moverFila = (tr, direccion = 'up') => {
        const $tr = $(tr);
        $tr.hide();

        if (direccion === "down"){
            $tr.next().after($tr);
        }

        if (direccion === "up"){
            $tr.prev().before($tr);
        }

        $tr.show("fast");
    };


    return this.setInit();
};
var ServicioGeneral = function(){
    var $mdl,
        $frm,
        $txtIdServicio,
        $txtDescripcion,
        $txtDescripcionDetallada,
        $txtCantidadExamenes,
        $txtIdCategoriaServicio,
        $txtComision,
        $txtIdTipoAfectacion,
        $txtPrecioVenta,
        $txtValorVenta,
        $btnEliminar,
        $btnGuardar;
    
    var COMBO_CONSTRUIDO = false;

    this.setInit = function(){
        this.setDOM();
        this.setEventos();

        new PrecioVentaComponente({$txtPrecioVenta: $txtPrecioVenta, $txtValorVenta: $txtValorVenta, $txtIdTipoAfectacion : $txtIdTipoAfectacion});

        return this;
    };

    this.setDOM = function(){
        $mdl = $("#mdl-serviciogeneral");
        $frm = $("#frm-serviciogeneral");
      
        $txtIdServicio = $("#txt-serviciogeneral-seleccionado");
        $txtDescripcion = $("#txt-serviciogeneral-descripcion");
        $txtDescripcionDetallada = $("#txt-serviciogeneral-descripciondetallada");
        $txtCantidadExamenes = $("#txt-serviciogeneral-cantidadexamenes");
        $txtIdCategoriaServicio = $("#txt-serviciogeneral-categoriaservicio");
        $txtComision = $("#txt-serviciogeneral-comision");
        $txtIdTipoAfectacion = $("#txt-serviciogeneral-tipoafectacion");
        $txtPrecioVenta = $("#txt-serviciogeneral-precioventa");
        $txtValorVenta = $("#txt-serviciogeneral-valorventa");

        $btnEliminar = $("#btn-serviciogeneral-eliminar");
        $btnGuardar = $("#btn-serviciogeneral-guardar");
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
                new SelectComponente({$select : $txtIdCategoriaServicio}).render(ARREGLO_CATEGORIA_SERVICIO);
                new SelectComponente({$select : $txtIdTipoAfectacion, opcion_vacia: false}).render(ARREGLO_TIPO_AFECTACION);

                COMBO_CONSTRUIDO = true;
            }
        });

        $mdl.on("hidden.bs.modal", function(e){
            $btnEliminar.hide();
            $mdl.find("form")[0].reset();
        });
    };

    this.nuevoRegistro = function(){
        $mdl.find("form")[0].reset();
        $mdl.modal("show");
        $mdl.find(".modal-title").html("Nuevo Servicio");

        $txtCantidadExamenes.val("1");

        $txtIdServicio.val("");
    };

    this.leer = function(id, $tr_fila){
        var self = this;

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"servicio.controlador.php?op=leer_servicio_general",
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
        $mdl.find(".modal-title").html("Editando Servicio");

        $txtIdServicio.val(data.id_servicio);
        $txtDescripcion.val(data.descripcion);
        $txtDescripcionDetallada.val(data.descripcion_detallada);
        $txtCantidadExamenes.val(data.cantidad_examenes);

        $txtIdCategoriaServicio.val(data.id_categoria_servicio);
        $txtComision.val(data.comision);
        $txtIdTipoAfectacion.val(data.idtipo_afectacion);
        $txtValorVenta.val(data.valor_venta);
        $txtPrecioVenta.val(data.precio_venta);
        
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

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"servicio.controlador.php?op=registrar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_servicio : $txtIdServicio.val(),
                p_descripcion : $txtDescripcion.val(),
                p_descripcion_detallada : $txtDescripcionDetallada.val(),
                p_cantidad_examenes : $txtCantidadExamenes.val(),
                p_id_categoria_servicio : $txtIdCategoriaServicio.val(),
                p_id_tipo_afectacion : $txtIdTipoAfectacion.val(),
                p_valor_venta : $txtValorVenta.val(),
                p_precio_venta : $txtPrecioVenta.val(),
                p_comision :  $txtComision.val()
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

    return this.setInit();
};
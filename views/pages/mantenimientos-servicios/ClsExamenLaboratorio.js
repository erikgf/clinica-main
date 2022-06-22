var ExamenLaboratorio = function(){
    var $mdl,
        $frm,
        $txtIdServicio,
        $txtDescripcion,
        $txtDescripcionDetallada,
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
        this.getMuestras();
        this.getSecciones();
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

                new PrecioVentaComponente({$txtPrecioVenta: $txtPrecioVenta, $txtValorVenta: $txtValorVenta, $txtIdTipoAfectacion : $txtIdTipoAfectacion});                
            })
            .fail(function(error){
                console.error(error);
            });
    };

    this.getMuestras = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.muestra.controlador.php?op=listar_combo",
            type: "POST",
            dataType: 'json',
            delay: 250,
            success: function(result){
                new SelectComponente({$select : $txtIdMuestra, opcion_vacia: false}).render(result);
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
            url : VARS.URL_CONTROLADOR+"lab.seccion.controlador.php?op=listar_combo",
            type: "POST",
            dataType: 'json',
            delay: 250,
            success: function(result){
                new SelectComponente({$select : $txtIdSeccion, opcion_vacia: false}).render(result);
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
        $txtDescripcionDetallada = $("#txt-examenlaboratorio-descripciondetallada");
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
            $btnEliminar.hide();
            $mdl.find("form")[0].reset();
        });

        $tblExamenes.on("click", ".btn-agregarfila", function(e){
            e.preventDefault();
            //agregarFila();
        });

        $tblExamenes.on("click", ".btn-quitarfila", function(e){
            e.preventDefault();
            //quitarFila
        });
    };

    this.nuevoRegistro = function(){
        $mdl.find("form")[0].reset();
        $mdl.modal("show");
        $mdl.find(".modal-title").html("Nuevo Servicio Examen Lab.");

        $txtComision.val("0.00");
        $txtIdServicio.val("");

        $tblExamenes.find("tbody").html(tplServiciosExamenFormulario([{
            nivel : 0,
            descripcion: "",
            abreviatura: "",
            unidad: "",
            valores_referenciales: "",
            metodo : "",
            eliminar_fila: "0"
        }]));
    };

    this.leer = function(id, $tr_fila){
        var self = this;

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"servicio.controlador.php?op=leer_servicio_general",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_servicio : id116
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
        $mdl.find(".modal-title").html("Editando Servicio Examen Lab.");

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
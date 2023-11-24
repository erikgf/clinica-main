var Campaña = function(){
    var $mdl,   
        $txtIdCampaña,
        $txtNombre,
        $txtDescripcion,
        $txtFechaInicio,
        $txtFechaFin,
        $txtSede,
        $txtTipo,
        $txtServicioCategoria,
        $txtClase,
        $txtValor,
        $btnAgregarItem,
        $tblDescuentos,
        $btnEliminar,
        $btnGuardar;

    var $txtMontoMinimo,
        $txtMontoMaximo,
        $txtTipoPago;

    var tplCampañas,
        $tblCampañas,
        $tbdCampañas;

    var $overlayTabla, 
        $btnActualizar;

    this.setInit = function(){
        this.getTemplates();
        return this;
    };

    this.getTemplates = function(){
        var $reqCampañas =  $.get("template.campañas.php");
        var self = this;

        $.when($reqCampañas)
            .done(function(resCampañas){
                tplCampañas = Handlebars.compile(resCampañas);
                
                self.setDOM();
                self.setEventos();
                self.iniciarSelectServicio();
                self.listar();
            })
            .fail(function(error){
                console.error(error);
            });
    };

    this.setDOM = function(){
        $mdl = $("#mdl-campaña");
        $txtIdCampaña = $("#txt-campaña-seleccionado");
        $txtNombre = $("#txt-campaña-nombre");
        $txtDescripcion = $("#txt-campaña-descripcion");
        $txtFechaInicio = $("#txt-campaña-fechainicio");
        $txtFechaFin = $("#txt-campaña-fechafin");
        $txtSede = $("#txt-campaña-sede");
        $txtTipo = $("#txt-campaña-tipo");
        $txtClase = $("#txt-campaña-clase");
        $txtServicioCategoria = $("#txt-campaña-serviciocategoria");
        $txtValor = $("#txt-campaña-valor");

        $tblCampañas = $("#tbl-campañas");
        $tbdCampañas = $("#tbd-campañas");

        $tblDescuentos = $("#tbl-descuentos");
        $btnAgregarItem = $("#btn-campaña-agregaritem");

        $btnEliminar = $("#btn-campaña-eliminar");
        $btnGuardar = $("#btn-campaña-guardar");

        $overlayTabla = $("#overlay-tbl-campañas");
        $btnActualizar =  $("#btn-actualizar-campañas");

        $txtMontoMinimo = $("#txt-campaña-montominimo");
        $txtMontoMaximo  = $("#txt-campaña-montomaximo");
        $txtTipoPago  = $("#txt-campaña-tipopago");
    };

    this.setEventos = function () {
        var self = this;

        $btnActualizar.on("click", function(e){
            e.preventDefault();
            self.listar();
        });

        $("#btn-nuevocampañas").on("click", function(e){
            e.preventDefault();
            self.nuevoRegistro();
        });

        $btnEliminar.on("click", function () {
            self.anular($txtIdCampaña.val());
        });
        
        $btnGuardar.on("click", function(e){
            self.guardar();
        });

        $mdl.on("hidden.bs.modal", function(e){
            $btnEliminar.hide();
            $mdl.find("form")[0].reset();

            $txtClase.val("servicio");
            $txtTipo.val("1");
            $txtValor.val("0.00");
            $txtServicioCategoria.val("").select2("trigger");

            $tblDescuentos.empty();
        });

        $tbdCampañas.on("click", ".btn-editar", function(e){
            e.preventDefault();
            self.leer(this.dataset.id);
        });

        $tbdCampañas.on("click", ".btn-eliminar", function(e){
            e.preventDefault();
            self.anular(this.dataset.id);
        });

        $txtTipo.on("change", function(){
            $txtServicioCategoria.val("").select2("trigger");
            if (this.value === "servicio"){
                self.iniciarSelectServicio();
            } else {
                self.iniciarSelectCategoria();
            }
        });

        $btnAgregarItem.on("click", function(e){
            e.preventDefault();
            self.agregarItem();
        });

        $tblDescuentos.on("click", ".btn-eliminardescuento", function(e){
            e.preventDefault();
            self.quitarItem($(this));
        })
    };

    this.nuevoRegistro = function(){
        $mdl.find("form")[0].reset();
        $mdl.modal("show");
        $mdl.find(".modal-title").html("Nueva Campaña");

        $tblDescuentos.empty();
        $txtIdCampaña.val("");
        $txtTipoPago.val("0");
    };

    this.leer = function(id){
        var self = this;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"campaña.controlador.php?op=leer",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_campaña : id
            },
            success: function(result){
                $mdl.modal("show");
                self.render(result);

                $txtTipo.val("servicio");
                $txtClase.val("1");
                $txtValor.val("0.00");
                
                const resultDescuentos = JSON.parse(result.descuento_categorias_json);
                $tblDescuentos.html(self.renderItems(resultDescuentos));
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
        $mdl.find(".modal-title").html("Editando Campaña");

        $txtIdCampaña.val(data.id_campaña);
        $txtNombre.val(data.nombre);
        $txtDescripcion.val(data.descripcion);
        $txtFechaInicio.val(data.fecha_inicio);
        $txtFechaFin.val(data.fecha_fin);
        $txtSede.val(data.id_sede);

        $txtTipoPago.val(data.tipo_pago);
        $txtMontoMinimo.val(data.monto_minimo);
        $txtMontoMaximo.val(data.monto_maximo);
        
        $btnEliminar.show();
    };

    this.anular = function(id){
        var self = this;

        if (!confirm("¿Está seguro de dar de baja este campaña?")){
            return;
        }

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"campaña.controlador.php?op=anular",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_campaña : id
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

    this.guardar = function(){
        var self = this;


        if ($tblDescuentos.find("tr").length <= 0){
            toastr.error("No hay descuentos ingresados.");
            return;
        }

        var arregloJSON = [];

        $tblDescuentos.find("tr").each(function(i,o){
            const cells = o.children;
            const esPorcentaje = cells[3].dataset.val == "1" ? 1 : 0;
            const descuento = cells[4].dataset.val;
            arregloJSON.push({
                tipo: cells[1].dataset.val,
                id: cells[2].dataset.val,
                descripcion_servicio_categoria: cells[2].dataset.desc,
                porcentaje: esPorcentaje,
                descuento: descuento
            });
        });

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"campaña.controlador.php?op=guardar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_campaña : $txtIdCampaña.val(),
                p_nombre: $txtNombre.val(),
                p_descripcion: $txtDescripcion.val(),
                p_fecha_inicio: $txtFechaInicio.val(),
                p_fecha_fin: $txtFechaFin.val(),
                p_id_sede : $txtSede.val(),
                p_monto_maximo: $txtMontoMaximo.val(),
                p_monto_minimo: $txtMontoMinimo.val(),
                p_tipo_pago: $txtTipoPago.val(),
                p_descuento_categorias_json : JSON.stringify(arregloJSON)
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

    TABLA_CAMPAÑAS  = null;
    this.listar = function(){
        $btnActualizar.prop("disabled", true);
        $overlayTabla.show();

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"campaña.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                $btnActualizar.prop("disabled", false);
                $overlayTabla.hide();

                if (TABLA_CAMPAÑAS){
                    TABLA_CAMPAÑAS.destroy();
                }

                $tbdCampañas.html(tplCampañas(result));
                TABLA_CAMPAÑAS = $tblCampañas.DataTable({
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

    this.iniciarSelectServicio = function(){
        $txtServicioCategoria.select2({
            ajax: { 
                url : VARS.URL_CONTROLADOR+"servicio.controlador.php?op=buscar",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        p_cadenabuscar: params.term
                    };
                },
                processResults: function (response) {
                    return {results: response.datos};
                }
            },
            minimumInputLength: 3,
            width: '100%',
            placeholder:"Seleccionar servicio",
            tags: false,
        });
    };


    this.iniciarSelectCategoria = function(){
        $txtServicioCategoria.select2({
            ajax: { 
                url : VARS.URL_CONTROLADOR+"categoria.servicio.controlador.php?op=buscar",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        p_cadenabuscar: params.term
                    };
                },
                processResults: function (response) {
                    return {results: response.datos};
                }
            },
            minimumInputLength: 1,
            width: '100%',
            placeholder:"Seleccionar categoría",
            tags: false
        });
    };

    this.renderItems = function(arreglo){
        const html = arreglo.map(function(e){
            console.log(e);
          return  `<tr>
                        <td><button class="btn btn-xs btn-danger btn-eliminardescuento">ELIMINAR</button></td>
                        <td data-val="${e.tipo}">${e.tipo === "servicio" ? "SERVICIO" : "CATEGORIA"}</td>
                        <td data-val="${e.id}" data-desc="${e.descripcion_servicio_categoria}">${e.descripcion_servicio_categoria}</td>
                        <td data-val="${e.porcentaje == "1" ? "1" : "0"}">${e.porcentaje == "1" ? "PORCENTAJE" : "MONTO FIJO"}</td>
                        <td data-val="${e.descuento}"> -${e.porcentaje != "1" ? "S/":  ""}${parseFloat(e.descuento * (e.porcentaje == "1" ? 100 : 1)).toFixed(2)}${e.porcentaje == "1" ? "%" : ""}</td>  
                    </tr>`;
        });
        return html;
    };

    this.agregarItem = function(){
        const   id_servicio_categoria =  $txtServicioCategoria.val(),
                descuento = $txtValor.val();

        if (id_servicio_categoria === null || id_servicio_categoria === ""){
            toastr.error("Seleccione un servicio o categoría");
            return;
        }

        if (parseFloat(descuento) <= 0){
            toastr.error("El descuento ingresado debe ser mayor que 0.");
            return;
        }


        const objItem = {
            tipo : $txtTipo.val(),
            porcentaje: $txtClase.val(),
            id: id_servicio_categoria,
            descripcion_servicio_categoria: $txtServicioCategoria.find("option:selected").html(),
            descuento :  $txtClase.val() == '1' ? parseFloat(descuento * 0.01).toFixed(2) : descuento
        };

        $tblDescuentos.append(this.renderItems([objItem]));
    };

    this.quitarItem = function($btnAnular){
        $btnAnular.parents("tr").remove();
    };


    return this.setInit();
};
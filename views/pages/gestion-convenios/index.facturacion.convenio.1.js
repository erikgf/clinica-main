var FacturacionConvenio = function(){
    var $mdl,   
        $txtRazonSocial,
        $txtNumeroDocumento,
        $txtRazonSocial,
        $txtDireccion,
        $txtFechaVencimiento,
        $txtFechaEmision,
        $txtNumeroTicket,
        $chkNumeroTicket,
        $txtMontoCubierto,
        $txtIdTipoComprobante,
        $txtSerieComprobante,
        $txtSerieComprobanteModificado,
        $txtNumeroComprobanteModificado,
        $txtIdTipoMotivo,
        $txtDescripcionMotivo,
        $txtServicio,
        $txtCantidad,
        $txtPrecio,
        $btnAgregar,
        $txtObservaciones,
        $lblSubtotal,
        $lblIGV,
        $lblTotal,
        $btnGuardar,
        $tbdDetalle,
        $txtFormaPago;

    var tplFacturacionConvenio,
        tplFacturacionCuotasConvenio,
        $tblFacturacionConvenio,
        $tbdFacturacionConvenio,
        $tblFacturacionConvenioCuotas;

    var $blkFacturacionConvenioCuotas;

    var self = this;
    var TABLA_AJUSTADA = false;
    var HACE_CUANTOS_DIAS = 7;
    var TICKET_EXISTE = false;
    var IGV = 1.18;
    var HOY;

    
    this.setInit = function(){
        this.setDOM();
        this.setEventos();

        this.getTemplates();
        this.getMotivosNota();
        return this;
    };

    this.getTemplates = function(){
        var $reqFacturacionConvenio =  $.get("template.facturacion.convenio.php");
        var $reqFacturacionConvenioCuotas =  $.get("template.facturacion.cuotas.convenio.php");

        $.when($reqFacturacionConvenio, $reqFacturacionConvenioCuotas)
            .done(function(resFacturacionConvenio, resFacturacionCuotasConvenio){
                tplFacturacionConvenio = Handlebars.compile(resFacturacionConvenio[0]);
                tplFacturacionCuotasConvenio = Handlebars.compile(resFacturacionCuotasConvenio[0]);
                self.listar();

                objTablaCuotasCredito = new TablaCuotasCredito({$el: $blkFacturacionConvenioCuotas, tpl: tplFacturacionCuotasConvenio})
            })
            .fail(function(error){
                console.error(error);
            });
    };

    this.getTplFacturacionConvenio = function(){
        return tplFacturacionConvenio;
    }

    this.getMotivosNota = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"documento.electronico.controlador.php?op=obtener_tipo_motivos_nota",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
               p_id_tipo_nota: "07"
            },
            success: function(result){
                new SelectComponente({$select : $txtIdTipoMotivo, opcion_vacia: true}).render(result);
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
        $mdl = $("#mdl-facturacionconvenio");
        //$txtIdFacturacionConvenio = $("#txt-facturacionconvenio-seleccionado");
        $txtFechaInicio = $("#txt-facturacionconvenio-fechainicio");
        $txtFechaFin = $("#txt-facturacionconvenio-fechafin");

        $txtIdTipoComprobante = $("#txt-facturacionconvenio-idtipocomprobante");
        $txtNumeroDocumento = $("#txt-facturacionconvenio-numerodocumento");
        $txtRazonSocial = $("#txt-facturacionconvenio-razonsocial");
        $txtDireccion = $("#txt-facturacionconvenio-direccion");
        $txtFechaVencimiento = $("#txt-facturacionconvenio-fechavencimiento");
        $txtFechaEmision = $("#txt-facturacionconvenio-fechaemision");
        $txtNumeroTicket = $("#txt-facturacionconvenio-numeroticket");
        $chkNumeroTicket = $("#chk-facturacionconvenio-numeroticket");
        $txtMontoCubierto = $("#txt-facturacionconvenio-montocubierto");

        $txtSerieComprobante = $("#txt-facturacionconvenio-serie");

        $blkMod = $("#blk-facturacionconvenio-mod");
        $txtSerieComprobanteModificado = $("#txt-facturacionconvenio-seriecomprobantemod");
        $txtNumeroComprobanteModificado = $("#txt-facturacionconvenio-numerocomprobantemod");
        $txtIdTipoMotivo = $("#txt-facturacionconvenio-motivomod");
        $txtDescripcionMotivo = $("#txt-facturacionconvenio-descripcionmotivomod");
        
        $txtServicio = $("#txt-facturacionconvenio-servicio");
        $txtCantidad = $("#txt-facturacionconvenio-cantidad");
        $txtPrecio = $("#txt-facturacionconvenio-precio");
        $btnAgregar = $("#btn-agregar-item");
        $tbdDetalle  =$("#tbd-facturacionconvenio-detalle");

        $txtObservaciones = $("#txt-facturacionconvenio-observaciones");
        $lblSubtotal = $("#lbl-facturacionconvenio-subtotal");
        $lblIGV = $("#lbl-facturacionconvenio-igv");
        $lblTotal = $("#lbl-facturacionconvenio-total");

        $btnGuardar = $("#btn-facturacionconvenio-guardar");
        
        $overlayTabla = $("#overlay-tbl-facturacionconvenio");
        $btnActualizar  =  $("#btn-actualizar-facturacionconvenio");
        $btnNuevo = $("#btn-nuevo-facturacionconvenio");

        $tblFacturacionConvenio = $("#tbl-facturacionconvenio");
        $tbdFacturacionConvenio  = $("#tbd-facturacionconvenio");
        $tblFacturacionConvenioCuotas = $("#tbl-facturacionconvenio-cuotascredito");
        $blkFacturacionConvenioCuotas = $("#blk-facturacionconvenio-cuotascredito");

        $txtFormaPago = $("#txt-facturacionconvenio-formapago");
        $txtTipoMoneda = $("#txt-facturacionconvenio-tipomoneda")
        
        var hoy = new Date();
        var haceDias = new Date(hoy.getTime());
        haceDias.setDate(hoy.getDate() - HACE_CUANTOS_DIAS);
        Util.setFecha($txtFechaInicio, haceDias);
        Util.setFecha($txtFechaFin, hoy);

        HOY = hoy;
    };

    this.getLblTotal = function(){
        return $lblTotal;
    };

    this.getTxtFormaPago = function(){
        return $txtFormaPago;
    };

    this.getTxtFechaVencimiento = function(){
        return $txtFechaVencimiento;
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

        $btnNuevo.on("click", function(e){
            e.preventDefault();
            self.nuevoRegistro();
        });

        $txtIdTipoComprobante.on("change", function(e){
            e.preventDefault();
            self.seleccionarNotaCredito(this.value == "07");
        });

        $chkNumeroTicket.on("change", function(e){
            var isChecked = this.checked;
            if (!isChecked){
                $txtNumeroTicket.attr("readonly", true);
                limpiarDatosTrasTicket();
            } else {
                $txtNumeroTicket.attr("readonly", false).select();
            }
        });

        $txtNumeroTicket.on("change", function(){
            if (!$txtNumeroTicket.val().length){
                return;
            }
            consultarTicket($txtNumeroTicket.val());
        }); 

        $txtNumeroDocumento.on("change", function(e){
            consultarDocumento();
        });

        $btnAgregar.on("click", function(e){
            e.preventDefault();
            agregarItem();
        });

        $tbdDetalle.on("click", ".btn-quitar", function(e){
            e.preventDefault();
            quitaritem($(this).parents("tr"));
        });

        $tbdDetalle.on("change", ".txt-cantidad", function(e){
            actualizarFila($(this).parents("tr"));
        });

        $tbdDetalle.on("change", ".txt-precio", function(e){
            actualizarFila($(this).parents("tr"));
        });

        $txtServicio.on("change", function(e){
            setTimeout(() => {
                $txtPrecio.focus().select();
            }, 300);
        });

        $txtServicio.select2({
            ajax: { 
                url : VARS.URL_CONTROLADOR+"servicio.controlador.php?op=buscar",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        p_cadenabuscar: params.term, // search term
                        p_mostrar_precio: false,
                        p_idcategoria: null
                    };
                },
                processResults: function (response) {
                    return {results: response.datos};
                }
            },
            minimumInputLength: 1,
            width: '100%',
            multiple:false,
            placeholder:"Seleccionar",
            tags: true,
            allowClear: true
        });

        $("#tab-facturacionconvenio").on("shown.bs.tab", function(e){
            if (!TABLA_AJUSTADA){
                TABLA_EMPRESAS_CONVENIO.columns.adjust();
                TABLA_AJUSTADA = true;
            }
        });

        $tbdFacturacionConvenio.on("click", "tr .btn-enviarsunat", function(e){
            var $button = $(this),
                $tr = $button.parents("tr");
            e.preventDefault();
            enviarSUNAT($tr.data("id"), $tr.data("comprobante"), $button);
        });

        $tbdFacturacionConvenio.on("click", "tr .btn-modificarpornotacredito", function(e){
            var $button = $(this),
                $tr = $button.parents("tr");
            e.preventDefault();
            modificarPorNotaCredito($tr.data("id"), $tr.data("comprobante"), $button);
        });

        $txtFormaPago.on("click", function(e){
            e.preventDefault();
            if ($txtFormaPago.val() == "0"){
                objTablaCuotasCredito.toggle(false);
            } else {
                objTablaCuotasCredito.toggle(true);
            }

        });
    };

    this.nuevoRegistro = function(){
        $mdl.find("form")[0].reset();
        $mdl.modal("show");
        $mdl.find(".modal-title").html("Nuevo Comprobante");

        $chkNumeroTicket.prop("checked", false);
        $txtNumeroTicket.prop("readonly", true);

        var hoy = new Date();
        Util.setFecha($txtFechaEmision, hoy);
        Util.setFecha($txtFechaVencimiento, hoy);

        limpiarDatosTrasTicket();
    };

    this.guardar = function(){
        var validado = Util.validarFormulario($("#frm-facturacionconvenio"));

        if (!validado){
            return;
        }

        var arregloDetalle = [];
        var $trs = $tbdDetalle.find("tr");
        $trs.each(function(i, o){
            let $precio = o.children[2].children[0],
                $cantidad = o.children[3].children[0],
                esError = false;

            if ($precio.value <= 0.000){
                $precio.classList.add("is-invalid");
                esError = true;
            }

            if ($cantidad.value <= 0.000){
                $cantidad.classList.add("is-invalid");
                esError = true;
            }

            if (esError === false){
                let item = {
                    id_servicio : o.dataset.id ?? null,
                    idunidad_medida : "ZZ",
                    nombre_servicio : o.children[1].innerHTML,
                    precio_unitario : parseFloat($precio.value),
                    cantidad : $cantidad.value,
                    idtipo_afectacion : "10"
                };

                arregloDetalle.push(item); 
            } else {
                setTimeout(function(){
                    $precio.classList.remove("is-invalid");
                    $cantidad.classList.remove("is-invalid");
                }, 4000);
            }
            
        });

        if (!arregloDetalle.length){
            toastr.error("No se están registrando servicios en el comprobante");
            return;
        }

        var arregloCuotas = [];


        if ($txtIdTipoComprobante.val() == "01" && $txtFormaPago.val() == "0"){
            var objCuotas = objTablaCuotasCredito.obtenerDatos();
            if (objCuotas.r == "0"){
                return;
            }

            arregloCuotas = objCuotas.data;
        }

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"documento.electronico.controlador.php?op=guardar_por_convenio",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_numero_ticket : $txtNumeroTicket.val(),
                p_id_tipo_comprobante : $txtIdTipoComprobante.val(),
                p_serie_comprobante : $txtSerieComprobante.val(),
                p_serie_comprobante_modificado : $txtSerieComprobanteModificado.val(),
                p_numero_comprobante_modificado : $txtNumeroComprobanteModificado.val(),
                p_numero_acto_medico : TICKET_EXISTE ? $txtNumeroTicket.val() : "",
                p_id_tipo_motivo : $txtIdTipoMotivo.val(),
                p_descripcion_motivo : $txtDescripcionMotivo.val(),
                p_numero_documento : $txtNumeroDocumento.val(),
                p_razon_social : $txtRazonSocial.val(),
                p_direccion : $txtDireccion.val(),
                p_fecha_emision : $txtFechaEmision.val(),
                p_fecha_vencimiento: $txtFechaVencimiento.val(),
                p_observaciones: $txtObservaciones.val(),
                p_detalle : JSON.stringify(arregloDetalle),
                p_forma_pago : $txtFormaPago.val(),
                p_cuotas: JSON.stringify(arregloCuotas),
                p_importe_total : $lblTotal.html(),
                p_tipo_moneda : $txtTipoMoneda.val()
            },
            success: function(result){
                toastr.success(result.msj);
                $mdl.modal("hide");

                $btnActualizar.click();

                if (result.id){
                    window.open("../../../impresiones/comprobante.a4.pdf.php?id="+result.id);
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

    var TABLA_FACTURACION_CONVENIO  = null;
    this.listar = function(){
        $btnActualizar.prop("disabled", true);
        $overlayTabla.show();

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"documento.electronico.controlador.php?op=listar_comprobantes_convenio",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_fecha_inicio: $txtFechaInicio.val(),
                p_fecha_fin : $txtFechaFin.val()
            },
            success: function(result){
                $btnActualizar.prop("disabled", false);
                $overlayTabla.hide();

                if (TABLA_FACTURACION_CONVENIO){
                    TABLA_FACTURACION_CONVENIO.destroy();
                }

                $tbdFacturacionConvenio.html(tplFacturacionConvenio(result));
                TABLA_FACTURACION_CONVENIO = $tblFacturacionConvenio.DataTable({
                    "order": [[ 5, "desc" ]],
                     columns: [
                        { width: "75px "},
                        { width: "125px "},
                        null,
                        { width: "125px "},
                        { width: "165px "},
                        { width: "135px "},
                        { width: "135px "},
                        { width: "135px "},
                        { width: "135px "},
                        { width: "135px "},
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

    this.seleccionarNotaCredito = function(esSeleccionado){
        if (!esSeleccionado){
            $blkMod.hide();
            $txtSerieComprobanteModificado.attr("required", false).val("");
            $txtNumeroComprobanteModificado.attr("required", false).val("");
            $txtIdTipoMotivo.attr("required", false).val("");
            $txtDescripcionMotivo.attr("required", false).val("");
        } else {
            $blkMod.show();
            $txtSerieComprobanteModificado.attr("required", true).val("");
            $txtNumeroComprobanteModificado.attr("required", true).val("");
            $txtIdTipoMotivo.attr("required", true).val("");
            $txtDescripcionMotivo.attr("required", true).val("");
        }
    }

    var consultarDocumento = function(){
        var $spinner = $("#blk-facturacionconvenio-spinner");
        var fnOK = function(res){
            if (res.respuesta == "error"){
                $spinner.removeClass("fa-spin fa-spinner").addClass("fa-close text-red");
                setTimeout(function(){
                    $spinner.removeClass("fa-close text-red").addClass("fa-spin fa-spinner");
                    $spinner.hide();
                },1500);
                toastr.error(mensajeError);
                $txtNumeroDocumento.select();
                return;
            }

            if (res.respuesta == "ok"){
                if (res.estado != "ACTIVO"){
                    toastr.error("Cliente está usando un RUC NO ACTIVO.");
                }
                $txtRazonSocial.val(res.razon_social);
                $txtDireccion.val(res.direccion);
                $txtRazonSocial.focus();
            }
        };

        new ConsultarDocumento({
            $loader: $spinner,
            idTipoDoc : "6",
            $txtNumeroDocumento :  $txtNumeroDocumento,
            fnOK : fnOK,
            fnError : function(errorMensaje){
                toastr.error(errorMensaje);
            }
        }).buscar();
    };

    var agregarItem = function(){
        if ($txtServicio.val() == "" || $txtServicio.val() == null ||  $txtServicio.val() == "undefined"){
            toastr.error("Servicio no seleccionado.");
            return;
        }

        var objProducto = {
            id: $txtServicio.val(),
            descripcion: $txtServicio.find("option:selected").html(),
            precio : $txtPrecio.val(),
            cantidad: $txtCantidad.val()
        };

        var $html = renderItemHTML([objProducto]);
        $tbdDetalle.append($html);

        recalcularTotal();
        $txtServicio.select2('open');
        $txtPrecio.val("0.00");
        $txtCantidad.val("1");
    };

    var renderItemHTML = function(registros){
        var $html = "";

        for (var i = 0; i < registros.length; i++) {
            let r = registros[i];
            $html += `<tr data-id="${r.id}">
                        <td><button class="btn btn-sm btn-danger btn-quitar" title="Quitar"><i class="fa fa-trash"></i></td>
                        <td>${r.descripcion}</td>
                        <td class="text-right input-group-sm"><input type="number" step="0.01" value="${parseFloat(r.precio).toFixed(2)}" style="max-width:120px" class="form-control txt-precio"/></td>
                        <td class="text-right input-group-sm"><input type="number" step="1" value="${parseFloat(r.cantidad)}" style="max-width:120px" class="form-control txt-cantidad"/></td>
                        <td class="text-right txt-total">${parseFloat(r.cantidad * r.precio).toFixed(2)}</td>
                    </tr>`;

        };

        return $html;
    };

    var quitaritem = function($tr){
        $tr.remove();
        recalcularTotal();
    };

    var recalcularTotal = function(){
        var $trs = $tbdDetalle.find("tr");
        var total = 0, gravadas = 0;

        $trs.each(function(i,o){
            total += parseFloat(o.children[4].innerHTML);
        });

        gravadas = total / IGV;
        $lblTotal.html(parseFloat(total).toFixed(2));
        $lblSubtotal.html(parseFloat(gravadas).toFixed(2));
        $lblIGV.html(parseFloat(total -gravadas).toFixed(2));
    };

    var consultandoTicket = false;
    var consultarTicket = function(numero_ticket){
        if (consultandoTicket) return;
        var $badgeTicket = $("#txt-facturacionconvenio-numeroticketres");
        $badgeTicket.html("Buscando...").removeClass("badge-success badge-danger");
        consultandoTicket  =true;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.controlador.php?op=obtener_ticket_convenio_facturar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_numero_ticket: numero_ticket
            },
            success: function(result){
                consultandoTicket = false;
                $badgeTicket.html(result.msj);

                if (result.registro){
                    var registro = result.registro;
                    $badgeTicket.addClass("badge-success");
                    $txtNumeroDocumento.val(registro.numero_documento).change();
                    $tbdDetalle.html(renderItemHTML(registro.detalle));
                    $txtMontoCubierto.val(registro.monto_cubierto);
                    recalcularTotal();

                    TICKET_EXISTE = true;
                } else {
                    $badgeTicket.addClass("badge-danger");
                    limpiarDatosTrasTicket();
                }

                setTimeout(function(){
                        $badgeTicket.empty().removeClass("badge-success badge-danger");
                    },3000);

            },
             error: function (request) {
                consultandoTicket =false;
                $badgeTicket.empty();
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    var actualizarFila = function($tr){
        var total = parseFloat($tr.find(".txt-cantidad").val() * $tr.find(".txt-precio").val()).toFixed(2);
        $tr.find(".txt-total").html(total);

        recalcularTotal();
    };

    var limpiarDatosTrasTicket = function(){
        TICKET_EXISTE = false;

        $txtMontoCubierto.val("");
        $txtNumeroDocumento.val("");
        $txtRazonSocial.val("");
        $txtDireccion.val("");

        $tbdDetalle.empty();
        self.seleccionarNotaCredito(false);
        recalcularTotal();
    };

    var enviarSUNAT = function(id_documento_electronico, numero_comprobante, $btnEnviar){
        if (!confirm(`¿Desea enviar el comprobante ${numero_comprobante} a SUNAT?`)){
            return;
        }

        const fnActualizarResultado = (result) =>{
            if (result.respuesta == "ok"){
                toastr.success(result.mensaje);
            } else {
                toastr.error(result.mensaje);
            }
            
            var TR_FILA = $btnEnviar.parents("tr");
            var arr = [].slice.call($(tplFacturacionConvenio([result.registro])).find("td")),
                dataNuevaFila = $.map(arr, function(item) {
                    return item.innerHTML;
                });

            if (TABLA_FACTURACION_CONVENIO){
                if (TR_FILA){ 
                    TABLA_FACTURACION_CONVENIO
                        .row(TR_FILA)
                        .data(dataNuevaFila)
                        .draw();  
                } else {
                    TABLA_FACTURACION_CONVENIO.row.add(dataNuevaFila).draw(false);     
                }
            }
        }

        new EnviadorSUNAT({id_documento_electronico: id_documento_electronico, $btnEnviar: $btnEnviar})
                            .enviarSUNAT(fnActualizarResultado);
    };

    var modificarPorNotaCredito = function(id_documento_electronico){
        /*
            consultarPorID

            serie
            serie + comprobante mod
            numero_documento
            detalle

            if (id_atencion_medica_convenio)
        */

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"documento.electronico.controlador.php?op=obtener_documento_electronico_x_id",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_documento_electronico : id_documento_electronico
            },
            success: function(result){
                /*update $tr*/
                if (!result){
                    return;
                }

                self.nuevoRegistro();

                $txtSerieComprobante.val(result.serie);
                $txtIdTipoComprobante.val("07").change();
                $txtSerieComprobanteModificado.val(result.serie);
                $txtNumeroComprobanteModificado.val(result.numero_correlativo);

                $txtNumeroDocumento.val(result.numero_documento_cliente).change();
                $tbdDetalle.html(renderItemHTML(result.detalle));
                recalcularTotal();

            },
                error: function (request) {
                self.enviandoSUNAT = true;
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };
    
    this.crearComprobante = function(numero_ticket){
        this.nuevoRegistro();

        $chkNumeroTicket.prop("checked", true);
        $txtNumeroTicket.val(numero_ticket).change();
    };


    return this.setInit();
};

/*
modificarpornotacredito*/

const TablaCuotasCredito  = function(data){
    var self = this;
    let $el;
    let tpl;

    this.init = function(){
        if (!data){
            return this;
        }
        $el = data.$el;
        tpl = data.tpl;
        setEventos();
        return this;
    };

    this.toggle =  function(esconder = true){
        limpiarCuotasCredito();
        $el && $el[esconder ? "hide": "show"]();
    };

    var setEventos = function(){
        if (!$el){
            return;
        }

        $el.on("click", ".btn-eliminarcuota", function(e){
            e.preventDefault();
            self.quitarFila(this);
        });

        $el.on("click", ".btn-agregarcuota", function(e){
            e.preventDefault();
            self.agregarFila();
        });

        $el.on("change", ".txt-fechapago", function(e){
            if (this.value != ""){
                objFacturacionConvenio.getTxtFechaVencimiento().val(this.value);
            }
        });
    };

    this.obtenerDatos = function(){
        var $trs = $el.find("#tbl-facturacionconvenio-cuotascredito tbody tr");
        var total = parseFloat(objFacturacionConvenio.getLblTotal().html());
        var arregloCuotas = [];
        var monto_cuota_acumulado = 0;
        var fecha_vencimiento;
        $trs.each(function(i,o){
            var monto_cuota = parseFloat(o.cells[2].children[0].value);
            
            fecha_vencimiento = o.cells[3].children[0].value;
            monto_cuota_acumulado = monto_cuota_acumulado + monto_cuota;
            arregloCuotas.push({
                monto_cuota: monto_cuota,
                fecha_vencimiento: fecha_vencimiento
            });
        });

        if (objFacturacionConvenio.getTxtFormaPago().val() == "0"){
            if (monto_cuota_acumulado !== total){
                toastr.error("Se está ingresando montos de cuota que no están acorde al monto de la factura.");
                return {"r": 0};
            }
        }
        
        if (fecha_vencimiento != objFacturacionConvenio.getTxtFechaVencimiento().val()){
            toastr.error("Se está ingresando una  fecha de vencimiento de cuota que no están acorde a la fecha de vencimiento de la factura.");
            return {"r": 0};
        }

        return {"r":1, "data":arregloCuotas};
    };

    this.agregarFila = function(){
       var $tbody = $el.find("#tbl-facturacionconvenio-cuotascredito tbody");
       var numero_cuota = $tbody.find("tr").length;
       $tbody.append(tpl({
        numero_cuota: numero_cuota + 1,
        monto_cuota: "0.00"
       }));
    };

    this.quitarFila = function(button){
        var $tr = $(button).parents("tr");
        $tr.remove();
        resetearNumeroCuotas();
    };

    var resetearNumeroCuotas = function(){
        var $trs = $el.find("#tbl-facturacionconvenio-cuotascredito tbody tr");
        $trs.each(function(i,o){
            o.cells[1].innerHTML = (i + 1);
        });
    };

    var limpiarCuotasCredito = function(){
        $el.find("#tbl-facturacionconvenio-cuotascredito tbody").empty();
    };


    return this.init();
};

/*
const EnviadorSUNAT = function(data){
    var self = this;
    this.enviandoSUNAT = false;
    this.id_documento_electronico = data.id_documento_electronico;
    this.$btnEnviar = data.$btnEnviar;
    var tplFacturacionConvenio = objFacturacionConvenio.getTplFacturacionConvenio();

    this.enviarSUNAT = function(){
        if (this.enviandoSUNAT){
            return;
        }
        this.enviandoSUNAT = true;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"documento.electronico.controlador.php?op=enviar_sunat_x_id",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_documento_electronico : this.id_documento_electronico
            },
            success: function(result){
                self.enviandoSUNAT = false;
                if (result.respuesta == "ok"){
                    toastr.success(result.mensaje);
                } else {
                    toastr.error(result.mensaje);
                }
                
                var TR_FILA = self.$btnEnviar.parents("tr");
                var arr = [].slice.call($(tplFacturacionConvenio([result.registro])).find("td")),
                    dataNuevaFila = $.map(arr, function(item) {
                        return item.innerHTML;
                    });

                if (TABLA_FACTURACION_CONVENIO){
                    if (TR_FILA){ 
                        TABLA_FACTURACION_CONVENIO
                            .row(TR_FILA)
                            .data(dataNuevaFila)
                            .draw();  
                    } else {
                        TABLA_FACTURACION_CONVENIO.row.add(dataNuevaFila).draw(false);     
                    }
                }
            },
             error: function (request) {
                self.enviandoSUNAT = true;
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    return this;
};
*/

$(document).ready(function(){
    objFacturacionConvenio = new FacturacionConvenio();
});


var VerAtencion = function() {
    var $modalVerAtencion,
        $txtIdAtencion,
        $txtPaciente,
        $txtComprobante,
        $txtMedicoOrdenante,
        $txtMedicoRealizante,
        $txtFechaAtencion,
        $tblServicios,
        $txtPagoEfectivo,
        $txtPagoDeposito,
        $blkDeposito,
        $txtBanco,
        $txtNumeroOperacion,
        $txtPagoTarjeta,
        $blkTarjeta,
        $numeroTarjeta,
        $txtNumeroVoucher,
        $lblCajaEfectivo,
        $lblCajaDeposito,
        $lblCajaTarjeta,
        $lblCajaCredito,
        $blkCajaVuelto,
        $lblCajaVuelto,
        $lblCajaTotal,
        $txtObservaciones,
        $btnGuardar;

    var $blkDescuentoEnPago,
        $txtDescuentoEnPago,
        $txtDescuentoMotivo,
        $txtDescuentoAutorizador;

    var $blkFactura,
        $txtFacturaRuc,
        $txtFacturaRazonSocial,
        $txtFacturaDireccion;

    var $btnCambiarMedico;

    var tplServicioAgregadoContinuar;

    var getTemplates = function(){
        $.get("template.servicioagregado.veratencion.php", function(result, state){
            if (state == "success"){
                tplServicioAgregadoContinuar = Handlebars.compile(result);
            }
        });
    };

    this.setDOM = function(){
        $modalVerAtencion = $("#mdl-veratencion");
        $frmVerAtencion = $("#frm-veratencion");
        $txtIdAtencion = $("#txt-idatencion");
        $txtPaciente = $("#txt-paciente");
        $txtComprobante = $("#txt-comprobante");
        $txtFechaAtencion = $("#txt-fechaatencion");
        
        $blkDescuentoEnPago = $("#blk-descuentoenpago");
        $txtDescuentoEnPago = $("#txt-descuentoenpago");
        $txtDescuentoMotivo = $("#txt-descuentomotivo");
        $txtDescuentoAutorizador = $("#txt-descuentousuarioautorizador");

        $txtMedicoOrdenante = $("#txt-medicoordenante");
        $txtMedicoRealizante = $("#txt-medicorealizante");

        $tblServicios = $("#tbl-servicios");
        $txtPagoEfectivo = $("#txt-pagoefectivo");
        $txtPagoDeposito = $("#txt-pagodeposito");
        $blkDeposito = $("#blk-deposito");
        $txtBanco = $("#txt-banco");
        $txtNumeroOperacion = $("#txt-numerooperacion");
        $txtPagoTarjeta = $("#txt-pagotarjeta");
        $blkTarjeta = $("#blk-tarjeta");
        $numeroTarjeta = $(".numero-tarjeta");
        $txtNumeroVoucher = $("#txt-numerovoucher");
        $lblCajaEfectivo = $("#lbl-cajaefectivo");
        $lblCajaDeposito = $("#lbl-cajadeposito");
        $lblCajaTarjeta = $("#lbl-cajatarjeta");
        $lblCajaCredito = $("#lbl-cajacredito");
        $blkCajaVuelto = $("#blk-cajavuelto"); 
        $lblCajaVuelto = $("#lbl-cajavuelto");
        $lblCajaTotal = $("#lbl-cajatotal");

        $txtObservaciones  = $("#txt-observaciones");

        $blkFactura = $("#blk-factura");
        $txtFacturaRuc = $("#txt-facturaruc");
        $txtFacturaRazonSocial  = $("#txt-facturarazonsocial");
        $txtFacturaDireccion  = $("#txt-facturadireccion");

        $btnCambiarMedico = $("#btn-cambiarmedico");
    };
    
    this.setEventos = function(){
        
        $btnCambiarMedico.on("click", function (){
            cambiarMedico();
        });

        $modalVerAtencion.on("hidden.bs.modal", function(){
            $txtIdAtencion.val("");
        });
    };

    var renderServicios = function(data_servicios){        
        $tblServicios.html(tplServicioAgregadoContinuar(data_servicios));
        $("#lbl-cantidadservicios").html("("+data_servicios.length+")");
    }; 

    var GUARDANDO_ATENCION = false;
    var guardarAtencion = function(es_confirmacion){
        var _$btnGuardar;
        
        if (GUARDANDO_ATENCION){
            return;
        }

        if(!confirm("¿Estás seguro de guardar este registro?")){
            return;
        }

        if (es_confirmacion == undefined){
            es_confirmacion = false;
        }

        if (!es_confirmacion){
            _$btnGuardar = $btnGuardar;
        } else{
            _$btnGuardar = $btnGuardarValidacion;
        }
        
        _$btnGuardar.attr("disabled", true);
        GUARDANDO_ATENCION = true;

        if (!OBJETO_ATENCION){
            toastr.error("Hay un problema recuperando los datos del registro.");
            return;
        }

        var numeroTarjeta = "";
        $(".numero-tarjeta").each(function(i,o){
            numeroTarjeta += o.value+" ";
        });

        var idTipoComprobante = $("[name='rad-tipocomprobantepago']:checked").val();
        var objDescuento = OBJETO_ATENCION.objDescuento;

        if (!objDescuento){
            objDescuento = {
                monto_descuento : "0.00",
                id_validador : "",
                motivo : "",
                es_gratuito : false
            };
        }

        var id_usuario_validador_descuento_sin_efectivo = null,
            clave_descuento_sin_efectivo  = "",
            motivo_descuento_sin_efectivo = "";

        if (es_confirmacion){
            id_usuario_validador_descuento_sin_efectivo = $txtValidador.val();
            clave_descuento_sin_efectivo  = $txtClaveValidador.val();
            motivo_descuento_sin_efectivo = $txtMotivoValidacion.val();

            if (!motivo_descuento_sin_efectivo.length){
                toastr.error("Debe ingresar un motivo válido");
                return;
            }

            if (!clave_descuento_sin_efectivo.length){
                toastr.error("Debe ingresar una clave.");
                return;
            }

            if (id_usuario_validador_descuento_sin_efectivo == ""){
                toastr.error("Se debe seleccionar un usuario autorizador para esta atención.")
                return;
            }
        }

        var factura_ruc = $txtFacturaRuc.val(),
            factura_razon_social  = $txtFacturaRazonSocial.val(),
            factura_direccion = $txtFacturaDireccion.val();

        if (idTipoComprobante == "01"){
            if (factura_ruc.length != "11"){
                toastr.error("Se debe escribir un número de RUC válido.")
                return;
            }

            if (!factura_razon_social.length){
                toastr.error("Debe ingresar una razón social válida.")
                return;
            }
        }

        var data_formulario = {
            p_id_paciente : OBJETO_ATENCION.id_paciente,
            p_id_medico_realizante : OBJETO_ATENCION.id_medico_realizante,
            p_id_medico_ordenante : OBJETO_ATENCION.id_medico_ordenante,
            p_fecha_atencion : OBJETO_ATENCION.fecha_atencion,
            p_hora_atencion : OBJETO_ATENCION.hora_atencion,
            p_observaciones : OBJETO_ATENCION.observaciones,            
            p_id_tipo_comprobante : idTipoComprobante,
            p_serie : $txtSerie.val(),
            p_id_caja_instancia : $txtCaja.val(),
            p_pago_efectivo : $txtPagoEfectivo.val(),
            p_pago_deposito : $txtPagoDeposito.val(),
            p_id_banco : $txtBanco.val(),
            p_numero_operacion : $txtNumeroOperacion.val(),
            p_pago_tarjeta : $txtPagoTarjeta.val(),
            p_numero_tarjeta : numeroTarjeta.trim(),
            p_numero_voucher : $txtNumeroVoucher.val(),
            p_servicios : JSON.stringify(OBJETO_ATENCION.servicios),
            p_total : OBJETO_ATENCION.total,
            p_es_gratuito_descuento : objDescuento.es_gratuito,
            p_motivo_descuento : objDescuento.motivo,
            p_monto_descuento : objDescuento.monto_descuento,
            p_id_usuario_validador : objDescuento.id_validador,
            p_id_usuario_validador_descuento_sin_efectivo : id_usuario_validador_descuento_sin_efectivo,
            p_clave_descuento_sin_efectivo : clave_descuento_sin_efectivo,
            p_motivo_descuento_sin_efectivo : motivo_descuento_sin_efectivo,
            p_factura_ruc : factura_ruc,
            p_factura_razon_social : factura_razon_social,
            p_factura_direccion : factura_direccion
        };
        
        $.ajax({ 
                url : VARS.URL_CONTROLADOR+"atencion.medica.controlador.php?op=registrar",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: data_formulario,
                success: function(xhr){
                    toastr.success(xhr.msj);
                   
                    OBJETO_ATENCION = null;

                    if (es_confirmacion){
                        ESTOY_ABRIENDO_MODAL_CONFIRMAR = false;
                        $mdlValidacion.modal("hide");
                    } else {
                        $modalVerAtencion.modal("hide");
                    }
                    
                    objRegistroAtencion.limpiarCamposAtencion();

                    GUARDANDO_ATENCION = false;
                    _$btnGuardar.prop("disabled", false);

                    document.documentElement.scrollTop = 0;
                    var num_tab = 0;
                
                    if (idTipoComprobante == "00"){
                        window.open("../../../impresiones/ticket.atencion.sinprecios.php?id="+xhr.id_atencion_medica, ++num_tab);
                        window.open("../../../impresiones/ticket.atencion.php?id="+xhr.id_atencion_medica, ++num_tab);
                    } else {
                        window.open("../../../impresiones/ticket.atencion.sinprecios.php?id="+xhr.id_atencion_medica, ++num_tab);
                        if (xhr.id_documento_electronico != ""){
                            window.open("../../../impresiones/ticket.comprobante.php?id="+xhr.id_documento_electronico, ++num_tab);
                        }
                        window.open("../../../impresiones/ticket.atencion.php?id="+xhr.id_atencion_medica, ++num_tab);
                    }
                    
                },
                error: function (request, status, error) {
                    GUARDANDO_ATENCION  = false;
                    _$btnGuardar.prop("disabled", false);
                    toastr.error(request.responseText);
                    return;
                },
                cache: true
            }
        );   

    };

    this.obtenerAtencion = function(idAtencion){
        var self  = this;
        $.ajax({ 
                url : VARS.URL_CONTROLADOR+"atencion.medica.controlador.php?op=ver_atencion",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: {
                    p_id_atencion_medica : idAtencion
                },
                success: function(xhr){
                    self.renderAtencion(xhr);
                },
                error: function (request) {
                    toastr.error(request.responseText);
                    return;
                },
                cache: true
            }
        );    
    };

    this.renderAtencion = function(objAtencion){
        if (objAtencion == undefined || objAtencion == null){
            toastr.error("Datos de registro da atención no válidos.");
            return;
        }

        console.log(objAtencion);

        $("#lbl-atencion").html("Nro. RECIBO: "+objAtencion.numero_acto_medico);
        $txtIdAtencion.val(objAtencion.id_atencion_medica);
        $txtPaciente.val(objAtencion.numero_documento_paciente+ " - "+objAtencion.paciente);
        if (objAtencion.comprobante != ""){
            $("#blk-comprobante").show();
            $txtComprobante.val(objAtencion.comprobante);
            if (objAtencion.idtipo_documento_cliente == "01"){
                $blkFactura.show();
                $txtFacturaRuc.val(objAtencion.factura_ruc);
                $txtFacturaRazonSocial.val(objAtencion.factura_razon_social);
                $txtFacturaDireccion.val(objAtencion.factura_direccion);
            } else {
                $blkFactura.hide();
            }
        } else {
            $("#blk-comprobante").hide();
        }

        $txtFechaAtencion.val(objAtencion.fecha_atencion);
        var total = 0;
        total = objAtencion.importe_total;

        if (objAtencion.monto_descuento > 0){
            $txtDescuentoEnPago.val(objAtencion.monto_descuento);
            $txtDescuentoMotivo.val(objAtencion.motivo_descuento);
            $txtDescuentoAutorizador.val(objAtencion.usuario_descuento);
            $blkDescuentoEnPago.show();
        } else {
            $txtDescuentoEnPago.val("0.00");
            $txtDescuentoMotivo.val("");
            $txtDescuentoAutorizador.val("");
            $blkDescuentoEnPago.hide();
        }

        $txtMedicoRealizante.append(new Option(objAtencion.medico_realizante, objAtencion.id_medico_realizante, true, true)).trigger('change');
        $txtMedicoOrdenante.append(new Option(objAtencion.medico_ordenante, objAtencion.id_medico_ordenante, true, true)).trigger('change');

        renderServicios(objAtencion.servicios);
        
        total = parseFloat(Math.round10(total, -2)).toFixed(2);

        $txtPagoEfectivo.val(objAtencion.pago_efectivo);
        $txtPagoDeposito.val(objAtencion.pago_deposito);        
        $("#txt-banco").val(objAtencion.pago_deposito_banco);
        $("#txt-numerooperacion").val(objAtencion.pago_deposito_numerooperacion);
        if (objAtencion.pago_deposito > 0){
            $blkDeposito.show(); 
        } else {
            $blkDeposito.hide();
        }

        $txtPagoTarjeta.val(objAtencion.pago_tarjeta);
        if (objAtencion.pago_tarjeta > 0){
            $blkTarjeta.show(); 
            $("#txt-numerovoucher").val(objAtencion.numero_voucher_tarjeta);

            if (objAtencion.numero_tarjeta != ""){
                var arregloNumTarjetas = objAtencion.numero_tarjeta.split(" ");
                var $numeroTarjeta = $(".numero-tarjeta");
                $numeroTarjeta[0].value = arregloNumTarjetas[0];
                $numeroTarjeta[1].value = arregloNumTarjetas[1];
                $numeroTarjeta[2].value = arregloNumTarjetas[2];
                $numeroTarjeta[3].value = arregloNumTarjetas[3];
            }
        } else {
            $blkTarjeta.hide();
        }
     
        $lblCajaEfectivo.html(objAtencion.pago_efectivo);
        $lblCajaDeposito.html(objAtencion.pago_deposito);
        $lblCajaCredito.html(objAtencion.monto_saldo);
        $lblCajaTarjeta.html(objAtencion.pago_tarjeta);
        $lblCajaTotal.html(total);

        $txtObservaciones.val(objAtencion.observaciones);

        $modalVerAtencion.modal("show");
    };

    var cambiarMedico = function(){
        $btnCambiarMedico.prop("disabled", true);

        $.ajax({ 
                url : VARS.URL_CONTROLADOR+"atencion.medica.controlador.php?op=cambiar_medico",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: {
                    p_id_atencion_medica : $txtIdAtencion.val(),
                    p_id_medico_ordenante : $txtMedicoOrdenante.val(),
                    p_id_medico_realizante : $txtMedicoRealizante.val()
                },
                success: function(xhr){
                    toastr.success(xhr.msj);
                    $btnCambiarMedico.prop("disabled", false);
                },
                error: function (request) {
                    $btnCambiarMedico.prop("disabled", false);
                    toastr.error(request.responseText);
                    return;
                },
                cache: true
            }
        );
    };

    getTemplates();
    this.setDOM();
    this.setEventos();

    $txtMedicoOrdenante.select2({
        dropdownParent: $modalVerAtencion.find(".modal-content"),
        ajax: { 
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=buscar",
            type: "POST",
            dataType: 'json',
            data: function (params) {
                return {
                    p_cadenabuscar: params.term, // search term
                };
            },
            processResults: function (results) {
                return {
                    results: results.datos
                };
            },
        },
        minimumInputLength: 1,
        width: '100%',
        placeholder:"Seleccionar",
    });

    $txtMedicoRealizante.select2({
        dropdownParent: $modalVerAtencion.find(".modal-content"),
        ajax: { 
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=buscar",
            type: "POST",
            dataType: 'json',
            data: function (params) {
                return {
                    p_cadenabuscar: params.term, // search term
                };
            },
            processResults: function (results) {
                return {
                    results: results.datos
                };
            },
        },
        minimumInputLength: 1,
        width: '100%',
        placeholder:"Seleccionar",
    });

    return this;
};

$(document).ready(function(){
    objVerAtencion =  new VerAtencion(); 
});



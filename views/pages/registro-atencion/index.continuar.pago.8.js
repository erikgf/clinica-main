var ContinuarPago = function() {
    var $modalContinuarPago,
        $frmContinuarPago,
        $lblPacientePagar,
        $txtCaja,
        $txtFechaEmision,
        $radTipoComprobantePago,
        $txtSerie,
        $tblServicios,
        $txtPagoEfectivo,
        $txtPagoDeposito,
        $blkDeposito,
        $txtBanco,
        $txtNumeroOperacion,
        $txtFechaDeposito,
        $txtPagoTarjeta,
        $blkTarjeta,
        $numeroTarjeta,
        $txtNumeroVoucher,
        $txtFechaTransaccion,
        $lblCajaEfectivo,
        $lblCajaDeposito,
        $lblCajaTarjeta,
        $lblCajaCredito,
        $blkCajaVuelto,
        $lblCajaVuelto,
        $lblCajaTotal,
        $btnGuardar;

    var $blkDescuentoEnPago,
        $txtDescuentoEnPago;

    var $chkPacienteDiferenteBoleta;

    var $mdlValidacion,
        $btnGuardarValidacion,
        $txtMotivoValidacion,
        $txtValidador,
        $txtClaveValidador;

    var $blkFactura,
        $txtFacturaRuc,
        $txtFacturaRazonSocial,
        $txtFacturaDireccion;

    var $blkBoleta,
        $txtBoletaTipoDocumento,
        $txtBoletaNumeroDocumento,
        $txtBoletaNombres,
        $txtBoletaApellidoPaterno,
        $txtBoletaApellidoMaterno,
        $txtBoletaFechaNacimiento,
        $txtBoletaSexo,
        $txtBoletaDireccion;

    var $blkConvenio,
        $txtConvenioEmpresa,
        $txtConvenioPorcentaje;

    var tplServicioAgregadoContinuar;
    var SERIES = null;
    var ESTOY_ABRIENDO_MODAL_CONFIRMAR = false;
    var atencionPorCredito = false;

    var getTemplates = function(){
        $.get("template.servicioagregado.continuarpago.php", function(result, state){
            if (state == "success"){
                tplServicioAgregadoContinuar = Handlebars.compile(result);
            }
        });
    };

    this.setDOM = function(){
        $modalContinuarPago = $("#mdl-continuarpago");

        $frmContinuarPago = $("#frm-continuarpago");
        $lblPacientePagar = $("#lbl-pacientepagar");
        $txtCaja = $("#txt-caja");
        $txtFechaEmision = $("#txt-fechaemision");
        $radTipoComprobantePago = $("[name='rad-tipocomprobantepago']");
        $txtSerie = $("#txt-serie");
        $tblServicios = $("#tbl-servicios");
        $txtPagoEfectivo = $("#txt-pagoefectivo");
        $txtPagoDeposito = $("#txt-pagodeposito");
        $blkDeposito = $("#blk-deposito");
        $txtBanco = $("#txt-banco");
        $txtNumeroOperacion = $("#txt-numerooperacion");
        $txtFechaDeposito = $("#txt-fechadeposito");
        $txtPagoTarjeta = $("#txt-pagotarjeta");
        $blkTarjeta = $("#blk-tarjeta");
        $numeroTarjeta = $("#numero-tarjeta");
        $txtFechaTransaccion = $("#txt-fechatransacciontarjeta");
        $txtNumeroVoucher = $("#txt-numerovoucher");
        $lblCajaEfectivo = $("#lbl-cajaefectivo");
        $lblCajaDeposito = $("#lbl-cajadeposito");
        $lblCajaTarjeta = $("#lbl-cajatarjeta");
        $lblCajaCredito = $("#lbl-cajacredito");
        $blkCajaVuelto = $("#blk-cajavuelto"); 
        $lblCajaVuelto = $("#lbl-cajavuelto");
        $lblCajaTotal = $("#lbl-cajatotal");

        $blkDescuentoEnPago = $("#blk-descuentoenpago");
        $txtDescuentoEnPago = $("#txt-descuentoenpago");

        $btnGuardar = $modalContinuarPago.find("#btn-guardarpago");

        $mdlValidacion = $("#mdl-validacion");
        $btnGuardarValidacion =  $("#btn-guardarvalidacion");
        $txtMotivoValidacion = $("#txt-motivodescuentovalidacion");
        $txtValidador  = $("#txt-autorizadordescuentovalidacion");
        $txtClaveValidador = $("#txt-clavedescuentovalidacion");

        $blkFactura = $("#blk-factura");
        $txtFacturaRuc = $("#txt-facturaruc");
        $txtFacturaRazonSocial  = $("#txt-facturarazonsocial");
        $txtFacturaDireccion  = $("#txt-facturadireccion");

        $chkPacienteDiferenteBoleta = $("#chk-pacienteboleta");
        $blkBoleta = $("#blk-boleta");
        $txtBoletaTipoDocumento = $("#txt-boletatipodocumento");
        $txtBoletaNumeroDocumento = $("#txt-boletanumerodocumento");
        $txtBoletaNombres = $("#txt-boletanombres");
        $txtBoletaApellidoPaterno = $("#txt-boletaapellidopaterno");
        $txtBoletaApellidoMaterno = $("#txt-boletaapellidomaterno");
        $txtBoletaFechaNacimiento = $("#txt-boletafechanacimiento");
        $txtBoletaSexo = $("#txt-boletasexo");
        $txtBoletaDireccion = $("#txt-boletadireccion");


        $blkConvenio = $("#blk-convenio");
        $txtConvenioEmpresa = $("#txt-convenioempresa");
        $txtConvenioPorcentaje = $("#txt-convenioporcentaje");

    };
    
    this.setEventos = function(){
        $radTipoComprobantePago.on("change", function(){
            if (this.value == "01" || this.value == "03" || this.value == "00"){
                if (this.value == "01" || this.value == "03"){
                    mostrarSerie(true);
                    mostrarBloqueFactura(this.value == "01");
                } else {
                    mostrarSerie(false);
                    mostrarBloqueFactura(false);
                }
                mostrarBloqueConvenio(false);
                mostrarBloqueBoleta(false);
            } else {
                mostrarSerie(false);
                mostrarBloqueBoleta(false);
                mostrarBloqueFactura(false);

                if (this.value == "CO"){
                    if (OBJETO_ATENCION.objDescuento && OBJETO_ATENCION.objDescuento.monto_descuento > 0.00){
                        toastr.error("No se puede seleccionar tipo de pago por CONVENIO, el servicio ya tiene un DESCUENTO previo.")
                        $radTipoComprobantePago[1].checked = true;
                        return;
                    }
                    mostrarBloqueConvenio(true);
                }
            }
            setearSeries(this.value);
            
        });

        $txtCaja.on("change",function () {
            let idCaja = this.value;
            if (idCaja == ""){
                return;
            }
            localStorage.setItem('cache_caja', idCaja);
            let idTipoComprobante = $("[name='rad-tipocomprobantepago']:checked").val();
            setearSeries(idTipoComprobante);
        });

        $txtPagoEfectivo.on("keyup", function () {
            var $cajaPago = $(this);

            if ($cajaPago.val() == ""){
                $cajaPago.val("0.00");
                $cajaPago.select();
            }
        });

        $txtPagoDeposito.on("keyup", function () {
            var esMontoValido = false;
            if ($txtPagoDeposito.val() == ""){
                $txtPagoDeposito.val("0.00");
                $txtPagoDeposito.select();
            }
            
            esMontoValido = parseFloat($txtPagoDeposito.val()) > 0.00;
            $blkDeposito[ esMontoValido ? "show" : "hide"]();

            $txtBanco.prop("required", esMontoValido);
            $txtNumeroOperacion.prop("required", esMontoValido);
            $txtFechaDeposito.prop("required", esMontoValido);
        });

        $txtPagoTarjeta.on("keyup", function (){
            var esMontoValido = false;

            if ($txtPagoTarjeta.val() == ""){
                $txtPagoTarjeta.val("0.00");
                $txtPagoTarjeta.select();
            }   

            esMontoValido = parseFloat($txtPagoTarjeta.val()) > 0.00;
            $blkTarjeta[ esMontoValido ? "show" : "hide"]();
            $numeroTarjeta.prop("required", esMontoValido);
            $txtNumeroVoucher.prop("required", esMontoValido);
            $txtFechaTransaccion.prop("required", esMontoValido);
        });

        $txtPagoEfectivo.on("change",  function () {
            this.value = parseFloat(Math.round10(this.value, -2)).toFixed(2);
            $lblCajaEfectivo.html(this.value);
            cajaResultadoTotal();
        });

        $txtPagoDeposito.on("change",  function () {
            this.value = parseFloat(Math.round10(this.value, -2)).toFixed(2);
            $lblCajaDeposito.html(this.value);
            cajaResultadoTotal();
        });

        $txtPagoTarjeta.on("change",  function () {
            this.value = parseFloat(Math.round10(this.value, -2)).toFixed(2);
            $lblCajaTarjeta.html(this.value);
            cajaResultadoTotal();
        });

        $numeroTarjeta.inputFilter(function(value) {
            return /^\d*$/.test(value);    // Allow digits only, using a RegExp
        });    

        $numeroTarjeta.on("keyup", function(e){
            if (this.value.length >= 4){
                var numeroBloqueTarjeta = parseInt(this.dataset.ntarjeta), 
                    $siguienteInput;

                if (numeroBloqueTarjeta < 4){
                    $siguienteInput = $modalContinuarPago.find(".numero-tarjeta[data-ntarjeta="+(numeroBloqueTarjeta + 1)+"]");
                } else {
                    $siguienteInput = $txtNumeroVoucher;
                }

                if ($siguienteInput){
                    $siguienteInput.focus();
                    $siguienteInput.select();
                }
            }
        });

        $btnGuardar.on("click", function(e){
            e.preventDefault();
            if (Util.validarFormulario($frmContinuarPago)){
                if (OBJETO_ATENCION.objDescuento){
                    if (parseFloat($txtPagoTarjeta.val()) > 0.0000 ||
                        parseFloat($txtPagoDeposito.val()) > 0.0000 ||
                        parseFloat($lblCajaCredito.html()) > 0.0000 ){
                    
                        ESTOY_ABRIENDO_MODAL_CONFIRMAR = true;
                        $modalContinuarPago.modal("hide");
                        setTimeout(function(){
                            $mdlValidacion.modal("show");
                        },1000);

                        return;
                    }
                }
                guardarAtencion(false);
            };
        });

        $btnGuardarValidacion.on("click", function(e){
            e.preventDefault();
            if (Util.validarFormulario($mdlValidacion.find(".modal-content"))){
                guardarAtencion(true);
            };
        });

        $mdlValidacion.on("bs.modal.shown", function(e){
            $mdlValidacion.find("form")[0].reset();
            $txtValidador.val(null).trigger('change');
        });

        $mdlValidacion.on("bs.modal.hidden", function(e){
            if (ESTOY_ABRIENDO_MODAL_CONFIRMAR){
                ESTOY_ABRIENDO_MODAL_CONFIRMAR = false;
                $modalContinuarPago.modal("show");
            }
        });

        var buscandoNumeroDocumentoClienteRuc = false,
            $spinner = $("#blk-spinner-ruc");

        $txtFacturaRuc.on("change", function(e){
            var fnError = function(mensajeError){
                $spinner.removeClass("fa-spin fa-spinner").addClass("fa-close text-red");
                setTimeout(function(){
                    $spinner.removeClass("fa-close text-red").addClass("fa-spin fa-spinner");
                    $spinner.hide();
                },1500);
                toastr.error(mensajeError);
                $txtFacturaRuc.select();
            };

            if (buscandoNumeroDocumentoClienteRuc){
                return;
            }

            var numeroDocumento = $txtFacturaRuc.val(),
                numeroDocumentoLength = numeroDocumento.length;

            if (numeroDocumentoLength != 11){
                return;
            }

            if ($txtFacturaRazonSocial.val() != ""){
                return;
            }

            buscandoNumeroDocumentoClienteRuc = true;
            $spinner.show();
            $.ajax({ 
                url: VARS.URL_CONTROLADOR+"documento.electronico.controlador.php?op=consultar_documento_cliente",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: {
                    p_numero_documento : numeroDocumento
                },
                success: function(res){
                    buscandoNumeroDocumentoClienteRuc = false;
                    if (res.respuesta == "error"){
                        fnError(res.mensaje);
                        return;
                    }
                    $spinner.removeClass("fa-spin fa-spinner").addClass("fa-check text-green");
                    setTimeout(function(){
                        $spinner.removeClass("fa-check text-green").addClass("fa-spin fa-spinner");
                        $spinner.hide();
                    },1500);

                    if (res.respuesta == "ok"){
                        if (res.estado != "ACTIVO"){
                            toastr.error("Cliente está usando un RUC NO ACTIVO.");
                        }
                        $txtFacturaRazonSocial.val(res.razon_social);
                        $txtFacturaDireccion.val(res.direccion.trim());
                        $txtPagoEfectivo.focus();
                        $txtPagoEfectivo.select();
                    }

                },
                error: function (res) {
                    buscandoNumeroDocumentoClienteRuc = false;
                    fnError(res.responseText);
                    return;
                },
                cache: true
            });
        });


        $chkPacienteDiferenteBoleta.on("change", function(e){
            if (!$radTipoComprobantePago[1].checked){
                return;
            }

            mostrarBloqueBoleta(this.checked);
        });

        var buscandoNumeroDocumentoClienteOtro = false,
            $spinnerOtro = $("#blk-spinner-numerodocumento");

        $txtBoletaTipoDocumento.on("change", function(e){
            e.preventDefault();
            $txtBoletaNumeroDocumento.val("");
        });

        $txtBoletaNumeroDocumento.on("change", function(e){
            var fnError = function(mensajeError){
                $spinnerOtro.removeClass("fa-spin fa-spinner").addClass("fa-close text-red");
                setTimeout(function(){
                    $spinnerOtro.removeClass("fa-close text-red").addClass("fa-spin fa-spinner");
                    $spinnerOtro.hide();
                },1500);
                toastr.error(mensajeError);
                $txtFacturaRuc.select();
            };

            if (buscandoNumeroDocumentoClienteOtro){
                return;
            }

            if ($txtBoletaTipoDocumento.val() != "1"){
                return;
            }

            var numeroDocumento = $txtBoletaNumeroDocumento.val(),
                numeroDocumentoLength = numeroDocumento.length;


            if (numeroDocumentoLength != 8){
                return;
            }

            if ($txtBoletaNombres.val() != ""){
                return;
            }

            buscandoNumeroDocumentoClienteOtro = true;
            $spinnerOtro.show();
            $.ajax({ 
                url: VARS.URL_CONTROLADOR+"documento.electronico.controlador.php?op=consultar_documento_cliente",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: {
                    p_numero_documento : numeroDocumento
                },
                success: function(res){
                    buscandoNumeroDocumentoClienteOtro = false;
                    if (res.respuesta == "error"){
                        fnError(res.mensaje);
                        return;
                    }
                    $spinnerOtro.removeClass("fa-spin fa-spinner").addClass("fa-check text-green");
                    setTimeout(function(){
                        $spinnerOtro.removeClass("fa-check text-green").addClass("fa-spin fa-spinner");
                        $spinnerOtro.hide();
                    },1500);


                    if (res.api){
                        var api = res.api;
                        $txtBoletaNombres.val(api.nombres);
                        $txtBoletaApellidoMaterno.val(api.apell_mat);
                        $txtBoletaApellidoPaterno.val(api.apell_pat);
                        $txtBoletaFechaNacimiento.val(Util.formatearFechaCorrectamente(api.fec_nacimiento));
                        $txtBoletaSexo.val(api.sexo);
                        $txtBoletaDireccion.focus();
                        return;
                    }

                },
                error: function (res) {
                    buscandoNumeroDocumentoClienteOtro = false;
                    fnError(res.responseText);
                    return;
                },
                cache: true
            });
        });

        $txtConvenioPorcentaje.on("change", function(e){
            e.preventDefault();
            if (this.value == "" || parseFloat(this.value) <= 0.000000){
                this.value = "0.00";
            }

            var valor = parseFloat(this.value);
            if (valor >= 100.0000){
                this.value = "100.00";
            }

            aplicarDescuentoPorConvenio();
        });

        $modalContinuarPago.on("hidden.bs.modal", (e)=>{
            if (ESTOY_ABRIENDO_MODAL_CONFIRMAR === false){
                OBJETO_ATENCION = null;
                atencionPorCredito = false;
            }
        })
    };

    var mostrarSerie = function(deboMostrarSerie){
        if (deboMostrarSerie){
            $("#blk-serie").show();
            $txtSerie.show();
            $txtSerie.val("");
            $txtSerie.prop("required", true);
        } else {    
            $("#blk-serie").hide();
            $txtSerie.hide();
            $txtSerie.val("");
            $txtSerie.prop("required", false);
        }
    };

    var cajaResultadoTotal = function(){
        let efectivo = parseFloat($lblCajaEfectivo.html());
        let deposito = parseFloat($lblCajaDeposito.html());
        let tarjeta = parseFloat($lblCajaTarjeta.html());

        let total = parseFloat($lblCajaTotal.html());
        let pagoDe = efectivo + deposito + tarjeta;
        let credito;

        if(pagoDe>total){
            Util.forceTwoDecimals(pagoDe - total, $lblCajaVuelto);
            credito = "0";
            $blkCajaVuelto.show();
        } else {
            credito  = parseFloat(total - pagoDe);
            Util.forceTwoDecimals("0", $lblCajaVuelto);
            $blkCajaVuelto.hide();
        }

        Util.forceTwoDecimals(credito, $lblCajaCredito);
        Util.forceTwoDecimals(total, $lblCajaTotal);

        const cambioCredito = credito > 0;
        if (atencionPorCredito == cambioCredito){
            return;
        }

        atencionPorCredito = cambioCredito;
        checkCampañaServicios();
    };

    this.obtenerCajas = function(){
        var self = this;

        if (ID_CAJA_SELECCIONADA == null){
            return;
        }

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"caja.controlador.php?op=obtener_caja_abiertas_validas_registrar_atencion",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_caja : ID_CAJA_SELECCIONADA
            },
            success: function(xhr){
                if( xhr.rpt){
                    var listaCajas = xhr.datos;

                    if (!listaCajas.length){
                        $txtCaja.removeClass("text-red").addClass("bg-gradient-danger");
                        $txtCaja.html(`<option value="">No hay cajas disponibles</option>`);
                        $txtCaja.val("");
                    } else {
                        $txtCaja.removeClass("bg-gradient-danger").addClass("text-red");

                        var html = ``;
                        for (let index = 0; index < listaCajas.length; index++) {
                            const e = listaCajas[index];
                            html += `<option value="${e.id}">${e.descripcion}</option>`
                        }

                        $txtCaja.html(html);
                        self.obtenerSeries(); /*Las series vienen atadas a las cajas - CADA CAJA SOLO TIENE 1 SERIE*/
                    }
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

    this.obtenerSeries = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"serie.documento.controlador.php?op=obtener_series",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_tipocomprobantes : JSON.stringify(["01","03"])
            },
            success: function(xhr){
                if( xhr.rpt){
                    var objSeries = xhr.datos;
                    SERIES = objSeries;
                    setearSeries();
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

    this.renderServicios = function(data_servicios){        
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

        if (!OBJETO_ATENCION){
            toastr.error("Hay un problema recuperando los datos del registro.");
            return;
        }

        const numeroTarjeta = "**** **** **** "+$numeroTarjeta.val();

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
                toastr.error("Se debe escribir un número de RUC válido.");
                return;
            }

            if (!factura_razon_social.length){
                toastr.error("Debe ingresar una razón social válida.");
                return;
            }
        } 

        var boleta_tipo_documento = $txtBoletaTipoDocumento.val(),
            boleta_numero_documento  = $txtBoletaNumeroDocumento.val(),
            boleta_nombres = $txtBoletaNombres.val(),
            boleta_apellido_paterno  = $txtBoletaApellidoPaterno.val(),
            boleta_apellido_materno = $txtBoletaApellidoMaterno.val(),
            boleta_sexo  = $txtBoletaSexo.val(),
            boleta_fecha_nacimiento = $txtBoletaFechaNacimiento.val(),
            boleta_direccion = $txtBoletaDireccion.val();

        if (idTipoComprobante == "03" && $chkPacienteDiferenteBoleta[0].checked){
            if (!boleta_numero_documento.length){
                toastr.error("Debe ingresar número de documento válido.")
                return;
            }

            if (!boleta_nombres.length){
                toastr.error("Debe ingresar nombre válido.")
                return;
            }

            if (!boleta_apellido_paterno.length){
                toastr.error("Debe ingresar apellido paterno válido.")
                return;
            }

            if (!boleta_apellido_materno.length){
                toastr.error("Debe ingresar apellido materno válido.")
                return;
            }
        }

        var id_convenio_empresa =  $txtConvenioEmpresa.val(),
            convenio_porcentaje = $txtConvenioPorcentaje.val();
        
        if ($radTipoComprobantePago[3].checked){
            if (convenio_porcentaje == "0.00"){
                toastr.error("El porcentaje debe estar en 1% y 100%.");
                return;
            }
    
            if (id_convenio_empresa == ""){
                toastr.error("No se ha elegido una empresa de convenio.");
                return;
            }
        }

        _$btnGuardar.attr("disabled", true);
        GUARDANDO_ATENCION = true;

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
            p_fecha_deposito: $txtFechaDeposito.val(),
            p_pago_tarjeta : $txtPagoTarjeta.val(),
            p_numero_tarjeta : numeroTarjeta.trim(),
            p_numero_voucher : $txtNumeroVoucher.val(),
            p_fecha_transaccion: $txtFechaTransaccion.val(),
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
            p_factura_direccion : factura_direccion,
            p_boleta_tipo_documento : boleta_tipo_documento,
            p_boleta_numero_documento : boleta_numero_documento,
            p_boleta_nombres : boleta_nombres,
            p_boleta_apellido_paterno : boleta_apellido_paterno,
            p_boleta_apellido_materno : boleta_apellido_materno,
            p_boleta_sexo : boleta_sexo,
            p_boleta_fecha_nacimiento : boleta_fecha_nacimiento,
            p_boleta_direccion: boleta_direccion,
            p_id_convenio_empresa : id_convenio_empresa,
            p_convenio_porcentaje: convenio_porcentaje
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
                        $modalContinuarPago.modal("hide");
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

    var mostrarBloqueFactura = function(deboMostrar){
        if (deboMostrar){
            $blkFactura.show();
            $txtFacturaRuc.prop("required", true).removeClass("is-invalid");
            $txtFacturaRazonSocial.prop("required", true).removeClass("is-invalid");
            $txtFacturaDireccion.removeClass("is-invalid");
        } else {
            $blkFactura.hide();
            $txtFacturaRuc.prop("required", false).removeClass("is-invalid").val("");
            $txtFacturaRazonSocial.prop("required", false).removeClass("is-invalid").val("");
            $txtFacturaDireccion.removeClass("is-invalid").val("");
        }
    };


    var mostrarBloqueBoleta = function(deboMostrar){
        if (deboMostrar){
            $blkBoleta.show();
            $txtBoletaTipoDocumento.prop("required", true).removeClass("is-invalid");
            $txtBoletaNumeroDocumento.prop("required", true).removeClass("is-invalid");
            $txtBoletaNombres.prop("required", true).removeClass("is-invalid");
            $txtBoletaApellidoMaterno.prop("required", true).removeClass("is-invalid");
            $txtBoletaApellidoPaterno.prop("required", true).removeClass("is-invalid");
            $txtBoletaFechaNacimiento.prop("required", true).removeClass("is-invalid");
            $txtBoletaSexo.prop("required", true).removeClass("is-invalid");
        } else {
            $blkBoleta.hide();
            $txtBoletaTipoDocumento.prop("required", false).removeClass("is-invalid").val("1");
            $txtBoletaNumeroDocumento.prop("required", false).removeClass("is-invalid").val("");
            $txtBoletaNombres.prop("required", false).removeClass("is-invalid").val("");
            $txtBoletaApellidoMaterno.prop("required", false).removeClass("is-invalid").val("");
            $txtBoletaApellidoPaterno.prop("required", false).removeClass("is-invalid").val("");
            $txtBoletaFechaNacimiento.prop("required", false).removeClass("is-invalid").val("");
            $txtBoletaSexo.prop("required", false).removeClass("is-invalid").val("");
        }
    };

    var setearSeries = function(idTipoComprobante = null){
        var listaSeries = [];

        if (!idTipoComprobante){
            idTipoComprobante = $("[name='rad-tipocomprobantepago']:checked").val();
        }

        if (!SERIES){
            toastr.error("No se ha obtenido SERIES válidas.");
            return;
        }

        switch(idTipoComprobante){
            case "00": 
                return;
            case "01":
            case "03":
                listaSeries = $.map( SERIES, function( elemento, i ) {
                    return (elemento.idtipo_comprobante == idTipoComprobante && elemento.id_caja_instancia == $txtCaja.val()) ? elemento : null;
                  });;
                break;
        }

        if (!listaSeries.length){
            $txtSerie.addClass("bg-grandient-danger");
            $txtSerie.html(`<option value="">No hay series disponibles</option>`);
            $txtSerie.val("");
        } else {
            $txtSerie.removeClass("bg-grandient-danger");
            var html = ``;
            for (let index = 0; index < listaSeries.length; index++) {
                const e = listaSeries[index];
                html += `<option value="${e.serie}">${e.serie}</option>`;
            }

            $txtSerie.html(html);
            $txtSerie.find("option:selected").prop("selected", false);
            $txtSerie.find("option:first").prop("selected", true);
        }
    };

    var EMPRESAS_CONVENIO_LOADED = false;
    var mostrarBloqueConvenio = function(deboMostrar){
        if (deboMostrar){
            $blkConvenio.show();
            $txtConvenioEmpresa.prop("required", true).removeClass("is-invalid").val("");
            $txtConvenioPorcentaje.prop("required", true).removeClass("is-invalid");

            if (!EMPRESAS_CONVENIO_LOADED){
                $.ajax({ 
                    url : VARS.URL_CONTROLADOR+"empresa.convenio.controlador.php?op=obtener_combo",
                    type: "POST",
                    dataType: 'json',
                    delay: 250,
                    success: function(resultado){
                        EMPRESAS_CONVENIO_LOADED = true;
                        new SelectComponente({
                            $select : $txtConvenioEmpresa,
                            opcion_vacia: true
                        }).render(resultado);
                    },
                    error: function (request) {
                        toastr.error(request.responseText);
                        return;
                    },
                    cache: true
                    }
                );
                
            }
        } else {
            $blkConvenio.hide();
            $txtConvenioEmpresa.prop("required", false).removeClass("is-invalid").val("");
            $txtConvenioPorcentaje.prop("required", false).removeClass("is-invalid");

            $blkDescuentoEnPago.find("label").html("Descuento");

            if (!OBJETO_ATENCION.objDescuento){
                $blkDescuentoEnPago.hide();
                $txtDescuentoEnPago.val("0.00");
                
                var total = parseFloat(Math.round10(OBJETO_ATENCION.total, -2)).toFixed(2);
                $txtPagoEfectivo.val(total);
                $txtPagoDeposito.val("0.00").trigger("keyup");
                $txtPagoTarjeta.val("0.00").trigger("keyup");

                $lblCajaEfectivo.html(total);
                $lblCajaDeposito.html("0.00");
                $lblCajaCredito.html("0.00");
                $lblCajaTarjeta.html("0.00");
                $lblCajaTotal.html(total);
            }
            
            

        }
        $txtConvenioPorcentaje.val("0.00");
    };

    var aplicarDescuentoPorConvenio = function(){
        var porcentajeDescuento = $txtConvenioPorcentaje.val(),
            total = OBJETO_ATENCION.total,
            montoDescuentoConvenio = total * (porcentajeDescuento / 100);

        total = OBJETO_ATENCION.total - montoDescuentoConvenio;
        $blkDescuentoEnPago.show();
        $txtDescuentoEnPago.val(parseFloat(Math.round10(montoDescuentoConvenio, -2)).toFixed(2));

        total = parseFloat(Math.round10(total, -2)).toFixed(2);

        $txtPagoEfectivo.val(total);
        $txtPagoDeposito.val("0.00").trigger("keyup");
        $txtPagoTarjeta.val("0.00").trigger("keyup");

        $lblCajaEfectivo.html(total);
        $lblCajaDeposito.html("0.00");
        $lblCajaCredito.html("0.00");
        $lblCajaTarjeta.html("0.00");
        $lblCajaTotal.html(total);

        $blkDescuentoEnPago.find("label").html("Descuento POR CONVENIO");
    };
    
    getTemplates();
    this.setDOM();
    this.setEventos();
    this.obtenerCajas();

    $txtValidador.select2({
        dropdownParent: $mdlValidacion.find(".modal-content"),
        ajax: { 
            url : VARS.URL_CONTROLADOR+"usuario.controlador.php?op=obtener_autorizadores_descuentos",
            type: "POST",
            dataType: 'json',
            data: function (params) {
                return {
                    p_cadenabuscar: params.term, // search term
                };
            },
            processResults: function (results) {
                return {
                    results: results
                };
            },
        },
        minimumInputLength: 1,
        width: '100%',
        placeholder:"Seleccionar",
    });

    /*Función que permite activar los protocolos de la interfaz, teniendo como base principal el OBJETO_ATENCION.*/
    this.correr = function(objAtencion){
        if (objAtencion == undefined || objAtencion == null){
            toastr.error("Datos de registro da atención no válidos.");
            return;
        }
        
        $lblPacientePagar.html(objAtencion.numero_documento+ " - "+objAtencion.nombres_completos);
        $txtFechaEmision.val(objAtencion.fecha_atencion);
        
        var idCajaLocalStorage = localStorage.getItem("cache_caja");
        if (idCajaLocalStorage != null && idCajaLocalStorage != ""){
            $txtCaja.val(idCajaLocalStorage);
        }

        var idTipoComprobante = "03";
        $("[name='rad-tipocomprobantepago']")[1].checked = true; // preseleccion boleta

        $chkPacienteDiferenteBoleta[0].checked = false;
        mostrarBloqueFactura(idTipoComprobante == "01");
        mostrarBloqueBoleta(idTipoComprobante == "03" && $chkPacienteDiferenteBoleta[0].checked);
        mostrarSerie( idTipoComprobante != "00");
        setearSeries(idTipoComprobante);

        if (idTipoComprobante == "00"){
            $txtSerie.val("");
        }

        this.renderServicios(objAtencion.servicios);

        var total = 0;
        if (objAtencion.objDescuento){
            total = objAtencion.total - objAtencion.objDescuento.monto_descuento;
            $blkDescuentoEnPago.show();
            $txtDescuentoEnPago.val(parseFloat(Math.round10(objAtencion.objDescuento.monto_descuento, -2)).toFixed(2))
        } else {
            total = objAtencion.total;
            $blkDescuentoEnPago.hide();
            $txtDescuentoEnPago.val("0.00");
        }

        $blkDescuentoEnPago.find("label").html("Descuento");

        total = parseFloat(Math.round10(total, -2)).toFixed(2);

        $txtPagoEfectivo.val(total);
        $txtPagoDeposito.val("0.00").trigger("keyup");
        $txtNumeroOperacion.val("");
        $txtPagoTarjeta.val("0.00").trigger("keyup");
        $txtNumeroVoucher.val("");

        $numeroTarjeta.val("");        

        $lblCajaEfectivo.html(total);
        $lblCajaDeposito.html("0.00");
        $lblCajaCredito.html("0.00");
        $lblCajaTarjeta.html("0.00");
        $lblCajaTotal.html(total);

        $modalContinuarPago.modal("show"); 

        setTimeout(function(){
            $txtCaja.focus();
        },300);

        OBJETO_ATENCION = objAtencion;

        mostrarBloqueConvenio(idTipoComprobante == "CO");
    };

    return this;
};

/*
$(document).ready(function(){
    objContinuarPago =  new ContinuarPago(); 
});
*/



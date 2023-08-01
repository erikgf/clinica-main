var RegistrarIngreso = function() {
    var $modalRegistrarIngreso,
        $frmRegistrarIngreso,
        $txtTipoIngreso,
        $txtFechaRegistro,
        $txtBuscarRecibo,
        $btnBuscarRecibo,
        $blkPagoSaldoPaciente,
        $txtAtencionPaciente,
        $txtAtencionFecha,
        $txtAtencionTotal,
        $txtAtencionAdeudado,
        $txtAtencionPendiente,
        $txtFechaRegistro,
        $radTipoComprobantePago,
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
        $txtObservaciones,
        $btnGuardar;

    var $chkPacienteDiferenteBoleta,
        $blkComprobanteIngreso;

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
        $txtBoletaSexo;

    var OBJETO_ATENCION_BUSCADO = null;

    this.setDOM = function(){
        $modalRegistrarIngreso = $("#mdl-registraringreso");
        $frmRegistrarIngreso = $("#frm-registraringreso");

        $txtCaja = $modalRegistrarIngreso.find("#txt-caja");
        $txtTipoIngreso = $modalRegistrarIngreso.find("#txt-tipoingreso");
        $txtFechaRegistro = $modalRegistrarIngreso.find("#txt-fecharegistro");
        $txtBuscarRecibo = $modalRegistrarIngreso.find("#txt-buscarrecibo");
        $btnBuscarRecibo = $modalRegistrarIngreso.find("#btn-buscarrecibo");
        $txtAtencionPaciente =  $modalRegistrarIngreso.find("#txt-atencionpaciente");
        $txtAtencionFecha = $modalRegistrarIngreso.find("#txt-atencionfecha");
        $txtAtencionTotal =  $modalRegistrarIngreso.find("#txt-atenciontotal");
        $txtAtencionAdeudado = $modalRegistrarIngreso.find("#txt-atencionadeudado");
        $txtAtencionPendiente = $modalRegistrarIngreso.find("#txt-atencionpendiente");
        $blkPagoSaldoPaciente = $modalRegistrarIngreso.find("#blk-pagosaldopaciente");
        $blkComprobanteIngreso = $modalRegistrarIngreso.find("#blk-comprobantepagoingreso");

        $radTipoComprobantePago = $("[name='rad-tipocomprobantepago']");
        $txtPagoEfectivo = $("#txt-pagoefectivo");
        $txtPagoDeposito = $("#txt-pagodeposito");
        $blkDeposito = $("#blk-deposito");
        $txtBanco = $("#txt-banco");
        $txtNumeroOperacion = $("#txt-numerooperacion");
        $txtFechaDeposito = $("#txt-fechadeposito");
        $txtPagoTarjeta = $("#txt-pagotarjeta");
        $blkTarjeta = $("#blk-tarjeta");
        $numeroTarjeta = $(".numero-tarjeta");
        $txtNumeroVoucher = $("#txt-numerovoucher");
        $txtFechaTransaccion = $("#txt-fechatransaccion");
        $lblCajaEfectivo = $("#lbl-cajaefectivo");
        $lblCajaDeposito = $("#lbl-cajadeposito");
        $lblCajaTarjeta = $("#lbl-cajatarjeta");
        //$lblCajaCredito = $("#lbl-cajacredito");
        $blkCajaVuelto = $("#blk-cajavuelto"); 
        $lblCajaVuelto = $("#lbl-cajavuelto");
        $lblCajaTotal = $("#lbl-cajatotal");

        $btnGuardar = $modalRegistrarIngreso.find("#btn-guardaringreso");

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

        $txtObservaciones = $("#txt-ingresos-observaciones");

    };
    

    var validarMontos = false;
    this.setEventos = function(){
        $radTipoComprobantePago.on("change", function(){
            mostrarBloqueFactura(this.value == "01");
            mostrarBloqueBoleta(this.value == "03");
        });

        $txtCaja.on("change",function () {
            let idCaja = this.value;
            if (idCaja == ""){
                return;
            }
            localStorage.setItem('cache_caja', idCaja);
        });

        $txtTipoIngreso.on("change", function(e){
            e.preventDefault();
            var valor = this.value;

            switch(valor){
                case "4":
                    validarMontos = true;
                    $txtObservaciones.prop("required", false);
                    $blkPagoSaldoPaciente.show();
                break;

                case "10":
                    validarMontos = false;
                    $txtObservaciones.prop("required", true);
                    $blkPagoSaldoPaciente.hide();
                break;

                default:
                    validarMontos = false;
                    $blkPagoSaldoPaciente.hide();
                    $txtObservaciones.prop("required", true);
                break;
            }
        });

        $txtBuscarRecibo.on("keypress", function(e){
            if (e.keyCode == 13){
                buscarRecibo();
            }
        });

        $btnBuscarRecibo.on("click", function(e){
            e.preventDefault();
            buscarRecibo();
        });

        $txtPagoEfectivo.on("keyup", function () {
            var $cajaPago = $(this);

            if ($cajaPago.val() == "" || parseFloat($cajaPago.val()) <= 0.00){
                $cajaPago.val("0.00");
                $cajaPago.select();
            }
        });

        $txtPagoDeposito.on("keyup", function () {
            var esMontoValido = false;

            if ($txtPagoDeposito.val() == "" || parseFloat($txtPagoDeposito.val()) <= 0.00){
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

            if ($txtPagoTarjeta.val() == "" || parseFloat($txtPagoTarjeta.val()) <= 0.00){
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
                    $siguienteInput = $modalRegistrarIngreso.find(".numero-tarjeta[data-ntarjeta="+(numeroBloqueTarjeta + 1)+"]");
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
            if (Util.validarFormulario($frmRegistrarIngreso)){                
                guardarIngreso(false);
            };
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

    };

    var cajaResultadoTotal = function(){
        let efectivo = parseFloat($lblCajaEfectivo.html());
        let deposito = parseFloat($lblCajaDeposito.html());
        let tarjeta = parseFloat($lblCajaTarjeta.html());

        let total = parseFloat($lblCajaTotal.html());
        let pagoDe = efectivo + deposito + tarjeta;

        if (!validarMontos){
            Util.forceTwoDecimals(pagoDe, $lblCajaTotal);
            return;
        }

        if(pagoDe>total){
            Util.forceTwoDecimals(pagoDe - total, $lblCajaVuelto);
            //Util.forceTwoDecimals("0", $lblCajaCredito);
            $txtAtencionPendiente.html("0.00");
            $txtAtencionPendiente.removeClass("bg-warning");
            $blkCajaVuelto.show();
        } else {
            var montoPendiente = parseFloat(total - pagoDe);
            Util.forceTwoDecimals("0", $lblCajaVuelto);
            //Util.forceTwoDecimals(total - pagoDe, $lblCajaCredito);
            $txtAtencionPendiente.html(montoPendiente.toFixed(2));
            $txtAtencionPendiente.addClass("bg-warning");
            $blkCajaVuelto.hide();

            if (montoPendiente <= 0){
                $blkComprobanteIngreso.show();
            } else {
                $blkComprobanteIngreso.hide();
            }

        }
        
        Util.forceTwoDecimals(total, $lblCajaTotal);

    };

  
    var GUARDANDO_INGRESO = false;
    var guardarIngreso = function(){
        var tipoIngreso = $txtTipoIngreso.val();

        if (GUARDANDO_INGRESO){
            return;
        }

        if(!confirm("¿Estás seguro de guardar este ingreso?")){
            return;
        }
        
        $btnGuardar.attr("disabled", true);
        GUARDANDO_INGRESO = true;

        if (tipoIngreso == "4" && !OBJETO_ATENCION_BUSCADO){
            toastr.error("Hay un problema recuperando la atención buscada.");
            return;
        }

        var numeroTarjeta = "";
        $(".numero-tarjeta").each(function(i,o){
            numeroTarjeta += o.value+" ";
        });

        var idTipoComprobante = $("[name='rad-tipocomprobantepago']:checked").val();
        var factura_ruc = $txtFacturaRuc.val(),
            factura_razon_social  = $txtFacturaRazonSocial.val(),
            factura_direccion = $txtFacturaDireccion.val();

        var boleta_tipo_documento = $txtBoletaTipoDocumento.val(),
            boleta_numero_documento  = $txtBoletaNumeroDocumento.val(),
            boleta_nombres = $txtBoletaNombres.val(),
            boleta_apellido_paterno  = $txtBoletaApellidoPaterno.val(),
            boleta_apellido_materno = $txtBoletaApellidoMaterno.val(),
            boleta_sexo  = $txtBoletaSexo.val(),
            boleta_fecha_nacimiento = $txtBoletaFechaNacimiento.val();

        if (tipoIngreso == "4"){
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
        }

        var data_formulario = {
            p_id_atencion_medica : OBJETO_ATENCION_BUSCADO ? OBJETO_ATENCION_BUSCADO.id_atencion_medica : null,
            p_id_tipo_comprobante : idTipoComprobante,
            p_id_caja_instancia : $txtCaja.val(),
            p_monto_efectivo : $txtPagoEfectivo.val(),
            p_monto_deposito : $txtPagoDeposito.val(),
            p_id_banco : $txtBanco.val(),
            p_numero_operacion : $txtNumeroOperacion.val(),
            p_fecha_deposito: $txtFechaDeposito.val(),
            p_monto_tarjeta : $txtPagoTarjeta.val(),
            p_numero_tarjeta : numeroTarjeta.trim(),
            p_numero_voucher : $txtNumeroVoucher.val(),
            p_fecha_transaccion: $txtFechaTransaccion.val(),
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
            p_observaciones : $txtObservaciones.val(),
            p_id_tipo_movimiento : tipoIngreso
        };
        
        $.ajax({ 
                url : VARS.URL_CONTROLADOR+"caja.movimiento.controlador.php?op=registrar_ingreso",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: data_formulario,
                success: function(xhr){
                    toastr.success(xhr.msj);
                   
                    OBJETO_ATENCION_BUSCADO = null;
                    $modalRegistrarIngreso.modal("hide");

                    GUARDANDO_INGRESO = false;
                    $btnGuardar.prop("disabled", false);

                    document.documentElement.scrollTop = 0;

                    var num_tab = 0;
                
                    if (idTipoComprobante != "00"){
                        if (xhr.id_documento_electronico_registrado && xhr.id_documento_electronico_registrado != ""){
                            window.open("../../../impresiones/ticket.comprobante.php?id="+xhr.id_documento_electronico_registrado, ++num_tab);
                        }
                    }

                    $("#btn-actualizarmovimientos").click();
                },
                error: function (request, status, error) {
                    GUARDANDO_INGRESO  = false;
                    $btnGuardar.prop("disabled", false);
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

    /*Función que permite activar los protocolos de la interfaz, teniendo como base principal el OBJETO_ATENCION.*/
    this.correr = function(objAtencion){
        var hoy = new Date();
        Util.setFecha($txtFechaRegistro, hoy);

        var idCajaLocalStorage = localStorage.getItem("cache_caja");
        if (idCajaLocalStorage != null && idCajaLocalStorage != ""){
            $txtCaja.val(idCajaLocalStorage);
        }

        $txtTipoIngreso.val("");
        $blkPagoSaldoPaciente.hide();
        $blkComprobanteIngreso.hide();

        $lblCajaEfectivo.html("0.00");
        $lblCajaDeposito.html("0.00");
        $lblCajaTarjeta.html("0.00");
        $lblCajaTotal.html("0.00");


        $txtBuscarRecibo.val("");
        $txtAtencionPaciente.html("-");
        $txtAtencionFecha.html("-");
        $txtAtencionTotal.html("-");
        $txtAtencionAdeudado.html("-");
        $txtAtencionPendiente.html("-");

        $txtPagoEfectivo.val("0.00");
        $lblCajaEfectivo.html("0.00");
        $lblCajaTotal.html("0.00");

        $modalRegistrarIngreso.modal("show"); 

        setTimeout(function(){
            $txtCaja.focus();
        },300);

        OBJETO_ATENCION_BUSCADO = null;
        /*
        if (objAtencion == undefined || objAtencion == null){
            toastr.error("Datos de registro da atención no válidos.");
            return;
        }
        
        $lblPacientePagar.html(objAtencion.numero_documento+ " - "+objAtencion.nombres_completos);
        
        var idCajaLocalStorage = localStorage.getItem("cache_caja");
        if (idCajaLocalStorage != null && idCajaLocalStorage != ""){
            $txtCaja.val(idCajaLocalStorage);
        }

        var idTipoComprobante = "03";
        $("[name='rad-tipocomprobantepago']")[1].checked = true; // preseleccion boleta

        $chkPacienteDiferenteBoleta[0].checked = false;
        mostrarBloqueFactura(idTipoComprobante == "01");
        mostrarBloqueBoleta(idTipoComprobante == "03" && $chkPacienteDiferenteBoleta[0].checked);

        total = parseFloat(Math.round10(total, -2)).toFixed(2);

        $txtPagoEfectivo.val(total);
        $txtPagoDeposito.val("0.00").trigger("keyup");
        $txtPagoTarjeta.val("0.00").trigger("keyup");

        $lblCajaEfectivo.html(total);
        $lblCajaDeposito.html("0.00");
        $lblCajaCredito.html("0.00");
        $lblCajaTarjeta.html("0.00");
        $lblCajaTotal.html(total);

        $modalRegistrarIngreso.modal("show"); 

        setTimeout(function(){
            $txtCaja.focus();
        },300);

        OBJETO_ATENCION = objAtencion;
        */
    };

    var buscarRecibo = function(){
        var fnOK = function (res) {
            $txtAtencionPaciente.html(res.nombre_paciente);
            $txtAtencionFecha.html(res.fecha_atencion);
            $txtAtencionTotal.html(res.importe_total);

            var monto_deuda = parseFloat(res.monto_credito - res.monto_pagado).toFixed(2);
            $txtAtencionAdeudado.html(monto_deuda);
            $txtAtencionPendiente.html(monto_deuda);

            $txtPagoEfectivo.val(monto_deuda);
            $lblCajaEfectivo.html(monto_deuda);
            $lblCajaTotal.html(monto_deuda);

            if (monto_deuda <= 0){
                $txtAtencionAdeudado.addClass("text-green").removeClass("text-red");
                $blkComprobanteIngreso.hide();
            } else {
                $txtAtencionAdeudado.addClass("text-red").removeClass("text-green");
                $blkComprobanteIngreso.show();
            }
            OBJETO_ATENCION_BUSCADO = res;
        };

        var fnError = function(error){
            toastr.error(error);
        };

        objRecibo.buscarRecibo(fnOK, fnError);
    };

    this.setDOM();
    this.setEventos();
    var objRecibo = new ClsRecibo({
                $btnBuscarRecibo: $btnBuscarRecibo,
                $txtBuscarRecibo : $txtBuscarRecibo, 
                tipo_movimiento : "ingreso"
            });

    return this;
};



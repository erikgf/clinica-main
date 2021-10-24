var ContinuarPago = function() {
    var self = this;
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
        $btnGuardar;

    var tplServicioAgregadoContinuar;
    var ULTIMO_VALOR_TOTAL = null;
    var SERIES = null;
    var OBJETO_ATENCION;

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

        $btnGuardar = $modalContinuarPago.find("#btn-guardarpago");
    };
    
    this.setEventos = function(){
        $radTipoComprobantePago.on("change", function(){
            mostrarSerie(this.value != "00");
            setearSeries(this.value, true);
        });

        $txtCaja.on("change",function () {
            let idCaja = this.value;
            if (idCaja == ""){
                return;
            }
            localStorage.setItem('cache_caja', idCaja);
        });

        $txtSerie.on("change",function () {
            let serie = this.value;
            let idTipoComprobante = $("[name='rad-tipocomprobantepago']:checked").val();

            if (idTipoComprobante == "00") return;
            localStorage.setItem(idTipoComprobante == "01" ? "cache_serie_factura" : "cache_serie_boleta" , serie);
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

            console.log(esMontoValido, $txtBanco);
            $txtBanco.prop("required", esMontoValido);
            $txtNumeroOperacion.prop("required", esMontoValido);
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
                guardarAtencion();
            };
        });
    };


    /*

        at init
            load cajas abiertas : print simple no select2
            load series disponibles (segun fact + bol)
            data
                facturas : []
                boletas : []

        opening modal
            load OBJETO_ATENCION  from another page
            if (OBJETO_ATENCION == NULL) do nothing alerta and recomendacion de volver a cargar
            else
                set numero historia - paciente 
                servicios: load tempalte
                calcular totales
                set  : fecha emision

            if (caché total != neo total){
                efectivo = neo total
                credito = 0.00 (trigger change)
                tarjeta = 0.00 (trgger change)

                render Totales
            }

        events

    */
    var mostrarSerie = function(deboMostrarSerie){
        if (deboMostrarSerie){
            $txtSerie.show();
            $txtSerie.val("");
            $txtSerie.prop("required", true);
        } else {
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

        if(pagoDe>total){
            Util.forceTwoDecimals(pagoDe - total, $lblCajaVuelto);
            Util.forceTwoDecimals("0", $lblCajaCredito);
            $blkCajaVuelto.show();
        } else {
            Util.forceTwoDecimals("0", $lblCajaVuelto);
            Util.forceTwoDecimals(total - pagoDe, $lblCajaCredito);
            $blkCajaVuelto.hide();
        }
     
        Util.forceTwoDecimals(total, $lblCajaTotal);
    };

    this.obtenerCajas = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"caja.controlador.php?op=obtener_caja_abiertas_validas",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
            },
            success: function(xhr){
                if( xhr.rpt){
                    var listaCajas = xhr.datos;

                    if (!listaCajas.length){
                        $txtCaja.removeClass("bg-gradient-info").addClass("bg-gradient-danger");
                        $txtCaja.html(`<option value="">No hay cajas disponibles</option>`);
                    } else {
                        $txtCaja.removeClass("bg-gradient-danger").addClass("bg-gradient-info");

                        var html = ``;
                        for (let index = 0; index < listaCajas.length; index++) {
                            const e = listaCajas[index];
                            html += `<option value="${e.id}">${e.descripcion}</option>`
                        }

                        $txtCaja.html(html);

                        var idCajaLocalStorage = localStorage.getItem("cache_caja");
                        if (idCajaLocalStorage != null){
                            $txtCaja.val(idCajaLocalStorage);
                        }
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

    var renderServicios = function(data_servicios){        
        $tblServicios.html(tplServicioAgregadoContinuar(data_servicios));

        $("#lbl-cantidadservicios").html("("+data_servicios.length+")");
    }; 

    var limpiarCampos = function(){
        var  $blkAlertarEdicion = $("#blk-alertaredicion");
        $blkAlertarEdicion.hide();
        $blkAlertarEdicion.find("#lbl-nombreusuario").empty();

        $txtBuscarPaciente.focus();
        $txtBuscarPaciente.val(null).trigger("change");
        $blkAlertarEdicion = null;

        EVITAR_EVENTO_CAMBIAR_UBIGEO = true;
        $frmPaciente[0].reset();

        $txtDepartamento.val(null).trigger("change");
        $txtProvincia.val(null).trigger("change");
        $txtDistrito.val(null).trigger("change");
        EVITAR_EVENTO_CAMBIAR_UBIGEO = false;

        $btnEliminar.hide();
    };

    var GUARDANDO_ATENCION = false;
    var guardarAtencion = function(){
        if (GUARDANDO_ATENCION){
            return;
        }

        $btnGuardar.attr("disabled", true);
        GUARDANDO_ATENCION = true;

        if(!confirm("¿Estás seguro de guardar este registro?")){
            return;
        }

        if (!OBJETO_ATENCION){
            toastr.error("Hay un problema recuperando los datos del registro.");
            return;
        }

        var numeroTarjeta = "";
        $(".numero-tarjeta").each(function(i,o){
            numeroTarjeta += o.value+" ";
        });

        var data_formulario = {
            p_id_paciente : OBJETO_ATENCION.id_paciente,
            p_id_medico_realizante : OBJETO_ATENCION.id_medico_realizante,
            p_id_medico_ordenante : OBJETO_ATENCION.id_medico_ordenante,
            p_fecha_atencion : OBJETO_ATENCION.fecha_atencion,
            p_hora_atencion : OBJETO_ATENCION.hora_atencion,
            p_observaciones : OBJETO_ATENCION.observaciones,            
            p_id_tipo_comprobante : $("[name='rad-tipocomprobantepago']:checked").val(),
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
            p_total : $lblCajaTotal.html()
        };

        $.ajax({ 
                url : VARS.URL_CONTROLADOR+"atencion.medica.controlador.php?op=registrar",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: data_formulario,
                success: function(xhr){
                    toastr.success(xhr.msj);
                    /*
                        si ok
                        IMPRIMIR =x AutoLoad [] 2 pestañas

                        after tahat cerrar modal limpiar todo, setear todo a 0
                    */
                    OBJETO_ATENCION = null;
                    $modalContinuarPago.modal("hide");
                    objRegistroAtencion.limpiarCamposAtencion();

                    GUARDANDO_ATENCION = false;
                    $btnGuardar.attr("disabled", false);
                },
                error: function (request, status, error) {
                    GUARDANDO_ATENCION  = false;
                    $btnGuardar.attr("disabled", false);
                    toastr.error(request.responseText);
                    return;
                },
                cache: true
            }
        );   

        /*
        */
    };

    var setearSeries = function(idTipoComprobante = null, vieneDesdeEventoOnChange = false){
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
                    return elemento.idtipo_comprobante == idTipoComprobante ? elemento : null;
                  });;
                break;
        }

        if (!listaSeries.length){
            $txtSerie.addClass("bg-grandient-danger");
            $txtSerie.html(`<option value="">No hay series disponibles</option>`);
        } else {
            $txtSerie.removeClass("bg-grandient-danger");

            var html = `<option value="">Seleccionar SERIE</option>`;
            for (let index = 0; index < listaSeries.length; index++) {
                const e = listaSeries[index];
                html += `<option value="${e.serie}">${e.serie}</option>`;
            }

            $txtSerie.html(html);

            if (vieneDesdeEventoOnChange){
                $txtSerie.val("");
            } else {
                var serieLocalStorage = localStorage.getItem("cache_serie");
                if (serieLocalStorage != null){
                    $txtSerie.val(serieLocalStorage);
                }
            }
            
        }
    };
    
    getTemplates();
    this.setDOM();
    this.setEventos();
    this.obtenerSeries();
    this.obtenerCajas();

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


        let idTipoComprobante = $("[name='rad-tipocomprobantepago']:checked").val();
        if (idTipoComprobante == "00"){
            $txtSerie.val("");
        } else {
            var serieLocalStorage = localStorage.getItem(idTipoComprobante == "01" ? "cache_serie_factura" : "cache_serie_boleta");
            if (serieLocalStorage != null && serieLocalStorage != ""){
                $txtSerie.val(serieLocalStorage);
            }
        }

        renderServicios(objAtencion.servicios);
        var total = parseFloat(Math.round10(objAtencion.total, -2)).toFixed(2);

        $txtPagoEfectivo.val(total);
        $txtPagoDeposito.val("0.00").trigger("keyup");
        $txtPagoTarjeta.val("0.00").trigger("keyup");

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
    };

    return this;
};

$(document).ready(function(){
    objContinuarPago =  new ContinuarPago(); 
});



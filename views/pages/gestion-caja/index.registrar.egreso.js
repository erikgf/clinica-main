var RegistrarEgreso = function() {
    var $modalRegistrarEgreso,
        $frmRegistrarEgreso,
        $txtTipoEgreso,
        $txtFechaRegistro,
        $txtBuscarRecibo,
        $btnBuscarRecibo,
        $txtAtencionPaciente,
        $txtAtencionFecha,
        $txtAtencionRecibo,
        $txtAtencionEstado,
        $txtFechaRegistro,
        $txtPagoEfectivo,
        $lblCajaEfectivo,
        $lblCajaTotal,
        $txtObservaciones,
        $btnGuardar;

        var OBJETO_ATENCION_BUSCADO = null;

    this.setDOM = function(){
        $modalRegistrarEgreso = $("#mdl-registraregreso");
        $frmRegistrarEgreso = $("#frm-registraregreso");

        $txtCaja = $modalRegistrarEgreso.find("#txt-egresos-caja");
        $txtTipoEgreso = $modalRegistrarEgreso.find("#txt-tipoegreso");
        $txtFechaRegistro = $modalRegistrarEgreso.find("#txt-egresos-fecharegistro");
        $txtBuscarRecibo = $modalRegistrarEgreso.find("#txt-egresos-buscarrecibo");
        $btnBuscarRecibo = $modalRegistrarEgreso.find("#btn-egresos-buscarrecibo");
        $txtAtencionRecibo =  $modalRegistrarEgreso.find("#txt-egresos-atencionrecibo");
        $txtAtencionPaciente =  $modalRegistrarEgreso.find("#txt-egresos-atencionpaciente");
        $txtAtencionFecha = $modalRegistrarEgreso.find("#txt-egresos-atencionfecha");
        $txtAtencionTotal =  $modalRegistrarEgreso.find("#txt-egresos-atenciontotal");
        $txtAtencionDevuelto = $modalRegistrarEgreso.find("#txt-egresos-atenciondevuelto");
        $txtAtencionVuelto = $modalRegistrarEgreso.find("#txt-egresos-atencionvuelto");

        $blkAtencionMedicaMostrar = $modalRegistrarEgreso.find("#blk-egresos-atencionmedicamostrar");

        $txtPagoEfectivo = $("#txt-egresos-pagoefectivo");
        $lblCajaEfectivo = $("#lbl-egresos-cajaefectivo");
        $lblCajaTotal = $("#lbl-egresos-cajatotal");

        $btnGuardar = $modalRegistrarEgreso.find("#btn-guardaregreso");
        $txtObservaciones = $("#txt-egresos-observaciones");

    };
    
    var validarMontos = false;
    this.setEventos = function(){

        $txtCaja.on("change",function () {
            let idCaja = this.value;
            if (idCaja == ""){
                return;
            }
            localStorage.setItem('cache_caja', idCaja);
        });

        $txtTipoEgreso.on("change", function(e){
            e.preventDefault();
            var valor = this.value;

            console.log(valor);
            switch(valor){
                case "7":
                case "8":
                    validarMontos = true;
                    $txtObservaciones.prop("required", false);
                    $blkAtencionMedicaMostrar.show();
                break;

                case "2":
                case "11":
                    validarMontos = false;
                    $txtObservaciones.prop("required", true);
                    $blkAtencionMedicaMostrar.hide();
                break;

                default:
                    validarMontos = false;
                    $blkAtencionMedicaMostrar.hide();
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

        $txtPagoEfectivo.on("change",  function () {
            this.value = parseFloat(Math.round10(this.value, -2)).toFixed(2);
            $lblCajaEfectivo.html(this.value);
            cajaResultadoTotal();
        });


        $btnGuardar.on("click", function(e){
            e.preventDefault();
            if (Util.validarFormulario($frmRegistrarEgreso)){                
                guardarEgreso(false);
            };
        });

    };

    
    var cajaResultadoTotal = function(){
        let efectivo = parseFloat($lblCajaEfectivo.html());
        
        Util.forceTwoDecimals(efectivo, $lblCajaTotal);

    };

    this.obtenerCajas = function(){
        var self = this;
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

    var GUARDANDO_EGRESO = false;
    var guardarEgreso = function(){
        var tipoEgreso = $txtTipoEgreso.val();

        if (GUARDANDO_EGRESO){
            return;
        }

        if(!confirm("¿Estás seguro de guardar este egreso?")){
            return;
        }
        
        $btnGuardar.attr("disabled", true);
        GUARDANDO_EGRESO = true;

        if ((tipoEgreso == "7" || tipoEgreso == "8") && !OBJETO_ATENCION_BUSCADO){
            toastr.error("Hay un problema recuperando la atención buscada.");
            return;
        }

        var data_formulario = {
            p_id_atencion_medica : OBJETO_ATENCION_BUSCADO ? OBJETO_ATENCION_BUSCADO.id_atencion_medica : null,
            p_id_paciente : OBJETO_ATENCION_BUSCADO ? OBJETO_ATENCION_BUSCADO.id_paciente : null,
            p_id_caja_instancia : $txtCaja.val(),
            p_monto_efectivo : $txtPagoEfectivo.val(),
            p_observaciones : $txtObservaciones.val(),
            p_id_tipo_movimiento : tipoEgreso
        };
        
        $.ajax({ 
                url : VARS.URL_CONTROLADOR+"caja.movimiento.controlador.php?op=registrar_egreso",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: data_formulario,
                success: function(xhr){
                    toastr.success(xhr.msj);
                   
                    OBJETO_ATENCION_BUSCADO = null;
                    $modalRegistrarEgreso.modal("hide");

                    GUARDANDO_EGRESO = false;
                    $btnGuardar.prop("disabled", false);

                    document.documentElement.scrollTop = 0;
                    $("#btn-actualizarmovimientos").click();
                },
                error: function (request, status, error) {
                    GUARDANDO_EGRESO  = false;
                    $btnGuardar.prop("disabled", false);
                    toastr.error(request.responseText);
                    return;
                },
                cache: true
            }
        );   

    };

    var buscarRecibo = function(){
        var fnOK = function (res) {
            $txtAtencionRecibo.html("<small class='"+(res.estado_valido == "1" ? "text-green" : "text-red")+"'>"+res.numero_recibo+"</small>");
            $txtAtencionPaciente.html(res.nombre_paciente);
            $txtAtencionFecha.html(res.fecha_atencion);
            $txtAtencionTotal.html(res.importe_total);

            $txtAtencionDevuelto.html(res.monto_devuelto);
            var monto_posible_vuelto = parseFloat(res.monto_vuelto - res.monto_vueltos_entregados).toFixed(2)
            $txtAtencionVuelto.html(monto_posible_vuelto);

            $txtPagoEfectivo.val(monto_posible_vuelto);
            $lblCajaEfectivo.html(monto_posible_vuelto);
            $lblCajaTotal.html(monto_posible_vuelto);

            if (monto_posible_vuelto <= 0){
                $txtAtencionVuelto.addClass("text-green").removeClass("text-red");
            } else {
                $txtAtencionVuelto.addClass("text-red").removeClass("text-green");
            }

            OBJETO_ATENCION_BUSCADO = res;
        };

        var fnError = function(error){
            toastr.error(error);
        };

        objRecibo.buscarRecibo(fnOK, fnError);
    };


    /*Función que permite activar los protocolos de la interfaz, teniendo como base principal el OBJETO_ATENCION.*/
    this.correr = function(){
        var hoy = new Date();
        Util.setFecha($txtFechaRegistro, hoy);

        var idCajaLocalStorage = localStorage.getItem("cache_caja");
        if (idCajaLocalStorage != null && idCajaLocalStorage != ""){
            $txtCaja.val(idCajaLocalStorage);
        }

        $txtTipoEgreso.val("");
        $blkAtencionMedicaMostrar.hide();

        $lblCajaEfectivo.html("0.00");

        $txtBuscarRecibo.val("");
        $txtAtencionRecibo.html("-");
        $txtAtencionPaciente.html("-");
        $txtAtencionFecha.html("-");
        $txtAtencionTotal.html("-");
        $txtAtencionDevuelto.html("-");
        $txtAtencionVuelto.html("-");

        $txtPagoEfectivo.val("0.00");
        $lblCajaEfectivo.html("0.00");
        $lblCajaTotal.html("0.00");

        $modalRegistrarEgreso.modal("show"); 

        setTimeout(function(){
            $txtCaja.focus();
        },300);

        OBJETO_ATENCION_BUSCADO = null;
    };

    
    this.setDOM();
    this.setEventos();

    var objRecibo = new ClsRecibo({
        $btnBuscarRecibo: $btnBuscarRecibo,
        $txtBuscarRecibo : $txtBuscarRecibo, 
        tipo_movimiento : "egreso"
    });

    return this;
};



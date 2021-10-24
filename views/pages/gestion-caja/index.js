var GestionCaja = function() {
    var $txtCaja,
        $txtFechaInicio,
        $txtFechaFin,
        $blkMontos,
        $tblMovimientos,
        $tblCajaInstancias,
        $btnAbrirCaja,
        $btnCerrarCaja,
        $btnIngresoCaja,
        $btnEgresoCaja,
        $mdlAbrirCaja,
        $txtMontoApertura,
        $mdlCerrarCaja,
        $btnImprimir,
        $btnGuardarAbrir,
        $btnGuardarCerrar,
        $btnImprimir;

    var tplCajasInstancias, 
        tplMontos,
        tplMovimientos;
    
    var KEY_LS_CAJAINSTANCIA = "dmi_cajainstancia_seleccionada",
        KEY_LS_CAJA = "dmi_caja_seleccionada",
        KEY_LS_CAJASELECCIONADA = "cache_caja";

    var ID_CAJA_INSTANCIA = new MonitoredVariable(null);
        ID_CAJA_INSTANCIA.afterChange = (newValue, oldValue) => {
            if (newValue == undefined || newValue == null || newValue == ""){
                $btnCerrarCaja.hide();
                $btnIngresoCaja.hide();
                $btnEgresoCaja.hide();
                deseleccionarCajaInstancia();
            } else {
                seleccionarCajaInstancia(newValue);
                $btnCerrarCaja.show();
                $btnIngresoCaja.show();
                $btnEgresoCaja.show();
            }
        };

    this.getKEY_LS_CAJA_INSTANCIA = function(){return KEY_LS_CAJAINSTANCIA;}

    this.getTemplates = function(){
        $.get("template.cajas.instancias.php", function(result, state){
            if (state == "success"){
                tplCajasInstancias = Handlebars.compile(result);
                initCajasInstancias();
            }
        });

        $reqMovimientos =  $.get("template.movimientos.php");
        $reqMontos =  $.get("template.caja.montos.php");
        
        $.when($reqMovimientos, $reqMontos)
            .done(function(resMovimientos, resMontos){
                tplMovimientos = Handlebars.compile(resMovimientos[0]);
                tplMontos = Handlebars.compile(resMontos[0]);

                var idCajaInstancia = localStorage.getItem(KEY_LS_CAJAINSTANCIA);
                ID_CAJA_INSTANCIA.val = idCajaInstancia;
            })
            .fail(function(e1,e2){
                console.error(e1,e2);
            });
    };

    this.setDOM = function(){
        var idCaja = localStorage.getItem(KEY_LS_CAJA);
        $txtCaja = $("#txt-caja");

        if (!(idCaja == null || idCaja == undefined)){
            $txtCaja.val(idCaja);
        }

        var hoy = new Date();
        //haceSieteDias = new Date();
            
        $txtFechaInicio = $("#txt-fechainicio");
        $txtFechaFin = $("#txt-fechafin");

        //haceSieteDias.setDate(haceSieteDias.getDate() - 7);
        //Util.setFecha($txtFechaInicio, haceSieteDias);
        Util.setFecha($txtFechaInicio, hoy);
        Util.setFecha($txtFechaFin, hoy);

        $blkMontos = $("#blk-montos");
        $tblMovimientos = $("#tbl-cajamovimientos");
        $tblCajaInstancias  = $("#tbl-cajainstancias");

        $btnAbrirCaja = $("#btn-abrircaja");
        $btnCerrarCaja = $("#btn-cerrarcaja");

        $mdlAbrirCaja = $("#mdl-abrircaja");
        $mdlCerrarCaja = $("#mdl-cerrarcaja");
        $btnImprimir = $("#btn-imprimir");
        $btnIngresoCaja = $("#btn-ingresocaja");
        $btnEgresoCaja = $("#btn-egresocaja");

        $btnGuardarCerrar = $("#btn-guardarcerrar");
        $btnGuardarAbrir = $("#btn-guardarabrir");

        $txtMontoApertura = $("#txt-montoapertura");
        $btnImprimir = $("#btn-imprimir");
        
    };
    
    this.setEventos = function(){
        $("#btn-actualizarinstancias").on("click", function(e){
            initCajasInstancias();
        });

        $txtCaja.on("change", function(e){
            e.preventDefault();
            localStorage.setItem(KEY_LS_CAJA, this.value);
            initCajasInstancias();
        });

        $tblCajaInstancias.on("click", ".btn-ver", function(e){
            e.preventDefault();
            ID_CAJA_INSTANCIA.val = this.dataset.id;
        });

        $btnAbrirCaja.on("click", function(e){
            e.preventDefault();
            $mdlAbrirCaja.modal("show");
        });

        $mdlAbrirCaja.on("shown.bs.modal", function(e){
            e.preventDefault();
            resetearAbrirCaja();
        });

        $btnCerrarCaja.on("click", function(e){
            e.preventDefault();
            $mdlCerrarCaja.modal("show");
        });

        $mdlCerrarCaja.on("shown.bs.modal", function(e){
            e.preventDefault();
            cargarCerrarCaja();
        });

        $txtMontoApertura.on("change", function(e){
            var valor = this.value;
            if (valor == "" || 
                parseFloat(valor) <= 0){
                this.value = "0.00";
                return;
            }
        });

        $txtMontoApertura.on("click", function(e){
            $txtMontoApertura.select();
        })

        $("#txt-fechaapertura").on("change", function(e){
            var valor = this.value;
            if (valor == "" || valor == null){
                Util.setFecha($(this), new Date());
            }
        });

        $btnGuardarAbrir.on("click", function(e){
            e.preventDefault();
            guardarAbrirCaja();
        });

        $btnGuardarCerrar.on("click", function(e){
            e.preventDefault();
            guardarCerrarCaja();
        });


        $("#btn-actualizarmovimientos").on("click", function(e){
            seleccionarCajaInstancia(ID_CAJA_INSTANCIA.val);
        });

        $btnImprimir.on("click", function(e){
            e.preventDefault();
            imprimirArqueroCierre();
        });


        $tblMovimientos.on("click", ".btn-anularmovimiento", function (e) {
            var $btn = this,
                dataset = $btn.dataset;
            e.preventDefault();

            anularMovimiento(dataset.id, dataset.tipomovimiento, dataset.cliente);
        });

        $btnIngresoCaja.on("click", function(e){
            e.preventDefault();
            objRegistrarIngreso.correr();
        });

        $btnEgresoCaja.on("click", function(e){
            e.preventDefault();
            objRegistrarEgreso.correr();
        });
    };

    var initCajasInstancias = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"caja.controlador.php?op=obtener_instancias",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_fecha_inicio : $txtFechaInicio.val(),
                p_fecha_fin : $txtFechaFin.val(),
                p_id_caja : $txtCaja.val(),
            },
            success: function(result){
                $tblCajaInstancias.html(tplCajasInstancias(result));
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    var TABLA_MOVIMIENTOS;
    var seleccionarCajaInstancia = function(idCajaInstancia){
        if (idCajaInstancia == null || idCajaInstancia == ""){
            return;
        }

        localStorage.setItem(KEY_LS_CAJAINSTANCIA, idCajaInstancia);

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"caja.controlador.php?op=seleccionar_movimientos_caja_instancia",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_caja_instancia : idCajaInstancia
            },
            success: function(result){
                var $lblCajaSeleccionada = $(".lbl-cajaseleccionada");

                var balance_total = parseFloat(result.ingresos.monto_total) - parseFloat(result.egresos.monto_total);
                var balance_total_solo_efectivo = parseFloat(result.ingresos.monto_efectivo) - parseFloat(result.egresos.monto_efectivo);

                $lblCajaSeleccionada.html("[CAJA: "+result.nombre_caja+"-"+result.codigo+" "+result.usuario_caja+"]").removeClass("text-red text-info");
                
                setTimeout(function(){
                    $lblCajaSeleccionada.addClass("text-info");
                    $lblCajaSeleccionada = null;
                },300);

                result.balance_total = Math.round10(balance_total,-2).toFixed(2);
                result.balance_total_solo_efectivo = Math.round10(balance_total_solo_efectivo,-2).toFixed(2);

                $blkMontos.html(tplMontos(result));

                if (TABLA_MOVIMIENTOS){
                    TABLA_MOVIMIENTOS.destroy();
                    TABLA_MOVIMIENTOS = null;
                }
                $tblMovimientos.html(tplMovimientos(result.movimientos));

                if (result.movimientos.length){
                    TABLA_MOVIMIENTOS = $("#tbl-cajamovimientosmain").DataTable({
                        "ordering": true
                    });
                }

                if (result.esta_cerrada == "1"){
                    $btnCerrarCaja
                                .addClass("bg-gradient-navy")
                                .removeClass("bg-gradient-blue")
                                .html('<i class="fa fa-lock"></i> CERRADA');
                    
                } else {
                    $btnCerrarCaja
                                .removeClass("bg-gradient-navy")
                                .addClass("bg-gradient-blue")
                                .html('<i class="fa fa-key"></i> CERRAR');
                }

                $btnCerrarCaja.data("estado",result.esta_cerrada);
            },
            error: function (request) {
                ID_CAJA_INSTANCIA.val = null;
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    var deseleccionarCajaInstancia = function(){
        $blkMontos.html(tplMontos());
        $tblMovimientos.html(tplMovimientos());

        $(".lbl-cajaseleccionada").html("[CAJA: SIN SELECCIONAR]").removeClass("text-info").addClass("text-red");
    };
    
    var resetearAbrirCaja = function(){
        Util.setFecha($("#txt-fechaapertura"), new Date());
        $txtMontoApertura.val("0.00");
        $("#txt-cajaabrir").val($txtCaja.val());
    };

    var cargarCerrarCaja = function(){
        $("#blk-montoscerrar").html($blkMontos.html());

        var estaCerrada = $mdlCerrarCaja.find(".txt-estacerrada").val();
        console.log(estaCerrada);
        if (estaCerrada == "1"){
            $btnImprimir.html("IMPRIMIR");
            $btnGuardarCerrar.hide();
        } else {
            $btnImprimir.html("IMPRIMIR (PRELIMINAR)");
            $btnGuardarCerrar.show();
        }
    };

    var opcionesGuardarAbrirEspeciales = {
        es_fecha_anterior : "0",
        es_fecha_repetida : "0",
        clave_admin : ""
    };

    var guardarAbrirCaja = function(){
        if (!confirm("¿Está seguro de abrir caja?")){
            return;
        }
        
        if (!Util.validarFormulario($("#frm-abrircaja"))){
            return;
        };

        $.ajax({ 
            url: VARS.URL_CONTROLADOR+"caja.controlador.php?op=abrir_caja",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: {
               p_id_caja : $("#txt-cajaabrir").val(),
               p_id_caja_instancia : "",
               p_fecha_apertura : $("#txt-fechaapertura").val(),
               p_monto_apertura : $txtMontoApertura.val(),
               p_es_fecha_anterior : opcionesGuardarAbrirEspeciales.es_fecha_anterior,
               p_es_fecha_repetida : opcionesGuardarAbrirEspeciales.es_fecha_repetida,
               p_clave_admin : opcionesGuardarAbrirEspeciales.clave_admin
            },
            success: function(datos){
                if (datos.rpt == "0"){
                    if (datos.es_fecha_anterior){
                        if (!confirm("Se ha detectado una fecha de apertura anterior a la fecha del día de hoy ¿Desea continuar?")) return;
                        opcionesGuardarAbrirEspeciales.es_fecha_anterior = "1";
                        guardarAbrirCaja();
                        return;
                    }

                    if (datos.es_fecha_repetida){
                        if (!confirm("Se ha detectado que la fecha de apertura ingresada ya EXISTE. ¿Desea aceptar registrar otra caja en este día?")) return;
                        opcionesGuardarAbrirEspeciales.es_fecha_repetida = "1";
                        guardarAbrirCaja();
                        return;
                    }

                    return;
                }

                ID_CAJA_INSTANCIA.val = datos.id_caja_instancia;
                localStorage.setItem(KEY_LS_CAJASELECCIONADA, datos.id_caja_instancia);

                opcionesGuardarAbrirEspeciales.es_fecha_anterior = "0";
                opcionesGuardarAbrirEspeciales.es_fecha_repetida = "0";

                $mdlAbrirCaja.modal("hide");
                initCajasInstancias();
                toastr.success(datos.msj);
            },
            error: function (request) {
                toastr.error(request.responseText);
                return
            },
            cache: true
        });
    };

    var guardarCerrarCaja = function(){
        if (!confirm("¿Está seguro de cerrar caja?")){
            return;
        }

        $.ajax({ 
            url: VARS.URL_CONTROLADOR+"caja.controlador.php?op=cerrar_caja",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: {
               p_id_caja_instancia : ID_CAJA_INSTANCIA.val,
            },
            success: function(datos){
                ID_CAJA_INSTANCIA.val = datos.id_caja_instancia;
                localStorage.setItem(KEY_LS_CAJASELECCIONADA, null);

                $mdlCerrarCaja.modal("hide");
                initCajasInstancias();
                
                toastr.success(datos.msj);
            },
            error: function (request) {
                toastr.error(request.responseText);
                return
            },
            cache: true
        });
    };

    this.initCajas = function(){
        var self = this;
        $.ajax({ 
            url: VARS.URL_CONTROLADOR+"caja.controlador.php?op=obtener_cajas",
            type: "post",
            dataType: 'json',
            delay: 250,
            success: function(datos){
                var html = ``;
                for (let index = 0; index < datos.length; index++) {
                    const o = datos[index];
                    html += `<option value="${o.id_caja}">${o.descripcion}</option>`;
                }

                $("#txt-caja").html(html);
                $("#txt-cajaabrir").html(html);

                self.setDOM();
                self.setEventos();
                self.getTemplates();
            },
            error: function (request) {
                toastr.error(request.responseText);
                return
            },
            cache: true
        });
    };
        
    this.obtenerCajasVlidasParaMovimientos = function(){
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

                    var $txtCaja = $(".txt-cajas-movimientos");

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

    this.TABLA_MOVIMIENTOS = function(){
        return TABLA_MOVIMIENTOS;
    }

    var imprimirArqueroCierre = function(){
        if (ID_CAJA_INSTANCIA.val != null && ID_CAJA_INSTANCIA.val != ""){
            window.open("../../../impresiones/arqueo.caja.diario.php?id="+ID_CAJA_INSTANCIA.val, "1");
        }
    };

    var anularMovimiento = function(id_movimiento, tipo_movimiento, cliente){
        var motivo_anulacion = prompt("Ingrese el motivo de la anulación de "+tipo_movimiento+" del cliente "+cliente);

        if (motivo_anulacion == null || motivo_anulacion == "") {
            toastr.error("Debe ingresar un motivo de anulación.");
            return;
        }

        if (ID_CAJA_INSTANCIA.val == null || ID_CAJA_INSTANCIA.val == ""){
            toastr.error("No se ha seleccionado caja diaria.");
            return;
        }

        $.ajax({ 
            url: VARS.URL_CONTROLADOR+"caja.movimiento.controlador.php?op=anular_movimiento",
            type: "post",
            dataType: 'json',
            data : {
                p_id_movimiento : id_movimiento,
                p_motivo_anulacion : motivo_anulacion
            },
            delay: 250,
            success: function(datos){
                toastr.success(datos.msj);
                seleccionarCajaInstancia(datos.id_caja_instancia);
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
        });
        
    };

    this.initCajas();
    this.obtenerCajasVlidasParaMovimientos();
    return this;
};

$(document).ready(function(){
    objRegistrarEgreso =  new RegistrarEgreso(); 
    objRegistrarIngreso =  new RegistrarIngreso();     

    objGestionCaja = new GestionCaja(); 
});



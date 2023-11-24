// $.fn.modal.Constructor.prototype._enforceFocus = function() {}; fix for modals and select2
// $(document).ready(function () { $.fn.modal.Constructor.prototype.enforceFocus = function () { }; });
//Variables editables
var OBJETO_ATENCION = null,
    OBJETO_DESCUENTO = null;

    
var ID_CAJA_SELECCIONADA = null,
    STR_CACHE_CAJA = "cache_caja_nuevo";
var objCampaña = null;
var idCajaCached;

var RegistroAtencion = function() {
    var getTemplates = function(){
        $.get("template.servicioagregado.php", function(result, state){
            if (state == "success"){
                tplServicioAgregado = Handlebars.compile(result);
            }
        });
    };

    var setDOM = function(){
        $content = $(".content");

        $txtFechaAtencion = $("#txt-fechaatencion");
        $txtHoraAtencion = $("#txt-horaatencion");
        $txtPaciente = $("#txt-paciente");
        $txtMedicoRealizante = $("#txt-medicorealizante");
        $txtCategoria = $("#txt-categoria");
        $txtServicio = $("#txt-servicio");

        $mdlDescuento = $("#mdl-descuento");
        $chkGratuitoDescuento = $mdlDescuento.find("#chk-gratuitodescuento");
        $txtImporteTotalDescuento = $mdlDescuento.find("#txt-importetotaldescuento");
        $txtMotivoDescuento = $mdlDescuento.find("#txt-motivodescuento");
        $txtMontoDescuento = $mdlDescuento.find("#txt-montodescuento");
        $txtAutorizadorDescuento = $mdlDescuento.find("#txt-autorizadordescuento");
        $txtClaveDescuento = $mdlDescuento.find("#txt-clavedescuento");
        $btnAutorizarDescuento = $mdlDescuento.find("#btn-autorizardescuento");
        $btnEliminarDescuento = $mdlDescuento.find("#btn-eliminardescuento");

        $blkServicios = $("#blk-servicios");
        $blkDescuento = $("#blk-descuento");
        $blkSubtotal  = $("#blk-subtotal");
        $lblSubTotal  = $("#lbl-subtotal");

        $txtMedicoOrdenante = $("#txt-medicoordenante");
        $txtMedicoOrdenante = $("#txt-medicoordenante");
        $txtObservaciones = $("#txt-observaciones");

        $btnContinuar =$("#btn-continuar");

        $lblNombreCaja = $("#lbl-nombrecaja");
        $txtSeleccionadorCaja = $("#txt-seleccionadorcaja");

        seleccionarCajaTrabajo(idCajaCached);

    };
    
    var setEventos = function(){
        $blkServicios.on("click", ".btn-quitarservicio", function(e){
            let $this = this;
            let element = $this.parentElement.parentElement.parentElement;
            element.parentNode.removeChild(element);

            checkCampañaServicios();
            getSubtotalOfEach();
        });

        $blkServicios.on("change", ".precio-unitario", function(e){
            getSubtotalOfEach();
        });

        $txtPaciente.on("change", function(){
           canContinue();
        });

        $txtMedicoOrdenante.on("change",function(){
            canContinue();
         });

        $txtMedicoRealizante.on("change", function(){
            canContinue();
         });

        $txtCategoria.on("change", function(e){
            $txtServicio.prop('disabled', false);
            $txtCategoria.find("option[value='NaN']").remove();

            $txtServicio.select2({
                ajax: { 
                    url : VARS.URL_CONTROLADOR+"servicio.controlador.php?op=buscar",
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            p_cadenabuscar: params.term, // search term
                            p_idcategoria: $txtCategoria.val()
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
                debug: true,
                tags: false,
                cache: true
            });
            
            if($txtServicio.find(":selected").val()==""){
                $txtServicio.prop('disabled', true).empty()
            }
        });

        $lblNombreCaja.on("dblclick", function(e){
            $lblNombreCaja.hide();
            $txtSeleccionadorCaja.show();
            return;
        });

        $txtSeleccionadorCaja.on("change", function(e){
            var idcaja = this.value;

            if (idcaja == "0"){
                $txtSeleccionadorCaja.hide();
                $lblNombreCaja.show();
                return;
            }
            if (idcaja == ""){
                $txtSeleccionadorCaja.hide();
                $lblNombreCaja.show();
                $lblNombreCaja.html("SELECCIONAR CAJA");
            }

            seleccionarCajaTrabajo(idcaja);
            window.location.reload();
        });

        var variableParaEvitarSeguirSegundoChangeAlVaciar = false;
        $txtServicio.on("change", function(e){
            if(variableParaEvitarSeguirSegundoChangeAlVaciar){
                variableParaEvitarSeguirSegundoChangeAlVaciar = false;
                return;
            }

            $.ajax({ 
                url: VARS.URL_CONTROLADOR+"servicio.controlador.php?op=obtener_servicio",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: {
                   p_idservicio : this.value
                },
                success: function(xhr){
                    if(xhr.rpt){
                        var objServicio = xhr.datos;

                        if (objServicio.servicios_perfil){
                            agregarServicio(objServicio.servicios_perfil);
                        } else {
                            agregarServicio([objServicio]);
                        }
                        $txtServicio[0].selectedIndex = 0;
                        variableParaEvitarSeguirSegundoChangeAlVaciar = true;
                        $txtServicio.val(null).trigger('change');

                        canContinue();
                        getSubtotalOfEach();
                    }
                },
                error: function (request) {
                    toastr["error"](request.responseText);
                    return;
                },
                cache: true
            });
        });

        $mdlDescuento.on("shown.bs.modal", function(e){
            renderDescuento();            
        });

        $txtMontoDescuento.on("change", function(e){
            e.preventDefault();
            if (parseFloat($txtMontoDescuento.val()) > parseFloat($txtImporteTotalDescuento.val())){
                $txtMontoDescuento.val($txtImporteTotalDescuento.val());
            }

            if ($txtMontoDescuento.val() < 0){
                $txtMontoDescuento.val("0.00");
            }
        }); 

        $txtMontoDescuento.on("click", function(e){
            $txtMontoDescuento.select();
        });

        $chkGratuitoDescuento.on("change", function(){
            var seleccionado  = $chkGratuitoDescuento.prop("checked");
            if (seleccionado){
                $txtMontoDescuento.val($txtImporteTotalDescuento.val()).prop("readonly", true);
            } else {
                $txtMontoDescuento.val("0.00").prop("readonly", false);
            }
        });

        $btnAutorizarDescuento.on("click", function(e){
            e.preventDefault();
            guardarDescuento();
        }); 

        $btnEliminarDescuento.on("click", function(e){
            e.preventDefault();
            eliminarDescuento();
        });

        $btnContinuar.click(function(){
            let idPacienteSeleccionado = $($txtPaciente).val();
            $.ajax({ 
                url : VARS.URL_CONTROLADOR+"paciente.controlador.php?op=obtener_paciente_x_id",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: {
                    p_idpaciente : idPacienteSeleccionado
                },
                success: function(result){
                    if(result.rpt){
                        continuar(result.datos);
                    }else{
                        toastr.error("Paciente no válido.");
                        return;
                    }
                },
                error: function (request) {
                    toastr.error(request.responseText);
                    return;
                },
                cache: true
                }
            );

            function continuar(objPaciente){

                /*
                0.- setear objeto_anteicon
                1.- abrir modal
                */
                let arregloServicios = [], total = 0.00;
                $blkServicios.find(".blk-servicioagregado").each(function( i, o ){
                    var $o = $(o);
                    var dataset = o.dataset;
                    var objServicio = JSON.parse(dataset.serviciojson);
                    var subtotal = $o.find('.lbl-subtotalitem').html();
                    var $inputPrecios = $o.find('input.precio-unitario');
                    objServicio.subtotal = subtotal;
                    objServicio.cantidad  = 1;
                    objServicio.precio_unitario = subtotal * objServicio.cantidad;
                    objServicio.cam_precio_unitario = $inputPrecios.data("campana");
                    objServicio.real_precio_unitario = $inputPrecios.data("real");
                    objServicio.posible_campaña = $o.data("posiblecam");
                    objServicio.con_campaña = $o.data("concampana");
                    objServicio.descuento_total  = 0.00;

                    total += parseFloat(Math.round10(objServicio.subtotal, -2));
                    arregloServicios.push(objServicio);
                });

                OBJETO_ATENCION = {
                    id_paciente: objPaciente.id,
                    numero_historia : objPaciente.numero_historia,
                    nombres_completos : objPaciente.nombres_completos,
                    numero_documento: objPaciente.numero_documento,
                    medico_realizante: $txtMedicoRealizante.find("option:selected").html(), 
                    id_medico_realizante: $txtMedicoRealizante.val(),
                    medico_ordenante: $txtMedicoOrdenante.find("option:selected").html(), 
                    id_medico_ordenante: $txtMedicoOrdenante.val(),
                    fecha_atencion : $txtFechaAtencion.val(), 
                    hora_atencion : $txtHoraAtencion.val(),
                    observaciones: $txtObservaciones.val(),
                    servicios: arregloServicios,
                    total : total
                };

                OBJETO_ATENCION.objDescuento = OBJETO_DESCUENTO;

                if (objContinuarPago){
                    objContinuarPago.correr(OBJETO_ATENCION);
                }
                
            };
        });

    };

    var setFuncionesInicio = function(){
        var hoy = new Date();
        Util.setHora($txtHoraAtencion, hoy);
        Util.setFecha($txtFechaAtencion, hoy);

        /*Iniciando Selects*/
        $txtPaciente.select2({
            ajax: { 
                //url: "./mySQL/connect-pacientes.php",
                url : VARS.URL_CONTROLADOR+"paciente.controlador.php?op=buscar_pacientes",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        p_cadenabuscar: params.term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response.datos
                    };
                },
                cache: true
            },
            minimumInputLength: 3,
            width: '100%',
            multiple:false,
            placeholder:"Seleccionar",
            debug: true
        });

        setTimeout(function (){
            $txtPaciente.select2('open');
        },330);
        
        $txtCategoria.select2({
            ajax: { 
                url : VARS.URL_CONTROLADOR+"categoria.servicio.controlador.php?op=buscar",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        p_cadenabuscar: params.term, 
                    };
                },
                processResults: function (response) {
                    return {
                        results: response.datos
                    };
                },
                cache: true
            },
            width: '100%',
            multiple:false,
            placeholder:"Seleccionar categoría",
            debug: true,
            tags: false
        });

        $txtMedicoRealizante.select2({
            ajax: { 
                url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=buscar",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        p_cadenabuscar: params.term, // search term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response.datos
                    };
                },
                cache: true
            },
            minimumInputLength: 1,
            width: '100%',
            multiple:false,
            placeholder:"Seleccionar",
            debug: true,
            tags: false
        });

        $txtMedicoOrdenante.select2({
            ajax: { 
                url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=buscar",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        p_cadenabuscar: params.term, // search term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response.datos
                    };
                },
                cache: true
            },
            minimumInputLength: 1,
            width: '100%',
            multiple:false,
            placeholder:"Seleccionar",
            debug: true,
            tags: false
        });

        $txtAutorizadorDescuento.select2({
            dropdownParent: $mdlDescuento.find(".modal-content"),
            ajax: { 
                url : VARS.URL_CONTROLADOR+"usuario.controlador.php?op=obtener_autorizadores_descuentos",
                type: "post",
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

    };

    var agregarServicio = function(data_servicios){
        if (objCampaña != null){
            if (objCampaña.descuento_categorias_json){
                data_servicios = data_servicios.map((servicio)=>{
                    let precio_unitario = servicio.precio_unitario,
                        precio_sin_IGV = servicio.precio_sin_IGV,
                        real_precio_unitario = servicio.precio_unitario,
                        real_precio_sin_IGV = servicio.precio_sin_IGV,
                        cam_precio_unitario = servicio.precio_unitario,
                        cam_precio_sin_IGV = servicio.precio_sin_IGV,
                        posible_campaña = 0,
                        con_campaña = false;

                    for (let index = 0; index < objCampaña.descuento_categorias_json.length; index++) {
                        const element = objCampaña.descuento_categorias_json[index];
                        if (element.tipo === 'categoria'){
                            if (servicio.id_categoria_servicio == element.id){
                                precio_unitario = element.porcentaje == 1
                                                    ? parseFloat(servicio.precio_unitario * ( 1 - element.descuento)).toFixed(2)
                                                    : parseFloat(servicio.precio_unitario - element.descuento).toFixed(2);
                                precio_sin_IGV = parseFloat(precio_unitario * (1 - objCampaña.igv)).toFixed(2);
                                cam_precio_unitario = precio_unitario;
                                cam_precio_sin_IGV = precio_sin_IGV;
                                con_campaña = true;
                                posible_campaña = 1;
                            }
                        }

                        if (element.tipo === 'servicio'){
                            if (servicio.id_servicio == element.id){
                                precio_unitario = element.porcentaje == 1
                                                    ? parseFloat(servicio.precio_unitario * ( 1 - element.descuento)).toFixed(2)
                                                    : parseFloat(servicio.precio_unitario - element.descuento).toFixed(2);
                                precio_sin_IGV = parseFloat(precio_unitario * (1 - objCampaña.igv)).toFixed(2);
                                cam_precio_unitario = precio_unitario;
                                cam_precio_sin_IGV = precio_sin_IGV;
                                con_campaña = true;
                                posible_campaña = 1;
                            }
                        }
                    }


                    return {...servicio, 
                            con_campaña,
                            precio_unitario, precio_sin_IGV,
                            real_precio_unitario, real_precio_sin_IGV,
                            cam_precio_unitario, cam_precio_sin_IGV,
                            posible_campaña
                        };
                });
            }
        }

        $blkServicios.append(tplServicioAgregado(data_servicios));
        checkCampañaServicios();
    };

    var getSubtotal = function(){
        let currentSum = 0;
        let subTotales = Array.prototype.slice.call($('.lbl-subtotalitem'));
        subTotales.forEach((eachSubtotal)=>{
            let parsedSubtotal = parseFloat(eachSubtotal.innerHTML)
            currentSum = currentSum + parsedSubtotal;
        });

        $lblSubTotal.html(currentSum.toFixed(2));
    };

    var getSubtotalOfEach = function(){
        let cards =  Array.prototype.slice.call($(".blk-servicioagregado"));

        if (cards.length > 0){
            $blkDescuento.show();
            $blkSubtotal.removeClass("hide");

            cards.forEach((card)=>{
                let currentQuantity = 1;//card.querySelector('.btn-quantity').innerHTML;
                let precioUnitario = card.querySelector('.precio-unitario').value;
                let subtotalPlaceHolder = card.querySelector('.lbl-subtotalitem');
                precioUnitario = parseFloat(precioUnitario);
                currentQuantity = parseInt(currentQuantity);
                montoDeDescuento = 0;
                let result = (currentQuantity*precioUnitario)-montoDeDescuento;
    
                result = result.toFixed(2);
                subtotalPlaceHolder.innerHTML = result;
            });
        } else {
            $blkDescuento.hide();
            $blkSubtotal.addClass("hide");
        }

        getSubtotal();
    };

    var guardarDescuento  = function() {
        if (!Util.validarFormulario($mdlDescuento)){
            return;
        }

        $.ajax({ 
            url: VARS.URL_CONTROLADOR+"usuario.controlador.php?op=validar_descuento",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: {
               p_idusuario : $txtAutorizadorDescuento.val(),
               p_clave : $txtClaveDescuento.val()
            },
            success: function(res){
                OBJETO_DESCUENTO = {
                    id_validador : $txtAutorizadorDescuento.val(),
                    nombre_validador : $txtAutorizadorDescuento.find("option:selected").html(),
                    monto_descuento : $txtMontoDescuento.val(),
                    motivo : $txtMotivoDescuento.val(),
                    es_gratuito : $chkGratuitoDescuento.prop("checked")
                };

                $mdlDescuento.modal("hide");
                mostrarDescuento();
            },
            error: function (res) {
                $txtClaveDescuento.val("");
                toastr["error"](res.responseText);
                return;
            },
            cache: true
        });
    };

    var mostrarDescuento = function(mostrar){
        if (mostrar == undefined) {
            mostrar = true;
        }

        if (mostrar){
            $("#blk-mostrardescuento").show();
            $("#btn-descuento").hide();
            $("#lbl-descuento").html("- "+ parseFloat(OBJETO_DESCUENTO.monto_descuento).toFixed(2));
        } else {
            $("#blk-mostrardescuento").hide();
            $("#btn-descuento").show();
            $("#lbl-descuento").html("");
        }
        
    };

    var eliminarDescuento = function(){
        OBJETO_DESCUENTO = null;
        mostrarDescuento(false);
        renderDescuento();
    };

    var renderDescuento = function(){
        $mdlDescuento.find(".is-invalid").removeClass(".is-invalid");
        
        if (OBJETO_DESCUENTO){
            $chkGratuitoDescuento.prop("checked", OBJETO_DESCUENTO.es_gratuito);
            $txtImporteTotalDescuento.val($lblSubTotal.html());
            $txtMontoDescuento.val(OBJETO_DESCUENTO.monto_descuento).prop("readonly", OBJETO_DESCUENTO.es_gratuito);
            $txtMotivoDescuento.val(OBJETO_DESCUENTO.motivo);
            $txtAutorizadorDescuento.append(new Option(OBJETO_DESCUENTO.nombre_validador, OBJETO_DESCUENTO.id_validador, true, true)).trigger('change');
            $txtClaveDescuento.val("");

            $btnEliminarDescuento.show();
            return;
        }

        $chkGratuitoDescuento.prop("checked", false);
        $txtImporteTotalDescuento.val($lblSubTotal.html());
        $txtMontoDescuento.val("0.00").prop("readonly", false);
        $txtMotivoDescuento.val("");
        $txtAutorizadorDescuento.val(null).trigger("change");
        $txtClaveDescuento.val("");

        $btnEliminarDescuento.hide();
    };

    var canContinue = function(){
        let ordersCount = $blkServicios.children().length;
        let idPaciente = $txtPaciente.val();
        let idMedicoRealizante = $txtMedicoRealizante.val();
        let idMedicoOrdenante = $txtMedicoOrdenante.val();

        if (ordersCount <= 0){
            $btnContinuar.prop("disabled", true);
            return;
        }

        if (idPaciente == "0" || idPaciente == "" || idPaciente == null){
            $btnContinuar.prop("disabled", true);
            return;
        }

        if (idMedicoRealizante == "0" || idMedicoRealizante == "" || idMedicoRealizante == null){
            $btnContinuar.prop("disabled", true);
            return;
        }

        if (idMedicoOrdenante == "0" || idMedicoOrdenante == "" || idMedicoOrdenante == null ){
            $btnContinuar.prop("disabled", true);
            return;
        }

        $btnContinuar.prop("disabled", false);
    };

    this.limpiarCamposAtencion = function(){
        var hoy = new Date();
        Util.setFecha($txtFechaAtencion, hoy);
        Util.setHora($txtHoraAtencion, hoy);

        $txtPaciente.val(null).trigger("change");
        $blkServicios.empty();

        getSubtotal();
        getSubtotalOfEach();

        $txtMedicoOrdenante.val(null);
        $txtMedicoRealizante.val(null);

        $txtMedicoOrdenante.append(new Option("PARTICULAR", "1", true, true)).trigger('change');
        $txtMedicoRealizante.append(new Option("ROSAS DPI", "2", true, true)).trigger('change');
        OBJETO_ATENCION = null;

        $txtObservaciones.val("");
        eliminarDescuento();
        
        canContinue();
        $txtPaciente.select2("open");
    };

    
    var seleccionarCajaTrabajo = function(idcaja){
        if (idcaja == ""){
            idcaja = null;
        }

        ID_CAJA_SELECCIONADA = idcaja;

        localStorage.setItem(STR_CACHE_CAJA, idcaja);
        $lblNombreCaja.data("idcaja",idcaja);

        if (idcaja == null){
            $lblNombreCaja.html("SELECCIONAR CAJA");
        } else {
            $lblNombreCaja.html("CAJA DE TRABAJO: "+idcaja);
        }
        $lblNombreCaja.show();
        $txtSeleccionadorCaja.hide();

        //window.location.reload();
    };

    var initReloj = function(){
        setInterval(function(){
            Util.setHora($txtHoraAtencion, new Date());
        }, 1000);
    };


    var initCampaña = function(){
        $.ajax({ 
            url: VARS.URL_CONTROLADOR+"campaña.controlador.php?op=obtener",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: {
                p_idcaja : idCajaCached
            },
            success: function(xhr){
                const $lblNombreCampaña = $("#lbl-nombrecampaña");
                const $divCampaña = $lblNombreCampaña.parent().parent();

                if(xhr){
                    if (xhr.descuento_categorias_json != null){
                        xhr.descuento_categorias_json = JSON.parse(xhr.descuento_categorias_json);
                    }
                    objCampaña = xhr;
                    $lblNombreCampaña.html(objCampaña.nombre);
                    $divCampaña.show();
                } else {
                    $divCampaña.hide()
                }
                bootstrap();
            },
            error: function (request) {
                toastr["error"](request.responseText);
                return
            }
        });
    };

    var bootstrap = function(){
        getTemplates();
        setDOM();
        setFuncionesInicio();
        setEventos();

        objContinuarPago =  new ContinuarPago(); 
    };
    

    idCajaCached = localStorage.getItem(STR_CACHE_CAJA);
    initReloj();
    initCampaña();

    return this;
};

var checkCampañaServicios = () => {
    if (objCampaña){
        console.log("CHECKUM");

        let deboAplicar = true;
        let totalReal = 0.00;
        let $cards =  Array.prototype.slice.call($(".blk-servicioagregado"));
        $cards.forEach((card)=>{
            const $precioUnitario = card.querySelector('.precio-unitario');
            totalReal += parseFloat($precioUnitario.dataset.real);
        });

        if (objCampaña.monto_minimo){
            deboAplicar = totalReal >= objCampaña.monto_minimo;
        }

        if (objCampaña.monto_maximo){
            deboAplicar = totalReal < objCampaña.monto_maximo;
        }

        if (OBJETO_ATENCION){
            if (objCampaña.tipo_pago == 0){
                deboAplicar = parseFloat($("#lbl-cajacredito").html()) <= 0;
                console.log({deboAplicar})

            }
        }

        actualizarCampañaServicios(deboAplicar, $cards)
    }

};

var actualizarCampañaServicios = (deboAplicar,  $cards) => {
    //Modificar $html
    //objContinuarPago actualizarCampañaServicios
    $cards.forEach((card)=>{
        const posibleCampaña = card.dataset.posiblecam;
        if (posibleCampaña == 1){
            const $precioUnitario = card.querySelector('.precio-unitario');
            const $nombre = card.querySelector('.nombre');

            card.dataset.concampana = deboAplicar ? "true" : "false";
            $precioUnitario.value = deboAplicar ? $precioUnitario.dataset.campana : $precioUnitario.dataset.real;
            if (deboAplicar){
                $nombre.classList.add("text-primary");
                $precioUnitario.classList.add("bg-primary");                
            } else {
                $nombre.classList.remove("text-primary");
                $precioUnitario.classList.remove("bg-primary");
            }
        }
    });

    if (OBJETO_ATENCION){

        let montoTotal  = 0.00;

        OBJETO_ATENCION.servicios = OBJETO_ATENCION.servicios.map(servicio=>{
            if (servicio.posible_campaña == 0){
                montoTotal += parseFloat(servicio.precio_unitario);
                return servicio;
            }

            const precio_unitario = deboAplicar ? servicio.cam_precio_unitario : servicio.real_precio_unitario;
            montoTotal += parseFloat(precio_unitario);
            return {
                ...servicio,
                con_campaña : deboAplicar,
                precio_unitario,
                subtotal: precio_unitario
            }
        });

        console.log({
            serv: OBJETO_ATENCION.servicios,
            montoTotal
        });

        objContinuarPago.renderServicios(OBJETO_ATENCION.servicios);

        const actual = parseFloat($("#lbl-cajatotal").html());
        const nuevo = parseFloat(montoTotal);
        const diferencia = nuevo - actual;
        const nuevoEfectivo = parseFloat($("#lbl-cajaefectivo").html()) + parseFloat(diferencia);

        console.log({actual, nuevo, diferencia, nuevoEfectivo})

        $("#lbl-cajatotal").html(parseFloat(nuevo).toFixed(2));
        $("#lbl-cajaefectivo").html(nuevoEfectivo.toFixed(2));
        $("#txt-pagoefectivo").val(nuevoEfectivo.toFixed(2));
    }
};


$(document).ready(function(){
    objRegistroAtencion = new RegistroAtencion(); 
});

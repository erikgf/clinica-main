var Atenciones = function() {
    var $blkTabsMuestrasResultados,
        $txtFechaInicio,
        $txtFechaFin,
        $txtTipoFiltro,
        $tblAtenciones,
        $tbdAtenciones,
        $tbdAtencionDetalle;

    var $blkRegistroResultados,
        $blkRegistroMuestra;

    var $lblRecibo,
        $lblPaciente,
        $btnImprimirMuestra,
        $btnImprimirMuestraSinLogo,
        $btnImprimirMuestraLogo,
        $btnCancelarMuestra,
        $btnGuardarMuestra;

    var $btnCancelarResultados,
        $btnGuardarResultados,
        $btnValidarResultados,
        $btnCancelarValidacion;

    var $lblServicioAtencion,
        $tbdExamenes;

    var CLASE_TR_SELECCIONADO = "bg-gradient-info";
    var tplAtenciones,
        tplAtencionDetalle,
        tplExamenes;

    var KEY_LS_TIPO_FILTRO = "dmi_filtro_estado_examen_laboratorio";

    this.getTemplates = function(){
        var $reqAtenciones =  $.get("template.atenciones.php");
        var $reqAtencionDetalle =  $.get("template.atenciondetalle.php");
        var $reqExamenes =  $.get("template.examenes.php");
        
        $.when($reqAtenciones, $reqAtencionDetalle, $reqExamenes)
            .done(function(resAtenciones, resAtencionDetalle, resExamenes){
                if (localStorage.getItem(KEY_LS_TIPO_FILTRO)){
                    $("#txt-tipofiltro").val(localStorage.getItem(KEY_LS_TIPO_FILTRO));
                }

                if (resAtenciones[1] == "success"){
                    tplAtenciones = Handlebars.compile(resAtenciones[0]);    
                    listarAtenciones();
                }

                if (resAtencionDetalle[1] == "success"){
                    tplAtencionDetalle = Handlebars.compile(resAtencionDetalle[0]);    
                }

                if (resExamenes[1] == "success"){
                    tplExamenes = Handlebars.compile(resExamenes[0]);    
                }

            })
            .fail(function(e1, e2, e3){
                console.error(e1, e2, e3);
            });
    };

    this.setDOM = function(){
        $blkTabsMuestrasResultados = $("#blk-tabs-muestrasresultados");
        $txtFechaInicio = $blkTabsMuestrasResultados.find("#txt-fechainicio");
        $txtFechaFin  = $blkTabsMuestrasResultados.find("#txt-fechafin");
        $txtTipoFiltro = $blkTabsMuestrasResultados.find("#txt-tipofiltro");
        $tblAtenciones =  $blkTabsMuestrasResultados.find("#tbl-atenciones");
        $tbdAtenciones =  $blkTabsMuestrasResultados.find("#tbd-atenciones");
        $btnActualizarAtenciones = $blkTabsMuestrasResultados.find("#btn-actualizaratenciones");

        $blkRegistroMuestra = $blkTabsMuestrasResultados.find("#blk-registromuestra");
        $lblRecibo = $blkTabsMuestrasResultados.find(".lbl-recibo");
        $lblPaciente = $blkTabsMuestrasResultados.find(".lbl-paciente");

        $tbdAtencionDetalle =  $blkTabsMuestrasResultados.find("#tbd-atenciondetalle");
        $btnImprimirMuestra = $blkTabsMuestrasResultados.find("#btn-imprimirmuestra");

        $btnImprimirMuestraLogo= $blkTabsMuestrasResultados.find("#btn-imprimirmuestra-logo");
        $btnImprimirMuestraSinLogo= $blkTabsMuestrasResultados.find("#btn-imprimirmuestra-sinlogo");

        $btnImprimirMuestraTodoJuntoLogo= $blkTabsMuestrasResultados.find("#btn-imprimirmuestra-todojunto-logo");
        $btnImprimirMuestraTodoJuntoSinLogo= $blkTabsMuestrasResultados.find("#btn-imprimirmuestra-todojunto-sinlogo");

        $btnCancelarMuestra = $blkTabsMuestrasResultados.find("#btn-cancelarmuestra");
        $btnGuardarMuestra = $blkTabsMuestrasResultados.find("#btn-guardarmuestra");

        $blkRegistroResultados = $blkTabsMuestrasResultados.find("#blk-registroresultados");
        $lblServicioAtencion = $blkTabsMuestrasResultados.find("#lbl-servicioatencion");
        $tbdExamenes = $blkTabsMuestrasResultados.find("#tbd-examenes");

        $btnCancelarResultados = $blkTabsMuestrasResultados.find(".btn-cancelarresultados");
        $btnGuardarResultados = $blkTabsMuestrasResultados.find("#btn-guardarresultados");
        $btnValidarResultados = $blkTabsMuestrasResultados.find("#btn-validarresultados");
        $btnCancelarValidacion = $blkTabsMuestrasResultados.find("#btn-cancelarvalidacion");
        
    };
    
    this.setEventos = function(){
        $txtFechaInicio.on("change", function(e){
            if (this.value == ""){
                Util.setFecha($txtFechaInicio, new Date());
            }

            listarAtenciones();
        });

        $txtFechaFin.on("change", function(e){
            if (this.value == ""){
                Util.setFecha($txtFechaFin, new Date());
            }

            listarAtenciones();
        }); 


        $txtTipoFiltro.on("change", function(e){
            e.preventDefault();
            localStorage.setItem(KEY_LS_TIPO_FILTRO, this.value);
            listarAtenciones();
        }); 

        $btnActualizarAtenciones.on("click", function(e) {
            e.preventDefault();
            listarAtenciones();     
        });

        $tbdAtenciones.on("click","tr  .btn-ver", function(){
            var $this = this,
                $tr = $this.parentElement.parentElement,
                classList = $tr.classList;

            if (classList.contains("not-tr")){
                return;
            }
                
            if (classList.contains(CLASE_TR_SELECCIONADO)){
                classList.remove(CLASE_TR_SELECCIONADO);
                deseleccionarAtencion();
                return;
            } else {
                classList.add(CLASE_TR_SELECCIONADO);
                seleccionarAtencion($tr);
            }
        });

        $btnImprimirMuestraSinLogo.on("click", function(e){
            e.preventDefault();
            imprimirResultadosExamenes("2");
        });

        $btnImprimirMuestraLogo.on("click", function(e){
            e.preventDefault();
            imprimirResultadosExamenes("1");
        });

        $btnImprimirMuestraTodoJuntoSinLogo.on("click", function(e){
            e.preventDefault();
            imprimirResultadosExamenes("2","1");
        });

        $btnImprimirMuestraTodoJuntoLogo.on("click", function(e){
            e.preventDefault();
            imprimirResultadosExamenes("1","1");
        });

        $btnCancelarMuestra.on("click", function(e){
            if ($TR_SELECCIONADO){
                $TR_SELECCIONADO.classList.remove(CLASE_TR_SELECCIONADO);
                $blkRegistroMuestra.hide();
                $blkRegistroResultados.hide();   
                $("#txt-idatencionmedicoservicioseleccionado").val("");
                $TR_SELECCIONADO = null;
            }
        });

        $btnGuardarMuestra.on("click", function(e){
            e.preventDefault();
            if ($TR_SELECCIONADO){
                guardarMuestra($TR_SELECCIONADO.dataset.id);    
            }
        });

        $tbdAtencionDetalle.on("click" ,".btn-registraresultados", function(e){
            e.preventDefault();
            listarServicioExamenLaboratorio(this.dataset.id);
        }); 

        $btnCancelarResultados.on("click", function(e){
            e.preventDefault();
            cancelarResultados();
        });

        $btnGuardarResultados.on("click", function(e){
            e.preventDefault();
            guardarResultados();
        });

        $btnValidarResultados.on("click", function(e){
            e.preventDefault();
            validarResultados();
        });

        $btnCancelarValidacion.on("click", function(e){
            e.preventDefault();
            cancelarValidacion();
        });

        $tbdExamenes.on("click", ".btn-agregarfila", function(e){
            e.preventDefault();
            var $tr = $(this.parentElement.parentElement),
                nivel  = $tr.data("nivel"),
                $trNueva = $(tplExamenes({detalle: [{
                    id_lab_examen : "",
                    nivel : nivel == "99" ? "0" : nivel
                }], fue_validado : "0", tiene_resultados: "0"}));

            $tr.after($trNueva);
            setTimeout(function(){
                $trNueva.find("input").eq(0).focus();
            }, 300);
        });

        $tbdExamenes.on("click", ".btn-quitarfila", function(e){
            e.preventDefault();
            var $tr = $(this.parentElement.parentElement);
            $tr.remove();
        });

        $tbdExamenes.on("focus", "input", function(e){
            e.preventDefault();
            this.select();
        });

        $tbdExamenes.on("click", ".btn-quitarfila", function(e){
            e.preventDefault();
            var $tr = $(this.parentElement.parentElement);
            $tr.remove();
        });

        $tbdExamenes.on("dblclick", "input.txt-valorexamen", function(e){
            e.preventDefault();
            obtenerAbreviaturas($(this));
        });

        $tbdExamenes.on("keydown", "input", function(e){
            navegarEnTablaExamenes(this, e.keyCode);
        });

        $tbdExamenes.on("focusout", ".txt-valorexamen ", function(e){ 
            var $txt = $(this),
                $ul = $txt.next().find("ul");
            setTimeout(function(){
                $ul.empty(); 
            }, 300);
        });

        $tbdExamenes.on("keyup", ".txt-valorexamen", function(e){
            e.preventDefault();
            filtrarAbreviaturas($(this).next().find("ul"), this.value);
        });

        $tbdExamenes.on("click", ".blk-buscador ul li", function(e){
            e.preventDefault();

            var $ul = this.parentElement;
            var $input = $ul.parentElement.parentElement.children[0];
            $input.value = this.innerText;
            $ul.innerHTML = "";

            navegarEnTablaExamenes($input, 40);
        });
    };

    $TR_SELECCIONADO = null;
    var deseleccionarAtencion = function(){
        if ($TR_SELECCIONADO != null){
            $TR_SELECCIONADO.classList.remove(CLASE_TR_SELECCIONADO);
        }
        $TR_SELECCIONADO = null;
        $blkRegistroResultados.hide();
        $blkRegistroMuestra.hide();

        $("#txt-idatencionmedicoservicioseleccionado").val("");
    };

    var seleccionarAtencion = function($tr){
        if ($TR_SELECCIONADO != null){
            $TR_SELECCIONADO.classList.remove(CLASE_TR_SELECCIONADO);
        }
        $TR_SELECCIONADO = $tr;
        $blkRegistroMuestra.show();
        $blkRegistroResultados.hide();
        listarAtencionesDetalle($tr.dataset.id);

        $blkRegistroMuestra.focus();
    };

    var listarAtenciones = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.controlador.php?op=listar_atenciones_laboratorio",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_fecha_inicio: $txtFechaInicio.val(),
                p_fecha_fin : $txtFechaFin.val(),
                p_tipo_filtro : $txtTipoFiltro.val()
            },
            success: function(result){
                renderAtenciones(result);
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    TABLA_ATENCIONES = null;
    var renderAtenciones = function(data) {
        if (TABLA_ATENCIONES){
            $tblAtenciones.DataTable().clear().destroy();
            TABLA_ATENCIONES = null;
        }

        $tbdAtenciones.html(tplAtenciones(data));
        if (!data.length){
            return;
        }

        TABLA_ATENCIONES = $tblAtenciones.DataTable({
            ordering: false,
            responsive: true,
        });
    };

    var listarAtencionesDetalle = function(id_atencion_medica, noQuitarDePantalla = false){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.controlador.php?op=listar_atencion_detalle_laboratorio",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_atencion_medica : id_atencion_medica
            },
            success: function(result){
                renderAtencionesDetalle(result, noQuitarDePantalla);
            },
            error: function (request) {
                toastr.error(request.responseText);
                deseleccionarAtencion();
                return;
            },
            cache: true
            }
        );
    };

    TABLA_ATENCIONES_DETALLE = null;
    var renderAtencionesDetalle = function(data, noQuitarDePantalla) {
        const $tblAtencionDetalle = $tbdAtencionDetalle.parent();
        const detalle = data.detalle,
            yaSeRegistroMuestra = data.ya_fue_registrado_muestra == "1"
            //,yaSeRegistroResultado = data.ya_fue_registrado_resultado == "1",
            //yaSeRegistroValidado = data.ya_fue_registrado_validado == "1";
            ;

        $lblRecibo.html(data.numero_recibo);
        $lblPaciente.html(data.paciente);

        if (TABLA_ATENCIONES_DETALLE){
            $tblAtencionDetalle.DataTable().clear().destroy();
            TABLA_ATENCIONES_DETALLE = null;
        }

        $tbdAtencionDetalle.html(tplAtencionDetalle({registros: detalle}));
        if (!detalle.length){
            return;
        }

        setTimeout(()=>{ 
            TABLA_ATENCIONES_DETALLE = $tblAtencionDetalle.DataTable({
                searching: false,
                ordering: false,
                responsive: true,
                paging:false,
                info: false,
                columnDefs: [
                    {width: 40, responsivePriority : 1, targets: 1},
                    {width: 50, targets: 2},
                    {width: 50, targets: 3},
                    {responsivePriority : 2, targets: 4},
                    {width: 120, targets: 5},
                    {width: 120, targets: 6},
                    {width: 120,targets: 7},
                    {width: 120, targets: 8},
                ] 
            });
        }, 100);
        

        if (yaSeRegistroMuestra){
            $btnGuardarMuestra.hide();
        } else {
            $btnGuardarMuestra.show();
        }
        /*
        if (yaSeRegistroResultado){
            $btnImprimirMuestra.prop("disabled", false);
        } else {
            $btnImprimirMuestra.prop("disabled", true);
        }
        */

        if (!noQuitarDePantalla){
            $blkRegistroMuestra.show();
            $blkRegistroResultados.hide();    
        }
        
    };

    var COLS_TABLA_ATENCION_DETALLE = {
        "opcion": 0,
        "imprimir" : 1,
        "fue_muestreado" : 2,
        "descripcion": 3,
        "fecha_hora_muestra" : 4,
        "fecha_hora_entrega": 5
    };

    var guardarMuestra = function(id_atencion_medica){
        if (id_atencion_medica == null){
            toastr.error("No se ha seleccionado ID de atención.");
            return;
        }

        var arregloExamenes = [];
        var $examenes = [].slice.call($tbdAtencionDetalle.find("tr[data-id]"));

        for (let index = 0; index < $examenes.length; index++) {
            var tr = $examenes[index],
                id_atencion_medica_servicio =  tr.dataset.id,
                fue_muestreado_antes = tr.dataset.fuemuestreadoantes,
                $fue_muestreado = tr.children[COLS_TABLA_ATENCION_DETALLE.fue_muestreado].children[0],
                fue_muestreado = 0,
                $fecha_hora_muestra, $fecha_hora_entrega,
                fecha_hora_muestra = "", fecha_hora_entrega = "";

            if (fue_muestreado_antes == "1"){
                continue;
            }

            if ($fue_muestreado){
                fue_muestreado = $fue_muestreado.checked;
            }

            if (fue_muestreado){
                $fecha_hora_muestra = tr.children[COLS_TABLA_ATENCION_DETALLE.fecha_hora_muestra].children[0];
                if ($fecha_hora_muestra){
                    fecha_hora_muestra = $fecha_hora_muestra.value;
                }

                if (fecha_hora_muestra == ""){
                    toastr.error("No se está ingresando una fecha y hora de muestra en un examen marcado como muestreado.");
                    return;
                }

                $fecha_hora_entrega = tr.children[COLS_TABLA_ATENCION_DETALLE.fecha_hora_entrega].children[0];
                if ($fecha_hora_entrega){
                    fecha_hora_entrega = $fecha_hora_entrega.value;
                }               
            } 

            if (id_atencion_medica_servicio == ""){
                continue;
            }

             if (fue_muestreado_antes == '1' && fecha_hora_entrega == ""){
                continue;
            }

            arregloExamenes.push({
                id_atencion_medica_servicio : id_atencion_medica_servicio,
                fue_muestreado: fue_muestreado,
                fecha_hora_muestra : fecha_hora_muestra,
                fecha_hora_entrega : fecha_hora_entrega
            });
        }

        if (!arregloExamenes.length){
            toastr.error("No hay nada que guardar.");
            return;
        }


        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.servicio.controlador.php?op=guardar_servicio_laboratorio_examen",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_atencion_medica : id_atencion_medica,
                p_arreglo_examenes: JSON.stringify(arregloExamenes)
            },
            success: function(result){
                toastr.success(result.msj);
                listarAtencionesDetalle(id_atencion_medica);
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    var listarServicioExamenLaboratorio = function(id_atencion_medica_servicio){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.servicio.controlador.php?op=listar_servicio_laboratorio_examen",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_atencion_medica_servicio : id_atencion_medica_servicio
            },
            success: function(result){
                $("#txt-idatencionmedicoservicioseleccionado").val(id_atencion_medica_servicio);
                renderServicioPruebaLaboratorio(result);
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    var CADENA_REGISTRO_PREVIO = null;
    var renderServicioPruebaLaboratorio = function(data) {
        var detalle = data.detalle;

        $lblServicioAtencion.html(data.servicio_atencion);        
        $tbdExamenes.html(tplExamenes({detalle: detalle, fue_validado : data.fue_validado, tiene_resultados : data.tiene_resultados}));

        $blkRegistroResultados.show();
        $blkRegistroMuestra.hide();

        $tbdExamenes.find("input").eq(0).select();

        if (data.fue_validado == "1"){
            $btnCancelarValidacion.show();
        } else {
            $btnCancelarValidacion.hide();
        }

        if (data.fue_validado == "0" && data.tiene_resultados == "1"){
            $btnValidarResultados.show();
        } else {
            $btnValidarResultados.hide();
        }

        if (data.fue_validado == "1"){
            $btnGuardarResultados.hide();
        } else {
            $btnGuardarResultados.show();
        }

        if (data.tiene_resultados == "1"){
            CADENA_REGISTRO_PREVIO = _obtenerCadenaRegistro();
        } else {
            CADENA_REGISTRO_PREVIO = null;
        }
    };

    var _obtenerCadenaRegistro = function() {
        //1.- obtener trssolotexto+cadena+ id: ID unico
        var arr = [].slice.call($tbdExamenes.find("tr input"));
        var cadena = "";
        arr.forEach($inputs => {
            cadena += $inputs.value;
        });
        
        return cadena;
    };

    var cancelarResultados = function(){
        if ($TR_SELECCIONADO){
            $blkRegistroMuestra.show();
            $blkRegistroResultados.hide();   
            $("#txt-idatencionmedicoservicioseleccionado").val("");
        }
    };

    var seConfirmoLosCambiosDeCadena = false;
    var validarResultados = function(deboPreguntarSiEstaSeguro = true){
        var id_atencion_medica_servicio = $("#txt-idatencionmedicoservicioseleccionado").val();
        if (id_atencion_medica_servicio == null || id_atencion_medica_servicio == ""){
            toastr.error("No se ha seleccionado ID de atención.");
            return;
        }

        if (deboPreguntarSiEstaSeguro == true){
            if (CADENA_REGISTRO_PREVIO != null){
                var cadena_actual = _obtenerCadenaRegistro();
                if (CADENA_REGISTRO_PREVIO != cadena_actual && !seConfirmoLosCambiosDeCadena){
                    if (confirm("Se ha detectado cambios en el registro de resultados. ¿Desea guardar estos cambios antes de validar?")){
                        seConfirmoLosCambiosDeCadena = true;
                        var validarInmediatamenteTrasGuardar = true;
                        guardarResultados(validarInmediatamenteTrasGuardar);
                        return;
                    }        
                }
            }

            if (!confirm("¿Está seguro de validar este examen/grupo de examen?")){
                return;
            }
        }

        seConfirmoLosCambiosDeCadena = false;

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.servicio.controlador.php?op=validar_servicio_laboratorio_examen_resultados",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_atencion_medica_servicio : id_atencion_medica_servicio
            },
            success: function(result){
                toastr.success(result.msj);
                if ($TR_SELECCIONADO){
                    listarAtencionesDetalle($TR_SELECCIONADO.dataset.id);
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

    var COLS_TABLA_RESULTADOS = {
        "descripcion": 0,
        "resultado": 1,
        "unidad" : 2,
        "valores_referencia": 3,
        "metodo" : 4,
        "agregar_filas" : 5
    };

    var guardarResultados = function(validarInmediatamenteTrasGuardar = false){
        var id_atencion_medica_servicio = $("#txt-idatencionmedicoservicioseleccionado").val();
        if (id_atencion_medica_servicio == null || id_atencion_medica_servicio == ""){
            toastr.error("No se ha seleccionado ID de atención.");
            return;
        }

        var arregloExamenesValores = [];
        var $examenes = [].slice.call($blkRegistroResultados.find("tbody tr"));

        for (let index = 0; index < $examenes.length; index++) {
            var tr = $examenes[index],
                trChildren = tr.children,
                id_lab_examen =  tr.dataset.id ?? "",
                nivel = tr.dataset.nivel,
                descripcion,
                resultado = trChildren[COLS_TABLA_RESULTADOS.resultado].children[0].value,
                unidad =  trChildren[COLS_TABLA_RESULTADOS.unidad].children[0].value,
                valores_referencia =  trChildren[COLS_TABLA_RESULTADOS.valores_referencia].children[0].value,
                metodo =  trChildren[COLS_TABLA_RESULTADOS.metodo].children[0].value;

            if (id_lab_examen == ""){
                if (trChildren[COLS_TABLA_RESULTADOS.descripcion].children.length){
                    descripcion = trChildren[COLS_TABLA_RESULTADOS.descripcion].children[0].value;
                } else {
                    descripcion = trChildren[COLS_TABLA_RESULTADOS.descripcion].innerText;
                }
            } else {
                descripcion = trChildren[COLS_TABLA_RESULTADOS.descripcion].innerText;
            }

            arregloExamenesValores.push({
                id_lab_examen : id_lab_examen,
                nivel: nivel,
                descripcion : descripcion,
                resultado : resultado,
                unidad: unidad,
                valores_referencia : valores_referencia,
                metodo : metodo
            });
        }

        if (!arregloExamenesValores.length){
            toastr.error("No hay nada que guardar.");
            return;
        }

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.servicio.controlador.php?op=guardar_servicio_laboratorio_examen_resultados",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_atencion_medica_servicio : id_atencion_medica_servicio,
                p_resultados_examenes_laboratorio : JSON.stringify(arregloExamenesValores)
            },
            success: function(result){
                toastr.success(result.msj);
                listarServicioExamenLaboratorio(id_atencion_medica_servicio);

                var noQuitarDePantalla = true;
                listarAtencionesDetalle(result.id_atencion_medica, noQuitarDePantalla);

                CADENA_REGISTRO_PREVIO = _obtenerCadenaRegistro();

                if (validarInmediatamenteTrasGuardar){
                    var deboPreguntarSiEstaSeguro = false;
                    validarResultados(deboPreguntarSiEstaSeguro)
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

    this.getTemplates();
    this.setDOM();
    this.setEventos();

    var hoy = new Date();
    Util.setFecha($txtFechaInicio, hoy);
    Util.setFecha($txtFechaFin, hoy);
    hoy = null;

    var obtenerAbreviaturas = function($input){
        var id_atencion_medica_servicio = $("#txt-idatencionmedicoservicioseleccionado").val();
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.abreviatura.controlador.php?op=obtener_abreviaturas",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_atencion_medica_servicio : id_atencion_medica_servicio
            },
            success: function(result){
                var $html = ``;
                result.forEach(o => {
                    $html += `<li>${o.descripcion}</li>`;
                });
                $input.next().find("ul").html($html);
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    var filtrarAbreviaturas = function($ul, cadena){
       var $lis = $ul.find("li");
       $lis.each(function(i,o){
           var txt = o.innerText,
            $o = $(o);
           if (!txt.includes(cadena)){
            $o.hide();
           } else {
            $o.show();
           }
       });
    };

    var imprimirResultadosExamenes = function(logo = "0", todo_junto = "0"){
        var id_atencion_medica;
        if (!$TR_SELECCIONADO){ 
            return;
        }
        
        var $trs = [].slice.call($tbdAtencionDetalle.find("tr"));
        var idams = []; 
        $trs.forEach($tr => {
            var $chk = $tr.children[COLS_TABLA_ATENCION_DETALLE.imprimir].children[0];            
            if ($chk && $chk.checked && !$chk.disabled){
                idams.push(parseInt($tr.dataset.id));
            }
        });

        if (!idams.length){
            toastr.error("No se ha seleccionado examenes a imprimir.");
            return;
        }

        id_atencion_medica = $TR_SELECCIONADO.dataset.id;

        var strIdams = JSON.stringify(idams);
        actualizarExamenesImpresos(id_atencion_medica, strIdams);
        window.open("../../../impresiones/examen.resultados.laboratorio.pdf.php?id="+id_atencion_medica+"&logo="+logo+"&idams="+strIdams+"&tj="+todo_junto, "1");
    };

    var cancelarValidacion = function(){
        var id_atencion_medica_servicio = $("#txt-idatencionmedicoservicioseleccionado").val();
        if (id_atencion_medica_servicio == null || id_atencion_medica_servicio == ""){
            toastr.error("No se ha seleccionado ID de atención.");
            return;
        }
       
        if (!confirm("¿Está seguro de cancelar la validación de este examen?")){
            return;
        }

        seConfirmoLosCambiosDeCadena = false;

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.servicio.controlador.php?op=cancelar_validar_servicio_laboratorio_examen_resultados",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_atencion_medica_servicio : id_atencion_medica_servicio
            },
            success: function(result){
                toastr.success(result.msj);
                listarServicioExamenLaboratorio(id_atencion_medica_servicio);

                var noQuitarDePantalla = true;
                listarAtencionesDetalle(result.id_atencion_medica, noQuitarDePantalla);

                CADENA_REGISTRO_PREVIO = _obtenerCadenaRegistro();
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    var navegarEnTablaExamenes = function($input, keyCode){
        var verificarHorizontalTr = function(esArriba){
            var $tr = $input.parentElement.parentElement,
                pos = $input.dataset.pos,
                rowIndex = $tr.rowIndex,
                $tdNuevo,
                $inputNuevo,
                nuevoRowIndex;

            var $tbdExamenesJS = $tbdExamenes[0];
            
            if (esArriba){
                nuevoRowIndex = rowIndex - 1;
                if (nuevoRowIndex <= 0){
                    return;
                }

            } else {
                nuevoRowIndex = rowIndex + 1;
                if (nuevoRowIndex > $tbdExamenes.find("tr").length){
                    return;
                }
            }

            $tdNuevo = $tbdExamenesJS.children[nuevoRowIndex - 1];
            if ($tdNuevo.children.length){
                $inputNuevo = $tdNuevo.children[pos].children[0];
                if ($inputNuevo){
                    $inputNuevo.focus();
                }
                return;
            }
            
        };

        var verificarVerticalTr = function(esIzquierda){
            var $tr = $input.parentElement.parentElement,
                pos = parseInt($input.dataset.pos ? $input.dataset.pos : "-1"),
                $tdNuevo,
                $inputNuevo;

            if (pos <= -1){
                return;
            }
            
            if (esIzquierda){
                pos--; 
                if (pos < 0){
                    console.log("off");
                    return;
                }

            } else {
                pos++;

                if (pos >= $tr.children.length - 1){
                    console.log("off");
                    return;
                }
            }
            
            $tdNuevo = $tr.children[pos];
            if ($tdNuevo.children.length){
                $inputNuevo = $tdNuevo.children[0];
                if ($inputNuevo){
                    $inputNuevo.focus();
                }
                return;
            }
        };

        switch(keyCode){
            case 37: //izquierda
                verificarVerticalTr(true);
                break;
            case 38: //arriba
                verificarHorizontalTr(true);
                break;
            case 39: //derecha
                verificarVerticalTr(false);
                break;
            case 40: //abajo
                verificarHorizontalTr(false);
                break;
            default:
                break;
        }
    };

    var actualizarExamenesImpresos = function(id_atencion_medica, strArregloIdExamenes){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.servicio.controlador.php?op=actualizar_servicio_laboratorio_examen_resultados_impresion",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_atencion_medica: id_atencion_medica,
                p_id_examenes_laboratorio : strArregloIdExamenes
            },
            success: function(result){
                var noQuitarDePantalla = true;
                listarAtencionesDetalle(id_atencion_medica, noQuitarDePantalla);
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    return this;
};

$(document).ready(function(){
    objAtenciones = new Atenciones();
});



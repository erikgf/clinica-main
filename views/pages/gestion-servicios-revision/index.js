var GestionServicioRevision = function() {
    var $txtFechaInicio,
        $txtFechaFin,
        $txtArea,
        $tblExamenes,
        $btnBuscar;

    var HACE_CUANTOS_DIAS = 0;
    var tplExamenes;

    var STR_AREA_CACHE = "cache_area",
        ARREGLO_MEDICOS_INFORMANTES = [],
        ARREGLO_MEDICOS_REALIZANTES = [];

    var ID_DENSITOMETRIA = 6;
    var ID_ECOGRAFIA = 1;

    this.setDOM = function(){
        var hoy = new Date();
        var haceDias = new Date(hoy.getTime());
        haceDias.setDate(hoy.getDate() - HACE_CUANTOS_DIAS);
        //haceSieteDias = new Date();
        $txtFechaInicio = $("#txt-fechainicio");
        $txtFechaFin = $("#txt-fechafin");
        $txtArea = $("#txt-area");
        $btnBuscar = $("#btn-buscar");

        Util.setFecha($txtFechaInicio, hoy);
        Util.setFecha($txtFechaFin, haceDias);

        $tblExamenes = $("#tbl-examenes");

    };
    
    this.setEventos = function(){
        $btnBuscar.on("click", function(e){
            listarExamenes();
        });

        $txtArea.on("change", function(e){
            e.preventDefault();
            if (this.value != ""){
                var optionSeleccionado = $txtArea.select2("data")[0];
                localStorage.setItem(STR_AREA_CACHE, JSON.stringify({id: optionSeleccionado.id, "text": optionSeleccionado.text})); 
                listarExamenes();
            }
        }); 

        $tblExamenes.on("click", ".btn-guardar", function(e) {
             e.preventDefault();
             guardarAtencion($(this));
        });
    };

    this.getTemplates = function(){
        var $getTemplate = $.get("template.examenes.php");
        var $getMedicosRealizantes = $.ajax({ 
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=obtener_medicos_realizantes",
            type: "POST",
            dataType: 'json',
            delay: 250,
            cache: true
        });
        var $getMedicosInformantes =  $.ajax({ 
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=obtener_medicos_informantes",
            type: "POST",
            dataType: 'json',
            delay: 250,
            cache: true
        });

        $.when($getTemplate, $getMedicosRealizantes, $getMedicosInformantes)
            .done(function(resTemplate, resMedicosRealizantes, resMedicosInformantes){
                if (resMedicosInformantes[1] == "success"){
                    ARREGLO_MEDICOS_INFORMANTES = resMedicosInformantes[0];
                }

                if (resMedicosRealizantes[1] == "success"){
                    ARREGLO_MEDICOS_REALIZANTES = resMedicosRealizantes[0];
                }

                if (resTemplate[1] == "success"){
                    tplExamenes = Handlebars.compile(resTemplate[0]);
                    listarExamenes();
                }

            })
            .fail(function(e1, e2){
                console.error(e1,e2);
            });
    };

    var TABLA_EXAMENES;
    var listarExamenes = function(){
        var area = $txtArea.val();
        if (!Util.validarFormulario($("#frm-busqueda"))){
            return;
        }

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.servicio.controlador.php?op=listar_examenes_asistentes",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_fecha_inicio : $txtFechaInicio.val(),
                p_fecha_fin : $txtFechaFin.val(),
                p_area : area
            },
            success: function(result){
                if (TABLA_EXAMENES){
                    TABLA_EXAMENES.destroy();
                    TABLA_EXAMENES = null;
                }
                $tblExamenes.find("tbody").html(tplExamenes({examenes: result, medicos_realizantes: ARREGLO_MEDICOS_REALIZANTES, medicos_informantes: ARREGLO_MEDICOS_INFORMANTES}));
                if (result.length){
                    TABLA_EXAMENES = $tblExamenes.DataTable({
                        "ordering": true
                    });
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

    this.TABLA_EXAMENES = function(){
        return TABLA_EXAMENES;
    };

    var guardarAtencion = function($btn){
        var $tr = $btn.parent().parent(),
            tr_dataset = $tr[0].dataset,
            id_atencion_medica_servicio = tr_dataset.id,
            fue_atendido = tr_dataset.fueatendido,
            $txtMedicoAtendido  = $tr.find(".txt-medicoatendido"),
            $txtMedicoRealizante =  $tr.find(".txt-medicorealizado"),
            id_medico_atendido = $txtMedicoAtendido.val(), //informante
            id_medico_realizante = $txtMedicoRealizante.val(),
            estado = $tr.find(".txt-estado").val(),
            observaciones = $tr.find(".txt-observaciones").val();

        var idArea = $txtArea.val();

        if (fue_atendido != "0"){
            if (!confirm("¿Este registro ya fue atendido con anterioridad, desea modificarlo?")){
                return;
            }
        }

        if (estado == "1"){
            if (idArea == ID_DENSITOMETRIA){
                id_medico_atendido = "";
                $txtMedicoAtendido.val("");
                id_medico_realizante = "";
                $txtMedicoRealizante.val("");
            } else {
                /*
                if (id_medico_atendido == ""){
                    toastr.error("Se debe seleccionar un médico informante");
                    return;
                }
                */
                if (idArea == ID_ECOGRAFIA){
                    id_medico_realizante = id_medico_atendido;
                    $txtMedicoRealizante.val($txtMedicoAtendido.val());
                }

                
            }
        }

        if (estado == "2"){
            if (!observaciones.length){
                toastr.error("Si se cancelará una atención debe ingresar una observación/motivo.");
                return;
            }

            $txtMedicoAtendido.val("");
            id_medico_atendido = "";

            $txtMedicoRealizante.val("");
            id_medico_realizante = "";
        }

        $btn.prop("disabled", true);
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.servicio.controlador.php?op=guardar_revision",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_atencion_medica_servicio : id_atencion_medica_servicio,
                p_id_medico_atendido : id_medico_atendido,
                p_id_medico_realizante : id_medico_realizante,
                p_observaciones: observaciones,
                p_estado : estado
            },
            success: function(result){
                toastr.success(result.msj);
                $btn.prop("disabled", false);
                var cadenaClaseBackground = {"0": "bg-white", "1": "bg-gradient-green", "2": "bg-gradient-red"};
                $tr.removeClass("bg-white bg-gradient-red bg-gradient-green").addClass(cadenaClaseBackground[estado]);
                $tr.data("fueatendido", estado);
                if (estado == "0"){
                    $txtMedicoRealizante.val("");
                    $txtMedicoAtendido.val("");
                    $tr.find(".txt-observaciones").val("");
                    $tr.attr("title", "Atendido: No fue atendido");
                } else {
                    $tr.attr("title", "Atendido: Fue Atendido "+result.fecha_hora_atendido);
                }
                
            },
            error: function (request) {
                $btn.prop("disabled", false);
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );

    };

    this.setDOM();
    this.setEventos();
    this.getTemplates();

    $txtArea.select2({
        ajax: { 
            url : VARS.URL_CONTROLADOR+"categoria.servicio.controlador.php?op=buscar_imagenes",
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
        placeholder:"Seleccionar área",
        debug: true,
        tags: false
    });

    var areaCache = localStorage.getItem(STR_AREA_CACHE);
    if (areaCache == undefined || areaCache == null){
        areaCache = {};
    } else{
        areaCache = JSON.parse(areaCache);
        $txtArea.append(new Option(areaCache.text, areaCache.id, true, true)).trigger('change');
    }
    areaCache = null;



    return this;
};

$(document).ready(function(){
    objGestionServicioRevision = new GestionServicioRevision(); 
});



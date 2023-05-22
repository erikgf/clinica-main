var GestionAtenciones = function() {
    var $txtFechaInicio,
        $txtFechaFin,
        $txtArea,
        $txtMedicosInformantes,
        $txtMedicosRealizantes,
        $btnActualizar,
        $btnExcel,
        $tblExamenes;

    var tplExamenes, tplPopoverPagos;
    var HACE_CUANTOS_DIAS= 0;
    var ESTADO = "*";
    var STR_AREA_CACHE = "cache_area";

    this.getTemplates = function(){
        var $templateExamenes = $.get("template.examenes.php");
        var $templatePopoverPagos = $.get("template.popover.pagos.php");

        $.when($templateExamenes, $templatePopoverPagos)
            .done(function(resExamenes, resPopoverPagos){
                if (resExamenes[1] == "success"){
                    tplExamenes = Handlebars.compile(resExamenes[0]);
                    listarExamenes();
                }

                if (resPopoverPagos[1] == "success"){
                    tplPopoverPagos = Handlebars.compile(resPopoverPagos[0]);
                }
            })
            .fail(function(e1,e2){
                console.error(e1,e2);
            });
    };


    this.setDOM = function(){
        var hoy = new Date();
        var haceDias = new Date(hoy.getTime());
        haceDias.setDate(hoy.getDate() - HACE_CUANTOS_DIAS);

        $txtFechaInicio = $("#txt-fechainicio");
        $txtFechaFin = $("#txt-fechafin");
        $btnActualizar = $("#btn-actualizarexamenes");

        $txtArea = $("#txt-area"); 
        $txtMedicosInformantes = $("#txt-medicoinformante");
        $txtMedicosRealizantes = $("#txt-medicorealizante");

        //haceSieteDias.setDate(haceSieteDias.getDate() - 7);
        //Util.setFecha($txtFechaInicio, haceSieteDias);
        Util.setFecha($txtFechaInicio, haceDias);
        Util.setFecha($txtFechaFin, hoy);

        $tblExamenes = $("#tbl-examenes");
        $btnExcel = $("#btn-excel");
    };
    
    this.setEventos = function(){
        $btnActualizar.on("click", function(e){
            listarExamenes();
        });
        /*
        $tblExamenes.on("click", ".btn-anularexamen", function (e) {
            var $btn = this,
                dataset = $btn.dataset;
            e.preventDefault();
            anularMovimiento(dataset.id, dataset.cliente);
        });
        */
        $txtMedicosInformantes.on("change", function(e){
            listarExamenes();
        });

        $txtMedicosRealizantes.on("change", function(e){
            listarExamenes();
        });

        $btnExcel.on("click", function(e){
            e.preventDefault();
            abrirExcel();
        });

        $tblExamenes.on("click", ".onmostrar-pagos", function(e) {
             e.preventDefault();
             mostrarPagos(this);
        });


        $(".cuadrado-estado").on("click", function(e){
            var $this = $(this),
                valor;
            if ($this.hasClass("cuadrado-estado-seleccionado")){
                return;
            }

            valor = $this.data("valor");
            $(".cuadrado-estado").removeClass("cuadrado-estado-seleccionado");
            $this.addClass("cuadrado-estado-seleccionado");
            ESTADO = valor;

            listarExamenes();
        });
    };

    var TABLA_EXAMENES;
    var listarExamenes = function(){
        var tmpHtml = $btnActualizar.html();
        $btnActualizar.prop("disabled", true);
        $btnActualizar.html("<span class='fa fa-spin fa-spinner'></span>");

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.servicio.controlador.php?op=listar_examenes_administrador",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_fecha_inicio : $txtFechaInicio.val(),
                p_fecha_fin : $txtFechaFin.val(),
                p_id_medico_atendido: $txtMedicosInformantes.val(),
                p_id_medico_realizante: $txtMedicosRealizantes.val(),
                p_id_area : $txtArea.val(),
                p_estado : ESTADO
            },
            success: function(result){
                $btnActualizar.prop("disabled", false);
                $btnActualizar.html(tmpHtml);

                if (TABLA_EXAMENES){
                    TABLA_EXAMENES.destroy();
                    TABLA_EXAMENES = null;
                }
                $tblExamenes.find("tbody").html(tplExamenes(result));
                if (result.length){
                    TABLA_EXAMENES = $tblExamenes.DataTable({
                        "ordering": true,
                        "scrollX": true
                    });
                }
            },
            error: function (request) {
                $btnActualizar.html(tmpHtml);
                $btnActualizar.prop("disabled", false);
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    this.TABLA_EXAMENES = function(){
        return TABLA_EXAMENES;
    }

    var abrirExcel = function(){
        window.open("../../../impresiones/ver.examenes.xls.php?fi="+$txtFechaInicio.val()+"&ff="+$txtFechaFin.val()+"&ma="+($txtMedicosInformantes.val() || "")+"&mr="+($txtMedicosRealizantes.val() || "")+"&est="+ESTADO+"&a="+($txtArea.val() || ""),"_blank")
    };

    var mostrarPagos = function(btn){
        var $btn = $(btn);

        if ($btn.data("popoveron") == "1"){
            $btn.data("popoveron", "0");
            $btn.popover("hide");
            $btn.html("<span class='fa fa-eye'></span>")
            return;
        }
        
        var idAtencionMedica = $btn.data("id");
        $btn.html("<span class='fa fa-spin fa-spinner'></span>");
        //toastr.success("Consultando... "+idAtencionMedica);

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.controlador.php?op=mostrar_pagos_de_atencion",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_atencion_medica : idAtencionMedica
            },
            success: function(result){
                if ($btn.data("popoveron") == "0"){
                    $btn.data("popoveron", "1");
                }

                $btn[0].dataset.content = tplPopoverPagos(result);
                $btn.popover('show');
                $btn.html("<span class='fa fa-eye-slash'></span>");
            },
            error: function (request) {
                $btn.data("popoveron", "0");
                $btn.popover("hide");
                $btn.html("<span class='fa fa-eye'></span>")
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
                var datos = response.datos;
                datos.push({id:"*", text: "TODAS LAS AREAS"});
                console.log(datos);
                return {
                    results: datos
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

    $txtMedicosRealizantes.select2({
        ajax: { 
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=obtener_medicos_realizantes",
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
                    results: response
                };
            },
            cache: true
        },
        width: '100%',
        multiple:false,
        placeholder:"Seleccionar médico realizante",
        debug: true,
        tags: false,
        allowClear: true
    });

    $txtMedicosInformantes.select2({
        ajax: { 
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=obtener_medicos_informantes",
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
                    results: response
                };
            },
            cache: true
        },
        width: '100%',
        multiple:false,
        placeholder:"Seleccionar médico informante",
        debug: true,
        tags: false,
        allowClear: true
    });
    
    return this;
};

$(document).ready(function(){
    objGestionAtenciones = new GestionAtenciones(); 
});



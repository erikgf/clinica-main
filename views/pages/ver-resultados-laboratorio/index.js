var GestionAtenciones = function() {
    var $txtCaja,
        $txtFechaInicio,
        $txtFechaFin,
        $tblAtenciones;

    var $mdlAtenciones,
        $tbdResultadosDetalle;

    var tplAtenciones, tplResultadosDetalle;

    this.getTemplates = function(){
        var template = "template.atenciones.php";
        $.get(template, function(result, state){
            if (state == "success"){
                tplAtenciones = Handlebars.compile(result);
                listar();
            }
        });

         template = "template.resultados.detalle.php";
        $.get(template, function(result, state){
            if (state == "success"){
                tplResultadosDetalle = Handlebars.compile(result);
            }
        });
    };

    this.setDOM = function(){
        $txtCaja = $("#txt-caja");
        var hoy = new Date();
        //haceSieteDias = new Date();
            
        $txtFechaInicio = $("#txt-fechainicio");
        $txtFechaFin = $("#txt-fechafin");

        //haceSieteDias.setDate(haceSieteDias.getDate() - 7);
        //Util.setFecha($txtFechaInicio, haceSieteDias);
        Util.setFecha($txtFechaInicio, hoy);
        Util.setFecha($txtFechaFin, hoy);

        $tblAtenciones = $("#tbl-atenciones");
        $mdlAtenciones = $("#mdl-resultadosdetalle");
        $tbdResultadosDetalle =  $("#tbd-resultadosdetalle");

    };
    
    this.setEventos = function(){
        $("#btn-actualizar").on("click", function(e){
            listar();
        });

        $txtFechaInicio.on("change", function(e){
            e.preventDefault();
            listar();
        });

        $txtFechaFin.on("change", function(e){
            e.preventDefault();
            listar();
        });

        $tblAtenciones.on("click", ".btn-preimprimir", function (e) {
            var $btn = this,
                dataset = $btn.dataset;
            e.preventDefault();
            preImprimir($btn, dataset.id, dataset.recibo, dataset.paciente);
        });

        $("#btn-imprimirlogo").on("click", function (e) {
            e.preventDefault();
            imprimir("1","0");
        });

        $("#btn-imprimirsinlogo").on("click", function (e) {
            e.preventDefault();
            imprimir("0","0");
        });

        $("#btn-imprimirtodojuntologo").on("click", function (e) {
            e.preventDefault();
            imprimir("1","1");
        });

        $("#btn-imprimirtodojuntosinlogo").on("click", function (e) {
            e.preventDefault();
            imprimir("0","1");
        });
    };

    var TABLA_ATENCIONES;
    var listar = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.controlador.php?op=listar_recepcion_laboratorio_resultados",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_fecha_inicio : $txtFechaInicio.val(),
                p_fecha_fin : $txtFechaFin.val()
            },
            success: function(result){
                if (TABLA_ATENCIONES){
                    $tblAtenciones.DataTable().clear().destroy();
                    TABLA_ATENCIONES = null;
                }

                $tblAtenciones.find("tbody").html(tplAtenciones(result));
                if (!result.length){
                    return;
                }
                
                if (result.length){
                    TABLA_ATENCIONES = $tblAtenciones.DataTable({
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

    var preImprimir = function($btn, id_atencion_medica, recibo, paciente){
        $btn.disabled = true;
        var fnResult = function(result){
            $btn.disabled = false;
            $mdlAtenciones.find("#txt-idatencionmedica").val(id_atencion_medica);
            $mdlAtenciones.find("#lbl-recibo").html(recibo+" - "+paciente);

            $tbdResultadosDetalle.html(tplResultadosDetalle(result));
            $mdlAtenciones.modal("show");
        };
        var fnError = function(request){
            $btn.disabled = false;
            toastr.error(request.responseText);
            return;
        };

        cargarResultadosDetalle(id_atencion_medica, fnResult, fnError);
    };

    var cargarResultadosDetalle = function(id_atencion_medica, fnResult, fnError){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.controlador.php?op=listar_recepcion_laboratorio_resultados_detalle",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_atencion_medica : id_atencion_medica,
            },
            success: fnResult,
            error: fnError,
            cache: true
            }
        );
    };

    var imprimir = function(logo = "0", todo_junto = "0"){
        var id_atencion_medica = $("#txt-idatencionmedica").val();
        if (id_atencion_medica == ""){
            return;
        }

        var $trs = [].slice.call($tbdResultadosDetalle.find("tr"));
        var idams = []; 
        $trs.forEach($tr => {
            var $chk = $tr.children[0].children[0];
            if ($chk && $chk.checked){
                idams.push(parseInt($tr.dataset.id));
            }
        });

        if (!idams.length){
            toastr.error("No se ha seleccionado examenes a imprimir.");
            return;
        }

        var strIdams = JSON.stringify(idams);
        actualizarExamenesImpresos(id_atencion_medica, strIdams);
        window.open("../../../impresiones/examen.resultados.laboratorio.pdf.php?id="+id_atencion_medica+"&logo="+logo+"+&idams="+strIdams+"&tj="+todo_junto, "1");
    };

    var actualizarExamenesImpresos = function(id_atencion_medica, strArregloIdExamenes){
        var fnResult = function(result){
            $tbdResultadosDetalle.html(tplResultadosDetalle(result));
        };
        var fnError = function(request){
            toastr.error(request.responseText);
            return;
        };

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
                cargarResultadosDetalle(id_atencion_medica, fnResult, fnError);       
                return;
            },
            error: function (request) {
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

    return this;
};

$(document).ready(function(){
    objGestionAtenciones = new GestionAtenciones(); 
});



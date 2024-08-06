var ReporteProduccionMedicos = function() {
    let $txtFechaInicio,
        $txtFechaFin,
        $txtMedicoInformante,
        $frmListar,
        $btnActualizar,
        $btnExcel,
        $tblExamenes;

    const HACE_CUANTOS_DIAS = 0;
    let tplExamenes;

    this.getTemplates = function(){
        const $templateExamenes = $.get("template.registros.hbs");

        $.when($templateExamenes)
            .done(function(resExamenes){
                tplExamenes = Handlebars.compile(resExamenes);
            })
            .fail(function(err){
                console.error(err);
            });
    };


    this.setDOM = function(){
        const hoy = new Date();
        const haceDias = new Date(hoy.getTime());
        haceDias.setDate(hoy.getDate() - HACE_CUANTOS_DIAS);

        $txtFechaInicio = $("#txt-fechainicio");
        $txtFechaFin = $("#txt-fechafin");
        $frmListar = $("#frm-listar");
        $btnActualizar = $("#btn-listar");

        $txtMedicoInformante = $("#txt-medicoinformante");

        //haceSieteDias.setDate(haceSieteDias.getDate() - 7);
        //Util.setFecha($txtFechaInicio, haceSieteDias);
        Util.setFecha($txtFechaInicio, haceDias);
        Util.setFecha($txtFechaFin, hoy);

        $tblExamenes = $("#tbl-examenes");
        $btnExcel = $("#btn-excel");
    };
    
    this.setEventos = function(){
        $frmListar.on("submit", function(e){
            e.preventDefault();
            listarExamenes();
        });
       
        $btnExcel.on("click", function(e){
            e.preventDefault();
            abrirExcel();
        });
    };

    var TABLA_EXAMENES;
    const listarExamenes = async function(){
        const tmpHtml = $btnActualizar.html();
        $btnActualizar.prop("disabled", true);
        $btnActualizar.html("<span class='fa fa-spin fa-spinner'></span>");

        try {
            const { datos } = await $.ajax({ 
                url : VARS.URL_CONTROLADOR+"atencion.medica.servicio.controlador.php?op=listar_produccion_medicos",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: {
                    p_fecha_inicio : $txtFechaInicio.val(),
                    p_fecha_fin : $txtFechaFin.val(),
                    p_id_medico: $txtMedicoInformante.val(),
                },
                cache: true
            });

            if (TABLA_EXAMENES){
                TABLA_EXAMENES.destroy();
                TABLA_EXAMENES = null;
            }

            $tblExamenes.find("tbody").html(tplExamenes(datos));
            if (datos.length){
                TABLA_EXAMENES = $tblExamenes.DataTable({
                    "ordering": true,
                    "scrollX": true,
                    "pageLength": 50
                });
            }
        } catch (err) {
            console.error(err);
            toastr.error(err.responseText);
        } finally {
            $btnActualizar.html(tmpHtml);
            $btnActualizar.prop("disabled", false);
        }

        
    };

    this.TABLA_EXAMENES = function(){
        return TABLA_EXAMENES;
    }

    var abrirExcel = function(){
        window.open(`../../../impresiones/medicos.produccion.xls.php?fi=${$txtFechaInicio.val()}&ff=${$txtFechaFin.val()}&m=${$txtMedicoInformante.val() || ""}`,"_blank")
    };

    this.setDOM();
    this.setEventos();
    this.getTemplates();

    $txtMedicoInformante.select2({
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
    objReporteProduccionMedicos = new ReporteProduccionMedicos(); 
});



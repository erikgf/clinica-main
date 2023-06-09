var Reportes = function() {
    var $txtFechaInicio,
        $txtFechaFin,
        $txtSede,
        $txtArea,
        $txtEstado,
        $tblExamenes,
        $frm;

    let $btnListar;
    let tplExamenes;
    var TABLA_EXAMENES;


    this.getTemplates = async () => {
        const $templateExamenes = await $.get("./template.examenes.hbs");
        tplExamenes = Handlebars.compile($templateExamenes);
    };

    this.setDOM = function(){
        $txtFechaInicio = $("#txt-fechainicio");
        $txtFechaFin = $("#txt-fechafin");
        $txtSede = $("#txt-sede");
        $txtArea = $("#txt-area");
        $txtEstado = $("#txt-estado");
        $tblExamenes = $("#tbl-examenes");

        $btnListar = $("#btn-listar");

        $frm = $("form");
    };
    
    this.setEventos = function(){
        $("#btn-excel").on("click", function(e){
            e.preventDefault();
            imprimirExcel();
        });

        $frm.on("submit", (e)=>{
            e.preventDefault();
            this.listarExamenes();
        });
    };

    var imprimirExcel = function(){
        var fi = $txtFechaInicio.val(),
            ff = $txtFechaFin.val(),
            sede = $txtSede.val(),
            areas = $txtArea.val(),
            estado = $txtEstado.val();

        window.open(`../../../impresiones/atenciones.sede.xls.php?fi=${fi}&ff=${ff}&s=${sede}&e=${estado}&ar=${JSON.stringify(areas)}`);
    };

    const getHTMLForCombo = (data)=>{
        let html = `<option value="*" selected>Todos</option>`;

        data.forEach(o=>{
            html += `<option value=${o.id}>${o.descripcion}</option>`;    
        });

        return html;
    };

    this.listarExamenes = function(){
        const tmpHtml = $btnListar.html();
        $btnListar.prop("disabled", true);
        $btnListar.html("<span class='fa fa-spin fa-spinner'></span>");

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.servicio.controlador.php?op=listar_examenes_atenciones_por_sede",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_fecha_inicio : $txtFechaInicio.val(),
                p_fecha_fin : $txtFechaFin.val(),
                p_estado: $txtEstado.val(),
                p_id_area : $txtArea.val(),
                p_sede : $txtSede.val()
            },
            success: function(result){
                $btnListar.prop("disabled", false);
                $btnListar.html(tmpHtml);

                if (TABLA_EXAMENES){
                    TABLA_EXAMENES.destroy();
                    TABLA_EXAMENES = null;
                }
                $tblExamenes.find("tbody").html(tplExamenes(result));
                if (result.length){
                    TABLA_EXAMENES = $tblExamenes.DataTable({
                        "pageLength": 25,
                        "ordering": true,
                        "scrollX": true
                    });
                }
            },
            error: function (request) {
                $btnListar.html(tmpHtml);
                $btnListar.prop("disabled", false);
                toastr.error(request.responseText);
                return;
            },
            always : function(){
                console.log("always");
            },
            cache: true
            }
        );
    };

    this.listarAreas = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"categoria.servicio.controlador.php?op=listar_solo__asistentes",
            type: "POST",
            dataType: 'json',
            delay: 250,
            success: function(result){
                $txtArea.html(getHTMLForCombo(result));
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            }
        })
    };

    this.listarSedes = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"sede.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            success: function(result){
                $txtSede.html(getHTMLForCombo(result));
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            }
        })
    };

    this.setDOM();
    this.setEventos();
    this.getTemplates();

    this.listarAreas();
    this.listarSedes();

    return this;
};



$(document).ready(function(){
    objReportes = new Reportes();
});



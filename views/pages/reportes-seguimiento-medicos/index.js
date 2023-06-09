var Reportes = function() {
    var $txtFechaInicio,
        $txtFechaFin,
        $txtMonto,
        $txtArea,
        $txtPromotora,
        $txtSede,
        $frm,
        $btnListar;

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
        $txtPromotora = $("#txt-promotora");
        $frm = $("form");
        $btnListar = $("#btn-listar");
        $tblExamenes = $("#tbl-examenes");
        $txtMonto = $("#txt-monto");
    };
    
    this.setEventos = function(){

        Handlebars.registerHelper('renderKey', function (item, key) {
            return item[key]
        });

        $("#btn-excel").on("click", function(e){
            e.preventDefault();
            imprimirExcel();
        });

        $frm.on("submit", (e)=>{
            e.preventDefault();
            this.listarExamenes();
        });
    };

    const imprimirExcel = function(){
        let fi = $txtFechaInicio.val(),
            ff = $txtFechaFin.val(),
            monto = $txtMonto.val(),
            area =  $txtArea.val(),
            promotora = $txtPromotora.val(),
            sede= $txtSede.val();

        window.open(`../../../impresiones/liq.seguimiento.medicos.xls.php?fi=${fi}&ff=${ff}&m=${monto}&area=${JSON.stringify(area)}&promo=${JSON.stringify(promotora)}&sede=${JSON.stringify(sede)}`);
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
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=listar_liquidaciones_seguimiento_medico",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_fecha_inicio : $txtFechaInicio.val(),
                p_fecha_fin : $txtFechaFin.val(),
                p_id_area : $txtArea.val(),
                p_id_promotora : $txtPromotora.val(),
                p_sede : $txtSede.val(),
                p_monto: $txtMonto.val()
            },
            success: function(result){
                const { fechas, data } = result;
                const registros = [];

                let i = 0;
                let lastMedico = null;
                let objMedicoRegistrar = {};

                data.forEach((reg,i)=>{
                    let medico = reg.medico, promotora, mesAnioAtencion;
                    if (lastMedico == null || lastMedico != medico){
                        if (lastMedico != null){
                            registros.push(objMedicoRegistrar);
                        }
                        i = 0;

                        objMedicoRegistrar = {};
                        
                        promotora = reg.promotora;
                        fechasTemp = [...fechas];

                        objMedicoRegistrar.medico = medico;
                        objMedicoRegistrar.promotora = promotora;
                    }

                    mesAnioAtencion = reg.mes_anio_atencion;

                    for (let index = 0; index < fechasTemp.length; index++) {
                        const fecha = fechasTemp[index];
                        if (fecha == mesAnioAtencion){
                            objMedicoRegistrar[fecha] = reg.comision_sin_igv;
                            fechasTemp.splice(0, index + 1);
                            break;
                        }

                        objMedicoRegistrar[fecha] = "";
                    }

                    lastMedico = medico;
                });

                registros.push(objMedicoRegistrar);

                $btnListar.prop("disabled", false);
                $btnListar.html(tmpHtml);

                if (TABLA_EXAMENES){
                    TABLA_EXAMENES.destroy();
                    TABLA_EXAMENES = null;
                }
                $tblExamenes.html(tplExamenes({
                    fechas,
                    registros
                }));

                if (registros.length){
                    TABLA_EXAMENES = $tblExamenes.DataTable({
                        "pageLength": 50,
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

    this.listarPromotoras = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"promotora.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            success: function(result){
                let $comboHTML = getHTMLForCombo(result)+' '+'<option value="0">NO TIENE</option>';
                $txtPromotora.html($comboHTML);
                
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
    this.listarPromotoras();

    var hoy = new Date();
    Util.setFecha($txtFechaInicio, hoy);
    Util.setFecha($txtFechaFin, hoy);
    hoy = null;

    return this;
};

$(document).ready(function(){
    objReportes = new Reportes();
});



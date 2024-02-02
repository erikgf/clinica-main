var LiquidacionesMedicos = function() {
    var $txtFechaInicio,
        $txtFechaFin,
        $txtSede,
        $txtMedicos,
        $tblMedicos,
        $tbdMedicos,
        $btnVerResultados,
        $btnImprimirPDF,
        $btnImprimirEXCEL;

    var tplMedicos;

    this.getTemplates = function(){
        var $reqMedicos =  $.get("template.liquidaciones.medicos.php");
        
        $.when($reqMedicos)
            .done(function(resMedicos){
                tplMedicos = Handlebars.compile(resMedicos);

                cargarMedicos();
            })
            .fail(function(e1){
                console.error(e1);
            });
    };

    this.setDOM = function(){
        var $DOM = $("#blk-tabs-liquidacionmedicos");

        $txtFechaInicio = $DOM.find(".txt-fechainicio-liquidaciontotal");
        $txtFechaFin  = $DOM.find(".txt-fechafin-liquidaciontotal");
        $txtTotalesMayoresA  =  $DOM.find("#txt-totalesmayores-liquidaciontotal");
        $txtSede = $DOM.find("#txt-sede-liquidaciontotal");
        $tblMedicos =  $DOM.find("#tbl-medicos-liquidaciontotal");
        $tbdMedicos =  $DOM.find("#tbd-medicos-liquidaciontotal");
        $btnVerResultados = $DOM.find(".btn-verresultados-liquidaciontotal");
        $btnImprimirPDF = $DOM.find(".btn-imprimir-liquidaciontotal-pdf");
        $btnImprimirEXCEL = $DOM.find(".btn-imprimir-liquidaciontotal-excel");

        $DOM = null;
    };
    
    this.setEventos = function(){
        $txtFechaInicio.on("change", function(e){
            if (this.value == ""){
                Util.setFecha($txtFechaInicio, new Date());
            }

            //cargarMedicos();
            //renderMedicos([]);
        });

        $txtFechaFin.on("change", function(e){
            if (this.value == ""){
                Util.setFecha($txtFechaFin, new Date());
            }

            //cargarMedicos();
            //renderMedicos([]);
        }); 


        $txtTotalesMayoresA.on("change", function(e){
            if (this.value == ""){
                $txtTotalesMayoresA = "0.00";
            }

            //cargarMedicos();
            //renderMedicos([]);
        });

        $btnVerResultados.on("click", function(e){
            e.preventDefault();
            renderMedicos([]);
            cargarMedicos();
        });

        $btnImprimirPDF.on("click", function(e){
            e.preventDefault();
            const sentData = {
                fi: $txtFechaInicio.val(),
                ff: $txtFechaFin.val(),
                tt: $txtTotalesMayoresA.val(),
                s : $txtSede.val()
            };

            const params = new URLSearchParams(sentData);
            window.open(`../../../impresiones/liquidacion.medicos.php?${params.toString()}`,"_blank");
        });

        $btnImprimirEXCEL.on("click", function(e){
            e.preventDefault();

            const sentData = {
                fi: $txtFechaInicio.val(),
                ff: $txtFechaFin.val(),
                tt: $txtTotalesMayoresA.val(),
                s : $txtSede.val()
            };

            const params = new URLSearchParams(sentData);
            window.open(`../../../impresiones/liquidacion.medicos.xls.php?${params.toString()}`,"_blank");
        });
    };

    const cargarMedicos = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=listar_liquidacion_medicos",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_fecha_inicio: $txtFechaInicio.val(),
                p_fecha_fin : $txtFechaFin.val(),
                p_totales_mayores : $txtTotalesMayoresA.val(),
                p_id_sede : $txtSede.val()
            },
            success: function(result){
                renderMedicos(result);
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    var renderMedicos = function(medicos) {
        if (!medicos.length){
            $tbdMedicos.html(tplMedicos([]));
            $("#txt-total-comision-liquidaciontotal").html("S/ 0.00");
            return;
        }

        $tbdMedicos.html(tplMedicos(medicos));

        var totalComision = 0;

        medicos.forEach(o => {
            totalComision += parseFloat(o.comision_sin_igv);
        });

        $("#txt-total-comision-liquidaciontotal").html("S/ "+Math.round10(totalComision, -2).toFixed(2));
    };

    const cargarSedes = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"sede.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            success: function(result){
                let html = ``;

                html += `<option value="">Todas</option>`;
                result.forEach(sede => {
                    html += `<option value="${sede.id}">${sede.descripcion}</option>`;
                });

                $txtSede.html(html);
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

    cargarSedes();

    return this;
};

$(document).ready(function(){
    objLiquidacionesMedicos = new LiquidacionesMedicos();
});



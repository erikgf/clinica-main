var LiquidacionesIndividualMedicos = function() {
    var $txtFechaInicio,
        $txtFechaFin,
        $txtMedicos,
        $tblAtenciones,
        $tbdAtenciones,
        $btnImprimir;

    var tplAtenciones, $txtTotalesMayoresA;

    this.getTemplates = function(){
        var $reqAtenciones =  $.get("template.liquidaciones.medicos.atenciones.php");
        
        $.when($reqAtenciones)
            .done(function(resAtenciones){
                tplAtenciones = Handlebars.compile(resAtenciones);
            })
            .fail(function(e1){
                console.error(e1);
            });
    };

    this.setDOM = function(){
        var $DOM = $("#blk-tabs-liquidacionindividualmedico");

        $txtFechaInicio = $DOM.find(".txt-fechainicio");
        $txtFechaFin  = $DOM.find(".txt-fechafin");
        $txtMedicos  =  $DOM.find("#txt-medicos-liquidacion");
        $txtTotalesMayoresA  =  $DOM.find("#txt-totalesmayores-liquidacion");
        $tblAtenciones =  $DOM.find("#tbl-atenciones");
        $tbdAtenciones =  $DOM.find("#tbd-atenciones");
        $btnImprimir = $DOM.find(".btn-imprimir");

        $DOM = null;
    };
    
    this.setEventos = function(){
        $txtMedicos.on("change", function(e){
            /*
            if (this.value == ""){
                renderAtenciones([]);
                return;
            }
            */
            cargarAtenciones();
        });

        $txtFechaInicio.on("focusout", function(e){
            if (this.value == ""){
                Util.setFecha($txtFechaInicio, new Date());
            }

            cargarMedicos();
            cargarAtenciones();
        });

        $txtFechaFin.on("focusout", function(e){
            if (this.value == ""){
                Util.setFecha($txtFechaFin, new Date());
            }

            cargarMedicos();
            cargarAtenciones();
        }); 


        $txtTotalesMayoresA.on("change", function(e){
            if (this.value == ""){
                $txtTotalesMayoresA = "100.00";
            }

            cargarMedicos();
            cargarAtenciones();
        }); 

        $btnImprimir.on("click", function(e){
            e.preventDefault();
            window.open("../../../impresiones/liquidacion.individual.medicos.php?fi="+$txtFechaInicio.val()+"&ff="+$txtFechaFin.val()+"&tt="+$txtTotalesMayoresA.val()+"&idm="+$txtMedicos.val(),"_blank")
        });
    };

    TABLA_ATENCIONES  = null;
    var cargarAtenciones = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=listar_atenciones_comision_liquidacion_medico",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_medico : $txtMedicos.val(),
                p_fecha_inicio: $txtFechaInicio.val(),
                p_fecha_fin : $txtFechaFin.val(),
                p_totales_mayores : $txtTotalesMayoresA.val()
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

    var renderAtenciones = function(atenciones) {
        if (!atenciones.length){
            $tbdAtenciones.html(tplAtenciones([]));
            $("#txt-total-totalsinigv").html("0.00");
            $("#txt-total-comision").html("0.00");
            return;
        }

        $tbdAtenciones.html(tplAtenciones(atenciones));

        var totalTotalSinIgv = 0,
            totalComision = 0;

        atenciones.forEach(o => {
            totalTotalSinIgv += parseFloat(o.subtotal_sin_igv);
            totalComision += parseFloat(o.sin_igv);
        });

        $("#txt-total-totalsinigv").html(Math.round10(totalTotalSinIgv, -2).toFixed(2));
        $("#txt-total-comision").html(Math.round10(totalComision, -2).toFixed(2));
    };

    var cargarMedicos = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=listar_para_liquidaciones",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_fecha_inicio: $txtFechaInicio.val(),
                p_fecha_fin : $txtFechaFin.val(),
                p_totales_mayores : $txtTotalesMayoresA.val()
            },
            success: function(result){
                var html = ``;

                html += `<option value="">Todos los médicos</option>`;
                result.forEach(medico => {
                    html += `<option value="${medico.id_medico}">${medico.descripcion}</option>`;
                });

                $txtMedicos.html(html);
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

    cargarMedicos();
    cargarAtenciones();
    
    return this;
};

$(document).ready(function(){
    objLiquidacionesIndividualMedicos = new LiquidacionesIndividualMedicos();
});



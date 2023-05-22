var GestionAtenciones = function() {
    var $txtFechaInicio,
        $txtFechaFin,
        $btnExcel,
        $tblMovimientos;

    var tplMovimientos;
    this.getTemplates = function(){
            $.get("template.movimientos.php", function(result, state){
                if (state == "success"){
                    tplMovimientos = Handlebars.compile(result);
                    listarMovimientos();
                }
            });
        };


    this.setDOM = function(){
        var hoy = new Date();
        //haceSieteDias = new Date();
        $txtFechaInicio = $("#txt-fechainicio");
        $txtFechaFin = $("#txt-fechafin");

        //haceSieteDias.setDate(haceSieteDias.getDate() - 7);
        //Util.setFecha($txtFechaInicio, haceSieteDias);
        Util.setFecha($txtFechaInicio, hoy);
        Util.setFecha($txtFechaFin, hoy);

        $tblMovimientos = $("#tbl-cajamovimientos");
        $btnExcel = $("#btn-excel");
    };
    
    this.setEventos = function(){
        $("#btn-actualizarmovimientos").on("click", function(e){
            listarMovimientos();
        });

        /*
        $txtFechaInicio.on("change", function(e){
            e.preventDefault();
            listarMovimientos();
        });

        $txtFechaFin.on("change", function(e){
            e.preventDefault();
            listarMovimientos();
        });
        */

        $btnExcel.on("click", function(e){
            e.preventDefault();
            abrirExcel();
        });
    };

    var TABLA_MOVIMIENTOS;
    var listarMovimientos = function(){

        var $btn = $("#btn-actualizarmovimientos");
        var str = $btn.html();

        $btn.html("LISTANDO...").prop("disabled", true);

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"documento.electronico.controlador.php?op=listar_atenciones_comprobante",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_fecha_inicio : $txtFechaInicio.val(),
                p_fecha_fin : $txtFechaFin.val()
            },
            success: function(result){
                var series = result.series,
                    totales = result.totales,
                    otros_totales = result.otros_totales;

                $btn.html(str).prop("disabled", false);
                var comprobantes = [];
                series.forEach(serie => {
                    comprobantes = comprobantes.concat(serie.comprobantes);
                });


                if (TABLA_MOVIMIENTOS){
                    TABLA_MOVIMIENTOS.destroy();
                    TABLA_MOVIMIENTOS = null;
                }
                $tblMovimientos.find("tbody").html(tplMovimientos(comprobantes));

                if (comprobantes.length){
                    TABLA_MOVIMIENTOS = $tblMovimientos.DataTable({
                        "ordering": true,
                        "scrollX": true,
                        "lengthMenu": [ 25, 50, 100, 150 ],
                        "pageLength": 25
                    });
                }

                $("#txt-totalgravadas").val(totales.total_gravadas.toFixed(2));
                $("#txt-totaligv").val(totales.total_igv.toFixed(2));
                $("#txt-importetotal").val(totales.importe_total.toFixed(2));


                $("#txt-efectivo").val(otros_totales.total_efectivo.toFixed(2));
                $("#txt-credito").val(otros_totales.total_credito.toFixed(2));
                $("#txt-deposito").val(otros_totales.total_deposito.toFixed(2));
                $("#txt-tarjeta").val(otros_totales.total_tarjeta.toFixed(2));
            },
            error: function (request) {
                $btn.html(str).prop("disabled", false);
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };


    this.TABLA_MOVIMIENTOS = function(){
        return TABLA_MOVIMIENTOS;
    };

    var abrirExcel = function(){
        window.open("../../../impresiones/atenciones.reporte.facturacion.xls.php?fi="+$txtFechaInicio.val()+"&ff="+$txtFechaFin.val(),"_blank")
    };

    
    this.setDOM();
    this.setEventos();
    this.getTemplates();
    
    return this;
};

$(document).ready(function(){
    objGestionAtenciones = new GestionAtenciones(); 
});



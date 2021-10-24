var ReportesLaboratorio = function() {
    var $txtFechaInicio,
        $txtFechaFin;

    this.setDOM = function(){
        $txtFechaInicio = $("#txt-fechainicio");
        $txtFechaFin = $("#txt-fechafin");
    };
    
    this.setEventos = function(){
        $("#btn-pdf").on("click", function(e){
            e.preventDefault();
            imprimirPDF();
        });

        $("#btn-excel").on("click", function(e){
            e.preventDefault();
            imprimirExcel();
        });
    };

    var imprimirExcel = function(){
        var fi = $txtFechaInicio.val(),
            ff = $txtFechaFin.val();
        window.open("../../../impresiones/laboratorio.reporte.atenciones.xls.php?fi="+fi+"&ff="+ff);
    };  

    var imprimirPDF = function(){
        var fi = $txtFechaInicio.val(),
            ff = $txtFechaFin.val();
        window.open("../../../impresiones/laboratorio.reporte.atenciones.pdf.php?fi="+fi+"&ff="+ff);
    };

    this.setDOM();
    this.setEventos();

    var hoy = new Date();
    Util.setFecha($txtFechaInicio, hoy);
    Util.setFecha($txtFechaFin, hoy);
    hoy = null;

    return this;
};

$(document).ready(function(){
    objReportesLaboratorio = new ReportesLaboratorio();
});



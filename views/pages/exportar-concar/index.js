const ExportarConcar = function() {
    var $txtFechaInicio,
        $txtFechaFin,
        $txtCorrelativoInicio,
        $frm;

    this.setDOM = function(){
        $txtFechaInicio = $("#txt-fechainicio");
        $txtFechaFin = $("#txt-fechafin");
        $txtCorrelativoInicio = $("#txt-correlativo-inicio");

        $btnExportar = $("#btn-exportar");
        $frm = $("form");
    };
    
    this.setEventos = function(){
        $frm.on("submit", (e)=>{
            e.preventDefault();
            this.exportarExcelConcar();
        });
    };

    this.exportarExcelConcar = function(){
        var fi = $txtFechaInicio.val(),
            ff = $txtFechaFin.val(),
            correlativo = $txtCorrelativoInicio.val();

        //window.open(`../../../impresiones/exportar.concar.xls.php?fi=${fi}&ff=${ff}&c=${correlativo}`, 0);
        window.open(`../../../impresiones/exportar.concar.anexos.xls.php?fi=${fi}&ff=${ff}`, 1);
    };

    this.setDOM();
    this.setEventos();

    return this;
};



$(document).ready(function(){
    objReportes = new ExportarConcar();
});



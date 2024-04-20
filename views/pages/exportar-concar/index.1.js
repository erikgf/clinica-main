const ExportarConcar = function() {
    var $txtFechaInicio,
        $txtFechaFin,
        $txtCorrelativoInicio,
        $btnExportar,
        $frmVentasAnexos;

    var $txtFechaInicioCancelaciones,
        $txtFechaFinCancelaciones,
        $txtCorrelativoInicioCancelaciones,
        $btnExportarCancelaciones,
        $frmCancelaciones;

    this.setDOM = function(){
        $txtFechaInicio = $("#txt-fechainicio");
        $txtFechaFin = $("#txt-fechafin");
        $txtCorrelativoInicio = $("#txt-correlativo-inicio");

        $btnExportar = $("#btn-exportar");
        $frmVentasAnexos = $("#frm-ventasanexos");

        $txtFechaInicioCancelaciones = $("#txt-fechainiciocancelaciones");
        $txtFechaFinCancelaciones = $("#txt-fechafincancelaciones");
        $txtCorrelativoInicioCancelaciones = $("#txt-correlativocancelaciones-inicio");
        $btnExportarCancelaciones = $("#btn-exportarcancelaciones");
        $frmCancelaciones = $("#frm-cancelaciones");
    };
    
    this.setEventos = function(){
        $frmVentasAnexos.on("submit", (e)=>{
            e.preventDefault();
            this.exportarExcelConcar();
        });

        $frmCancelaciones.on("submit", (e)=>{
            e.preventDefault();
            this.exportarExcelConcarCancelaciones();
        });
    };

    this.exportarExcelConcar = function(){
        var fi = $txtFechaInicio.val(),
            ff = $txtFechaFin.val(),
            correlativo = $txtCorrelativoInicio.val();

        window.open(`../../../impresiones/exportar.concar.xls.php?fi=${fi}&ff=${ff}&c=${correlativo}`, 0);
        window.open(`../../../impresiones/exportar.concar.anexos.xls.php?fi=${fi}&ff=${ff}`, 1);
    };

    this.exportarExcelConcarCancelaciones = function(){
        var fi = $txtFechaInicioCancelaciones.val(),
            ff = $txtFechaFinCancelaciones.val(),
            correlativo = $txtCorrelativoInicioCancelaciones.val();

        window.open(`../../../impresiones/exportar.concar.cancelacionesventa.xls.php?fi=${fi}&ff=${ff}&c=${correlativo}`, 0);
    };

    this.setDOM();
    this.setEventos();

    return this;
};



$(document).ready(function(){
    objReportes = new ExportarConcar();
});



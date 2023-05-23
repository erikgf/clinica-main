var Reportes = function() {
    var $txtFechaInicio,
        $txtFechaFin,
        $txtMonto;

    this.setDOM = function(){
        $txtFechaInicio = $("#txt-fechainicio");
        $txtFechaFin = $("#txt-fechafin");
        $txtMonto = $("#txt-monto");
    };
    
    this.setEventos = function(){
        $("#btn-excel").on("click", function(e){
            e.preventDefault();
            imprimirExcel();
        });
    };

    var imprimirExcel = function(){
        var fi = $txtFechaInicio.val(),
            ff = $txtFechaFin.val(),
            monto = $txtMonto.val();

        window.open("../../../impresiones/liq.seguimiento.medicos.xls.php?fi="+fi+"&ff="+ff+"&m"+monto);
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
    objReportes = new Reportes();
});



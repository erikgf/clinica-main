var ReportesCajas = function() {
    var $txtFecha,
        $txtCajas,
        $btnExcel;

    this.setDOM = function(){
        $btnExcel = $("#btn-excel");
        $txtFecha = $("#txt-fecha");
        $txtCajas = $("#txt-cajasdisponibles");
    };
    
    this.setEventos = function(){
        $("#frm-excel").on("submit", function(e){
            e.preventDefault();
            imprimirExcel();
        });

        $txtFecha.on("change", (e)=>{
            obtenerCajasDisponibles($txtFecha.val());
        });
    };

    const getHTMLForCombo = (data)=>{
        let html = "";
        data.forEach(o=>{
            html += `<option value=${o.id}>${o.descripcion}</option>`;    
        });

        return html;
    };

    const obtenerCajasDisponibles = (fecha) => {
        const $ldnCajasDisponibles = $("#ldn-cajasdisponibles");
        $btnExcel.prop("disabled", true);

        $ldnCajasDisponibles.html('<i class="fa fa-spin fa-spinner"></i>');
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"caja.reportes.controlador.php?op=obtener_caja_instancia_por_fecha",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_fecha : fecha
            },
            success: function(result){
                $ldnCajasDisponibles.empty();
                $btnExcel.prop("disabled", false);
                $txtCajas.html(getHTMLForCombo(result));
            },
            error: function (request) {
                $ldnCajasDisponibles.empty();
                $btnExcel.prop("disabled", false);
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );


    };

    var imprimirExcel = function(){
        var f = $txtFecha.val(), cajas = $txtCajas.val();
        
        window.open("../../../impresiones/reporte.cajas.xls.php?f="+f+"&cs="+JSON.stringify(cajas));
    };  

    this.setDOM();
    this.setEventos();

    var hoy = new Date();
    Util.setFecha($txtFecha, hoy);
    hoy = null;

    return this;
};

$(document).ready(function(){
    objReporte = new ReportesCajas();
});



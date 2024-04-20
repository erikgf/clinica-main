const GenerarReportePromotora = function({id}){
    this.$ = null;
    this.$el = null;

    let $txtFechaInicio, $txtFechaFin, $btnImprimirPDF, $btnImprimirEXCEL;

    this.init = () => {
        this.$ = window.$;
        if (!id){
            return null;
        }

        this.$el = this.$(id);
        this.setDOM();
        this.setEventos();
    };

    this.setDOM = () => {
        $txtFechaInicio = this.$el.find("#txt-fechainicio");
        $txtFechaFin = this.$el.find("#txt-fechafin");
        $btnImprimirPDF = this.$el.find("#btn-imprimir-pdf");
        $btnImprimirEXCEL = this.$el.find("#btn-imprimir-excel");
    };

    this.setEventos = () => {
        $btnImprimirPDF.on("click", (e) => {
            e.preventDefault();
            this.generarPDF({fechaInicio: $txtFechaInicio.val(), fechaFin: $txtFechaFin.val()});
        });
        
        $btnImprimirEXCEL.on("click", (e) => {
            e.preventDefault();
            this.generarEXCEL({fechaInicio: $txtFechaInicio.val(), fechaFin: $txtFechaFin.val()});
        });

        const date = new Date();
        const [strDate] = date.toISOString().split("T");
        $txtFechaInicio.val(strDate);
        $txtFechaFin.val(strDate);
    };

    this.generarPDF = ({fechaInicio, fechaFin}) =>{
        window.open(`../../../impresiones/medicos.promotoras.promotora.php?fi=${fechaInicio}&ff=${fechaFin}`,"_blank");
    };

    this.generarEXCEL = ({fechaInicio, fechaFin}) => {
        window.open(`../../../impresiones/medicos.promotoras.promotora.xls.php?fi=${fechaInicio}&ff=${fechaFin}`,"_blank");
    };

    return this.init();
};
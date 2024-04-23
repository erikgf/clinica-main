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
        this.limitarFechas();
    };

    this.setDOM = () => {
        $form = this.$el.find("form");
        $txtFechaInicio = this.$el.find("#txt-fechainicio");
        $txtFechaFin = this.$el.find("#txt-fechafin");
        $btnImprimirPDF = this.$el.find("#btn-imprimir-pdf");
        $btnImprimirEXCEL = this.$el.find("#btn-imprimir-excel");
    };

    this.setEventos = () => {
        $form.on("submit", (e)=>{
            e.preventDefault();
            const { originalEvent : { submitter : $btn} } = e;
            const id = $btn.id;
            const fechaInicio =  $txtFechaInicio.val(), fechaFin = $txtFechaFin.val();

            if (id === "btn-imprimir-pdf"){
                this.generarPDF({fechaInicio, fechaFin});
                return;
            }

            if (id === "btn-imprimir-excel"){
                this.generarEXCEL({fechaInicio, fechaFin});
                return;
            }
        });

        const date = new Date();
        const [strDate] = date.toISOString().split("T");
        $txtFechaInicio.val(strDate);
        $txtFechaFin.val(strDate);
    };

    this.limitarFechas = () => {
        const date = new Date();
        const [strDateHoy] = date.toISOString().split("T");
        $txtFechaInicio.prop("max", strDateHoy);
        $txtFechaInicio.prop("max", strDateHoy);

        date.setDate(date.getDate() - 90); //90 días atrás;
        const [strDate] = date.toISOString().split("T");;
        $txtFechaInicio.prop("min", strDate);
        $txtFechaFin.prop("min", strDate);
    };

    this.generarPDF = ({fechaInicio, fechaFin}) =>{
        window.open(`../../../impresiones/medicos.promotoras.promotora.php?fi=${fechaInicio}&ff=${fechaFin}`,"_blank");
    };

    this.generarEXCEL = ({fechaInicio, fechaFin}) => {
        window.open(`../../../impresiones/medicos.promotoras.promotora.xls.php?fi=${fechaInicio}&ff=${fechaFin}`,"_blank");
    };

    return this.init();
};
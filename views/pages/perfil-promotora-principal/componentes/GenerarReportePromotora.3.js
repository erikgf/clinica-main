const GenerarReportePromotora = function({id}){
    this.$ = null;
    this.$el = null;

    let $txtFechaInicio, $txtFechaFin, $btnImprimirPDF, $btnImprimirEXCEL;
    let $txtMesAnio;

    this.init = () => {
        this.$ = window.$;
        if (!id){
            return null;
        }

        this.$el = this.$(id);
        this.setDOM();
        this.setEventos();
        this.renderMesesAños();
        //this.limitarFechas();
    };

    this.setDOM = () => {
        $form = this.$el.find("form");
        //$txtFechaInicio = this.$el.find("#txt-fechainicio");
        //$txtFechaFin = this.$el.find("#txt-fechafin");
        $txtMesAnio = this.$el.find("#txt-mesanio");
        $btnImprimirPDF = this.$el.find("#btn-imprimir-pdf");
        $btnImprimirEXCEL = this.$el.find("#btn-imprimir-excel");
    };

    this.setEventos = () => {
        $form.on("submit", (e)=>{
            e.preventDefault();
            const { originalEvent : { submitter : $btn} } = e;
            const id = $btn.id;
            /*
            const fechaInicio =  $txtFechaInicio.val(), fechaFin = $txtFechaFin.val();

            if (id === "btn-imprimir-pdf"){
                this.generarPDF({fechaInicio, fechaFin});
                return;
            }

            if (id === "btn-imprimir-excel"){
                this.generarEXCEL({fechaInicio, fechaFin});
                return;
            }
            */
            const mesAño =  $txtMesAnio.val();
            const [mes , año] = mesAño.split("_");

            if (id === "btn-imprimir-pdf"){
                this.generarPDF({mes, año});
                return;
            }

            if (id === "btn-imprimir-excel"){
                this.generarEXCEL({mes, año});
                return;
            }
        });

        /*
        const date = new Date();
        const [strDate] = date.toISOString().split("T");
        $txtFechaInicio.val(strDate);
        $txtFechaFin.val(strDate);
        */
    };

    /*
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
    */
    this.generarPDF = ({mes, año}) =>{
        window.open(`../../../impresiones/medicos.promotoras.php?m=${mes}&a=${año}`,"_blank");
    };

    this.generarEXCEL = ({mes, año}) => {
        window.open(`../../../impresiones/medicos.promotoras.xls.php?m=${mes}&a=${año}`,"_blank");
    };

    this.renderMesesAños = async () => {
        const date = new Date();
        const CANTIDAD_MESES = 3;
        const mesActualBase = date.getMonth() + 1;
        const añoActualBase = date.getFullYear();
        const mesesAños = [];
        let añoActual, mesActual;
        

        for (let index = 0; index < CANTIDAD_MESES; index++) {
            mesActual = mesActualBase - index;
            if ( mesActual <= 0){
                mesActual = mesActual + 12;
                añoActual = añoActualBase - 1;
            } else {
                añoActual = añoActualBase;
            }
            
            mesActual = mesActual < 10 ? `0${mesActual}` : mesActual;

            mesesAños.unshift({
                mes: mesActual, 
                año : añoActual
            });
        }

        const resSelect = await $.get("template.select.noseleccionar.hbs");
        const templateSelect = Handlebars.compile(resSelect);

        const meses = Util.getMeses();
        let mesAñoSeleccionado = "";

        console.log({meses});
        $txtMesAnio.html(templateSelect(mesesAños.map( mesAño => {
            const { descripcion: nombreMes} = meses.find( mes => mes.id == mesAño.mes);
            const id =  `${mesAño.mes}_${mesAño.año}`;
            mesAñoSeleccionado = id;
            return {
                id, descripcion: `${nombreMes} - ${mesAño.año}` 
            }
        })));

        $txtMesAnio.val(mesAñoSeleccionado);
    };

    return this.init();
};
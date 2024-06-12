const App = function () {
    const TEMPLATE_NAME_SELECT = "template.select.hbs";
    this.template = null;

    this.init = async () => {
        //templates
        const resSelect = await $.get(TEMPLATE_NAME_SELECT);
        this.template = { 
            select: Handlebars.compile(resSelect),
        };

        this.cargarPromotoras();
        this.renderMesesAños();

        //initTabs
        this.objLiquidacioneSinSobre = new LiquidacionesSinSobre({app: this});
        this.objLiquidacioneSinSobre = new EntregasSobres({app: this});

        return this;
    };
   
    this.renderMesesAños = async () => {
        const $txtMes = $(".txt-mes");
        const $txtAño = $(".txt-año");
        const meses = Util.getMeses();
        const años = Util.getAños();

        $txtMes.html(this.template.select({opciones: meses, "es_seleccionar":false}));
        $txtAño.html(this.template.select({ opciones: años.map( año => ({ id: año, descripcion: año })), "es_seleccionar" : false}));

        const date = new Date();
        let mesActualBase = date.getMonth();
        let mesActual = mesActualBase === 0 ? 12 : mesActualBase;
        const anioActual = mesActualBase === 0 ? date.getFullYear() - 1 : date.getFullYear();
        mesActual = mesActual < 10 ? `0${mesActual}` : mesActual;

        $txtMes.val(mesActual);
        $txtAño.val(anioActual);
    };

    this.cargarPromotoras = () => {
        const $txtPromotora = $(".txt-promotora");
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"promotora.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: (result) => {
                $txtPromotora.html(this.template.select({opciones: result, es_seleccionar: true }));
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    return this.init();
};

$(document).ready(function(){
    new App();
});
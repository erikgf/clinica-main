const ResumenCantidadExamenes = function ({id, renderData = null}){
    this.$ = window.$;
    this.$el = null;
    this.template = null;
      
    this.data = null;
    this.primerRender = false;

    const TEMPLATE_NAME = "./components/template.resumen.cantidad.examenes.php";

    this.init = () => {
        if (id){
            this.$el = $(id);
        }

        this.requestTemplate();

        if (renderData){
            this.data = renderData;
            this.show(renderData);
        }
    }

    this.requestTemplate = async () => {
        const res = await this.$.get(TEMPLATE_NAME);
        this.template = Handlebars.compile(res);
    };

    this.render = (_data) => {
        this.$el.html(this.template({data: _data})).show();
        if (this.primerRender == false){
            this.primerRender = true;
        }
    };

    this.show = (_data) => {
        this.render(_data);
    };

    this.hide = () => {
        this.$el.hide();
    };

    return this.init();
}
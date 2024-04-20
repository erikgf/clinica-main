const HistorialPaciente = function ({id, idPaciente}){
    this.$ = window.$;
    this.$el = null;
    this.template = null;
    this.cargando = false;
      
    this.data = null;
    this.idPaciente = null;
    this.primerRender = false;

    const TEMPLATE_NAME = "./template.historial.paciente.php";

    this._setCargando = (isCargando) => {
        if (isCargando){
            this.cargando = true;
            this.$el.html(` <div class="HistorialPaciente_container local-loader">
                                <div class="overlay" style="height: 300px">
                                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                                </div>
                            </div>`);
            return;
        } 
        this.cargando = false;
        this.$el.find(".local-loader").remove();
    }

    this.init = () => {
        if (id){
            this.$el = $(id);
        }

        if (idPaciente){
            this.idPaciente = idPaciente;
        }

        this.requestTemplate();
        if (idPaciente){
            this.requestData(idPaciente);
        }
    }

    this.requestTemplate = async () => {
        const res = await this.$.get(TEMPLATE_NAME);
        this.template = Handlebars.compile(res);
    };

    this.requestData = async (idPaciente) => {
        this._setCargando(true);

        try{
            const res = await this.$.ajax({
                url: VARS.URL_CONTROLADOR+"historial.paciente.controlador.php?op=listar",
                type: "post",
                dataType: 'json',
                delay: 5000,
                data: {
                    id_paciente : idPaciente
                }
            });

            this.data = res;
            this.render(this.data);
        } catch ( error ){
            console.error(error);
        } finally {
            this._setCargando(false);
        }

    };

    this.render = (_data) => {
        this.$el.html(this.template({data: _data.map( area => { return {...area, cantidad_registros: area.items.length}})})).show();
        if (this.primerRender == false){
            this.primerRender = true;
        }
    };

    this.show = (idPaciente) => {
        this.requestData(idPaciente);
    };

    this.hide = () => {
        this.$el.hide();
    };

    return this.init();
}
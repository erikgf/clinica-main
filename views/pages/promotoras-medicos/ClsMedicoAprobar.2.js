const MedicoAprobar = function({id}){
    this.$ = null;
    this.$el = null;
    this.template = null;
    this.data = null;
    this.cargando = false;
    this.primerRender = false;
    this.htmlCargando = `<div class="local-loader">
                            <div class="overlay" style="height: 300px">
                                <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                            </div>
                        </div>`;
    
    const TEMPLATE_NAME = "./template.lst.medicos.aprobar.hbs";
    let DT = null;

    let $cboEstado, $tblMain, $tbdMain;

    this.init = () => {
        this.$ = window.$;
        if (!id){
            return null;
        }

        this.$el = this.$(id);

        this.setDOM();
        this.setEventos();
        this.requestTemplates();

        this.listar();
        return this;
    };

    this.setDOM = () => {
        $cboEstado = this.$el.find("#txt-medico-estado");
        $tblMain = this.$el.find("table");
        $tbdMain = this.$el.find("tbody");
    };

    this.setEventos = () => {
        this.$el.on("click",".on-refresh", () => {
            if (this.$el.hasClass("collapsed-card")){
                this.$el.find(".btn-tool").click();
            }

            this.listar();
        });

        this.$el.on("change", "select", () => {
            if (this.$el.hasClass("collapsed-card")){
                this.$el.find(".btn-tool").click();
            }

            this.listar();
        });

        this.$el.on("click",".btn-tool", ()=>{
            if (this.firstTimeOpenedCard == false){
                this.firstTimeOpenedCard = true;
                this.listar();
            }
       });

        $tbdMain.on("click", "tr button.on-aprobar", (e) =>{
            const $btn = $(e.currentTarget);
            this.aprobar($btn);
        });

        $tbdMain.on("click", "tr button.on-rechazar", (e) =>{
            const $btn = $(e.currentTarget);
            this.rechazar($btn);
        });
    };

    this._setCargando = (isCargando) => {
        const $zonaLoader = this.$el.find(".zona-loader");
        if (isCargando){
            this.cargando = true;
            $zonaLoader.html(this.htmlCargando).show();
            return;
        } 
        this.cargando = false;
        $zonaLoader.find(".local-loader").remove();
        $zonaLoader.hide();
    }

    this.requestTemplates = async () => {
        const res = await this.$.get(TEMPLATE_NAME);
        this.template = { 
            lista: Handlebars.compile(res),
        };
    };

    this.render = (_data) => {
        
        if (DT){
            DT.destroy();
            DT = null;
        }

        $tbdMain.html(this.template.lista(_data));

        if (_data.length > 0){
            DT = $tblMain.DataTable({
                "responsive":true,
                "pageLength": 10,
            });
        }

        if (this.primerRender == false){
            this.primerRender = true;
        }
    };

    this.aprobar = async ($btn) => {
        const id = $btn.data("id");

        $btn.prop("disabled", true);

        try{
            await this.$.ajax({
                url: VARS.URL_CONTROLADOR+"medico.promotora.admin.controlador.php?op=aprobar",
                type: "post",
                dataType: 'json',
                delay: 5000,
                data: {
                    p_id_medico : id
                }
            });

            toastr.success("Aprobado correctamente.");
            this.listar();

        } catch ( error ){
            console.error(error);
        } finally {
            $btn.prop("disabled", false);
        }
    };

    this.rechazar = async ($btn) => {
        const descripcion = $btn.data("descripcion");
        const observaciones = prompt(`Ingrese el motivo/observaciones del rechazo del MÉDICO: ${descripcion}`);

        if (observaciones == null){
            return;
        } 

        const id = $btn.data("id");

        $btn.prop("disabled", true);

        try{
            await this.$.ajax({
                url: VARS.URL_CONTROLADOR+"medico.promotora.admin.controlador.php?op=rechazar",
                type: "post",
                dataType: 'json',
                delay: 5000,
                data: {
                    p_id_medico : id,
                    p_observaciones : observaciones

                }
            });

            toastr.success("Rechazado correctamente.");
            this.listar();

        } catch ( error ){
            console.error(error);
        } finally {
            $btn.prop("disabled", false);
        }
    };

    this.listar = async () => {
        const $btnActualizar = this.$el.find(".on-refresh");
        this._setCargando(true);
        $btnActualizar.prop("disabled", true);

        try{
            const res = await this.$.ajax({
                url: VARS.URL_CONTROLADOR+"medico.promotora.admin.controlador.php?op=listar_para_aprobar",
                type: "post",
                dataType: 'json',
                delay: 5000,
                data: {
                    p_estado: $cboEstado.val()
                }
            });

            this.data = res;
            this.render(this.data);

            if ($cboEstado.val() == 'P'){
                const cantidadPendientes= this.data?.length;
                const $tabPromotorasMedicos = this.$("#tab-promotoras-medicos");
                this.$el.find("#lbl-cantidad-medicosaprobar").html(cantidadPendientes);
    
                if (cantidadPendientes > 0){
                    $tabPromotorasMedicos.addClass("alertas-disponibles");
                    $tabPromotorasMedicos.find("b").html(`(${cantidadPendientes})`);
                } else {
                    $tabPromotorasMedicos.removeClass("alertas-disponibles");
                    $tabPromotorasMedicos.find("b").empty();
                    this.$(".navbar-nav.ml-auto").remove();
                }

                return;
            }
            
        } catch ( error ){
            console.error(error);
        } finally {
            this._setCargando(false);
            $btnActualizar.prop("disabled", false);
        }
    };

    return this.init();
};
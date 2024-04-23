const MantenimientoMedicosActivar = function({id}){
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
    
    const TEMPLATE_NAME = "./template.lst.medicos.activar.hbs";

    let $tblMain, $tbdMain, $modalRegistro;
    this.firstTimeOpenedCard = false;

    this.init = () => {
        this.$ = window.$;
        if (!id){
            return null;
        }

        this.$el = this.$(id);

        this.requestTemplates();
        this.setDOM();
        this.setEventos();

        return this;
    };

    this.setDOM = () => {
        $tblMain = this.$el.find("table");
        $tbdMain = this.$el.find("tbody");
        $modalRegistro = $("#mdl-medico");
    };

    this.setEventos = () => {
       this.$el.on("click",".btn-tool", ()=>{
            if (this.firstTimeOpenedCard == false){
                this.firstTimeOpenedCard = true;
                this.listar();
            }
       });

       this.$el.on("click",".on-refresh", () => {
            this.actualizar();
       });

       this.$el.on("click", ".on-new-record", () => {
            this.nuevoRegistro();
       });

        $tbdMain.on("click", "tr button.on-edit", (e) =>{
            const $btn = $(e.currentTarget);
            this.leer($btn);
        });

        $tbdMain.on("click", "tr button.on-delete", (e) =>{
            const $btn = $(e.currentTarget);
            this.eliminar($btn);
        });

       $modalRegistro.on("submit","form", (e) => {
            e.preventDefault();
            const form = e.currentTarget;
            console.log({form});
            this.guardar();
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
        $tbdMain.html(this.template.lista(_data)).show();
        if (this.primerRender == false){
            this.primerRender = true;
        }
    };

    this.guardar = async () => {
        const $form = $modalRegistro.find("form");
        const $btnGuardar = $form.find(":submit");

        $btnGuardar.prop("disabled", true);
        const dataForm = {
            p_id_medico : $form[0].id_medico_seleccionado.value,
            p_numero_documento : $form[0].numero_documento.value,
            p_colegiatura : $form[0].colegiatura.value,
            p_id_especialidad : $form[0].especialidad.value,
            p_fecha_nacimiento : $form[0].fecha_nacimiento.value,
            p_apellidos_nombres : $form[0].apellidos_nombres.value,
        };

        const action = dataForm.p_id_medico == "" 
                            ? "guardar"
                            : "editar";

        try{
            const data = await this.$.ajax({
                url: `${VARS.URL_CONTROLADOR}medico.promotora.controlador.php?op=${action}`,
                type: "post",
                dataType: 'json',
                delay: 5000,
                data: dataForm
            });

            if (this.data){
                this.data.push(data);
                this.render(this.data);
            }
            
            $modalRegistro.modal("hide");
            toastr.success("Registro realizado correctamente!");

        } catch ( error ){
            console.error(error);
            toastr.error(error);
        } finally {
            $btnGuardar.prop("disabled", false);
        }
    }

    this.nuevoRegistro = () => {
        const $form = $modalRegistro.find("form");
        $modalRegistro.find(".modal-title").html("Nuevo Médico");
        $form[0].reset();

        $modalRegistro.modal("show");
    };

    this.leer = async ($btn) => {
        const $form = $modalRegistro.find("form");
        const id = $btn.data("id");

        $btn.prop("disabled", true);
        $modalRegistro.find(".modal-title").html("Editando Médico");
        $modalRegistro.modal("show");

        try{
            const data = await this.$.ajax({
                url: VARS.URL_CONTROLADOR+"medico.promotora.controlador.php?op=leer",
                type: "post",
                dataType: 'json',
                delay: 5000,
                data: {
                    p_id_medico : id
                }
            });

            const { elements } = $form[0];
            elements.id_medico_seleccionado.value = id;
            elements.numero_documento.value =  data.numero_documento;
            elements.colegiatura.value =  data.cmp;
            elements.especialidad.value =  data.id_especialidad;
            elements.fecha_nacimiento.value =  data.fecha_nacimiento;
            elements.apellidos_nombres.value =  data.nombres_apellidos;

        } catch ( error ){
            console.error(error);
        } finally {
            $btn.prop("disabled", false);
        }
    };

    this.actualizar = () => {
        if (this.$el.hasClass("collapsed-card")){
            this.firstTimeOpenedCard = false;
            this.$el.find(".btn-tool").click();
            return;
        }

        this.listar();
    };

    this.listar = async () => {
        const $btnActualizar = this.$el.find(".on-refresh");
        this._setCargando(true);
        $btnActualizar.prop("disabled", true);

        try{
            const res = await this.$.ajax({
                url: VARS.URL_CONTROLADOR+"medico.promotora.controlador.php?op=listar",
                type: "post",
                dataType: 'json',
                delay: 5000,
            });

            this.data = res;
            this.render(this.data);
        } catch ( error ){
            console.error(error);
        } finally {
            this._setCargando(false);
            $btnActualizar.prop("disabled", false);
        }
    };

    this.eliminar = async ($btn) => {
        if (!confirm("¿Está seguro de eliminar este registro?")){
            return;
        }
        const id = $btn.data("id");

        $btn.prop("disabled", true);
        try{
            const data = await this.$.ajax({
                url: `${VARS.URL_CONTROLADOR}medico.promotora.controlador.php?op=anular`,
                type: "post",
                dataType: 'json',
                delay: 5000,
                data: {
                    p_id_medico : id
                }
            });


            const $tr = $btn.parents("tr");
            $tr.remove();

            if (this.data){
                this.data = this.data.filter( item => {
                    return item.id != id
                });
            }
        } catch ( error ){
            console.error(error);
        } finally {
            $btn.prop("disabled", false);
        }
    };

    return this.init();
};
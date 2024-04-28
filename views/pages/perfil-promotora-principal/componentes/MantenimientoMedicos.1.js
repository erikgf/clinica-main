const MantenimientoMedicos = function({id}){
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
    
    const TEMPLATE_NAME = "./template.lst.medicos.activos.hbs";
    let DT = null;
    let $TR_SELECCIONADO = null;

    let $tblMain, $tbdMain;

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
        $tblMain = this.$el.find("table");
        $tbdMain = this.$el.find("tbody");
        $modalRegistro = $("#mdl-medico-viejo");
    };

    this.setEventos = () => {
       this.$el.on("click",".on-refresh", () => {
            this.listar();
       });

       $tbdMain.on("click", "tr button.on-edit", (e) =>{
            const $btn = $(e.currentTarget);
            this.leer($btn);
        });

        $modalRegistro.on("submit","form", (e) => {
            e.preventDefault();
            this.guardar();
       });

       $modalRegistro.on("hide.bs.modal", () => {
            $TR_SELECCIONADO = null;
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
        }

        $tbdMain.html(this.template.lista(_data)).show();
        DT = $tblMain.DataTable({
            "responsive":true,
            "pageLength": 10,
            /*
            "columns": [
                    { "width": "75px" },
                    null,
                    { "width": "135px" },
                    { "width": "115px" },
                    { "width": "115px" },
                    { "width": "115px" }
                  ]
            */
        });

        if (this.primerRender == false){
            this.primerRender = true;
        }
    };

    this.guardar = async () => {
        const $form = $modalRegistro.find("form");
        const $btnGuardar = $form.find(":submit");

        $btnGuardar.prop("disabled", true);
        const dataForm = {
            p_id_medico_modificado : $form[0].id_medico_seleccionado.value,
            //p_numero_documento : $form[0].numero_documento.value,
            p_colegiatura : $form[0].colegiatura.value,
            p_id_especialidad : $form[0].especialidad.value,
            p_fecha_nacimiento : $form[0].fecha_nacimiento.value,
            p_apellidos_nombres : $form[0].apellidos_nombres.value,
            p_celular: $form[0].celular.value,
            p_direccion: $form[0].direccion.value,
            p_id_sede : $form[0].sede.value
        };

        try{
            await this.$.ajax({
                url: `${VARS.URL_CONTROLADOR}medico.promotora.controlador.php?op=editar_viejo`,
                type: "post",
                dataType: 'json',
                delay: 5000,
                data: dataForm
            });

            toastr.success("Registro realizado correctamente!");

            if (DT){
                if ($TR_SELECCIONADO){
                    console.log({$TR_SELECCIONADO});
                    DT.row($TR_SELECCIONADO).remove().draw();
                }
            }

            $modalRegistro.modal("hide");

            if(pMantenimientoMedicosActivar){
                pMantenimientoMedicosActivar.actualizar();
            }

        } catch ( error ){
            console.error(error);
            toastr.error(error);
        } finally {
            $btnGuardar.prop("disabled", false);
        }
    }

    this.listar = async () => {
        const $btnActualizar = this.$el.find(".on-refresh");
        this._setCargando(true);
        $btnActualizar.prop("disabled", true);

        try{
            const res = await this.$.ajax({
                url: VARS.URL_CONTROLADOR+"medico.promotora.controlador.php?op=listar_medicos_activos",
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

    this.leer = async ($btn) => {
        const $form = $modalRegistro.find("form");
        const id = $btn.data("id");

        $btn.prop("disabled", true);
        $modalRegistro.find(".modal-title").html("Editando Médico");
        $modalRegistro.modal("show");

        try{
            const data = await this.$.ajax({
                url: VARS.URL_CONTROLADOR+"medico.promotora.controlador.php?op=leer_medico",
                type: "post",
                dataType: 'json',
                delay: 5000,
                data: {
                    p_id_medico : id
                }
            });

            const { elements } = $form[0];
            elements.id_medico_seleccionado.value = id;
           // elements.numero_documento.value =  data.numero_documento;
            elements.colegiatura.value =  data.cmp;
            elements.especialidad.value =  data.id_especialidad;
            elements.fecha_nacimiento.value =  data.fecha_nacimiento;
            elements.apellidos_nombres.value =  data.nombres_apellidos;
            elements.celular.value =  data.celular;
            elements.direccion.value =  data.direccion;
            elements.sede.value =  data.id_sede;

            $TR_SELECCIONADO = $btn.parents("tr");

        } catch ( error ){
            console.error(error);
        } finally {
            $btn.prop("disabled", false);
        }
    };

    return this.init();
};
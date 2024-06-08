const InformeExamenes = function () {
    const TEMPLATE_NAME_SELECT = "template.select.hbs";
    const TEMPLATE_NAME_LIST = "template.lst.informes.hbs";
    const TEMPLATE_NAME_HEADER = "template.blk.informecabecera.hbs";
    this._data = null;
    this._cadenaIDOrden = "";
    this.oEditor = null;

    this.init  =  () => {
        this.setDOM();
        this.setEventos();

        this.requestTemplates();

        return this;
    };

    this.setDOM = () => {
        this.$frmBuscar = $("#frm-buscar");
        this.$txtMedico = $("#txt-medico");
        this.$txtFechaInicio = $("#txt-fechainicio");
        this.$txtFechaFin = $("#txt-fechafin");
        this.$btnBuscar = $("#btn-buscar");
        this.$tbdInformes = $("#tbd-informes");
        this.$btnGuardarOrden = $("#btn-guardarorden");

        this.$mdlInforme = $("#mdl-informe");
        this.$blkInformeCabecera = $("#blk-informecabecera");
        this.$editor = $("#editor");
        this.$btnInformeGuardar = $("#btn-informeguardar");

        const hoy = new Date();
        Util.setFecha(this.$txtFechaInicio, hoy);
        Util.setFecha(this.$txtFechaFin, hoy);


        this.oEditor = new Quill('#editor', {
            modules: {
              toolbar: [
                ['bold', 'italic', 'underline'],
                [{ align: '' }, { align: 'center' }, { align: 'right' }, { align: 'justify' }],
                [{ list: 'bullet' }]
              ],
            },
            placeholder: 'Redactar informe...',
            theme: 'snow', // or 'bubble'
        });
    };

    this.setEventos = () => {

        this.$frmBuscar.on("submit", (e) => {
            e.preventDefault();
            this.obtenerInformes();
        });

        this.$txtMedico.on("change", (e) => {
            this.renderInformes(e.currentTarget.value);
        });

        this.$tbdInformes.on("click", ".on-down", (e)=>{
            const $tr = e.currentTarget.parentElement.parentElement;
            this.moverInforme($tr, "down");
        });

        this.$tbdInformes.on("click", ".on-up", (e)=>{
            const $tr = e.currentTarget.parentElement.parentElement;
            this.moverInforme($tr, "up");
        });

        this.$tbdInformes.on("click", ".on-edit", (e)=>{
            this.leer(e.currentTarget.dataset.id);
        });

        this.$tbdInformes.on("click", ".on-descargar", (e)=>{
            this.descargar(e.currentTarget.dataset.id);
        });

        this.$btnGuardarOrden.on("click", ()=>{
            this.guardarNuevoOrden();
        });

        this.$btnInformeGuardar.on("click", () => {
            this.informeGuardar();
        });
    };

    this.requestTemplates = async () => {
        this.$btnBuscar.prop("disabled", true);

        const resSelect = await $.get(TEMPLATE_NAME_SELECT);
        const resList = await $.get(TEMPLATE_NAME_LIST);
        const resCabeceara = await $.get(TEMPLATE_NAME_HEADER);

        this.template = { 
            select: Handlebars.compile(resSelect),
            lista: Handlebars.compile(resList),
            cabecera: Handlebars.compile(resCabeceara)
        };

        this.$btnBuscar.prop("disabled", false);
    };


    this.obtenerInformes = async () => {
        const htmlBtnBuscar = this.$btnBuscar.html();

        this.$btnBuscar.html('<i class="fa fa-spin fa-spinner"></i>');
        this.$btnBuscar.prop("disabled", true);
        this.$tbdInformes.addClass("invisible");
        this.renderInformes("");

        try {
            
            const data = await $.ajax({ 
                url : VARS.URL_CONTROLADOR+"informe.controlador.php?op=listar",
                data : {
                    p_fecha_inicio: this.$txtFechaInicio.val(),
                    p_fecha_fin : this.$txtFechaFin.val()
                },
                type: "POST",
                dataType: 'json',
                delay: 250,
                cache: true
            });
            
            this._data = data;
            this.$txtMedico.html(this.template.select(data.map( ({id, descripcion, cantidad}) => {
                return {
                    id, descripcion, cantidad
                };
            })))

            if (data.length === 1){
                const id = data[0].id;
                this.$txtMedico.val(data[0].id);
                this.renderInformes(id);
            }

        } catch (error) {
            toastr.error(error.responseText);
            console.error(error);
        } finally {
            this.$btnBuscar.html(htmlBtnBuscar);
            this.$btnBuscar.prop("disabled", false);
            this.$tbdInformes.removeClass("invisible");
        }
    };

    this.renderInformes = (id_medico) => {
        this.$btnGuardarOrden.hide();

        if (id_medico === ""){
            this.$tbdInformes.html(this.template.lista([]));
            this._cadenaIDOrden = "";
            return;
        }

        const informes = this._data?.find( medico => medico.id == id_medico)?.informes ?? [];
        this.$tbdInformes.html(this.template.lista(informes));

        this._cadenaIDOrden = informes.map( i => i.id_informe).join("|");
        this._cadenaIDOrdenModificable = this._cadenaIDOrden;
    };

    this.moverInforme = (tr, direccion = 'up') => {
        const $tr = $(tr);
        $tr.hide();

        if (direccion === "down"){
            $tr.next().after($tr);
        }

        if (direccion === "up"){
            $tr.prev().before($tr);
        }

        $tr.show("fast");

        const cadenaIDOrdenModificable = [].slice.call(this.$tbdInformes.find("tr")).map( tr => tr.dataset.id).join("|");

        if (this._cadenaIDOrden != cadenaIDOrdenModificable){
            this.$btnGuardarOrden.show();
        } else {
            this.$btnGuardarOrden.hide();
        }
    };

    this.guardarNuevoOrden = async () => {
        const htmlBtnGuardar = this.$btnGuardarOrden.html();

        this.$btnGuardarOrden.html('<i class="fa fa-spin fa-spinner"></i>');
        this.$btnGuardarOrden.prop("disabled", true);

        const arreglo = [].slice.call(this.$tbdInformes.find("tr")).map( tr => tr.dataset.id );

        if (!arreglo.length){
            toastr.error("No hay informes a ordenar.")
            return;
        }

        try {
            
            await $.ajax({ 
                url : VARS.URL_CONTROLADOR+"informe.controlador.php?op=cambiar_orden",
                data : {
                    p_id_medico: this.$txtMedico.val(),
                    p_arreglo : JSON.stringify(arreglo)
                },
                type: "POST",
                dataType: 'json',
                delay: 250,
                cache: true
            });

            toastr.success("Registrado correctamente.");
            this._cadenaIDOrden = [].slice.call(this.$tbdInformes.find("tr")).map( tr => tr.dataset.id).join("|");
            this.$btnGuardarOrden.hide();

        } catch (error) {
            toastr.error(error.responseText);
            console.error(error);
        } finally {
            this.$btnGuardarOrden.html(htmlBtnGuardar);
            this.$btnGuardarOrden.prop("disabled", false);
        }

    };

    this.descargar = (id_informe) => {
        window.open(`../../../impresiones/informe.medico.doc.php?id=${id_informe}`, "_blank");
    };

    this.leer = async(id_informe) => {
        try {
            const data = await $.ajax({ 
                url : VARS.URL_CONTROLADOR+"informe.controlador.php?op=leer",
                data : {
                    p_id_informe: id_informe
                },
                type: "POST",
                dataType: 'json',
                delay: 250,
                cache: true
            }); 

            $("#txt-informe-seleccionado").val(id_informe);
            this.$blkInformeCabecera.html(this.template.cabecera(data))
            
            if (this.oEditor){
                this.oEditor.clipboard.dangerouslyPasteHTML(data.contenido_informe);
            }
            this.$mdlInforme.modal("show");

        } catch (error) {
            toastr.error(error.responseText);
            console.error(error);
        } finally {

        }
    };


    this.informeGuardar = async () => {
        if (!(confirm("¿Está seguro de guardar los cambios?"))){
            return;
        }

        const htmlBtnGuardar = this.$btnInformeGuardar.html();

        this.$btnInformeGuardar.html('<i class="fa fa-spin fa-spinner"></i>');
        this.$btnInformeGuardar.prop("disabled", true);

        try {
            
            await $.ajax({ 
                url : VARS.URL_CONTROLADOR+"informe.controlador.php?op=modificar_contenido",
                data : {
                    p_id_informe: $("#txt-informe-seleccionado").val(),
                    p_contenido: this.oEditor.getSemanticHTML()
                },
                type: "POST",
                dataType: 'json',
                delay: 250,
                cache: true
            });

            toastr.success("Registrado correctamente.");

        } catch (error) {
            toastr.error(error.responseText);
            console.error(error);
        } finally {
            this.$btnInformeGuardar.html(htmlBtnGuardar);
            this.$btnInformeGuardar.prop("disabled", false);
        }

    };

    return this.init();
};



$(function(){
    x = new InformeExamenes();
})
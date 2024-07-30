const MatrizProduccionMedico = function(){
    const Constantes = {
        TIPO_VALOR_MONTO: {
            value: "M",
            key: "S/",
            desc: "Monto Fijo"
        },
        TIPO_VALOR_PORCENTAJE : {
            value: "P",
            key: "%",
            desc: "Porcentaje"
        },
        LOADING: `<i class="fa fa-spinner fa-spin"></i>`
    };

    this._registros = [];
    this._medicosLibres = [];
    this._categorias = [];

    this.init =  () => {
        this.setDOM();
        this.setEventos();
        this.getTemplates();

    };

    this.getTemplates = async () => {
        const htmlCategorias = await $.get("./template.matriz.produccion.medicos.categorias.hbs");
        const htmlRegistros = await $.get("./template.matriz.produccion.medicos.registros.hbs");
        const htmlMedicosLibres = await $.get("./template.matriz.produccion.medicos.medicoslibres.hbs");

        this.templates = {
            categorias : Handlebars.compile(htmlCategorias),
            registros : Handlebars.compile(htmlRegistros),
            medicosLibres: Handlebars.compile(htmlMedicosLibres)
        };

        this.listar();
    };

    this.setDOM = () => {
        this.$btnActualizar = $("#btn-actualizar-produccionmedicos");
        this.$tbl = $("#tbl-produccionmedicos");
        this.$tHead = this.$tbl.find("thead");
        this.$tBody = this.$tbl.find("tbody");
        this.$lblCargando = $("#lbl-cargando-produccionmedicos");
        this.$inpAgregarMedico = new InputSearch({
            id : "blk-agregar-medico",
            placeholder : "AGREGAR NUEVO MÉDICO",
            onSelect : ({id, text}) => {
                this.agregarMedico({
                    id_medico : id,
                    nombres_apellidos : text
                });
            }
        })
    };

    this.setEventos = () => {
        this.$btnActualizar.on("click", ()=>{
            this.listar();
        });

        this.$tbl.on("click", ".btn-produccionmedicos-editar", (e)=>{
            const $td = $(e.currentTarget).parents("td")
            $td.find(".cell-readonly").hide();
            $td.find(".cell-operating").show();
        });
       
        this.$tbl.on("click", ".btn-produccionmedicos-cancelar", (e)=>{
            const $td = $(e.currentTarget).parents("td")
            $td.find(".cell-readonly").show();
            $td.find(".cell-operating").hide();
        });

        this.$tbl.on("click", ".btn-produccionmedicos-guardar", (e) =>{
            const $td = $(e.currentTarget).parents("td");
            const id_medico = $td.data("idmedico");
            const id_sub_categoria_servicio = $td.data("idcategoria");
            const tipo_valor = $td.find(".tipo-valor").val();
            const valor = $td.find(".valor").val();

            this.guardar({
                id_medico,
                id_sub_categoria_servicio,
                tipo_valor,
                valor
            }, $td)
        });

        this.$tbl.on("click", ".btn-produccionmedicos-eliminarmedico", (e)=>{
            this.eliminarMedico($(e.currentTarget));
        });

    };

    this.listar = async () => {
        this.$lblCargando.show();
        this.$tbl.hide();
        this.$btnActualizar.prop("disabled", true);

        try {
            const { categorias, medicos, medicosLibres } = await $.ajax({ 
                    url : VARS.URL_CONTROLADOR+"categoria.produccion.medico.controlador.php?op=listar",
                    type: "POST",
                    dataType: 'json',
                    delay: 250,
                    cache: true
                }); 

            this.$tHead.html(this.templates.categorias(categorias));
            this.$tBody.html(this.templates.registros(medicos));

            this._registros = medicos;
            this._categorias = categorias;
            this.renderMedicosLibres(medicosLibres);

        } catch (error) {
            console.error(error);
        } finally {
            this.$lblCargando.hide();
            this.$tbl.show();
            this.$btnActualizar.prop("disabled", false);
        }
    };

    this.renderMedicosLibres = (_medicosLibres) => {
        this._medicosLibres = _medicosLibres;

        const data = _medicosLibres.map( item => {
            return {
                id: item.id_medico,
                text: item.nombres_apellidos
            }
        });

        this.$inpAgregarMedico.updateRegistros(data);

        /*
        if (Boolean(this.$cboAgregarMedico.data("select2"))){
            //this.$cboAgregarMedico.val(null);
            //this.$cboAgregarMedico.select2("data", data, true);
            //this.$cboAgregarMedico.trigger("change");
            return;
        }
        */

        /*
        this.$cboAgregarMedico.select2({
            minimumInputLength : 3,
            width: "100%",
            placeholder: {
                id: "",
                text: "AGREGAR NUEVO MÉDICO"
            },
            allowClear: true,
            data,
        });
        */
        
       // this.$cboAgregarMedico.val(null).trigger("change");
    };

    this.renderRegistros = ({registros, esReiniciarTabla = false}) => {
        this._registros = registros;
        if (esReiniciarTabla){
            this.$tBody.html(this.templates.registros(registros));
            return;
        }
        this.$tBody.append(this.templates.registros(registros.pop()));
    };

    this.agregarMedico = (objMedico) => {
        if (!objMedico){
            return;
        }

        const itemNuevo = {
            ...objMedico,
            valores : this._categorias.map( item => ({
                id_sub_categoria_servicio : item.id,
                valor: "0.00",
                tipo_valor: Constantes.TIPO_VALOR_MONTO
            }))
        };

        this.renderRegistros({registros: [...this._registros, itemNuevo], esReiniciarTabla : this._registros.length <= 0});
        this.renderMedicosLibres(this._medicosLibres.filter( medico => medico.id_medico != objMedico.id_medico));
    };

    this.guardar = async (data, $td) => {
        $td.find(".cell-readonly").show();
        $td.find(".cell-operating").hide();

        const $btnEditar = $td.find(".btn-produccionmedicos-editar");
        $btnEditar.html(Constantes.LOADING);
        $btnEditar.prop("disabled", true);

        const tipoValorDesc = data.tipo_valor == Constantes.TIPO_VALOR_PORCENTAJE.value 
                            ? Constantes.TIPO_VALOR_PORCENTAJE.key
                            : Constantes.TIPO_VALOR_MONTO.key;

        const fixedValor = parseFloat(data.valor).toFixed(2);
        $td.find(".tipo-valor-desc").html(tipoValorDesc);
        $td.find(".valor-desc").html(fixedValor);
        $td.find(".valor").val(fixedValor);

        try {
            await $.ajax({ 
                url : VARS.URL_CONTROLADOR+"categoria.produccion.medico.controlador.php?op=registrar",
                type: "POST",
                dataType: 'json',
                data : {
                    p_id_medico: data.id_medico,
                    p_id_sub_categoria_servicio: data.id_sub_categoria_servicio,
                    p_valor: data.valor,
                    p_tipo_valor: data.tipo_valor
                },
                delay: 250,
                cache: true
            }); 

            $btnEditar.html(`<i class="fa fa-check"></i>`);
            $btnEditar.removeClass("btn-info").addClass("btn-success");

        } catch (error) {
            console.error(error);
            if (typeof error?.responseText === "string"){
                toastr.error(error.responseText);
            }

            $td.find(".cell-readonly").hide();
            $td.find(".cell-operating").show();
            $td.find(".valor").focus();
            
            $btnEditar.html(`<i class="fa fa-close"></i>`);
            $btnEditar.removeClass("btn-info").addClass("btn-danger");
        } finally {
            setTimeout(()=>{
                $btnEditar.html(`<i class="fa fa-edit"></i>`);
                $btnEditar.removeClass("btn-success btn-danger").addClass("btn-info");
                $btnEditar.prop("disabled", false);
            }, 2000);
        }
    };

    this.eliminarMedico = async($btn) => {
        if (!(confirm("¿Estás seguro de realizar este cambio?"))){
            return;
        }

        const id_medico = $btn.data("idmedico");
        const nombres_apellidos = $btn.data("nombresapellidos");
        if (!Boolean(id_medico)){
            return;
        }
        const innerHTML = $btn.html();
        $btn.html(Constantes.LOADING);
        $btn.prop("disabled", true);

        try {
            await $.ajax({ 
                url : VARS.URL_CONTROLADOR+"categoria.produccion.medico.controlador.php?op=eliminar_medico",
                type: "POST",
                dataType: 'json',
                data : {
                    p_id_medico: id_medico
                },
                delay: 250,
                cache: true
            });

            $btn.parents("tr").remove();
            toastr.success("Registro eliminado correctamente.");

            const medicoEliminado = {
                id_medico,
                nombres_apellidos
            };

            this.renderMedicosLibres([...this._medicosLibres, medicoEliminado]);

        } catch (error) {
            console.error(error);
            if (typeof error?.responseText === "string"){
                toastr.error(error.responseText);
            }
        } finally {
            $btn.html(innerHTML);
            $btn.prop("disabled", false);
        }
    };

    return this.init();
};

$(document).ready(function (){
    objMatrizProduccionMedico =  new MatrizProduccionMedico();
});

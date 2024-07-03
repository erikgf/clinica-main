const EntregasSobres = function (){
    const TEMPLATE_NAME_LST_SOBRES = "template.lst.entregassobre.hbs";
    const TEMPLATE_NAME_BLK_DETALLE = "template.verdetalle.hbs";
    this.template = null;
    let $tbdRegistros, $tblRegistros, $txtBuscador, $frmBuscar, $txtMes, $txtAño,$txtPromotora, $blkPaginacion;   
    let $blkVerDetalle;
    this.objPaginador = null;

    this.hoy = Util.setFecha(null, new Date());
    
    this.init = async () => {
        this.setDOM();
        this.setEventos();

        const resLst = await $.get(TEMPLATE_NAME_LST_SOBRES);
        const resDetalle = await $.get(TEMPLATE_NAME_BLK_DETALLE);
        this.template = { 
            lista: Handlebars.compile(resLst),
            detalle: Handlebars.compile(resDetalle)
        };

        this.objPaginador = new Paginador({
            $tbody : $tbdRegistros,
            $txtBuscador : $txtBuscador,
            $paginador: $blkPaginacion,
            template: this.template.lista,
            keysParaBuscar : ["medico", "promotora"]
        });
    };

    this.setDOM = function(){    
        $tbdRegistros  = $("#tbd-entregasobres-registros");
        $tblRegistros  = $("#tbl-entregasobres-registros");

        $txtBuscador = $("#txt-entregasobres-buscar");

        $frmBuscar = $("#frm-entregasobres");
        $txtMes = $("#txt-entregasobres-mes");
        $txtAño = $("#txt-entregasobres-año");
        $txtPromotora = $("#txt-entregasobres-promotora");

        $btnGuardar = $("#btn-entregasobres-guardar");
        $blkPaginacion = $("#blk-entregasobres-paginacion");

        $blkVerDetalle = $("#blk-entregasobres-verdetalle");
    };
    
    this.setEventos = () => {

        $tblRegistros.on("change", "tr .chk-fechaaceptado", (e) => {
            const { currentTarget : target } = e;
            const { id } = target.dataset;
            const $fechaAceptado = target.nextElementSibling;
            const isChecked = target.checked;
            const fechaAceptado =  isChecked ? this.hoy : null;

            this.objPaginador.updateItem(parseInt(id), {
                checked_aceptado : isChecked,
                disabled_aceptado  : !isChecked,
                fecha_aceptado: fechaAceptado,
                observaciones: null
            });

            $fechaAceptado.disabled = !isChecked;
            $fechaAceptado.value = fechaAceptado;
        });

        $tblRegistros.on("change", "tr .chk-eliminar", (e) => {
            const { currentTarget : target } = e;
            const $tr = target.parentElement.parentElement;
            const { id } = $tr.dataset;

            this.objPaginador.updateItem(parseInt(id), {
                es_eliminar: target.checked
            });

            target.parentElement.parentElement.classList[target.checked ? "add" : "remove"]("bg-danger");
        });

        $tblRegistros.on("change", "tr .txt-fechaentregado", (e) => {
            const { currentTarget : target } = e;
            const { id } = target.dataset;

            this.objPaginador.updateItem(parseInt(id), {
                fecha_entregado: target.value
            });
        });

        $tblRegistros.on("change", "tr .txt-fechaaceptado", (e) => {
            const { currentTarget : target } = e;
            const { id } = target.dataset;

            this.objPaginador.updateItem(parseInt(id), {
                fecha_aceptado: target.value
            });
        });

        $tblRegistros.on("change", "tr .txt-observaciones", (e) => {
            const { currentTarget : target } = e;
            const { id } = target.dataset;
            this.objPaginador.updateItem(parseInt(id), {
                observaciones: target.value
            });
        });

        $tblRegistros.on("click", "tr .on-verdetalle", (e) => {
            const { currentTarget : target } = e;
            this.verDetalle(parseInt(target.dataset.id));
        });

        $frmBuscar.on("submit", (e) => {
            e.preventDefault();
            this.listar();
        });

        $btnGuardar.on("click", ()=> {
           this.guardar();
        });

        $blkVerDetalle.on("click", ".on-cerrardetalle", (e) => {
            this.onCerrarDetalle();
        });
    };


    this.listar = async () => {
        const $btnBuscar = $frmBuscar.find("button");
        const tempHtml = $btnBuscar.html();
        $btnBuscar.html('<i class="fa fa-spin fa-spinner"></i>');
        $tbdRegistros.parent().closest('.card').addClass("invisible");
        $btnBuscar.prop("disabled", true);

        try {
            const result = await $.ajax({ 
                url : VARS.URL_CONTROLADOR+"entrega.sobres.controlador.php?op=listar_sobre_entrega",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: {
                    p_mes: $txtMes.val(),
                    p_anio: $txtAño.val(),
                    p_id_promotora: $txtPromotora.val(),
                },
                cache: true
                }
            );
    
            this.renderListar(result);
        } catch (error) {
            toastr.error(error.responseText);
        } finally {
            $btnBuscar.html(tempHtml);
            $btnBuscar.prop("disabled", false);
            $tbdRegistros.parent().closest('.card').removeClass("invisible");
        }
    };

    this.renderListar = (data) => {
        const registros = data.map( (r, index) => {
            const anio_meses = Boolean(r.anio_meses) 
                                ?   r.anio_meses.split(",")?.map( liquidacion_mes_str => {
                                        const [mes, año, monto, es_registro_principal] = liquidacion_mes_str.split("|");
                                        return {
                                            mes, año, monto, es_registro_principal
                                        }
                                    })
                                :   [];

            return {
                ...r,
                index: index + 1,
                fecha_aceptado_base: r.fecha_aceptado,
                fecha_entregado_base: r.fecha_entregado,
                observaciones_base: r.observaciones,
                checked_aceptado: Boolean(r.fecha_aceptado),
                disabled_aceptado: !Boolean(r.fecha_aceptado),
                es_eliminar: false,
                anio_meses
            };
        });
    
        this.objPaginador.start(registros);
    };

    this.guardar = async () => {
        if (!confirm("¿Estás seguro de guardar los datos?")){
            return;
        }

        //obtener solo los modificados
        const dataEnviar = this.objPaginador.getData()?.filter( item => {
            return  item.fecha_aceptado != item.fecha_aceptado_old || 
                    item.fecha_entregado != item.fecha_entregado_base|| 
                    item.observaciones != item.observaciones||
                    item.es_eliminar
        });
        
        const dataEnviarFechaEntregadoFaltante = dataEnviar.filter( item => !Boolean(item.fecha_entregado));
        if (dataEnviarFechaEntregadoFaltante.length){
            toastr.error(`Los siguientes sobre no tienen fecha de entregado: ${ dataEnviarFechaEntregadoFaltante.map( item => item.medico ).join(",") }`, "Datos incompletos", 12500);
            return;
        }

        const dataEnviarFechaAceptadoFaltante = dataEnviar.filter( item => !Boolean(item.fecha_aceptado) && Boolean(item.checked_aceptado));
        if (dataEnviarFechaAceptadoFaltante.length){
            toastr.error(`Los siguientes sobre no tienen fecha de entregado: ${ dataEnviarFechaAceptadoFaltante.map( item => item.medico ).join(",") }`, "Datos incompletos", 12500);
            return;
        }

        const tempHtml = $btnGuardar.html();
        $btnGuardar.html('<i class="fa fa-spin fa-spinner"></i>');
        $btnGuardar.prop("disabled", true);

        try {
            await $.ajax({ 
                url : VARS.URL_CONTROLADOR+"entrega.sobres.controlador.php?op=registrar_actualizacion_sobres",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: {
                    p_registros: JSON.stringify(dataEnviar.map(({id, es_eliminar, fecha_entregado, fecha_aceptado, observaciones}) => {
                        return {
                            id,
                            es_eliminar, 
                            fecha_entregado,
                            fecha_aceptado,
                            observaciones
                        }
                    })),
                },
                cache: true
                }
            );

            toastr.success("Registro correcto!");
            this.objPaginador.deleteItems(dataEnviar.filter( item => Boolean(item.es_eliminar)).map(item => item.id));
    
        } catch (error) {
            toastr.error(error.responseText);
        } finally {
            $btnGuardar.html(tempHtml);
            $btnGuardar.prop("disabled", false);
        }

    };

    const _meses = Util.getMeses();
    this.verDetalle = (id) => {
        const itemBuscado = this.objPaginador.getData().find( item => item.id === parseInt(id) );
       
        if (itemBuscado){
            const { medico, promotora, anio_meses } = itemBuscado;
            $blkVerDetalle.html(this.template.detalle({
                medico, 
                promotora, 
                meses : anio_meses.map( ({mes, año, monto, es_registro_principal}) => {
                    return {
                        es_registro_principal: es_registro_principal == "1",
                        anio: año,
                        mes_nombre: _meses.find (_mes => _mes.id == mes)?.descripcion ?? "-",
                        monto
                    }
                })
            }));
            const top = `${Math.abs(document.body.getBoundingClientRect().top) - 5}px`;

            $blkVerDetalle.css("top", top);
            $blkVerDetalle.show("fast");
        }
    };

    this.onCerrarDetalle = () => {
        $blkVerDetalle.hide("fast");
    }

    return this.init();
};


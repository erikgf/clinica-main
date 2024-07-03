const LiquidacionesSinSobre = function (){
    const TEMPLATE_NAME_LST_LIQUIDACIONES = "template.lst.liquidacionessinsobre.hbs";
    const TEMPLATE_NAME_BLK_DETALLE = "template.verdetalle.hbs";
    this.template = null;
    let $tbdRegistros, $tblRegistros, $txtBuscador, $frmBuscar, $txtMes, $txtAño,$txtPromotora, $txtMontoMin, $blkPaginacion; 
    let $blkVerDetalle;  
    this.objPaginador = null;

    this.hoy = Util.setFecha(null, new Date());

    this.init = async () => {
        this.setDOM();
        this.setEventos();
        
        const resLst = await $.get(TEMPLATE_NAME_LST_LIQUIDACIONES);
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
        $tbdRegistros  = $("#tbd-liquidacionsinsobre-registros");
        $tblRegistros  = $("#tbl-liquidacionsinsobre-registros");

        $txtBuscador = $("#txt-liquidacionsinsobre-buscar");

        $frmBuscar = $("#frm-liquidacionsinsobre");
        $txtMes = $("#txt-liquidacionsinsobre-mes");
        $txtAño = $("#txt-liquidacionsinsobre-año");
        $txtPromotora = $("#txt-liquidacionsinsobre-promotora");
        $txtMontoMin = $("#txt-liquidacionsinsobre-montomin");

        $btnGuardar = $("#btn-liquidacionsinsobre-guardar");
        $blkPaginacion = $("#blk-liquidacionsinsobre-paginacion");

        $blkVerDetalle = $("#blk-liquidacionsinsobre-verdetalle");
    };
    
    this.setEventos = () => {
        $tblRegistros.on("change", "tr .chk-fechaentrega", (e) => {
            const { currentTarget : target } = e;
            const { id } = target.dataset;
            const $fechaEntrega = target.nextElementSibling;
            const $observaciones = target.parentElement.nextElementSibling.children[0];
            const isChecked = target.checked;
            const fechaEntrega =  isChecked ? this.hoy : null;

            this.objPaginador.updateItem(parseInt(id), {
                checked: isChecked,
                disabled : !isChecked,
                fecha_entregado: fechaEntrega,
                observaciones: null
            });

            $observaciones.disabled = !isChecked;
            $observaciones.value = null;
            $fechaEntrega.disabled = !isChecked;
            $fechaEntrega.value = fechaEntrega;

        });

        $tblRegistros.on("change", "tr .txt-fechaentrega", (e) => {
            const { currentTarget : target } = e;
            const { id } = target.dataset;

            this.objPaginador.updateItem(parseInt(id), {
                fecha_entregado: target.value
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
                url : VARS.URL_CONTROLADOR+"entrega.sobres.controlador.php?op=listar_para_registrar",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: {
                    p_mes: $txtMes.val(),
                    p_anio: $txtAño.val(),
                    p_id_promotora: $txtPromotora.val(),
                    p_monto_minimo: $txtMontoMin.val()
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
            const liquidaciones_mes = Boolean(r.liquidaciones_anteriores) 
                                ?   r.liquidaciones_anteriores.split(",")?.map( liquidacion_mes_str => {
                                        const [mes, año, monto] = liquidacion_mes_str.split("|");
                                        return {
                                            mes, año, monto
                                        }
                                    })
                                :   [];

            return {
                ...r,
                id : index + 1,
                checked: false,
                disabled: true,
                fecha_entregado: null,
                observaciones: null,
                mostrando_acumulado : r.acumulado > 0,
                liquidaciones_mes
            }
        });
    
        this.objPaginador.start(registros);
    };

    this.guardar = async () => {
        if (!confirm("¿Estás seguro de guardar los datos?")){
            return;
        }
        
        const dataEnviar = this.objPaginador.getData()?.filter( item => (item.checked));
        const dataEnviarFechaFaltante = dataEnviar.filter( item => !Boolean(item.fecha_entregado));

        if (dataEnviarFechaFaltante.length){
            toastr.error(`Los siguientes sobre no tienen fecha de entrega: ${ dataEnviarFechaFaltante.map( item => item.medico ).join(",") }`, "Datos incompletos", 12500);
            return;
        }

        const tempHtml = $btnGuardar.html();
        $btnGuardar.html('<i class="fa fa-spin fa-spinner"></i>');
        $btnGuardar.prop("disabled", true);

        try {
            await $.ajax({ 
                url : VARS.URL_CONTROLADOR+"entrega.sobres.controlador.php?op=registrar_sobres",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: {
                    p_registros: JSON.stringify(dataEnviar.map(({id_medico, id_promotora, mes, 
                            anio, fecha_entregado, observaciones, liquidaciones_mes}) => {
                        return {
                            mes, anio,
                            id_promotora, id_medico,
                            fecha_entregado,
                            observaciones,
                            meses : liquidaciones_mes.map ( ({mes, año, monto}) => {
                                return {
                                    mes,
                                    anio: año,
                                    monto
                                }
                            })
                        }
                    })),
                },
                cache: true
                }
            );

            toastr.success("Registro correcto!");

            this.objPaginador.deleteItems(dataEnviar.map(item => item.id ));
    
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
            const { medico, promotora, liquidaciones_mes } = itemBuscado;
            $blkVerDetalle.html(this.template.detalle({
                medico, 
                promotora, 
                meses : liquidaciones_mes.map( ({mes, año, monto}) => {
                    return {
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


var CategoriaProduccion = function(){
    var $mdl,
        $frm,
        $txtId,
        $txtDescripcion,
        $btnEliminar,
        $btnGuardar;

    var tplCategoriasProduccionFormulario;
    let TR_FILA = null;
    let TABLA_CATEGORIAS;
    let $overlayTabla;
    let $tbb, $tbl;

    this.setInit = function(){
        this.getTemplates();
        return this;
    };

    this.getTemplates = function(){
        var $reqCategoriasProduccion =  $.get("template.categoriaproduccion.php");
        var self = this;

        $.when($reqCategoriasProduccion)
            .done(function(resCategoriasProduccion){
                tplCategoriasProduccionFormulario = Handlebars.compile(resCategoriasProduccion);
                    
                self.setDOM();
                self.setEventos();

                self.listar();
            })
            .fail(function(error){
                console.error(error);
            });
    };

    this.setDOM = function(){
        $overlayTabla = $("#overlay-tbl-categoriaproduccion");
        $mdl = $("#mdl-categoriaproduccion");
        $frm = $("#frm-categoriaproduccion");

        $tbl = $("#tbl-categoriaproduccion");
        $tbb  = $("#tbd-categoriaproduccion");
      
        $txtId = $("#txt-categoriaproduccion-seleccionado");
        $txtDescripcion = $("#txt-categoriaproduccion-descripcion");
    
        $btnActualizar = $("#btn-actualizar-categoriaproduccion");
        $btnEliminar = $("#btn-categoriaproduccion-eliminar");
        $btnGuardar = $("#btn-categoriaproduccion-guardar");
    };

    this.setEventos = function () {
        $btnEliminar.on("click", () =>  {
            this.anular($txtId.val(), $mdl);
        });

        $btnGuardar.on("click", (e) => {
            e.preventDefault();
            this.guardar();
        });

        $mdl.on("hidden.bs.modal", () => {
            this.limpiarModal();
        });

        $mdl.on("shown.bs.modal", () => {
            $txtDescripcion.focus();
        });

        $btnActualizar.on("click", () => {
            this.listar();
        });
            
        $("#btn-nuevo-categoriaproduccion").on("click", () => {
            TR_FILA = null;
            this.nuevoRegistro();
        });

        $tbb.on("click", ".btn-editar", (e)=>{
            this.leer(e.currentTarget.dataset.id);
        });

        $tbb.on("click", ".btn-eliminar", (e) => {
            this.anular(e.currentTarget.dataset.id);
        });

    };

    this.nuevoRegistro = function(){
        $mdl.modal("show");
        $mdl.find(".modal-title").html("Nueva Categoría para Producción");

        this.limpiarModal();
    };

    this.limpiarModal = function(){
        $frm[0].reset();
        $btnEliminar.hide();
        $txtId.val("");
    };

    this.leer = function(id){
        var self = this;

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"categoriaproduccion.controlador.php?op=leer",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id : id
            },
            success: function(result){
                $mdl.modal("show");
                self.render(result);
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    this.render = function(data){
        $mdl.find(".modal-title").html("Editando Categoría para Producción");

        $txtId.val(data.id);
        $txtDescripcion.val(data.descripcion);

        $btnEliminar.show();
    };

    this.guardar = function(){
        if(!Util.validarFormulario($frm)){
            toastr.error("No están todos los campos completados.");
            return;
        }

        const self = this;

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"categoriaproduccion.controlador.php?op=guardar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id : $txtId.val(),
                p_descripcion : $txtDescripcion.val(),
            },
            success: function(result){
                toastr.success(result.msj);
                self.actualizarFilaTabla(result.registro);
                $mdl.modal("hide");
            },
            error: function (error) {
                console.error(error);
                toastr.error(error.responseText);
                return;
            },
            cache: true
            }
        );
    };

    this.actualizarFilaTabla = function(dataRegistro){
        const { id } = dataRegistro;
        const $trRegistroGuardado = tplCategoriasProduccionFormulario([dataRegistro]);

        console.log(id);
        const $trExistente = $tbb.find("tr[data-id="+id+"]");

        console.log({$trExistente});
        if ($trExistente.length > 0){
            $trExistente.replaceWith( $trRegistroGuardado );
            return;
        }

        const $firstItemTr = $tbb.find("tr");
        if ($firstItemTr.length === 0){
            $tbb.html($trRegistroGuardado);
        } else {
            $tbb.append($trRegistroGuardado);
        }
        /*

        if (dataRegistro && dataRegistro.id_tipo_servicio == $txtFiltroTipoServicio.val()){
            var arr = [].slice.call($(tplServicios([dataRegistro])).find("td")),
                dataFila = $.map(arr, function(item) {
                    return item.innerHTML;
                });

            if (TABLA_CATEGORIAS){
                if (TR_FILA){ 
                    TABLA_CATEGORIAS
                        .row(TR_FILA)
                        .data(dataFila)
                        .draw();  
                } else {
                    TABLA_CATEGORIAS.row.add(dataFila).draw(false);     
                }
            }

            TR_FILA = null; 
        }
        */
    };

    this.anular = function(id, $mdl_operando = null){
        if (!confirm("¿Está seguro de dar de baja esta categoría?")){
            return;
        }

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"categoriaproduccion.controlador.php?op=anular",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id : id
            },
            success: function(result){
                toastr.success(result.msj);

                const $tr = $tbb.find("tr[data-id="+id+"]");
                if ($tr){
                    $tr.remove();
                }
                
                if ($mdl_operando){
                    $mdl_operando.modal("hide");
                }
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    TABLA_CATEGORIAS  = null;
    this.listar = function(){
        $btnActualizar.prop("disabled", true);
        $overlayTabla.show();

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"categoriaproduccion.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                $btnActualizar.prop("disabled", false);
                $overlayTabla.hide();
                $tbb.html(tplCategoriasProduccionFormulario(result));
            },
            error: function (request) {
                $btnActualizar.prop("disabled", false);
                $overlayTabla.hide();
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    return this.setInit();
};

$(document).ready(function(){
    objCategoriaProduccion = new CategoriaProduccion();
});

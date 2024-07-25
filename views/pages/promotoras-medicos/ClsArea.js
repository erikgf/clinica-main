var Area = function(_template, _$tabla, _$tbody){
    var $mdl,   
        $txtIdArea,
        $txtDescripcion,
        $txtComision,
        $blkComisiones,
        $btnEliminar,
        $btnGuardar;

    var tplAreas,
        $tblAreas,
        $tbbAreas;
    
    this.setInit = function(){
        tplAreas  = _template;
        $tblAreas  = _$tabla;
        $tbbAreas  = _$tbody;

        this.setDOM();
        this.setEventos();

        this.cargar();
        return this;
    };

    this.setDOM = function(){
        $mdl = $("#mdl-area");
        $txtIdArea = $("#txt-area-seleccionado");
        $txtDescripcion = $("#txt-area-descripcion");
        $blkComisiones = $("#blk-area-comisiones");
        $txtComision = $("#txt-area-comision");
        $btnEliminar = $("#btn-area-eliminar");
        $btnGuardar = $("#btn-area-guardar");
    };

    this.setEventos = function () {
        var self = this;

        $btnEliminar.on("click", function () {
            self.anular(this.dataset.id);
        });
        
        $btnGuardar.on("click", function(e){
            self.guardar();
        });

        $mdl.on("hidden.bs.modal", function(e){
            $btnEliminar.hide();
            $mdl.find("form")[0].reset();
        });
    };


    this.renderComisionesSede = (data = null) => {
        if (Boolean(data)){
            return data.map((item) => {
                return `<div class="form-group">
                            <label for="txt-area-comision-${item.id}">% ${item.descripcion}</label>
                            <input type="number" step="0.01" class="form-control text-right txt-comisiones" data-idsede="${item.id}" value="${item.comision}" id="txt-area-comision-${item.id}"/>
                        </div>`
            }).join("");
        }

        const sedes = objMedico._sedes;
        return sedes.map((item) => {
            return `<div class="form-group">
                        <label for="txt-area-comision-${item.id}">% ${item.descripcion}</label>
                        <input type="number" step="0.01" class="form-control text-right txt-comisiones" data-idsede="${item.id}" id="txt-area-comision-${item.id}"/>
                    </div>`
        }).join("");
    };

    this.nuevoRegistro = function(){
        $mdl.find("form")[0].reset();
        $mdl.modal("show");
        $mdl.find(".modal-title").html("Nueva Área");

        $blkComisiones.html(this.renderComisionesSede());

        $txtIdArea.val("");
    };

    this.leer = function(id){
        var self = this;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"categoria.servicio.controlador.php?op=leer",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_categoria_servicio : id
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
        $mdl.find(".modal-title").html("Editando Área");

        $txtIdArea.val(data.id_categoria_servicio);
        $txtDescripcion.val(data.descripcion);

        //$txtComision.val(data.comision_area);
        $blkComisiones.html(this.renderComisionesSede(data.comisiones_sedes));

        $btnEliminar.show();
    };

    this.anular = function(id){
        var self = this;

        if (!confirm("¿Está seguro de dar de baja esta área?")){
            return;
        }

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"categoria.servicio.controlador.php?op=anular",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_categoria_servicio : id
            },
            success: function(result){
                toastr.success(result.msj);
                self.cargar();

                $mdl.modal("hide");
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    this.guardar = function(){

        const dataComisiones = $mdl.find(".txt-comisiones").toArray()
                    .map((itemTxt => {
                        return {
                            id_sede: itemTxt.dataset.idsede,
                            comision: itemTxt.value === "" ? 0.00 : itemTxt.value
                        }
                    }));

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"categoria.servicio.controlador.php?op=guardar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_categoria_servicio : $txtIdArea.val(),
                p_descripcion : $txtDescripcion.val(),
                //p_comision: $txtComision.val()
                p_comisiones: JSON.stringify(dataComisiones)
            },
            success: (result) => {
                toastr.success(result.msj);
                this.cargar();
                
                $mdl.modal("hide");
            },
            error: (request) => {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    this.cargar = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"categoria.servicio.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                $tbbAreas.html(tplAreas(result));
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    return this.setInit();
};
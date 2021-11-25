var LabUnidad = function(_template, _$tabla, _$tbody){
    var $mdl,   
        $txtIdUnidad,
        $txtDescripcion,
        $btnEliminar,
        $btnGuardar;

    var tplUnidades,
        $tblUnidades,
        $tbbUnidades;

    var TR_FILA = null;
    
    this.setInit = function(){
        tplUnidades  = _template;
        $tblUnidades  = _$tabla;
        $tbbUnidades  = _$tbody;

        this.setDOM();
        this.setEventos();

        this.cargar();
        return this;
    };

    this.setDOM = function(){
        $mdl = $("#mdl-unidad");
        $txtIdUnidad = $("#txt-unidad-seleccionado");
        $txtDescripcion = $("#txt-unidad-descripcion");
        $btnEliminar = $("#btn-unidad-eliminar");
        $btnGuardar = $("#btn-unidad-guardar");
    };

    this.setEventos = function () {
        var self = this;

        $btnEliminar.on("click", function () {
            self.anular($txtIdUnidad.val(), TR_FILA);
        });
        
        $btnGuardar.on("click", function(e){
            self.guardar();
        });

        $mdl.on("hidden.bs.modal", function(e){
            $btnEliminar.hide();
            $mdl.find("form")[0].reset();
        });
    };

    this.nuevoRegistro = function(){
        $mdl.find("form")[0].reset();
        $mdl.modal("show");
        $mdl.find(".modal-title").html("Nueva Unidad");

        $txtIdUnidad.val("");
        TR_FILA = null;
    };

    this.leer = function(id, $tr_fila){
        var self = this;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.unidad.controlador.php?op=leer",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_lab_unidad : id
            },
            success: function(result){
                $mdl.modal("show");
                self.render(result);

                TR_FILA = $tr_fila;
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    this.render = function(dataUnidad){
        $mdl.find(".modal-title").html("Editando Unidad");

        $txtIdUnidad.val(dataUnidad.id_unidad);
        $txtDescripcion.val(dataUnidad.descripcion);
        $btnEliminar.show();
    };

    this.anular = function(idUnidad, $tr_fila){
        if (!confirm("¿Está seguro de dar de baja este unidad")){
            return;
        }
        var self = this;

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.unidad.controlador.php?op=anular",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_lab_unidad : idUnidad
            },
            success: function(result){
                toastr.success(result.msj);

                if (TABLA_UNIDAD){
                    TABLA_UNIDAD
                        .row($tr_fila)
                        .remove()
                        .draw();    
                }

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
        var self = this;

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.unidad.controlador.php?op=guardar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_lab_unidad : $txtIdUnidad.val(),
                p_descripcion : $txtDescripcion.val()
            },
            success: function(result){
                toastr.success(result.msj);
                var arr = [].slice.call($(tplUnidades([result.registro])).find("td")),
                    dataNuevaFila = $.map(arr, function(item) {
                        return item.innerHTML;
                    });

                if (TABLA_UNIDAD){
                    if (TR_FILA){ 
                        TABLA_UNIDAD
                            .row(TR_FILA)
                            .data(dataNuevaFila)
                            .draw();  
                    } else {
                        TABLA_UNIDAD.row.add(dataNuevaFila).draw(false);     
                    }
                }
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

    TABLA_UNIDAD  = null;
    this.cargar = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.unidad.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                if (TABLA_UNIDAD){
                    TABLA_UNIDAD.destroy();
                }

                $tbbUnidades.html(tplUnidades(result));
                TABLA_UNIDAD = $tblUnidades.DataTable({
                    ordering: false,
                    columns: [
                        { width: "75px "},
                        null
                    ]
                });
                
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


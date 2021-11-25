var LabSeccion = function(_template, _$tabla, _$tbody){
    var $mdl,   
        $txtIdSeccion,
        $txtDescripcion,
        $btnEliminar,
        $btnGuardar;

    var tplSecciones,
        $tblSecciones,
        $tbbSecciones;

    var TR_FILA = null;
    
    this.setInit = function(){
        tplSecciones  = _template;
        $tblSecciones  = _$tabla;
        $tbbSecciones  = _$tbody;

        this.setDOM();
        this.setEventos();

        this.cargar();
        return this;
    };

    this.setDOM = function(){
        $mdl = $("#mdl-seccion");
        $txtIdSeccion = $("#txt-seccion-seleccionado");
        $txtDescripcion = $("#txt-seccion-descripcion");
        $btnEliminar = $("#btn-seccion-eliminar");
        $btnGuardar = $("#btn-seccion-guardar");
    };

    this.setEventos = function () {
        var self = this;

        $btnEliminar.on("click", function () {
            self.anular($txtIdSeccion.val(), TR_FILA);
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
        $mdl.find(".modal-title").html("Nuevo Sección");

        $txtIdSeccion.val("");
        TR_FILA = null;
    };

    this.leer = function(id, $tr_fila){
        var self = this;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.seccion.controlador.php?op=leer",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_lab_seccion : id
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

    this.render = function(dataSeccion){
        $mdl.find(".modal-title").html("Editando Sección");

        $txtIdSeccion.val(dataSeccion.id_seccion);
        $txtDescripcion.val(dataSeccion.descripcion);
        $btnEliminar.show();
    };

    this.anular = function(idSeccion, $tr_fila){
        if (!confirm("¿Está seguro de dar de baja este sección")){
            return;
        }
        var self = this;

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.seccion.controlador.php?op=anular",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_lab_seccion : idSeccion
            },
            success: function(result){
                toastr.success(result.msj);

                if (TABLA_SECCION){
                    TABLA_SECCION
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
            url : VARS.URL_CONTROLADOR+"lab.seccion.controlador.php?op=guardar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_lab_seccion : $txtIdSeccion.val(),
                p_descripcion : $txtDescripcion.val()
            },
            success: function(result){
                toastr.success(result.msj);
                var arr = [].slice.call($(tplSecciones([result.registro])).find("td")),
                    dataNuevaFila = $.map(arr, function(item) {
                        return item.innerHTML;
                    });

                if (TABLA_SECCION){
                    if (TR_FILA){ 
                        TABLA_SECCION
                            .row(TR_FILA)
                            .data(dataNuevaFila)
                            .draw();  
                    } else {
                        TABLA_SECCION.row.add(dataNuevaFila).draw(false);     
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

    TABLA_SECCION  = null;
    this.cargar = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.seccion.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                if (TABLA_SECCION){
                    TABLA_SECCION.destroy();
                }

                $tbbSecciones.html(tplSecciones(result));
                TABLA_SECCION = $tblSecciones.DataTable({
                    "ordering": false,
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


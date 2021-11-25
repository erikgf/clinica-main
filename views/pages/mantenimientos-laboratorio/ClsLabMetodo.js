var LabMetodo = function(_template, _$tabla, _$tbody){
    var $mdl,   
        $txtIdMetodo,
        $txtDescripcion,
        $btnEliminar,
        $btnGuardar;

    var tplMetodos,
        $tblMetodos,
        $tbbMetodos;

    var TR_FILA = null;
    
    this.setInit = function(){
        tplMetodos  = _template;
        $tblMetodos  = _$tabla;
        $tbbMetodos  = _$tbody;

        this.setDOM();
        this.setEventos();

        this.cargar();
        return this;
    };

    this.setDOM = function(){
        $mdl = $("#mdl-metodo");
        $txtIdMetodo = $("#txt-metodo-seleccionado");
        $txtDescripcion = $("#txt-metodo-descripcion");
        $btnEliminar = $("#btn-metodo-eliminar");
        $btnGuardar = $("#btn-metodo-guardar");
    };

    this.setEventos = function () {
        var self = this;

        $btnEliminar.on("click", function () {
            self.anular($txtIdMetodo.val(), TR_FILA);
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
        $mdl.find(".modal-title").html("Nuevo Método");

        $txtIdMetodo.val("");
        TR_FILA = null;
    };

    this.leer = function(id, $tr_fila){
        var self = this;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.metodo.controlador.php?op=leer",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_lab_metodo : id
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

    this.render = function(dataMetodo){
        $mdl.find(".modal-title").html("Editando Método");

        $txtIdMetodo.val(dataMetodo.id_metodo);
        $txtDescripcion.val(dataMetodo.descripcion);
        $btnEliminar.show();
    };

    this.anular = function(idMetodo, $tr_fila){
        if (!confirm("¿Está seguro de dar de baja este método")){
            return;
        }
        var self = this;

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.metodo.controlador.php?op=anular",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_lab_metodo : idMetodo
            },
            success: function(result){
                toastr.success(result.msj);

                if (TABLA_METODO){
                    TABLA_METODO
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
            url : VARS.URL_CONTROLADOR+"lab.metodo.controlador.php?op=guardar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_lab_metodo : $txtIdMetodo.val(),
                p_descripcion : $txtDescripcion.val()
            },
            success: function(result){
                toastr.success(result.msj);
                var arr = [].slice.call($(tplMetodos([result.registro])).find("td")),
                    dataNuevaFila = $.map(arr, function(item) {
                        return item.innerHTML;
                    });

                if (TABLA_METODO){
                    if (TR_FILA){ 
                        TABLA_METODO
                            .row(TR_FILA)
                            .data(dataNuevaFila)
                            .draw();  
                    } else {
                        TABLA_METODO.row.add(dataNuevaFila).draw(false);     
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

    TABLA_METODO  = null;
    this.cargar = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.metodo.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                if (TABLA_METODO){
                    TABLA_METODO.destroy();
                }

                $tbbMetodos.html(tplMetodos(result));
                TABLA_METODO = $tblMetodos.DataTable({
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


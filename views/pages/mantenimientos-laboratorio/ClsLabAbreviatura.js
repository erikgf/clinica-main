var LabAbreviatura = function(_template, _$tabla, _$tbody){
    var $mdl,   
        $txtIdAbreviatura,
        $txtDescripcion,
        $btnEliminar,
        $btnGuardar;

    var tplAbreviaturas,
        $tblAbreviaturas,
        $tbbAbreviaturas;

    var TR_FILA = null;
    
    this.setInit = function(){
        tplAbreviaturas  = _template;
        $tblAbreviaturas  = _$tabla;
        $tbbAbreviaturas  = _$tbody;

        this.setDOM();
        this.setEventos();

        this.cargar();
        return this;
    };

    this.setDOM = function(){
        $mdl = $("#mdl-abreviatura");
        $txtIdAbreviatura = $("#txt-abreviatura-seleccionado");
        $txtDescripcion = $("#txt-abreviatura-descripcion");
        $btnEliminar = $("#btn-abreviatura-eliminar");
        $btnGuardar = $("#btn-abreviatura-guardar");
    };

    this.setEventos = function () {
        var self = this;

        $btnEliminar.on("click", function () {
            self.anular($txtIdAbreviatura.val(), TR_FILA);
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
        $mdl.find(".modal-title").html("Nueva Abreviatura");

        $txtIdAbreviatura.val("");
        TR_FILA = null;
    };

    this.leer = function(id, $tr_fila){
        var self = this;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.abreviatura.controlador.php?op=leer",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_lab_abreviatura : id
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

    this.render = function(dataAbreviatura){
        $mdl.find(".modal-title").html("Editando Abreviatura");

        $txtIdAbreviatura.val(dataAbreviatura.id_abreviatura);
        $txtDescripcion.val(dataAbreviatura.descripcion);
        $btnEliminar.show();
    };

    this.anular = function(idAbreviatura, $tr_fila){
        if (!confirm("¿Está seguro de dar de baja este abreviatura")){
            return;
        }
        var self = this;

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.abreviatura.controlador.php?op=anular",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_lab_abreviatura : idAbreviatura
            },
            success: function(result){
                toastr.success(result.msj);

                if (TABLA_ABREVIATURA){
                    TABLA_ABREVIATURA
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
            url : VARS.URL_CONTROLADOR+"lab.abreviatura.controlador.php?op=guardar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_lab_abreviatura : $txtIdAbreviatura.val(),
                p_descripcion : $txtDescripcion.val()
            },
            success: function(result){
                toastr.success(result.msj);
                var arr = [].slice.call($(tplAbreviaturas([result.registro])).find("td")),
                    dataNuevaFila = $.map(arr, function(item) {
                        return item.innerHTML;
                    });

                if (TABLA_ABREVIATURA){
                    if (TR_FILA){ 
                        TABLA_ABREVIATURA
                            .row(TR_FILA)
                            .data(dataNuevaFila)
                            .draw();  
                    } else {
                        TABLA_ABREVIATURA.row.add(dataNuevaFila).draw(false);     
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

    TABLA_ABREVIATURA  = null;
    this.cargar = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.abreviatura.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                if (TABLA_ABREVIATURA){
                    TABLA_ABREVIATURA.destroy();
                }

                $tbbAbreviaturas.html(tplAbreviaturas(result));
                TABLA_ABREVIATURA = $tblAbreviaturas.DataTable({
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


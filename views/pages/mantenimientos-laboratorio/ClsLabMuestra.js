var LabMuestra = function(_template, _$tabla, _$tbody){
    var $mdl,   
        $txtIdMuestra,
        $txtDescripcion,
        $btnEliminar,
        $btnGuardar;

    var tplMuestras,
        $tblMuestras,
        $tbbMuestras;

    var TR_FILA = null;
    
    this.setInit = function(){
        tplMuestras  = _template;
        $tblMuestras  = _$tabla;
        $tbbMuestras  = _$tbody;

        this.setDOM();
        this.setEventos();

        this.cargar();
        return this;
    };

    this.setDOM = function(){
        $mdl = $("#mdl-muestra");
        $txtIdMuestra = $("#txt-muestra-seleccionado");
        $txtDescripcion = $("#txt-muestra-descripcion");
        $btnEliminar = $("#btn-muestra-eliminar");
        $btnGuardar = $("#btn-muestra-guardar");
    };

    this.setEventos = function () {
        var self = this;

        $btnEliminar.on("click", function () {
            self.anular($txtIdMuestra.val(), TR_FILA);
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
        $mdl.find(".modal-title").html("Nueva Muestra");

        $txtIdMuestra.val("");
        TR_FILA = null;
    };

    this.leer = function(id, $tr_fila){
        var self = this;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.muestra.controlador.php?op=leer",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_lab_muestra : id
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

    this.render = function(dataMuestra){
        $mdl.find(".modal-title").html("Editando Muestra");

        $txtIdMuestra.val(dataMuestra.id_muestra);
        $txtDescripcion.val(dataMuestra.descripcion);
        $btnEliminar.show();
    };

    this.anular = function(idMuestra, $tr_fila){
        if (!confirm("¿Está seguro de dar de baja este muestra")){
            return;
        }
        var self = this;

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.muestra.controlador.php?op=anular",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_lab_muestra : idMuestra
            },
            success: function(result){
                toastr.success(result.msj);

                if (TABLA_MUESTRA){
                    TABLA_MUESTRA
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
            url : VARS.URL_CONTROLADOR+"lab.muestra.controlador.php?op=guardar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_lab_muestra : $txtIdMuestra.val(),
                p_descripcion : $txtDescripcion.val()
            },
            success: function(result){
                toastr.success(result.msj);
                var arr = [].slice.call($(tplMuestras([result.registro])).find("td")),
                    dataNuevaFila = $.map(arr, function(item) {
                        return item.innerHTML;
                    });

                if (TABLA_MUESTRA){
                    if (TR_FILA){ 
                        TABLA_MUESTRA
                            .row(TR_FILA)
                            .data(dataNuevaFila)
                            .draw();  
                    } else {
                        TABLA_MUESTRA.row.add(dataNuevaFila).draw(false);     
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

    TABLA_MUESTRA  = null;
    this.cargar = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"lab.muestra.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                if (TABLA_MUESTRA){
                    TABLA_MUESTRA.destroy();
                }

                $tbbMuestras.html(tplMuestras(result));
                TABLA_MUESTRA = $tblMuestras.DataTable({
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


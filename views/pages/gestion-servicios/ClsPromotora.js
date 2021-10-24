var Promotora = function(_template, _$tabla, _$tbody){
    var $mdl,   
        $txtIdPromotora,
        $txtNumeroDocumento,
        $txtDescripcion,
        $txtComision,
        $btnEliminar,
        $btnGuardar;

    var tplPromotoras,
        $tblPromotoras,
        $tbbPromotoras;
    
    this.setInit = function(){
        tplPromotoras  = _template;
        $tblPromotoras  = _$tabla;
        $tbbPromotoras  = _$tbody;

        this.setDOM();
        this.setEventos();

        this.cargar();
        return this;
    };

    this.setDOM = function(){
        $mdl = $("#mdl-promotora");
        $txtIdPromotora = $("#txt-promotora-seleccionado");
        $txtNumeroDocumento = $("#txt-promotora-numerodocumento");
        $txtDescripcion = $("#txt-promotora-descripcion");
        $txtComision = $("#txt-promotora-comision");
        $btnEliminar = $("#btn-promotora-eliminar");
        $btnGuardar = $("#btn-promotora-guardar");
    };

    this.setEventos = function () {
        var self = this;

        $btnEliminar.on("click", function () {
            self.anular($txtIdPromotora.val());
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
        $mdl.find(".modal-title").html("Nueva Promotora");

        $txtIdPromotora.val("");
    };

    this.leer = function(id){
        var self = this;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"promotora.controlador.php?op=leer",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_promotora : id
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
        $mdl.find(".modal-title").html("Editando Promotora");

        $txtIdPromotora.val(data.id_promotora);
        $txtNumeroDocumento.val(data.numero_documento);
        $txtDescripcion.val(data.descripcion);
        $txtComision.val(data.comision_promotora);
        
        $btnEliminar.show();
    };

    this.anular = function(id){
        var self = this;

        if (!confirm("¿Está seguro de dar de baja esta promotora?")){
            return;
        }

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"promotora.controlador.php?op=anular",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_promotora : id
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
        var self = this;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"promotora.controlador.php?op=guardar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_promotora : $txtIdPromotora.val(),
                p_numero_documento: $txtNumeroDocumento.val(),
                p_descripcion : $txtDescripcion.val(),
                p_comision : $txtComision.val()
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

    this.cargar = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"promotora.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                $tbbPromotoras.html(tplPromotoras(result));

                var $html = `<option value="">Sin Promotora</option>`;
                result.forEach(promotora => {
                    $html += `<option value="${promotora.id}">${promotora.descripcion}</option>`;
                });
                $("#txt-medico-promotora").html($html);
                
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
var Rol = function(){
    var $mdl,   
        $txtIdRol,
        $txtDescripcion,
        $blkInterfaces,
        $btnGuardar;

    var tplRoles,
        $tblRoles,
        $tbdRoles;

    var $overlayTabla, 
        $btnActualizar;

    var TABLAS_AJUSTADA = false;
    
    this.setInit = function(){
        this.getTemplates();
        return this;
    };

    this.getTemplates = function(){
        var $reqRoles =  $.get("template.roles.php");
        var self = this;

        $.when($reqRoles)
            .done(function(resRoles){
                tplRoles = Handlebars.compile(resRoles);
                
                self.setDOM();
                self.setEventos();
                self.listar();
            })
            .fail(function(error){
                console.error(error);
            });
    };

    this.setDOM = function(){
        $mdl = $("#mdl-rol");
        $txtIdRol = $("#txt-rol-seleccionado");
        $txtDescripcion = $("#txt-rol-descripcion");
        $blkInterfaces = $("#blk-interfaces");

        $tblRoles = $("#tbl-roles");
        $tbdRoles = $("#tbd-roles");

        $btnGuardar = $("#btn-rol-guardar");

        $overlayTabla = $("#overlay-tbl-roles");
        $btnActualizar =  $("#btn-actualizar-roles");
    };

    this.setEventos = function () {
        var self = this;

        $btnActualizar.on("click", function(e){
            e.preventDefault();
            self.listar();
        });

        $("#btn-nuevoroles").on("click", function(e){
            e.preventDefault();
            self.nuevoRegistro();
        });
        
        $btnGuardar.on("click", function(e){
            self.guardar();
        });

        $mdl.on("hidden.bs.modal", function(e){
            $btnEliminar.hide();
            $mdl.find("form")[0].reset();
        });

        $tbdRoles.on("click", ".btn-editar", function(e){
            e.preventDefault();
            self.leer(this.dataset.id);
        });

    };

    this.nuevoRegistro = function(){
        $mdl.find("form")[0].reset();
        $mdl.modal("show");
        $mdl.find(".modal-title").html("Nuevo Rol");

        $txtIdRol.val("");
    };

    this.leer = function(id){
        var self = this;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"rol.controlador.php?op=leer",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_rol : id
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
        $mdl.find(".modal-title").html("Editando Rol");

        $txtIdRol.val(data.id_rol);
        $txtDescripcion.val(data.descripcion);

        let $html =``;
        
        let interfaces = data.interfaces;
        for (let i = 0; i < interfaces.length; i++) {
            const interfaz = interfaces[i];
            console.log(interfaz);
            $html += `  <div class="col-md-3 col-xs-12">
                            <label>${interfaz.rotulo}</label>
                            <input name="id_interfaz[]" class="txt-interfaz"  value="${interfaz.id_interfaz}" ${interfaz.active == "1" ? 'checked' : ''} type="checkbox">
                        </div>`;
        }

        $blkInterfaces.html($html);
    };


    this.guardar = function(){
        var self = this;

        let arregloInterfaces = [];
        $(".txt-interfaz").each(function(i,o){
            if (o.checked){
                arregloInterfaces.push(o.value);
            }
        });

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"rol.controlador.php?op=guardar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_rol : $txtIdRol.val(),
                p_descripcion : $txtDescripcion.val(),
                p_id_interfaz : JSON.stringify(arregloInterfaces)
            },
            success: function(result){
                toastr.success(result.msj);
                self.listar();
                
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

    TABLA_ROLES  = null;
    this.listar = function(){
        $btnActualizar.prop("disabled", true);
        $overlayTabla.show();

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"rol.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                $btnActualizar.prop("disabled", false);
                $overlayTabla.hide();
                  /*
                if (TABLA_ROLES){
                    TABLA_ROLES.destroy();
                }
                */

                $tbdRoles.html(tplRoles(result));
                /*
                TABLA_ROLES = $tblRoles.DataTable({
                    "ordering":true,
                    "pageLength": 10
                });
                */
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
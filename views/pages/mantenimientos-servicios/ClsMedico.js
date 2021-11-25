var Medico = function(_template, _$tabla, _$tbody){
    var $mdl,   
        $txtIdMedico,
        $txtNumeroDocumento,
        $txtApellidosNombres,
        $txtColegiatura,
        $txtRne,
        $txtTelefonoUno,
        $txtTelefonoDos,
        $txtCorreo,
        $txtDomicilio,
        $txtArea,
        $txtPromotora,
        $txtObservaciones,
        $btnEliminar,
        $btnGuardar;

    var $txtEsInformante,
        $txtTipoPersonalMedico,
        $txtEsRealizante;

    var tplMedicos,
        $tblMedicos,
        $tbbMedicos;
    
    this.setInit = function(){
        tplMedicos  = _template;
        $tblMedicos  = _$tabla;
        $tbbMedicos  = _$tbody;

        this.setDOM();
        this.setEventos();

        this.cargar();
        return this;
    };

    this.setDOM = function(){
        $mdl = $("#mdl-medico");
        $txtIdMedico = $("#txt-medico-seleccionado");
        $txtNumeroDocumento = $("#txt-medico-numerodocumento");
        $txtApellidosNombres = $("#txt-medico-apellidosnombres");
        $txtColegiatura = $("#txt-medico-colegiatura");
        $txtRne = $("#txt-medico-rne");
        $txtTelefonoUno = $("#txt-medico-telefonouno");
        $txtTelefonoDos = $("#txt-medico-telefonodos");
        $txtCorreo = $("#txt-medico-correo");
        $txtDomicilio = $("#txt-medico-domicilio");
        $txtEspecialidadMedico = $("#txt-medico-especialidad");
        $txtPromotora = $("#txt-medico-promotora");
        $txtObservaciones = $("#txt-medico-observaciones");
        $btnEliminar = $("#btn-medico-eliminar");
        $btnGuardar = $("#btn-medico-guardar");

        $txtEsInformante = $("#txt-medico-esinformante");
        $txtTipoPersonalMedico = $("#txt-medico-tipomedico");
        $txtEsRealizante = $("#txt-medico-esrealizante");
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

    this.nuevoRegistro = function(){
        $mdl.find("form")[0].reset();
        $mdl.modal("show");
        $mdl.find(".modal-title").html("Nuevo Médico");
        $txtIdMedico.val("");
    };

    this.leer = function(id){
        var self = this;
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=leer",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_medico : id
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

    this.render = function(dataMedico){
        $mdl.find(".modal-title").html("Editando Médico");

        $txtIdMedico.val(dataMedico.id_medico);
        $txtNumeroDocumento.val(dataMedico.numero_documento);
        $txtApellidosNombres.val(dataMedico.apellidos_nombres);
        $txtColegiatura.val(dataMedico.colegiatura);
        $txtRne.val(dataMedico.rne);
        $txtTelefonoUno.val(dataMedico.telefono_uno);
        $txtTelefonoDos.val(dataMedico.telefono_dos);
        $txtCorreo.val(dataMedico.correo);
        $txtDomicilio.val(dataMedico.domicilio);
        $txtEspecialidadMedico.val(dataMedico.id_especialidad);
        $txtPromotora.val(dataMedico.id_promotora);
        $txtObservaciones.val(dataMedico.observaciones);

        $txtEsInformante.val(dataMedico.es_informante);
        $txtTipoPersonalMedico.val(dataMedico.tipo_personal_medico);
        $txtEsRealizante.val(dataMedico.es_realizante);
        
        $btnEliminar.show();
    };

    this.anular = function(idMedico){
        if (!confirm("¿Está seguro de dar de baja este médico")){
            return;
        }
        var self = this;

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=anular",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_medico : idMedico
            },
            success: function(result){
                toastr.success(result.msj);
                self.cargar();
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
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=guardar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_medico : $txtIdMedico.val(),
                p_numero_documento : $txtNumeroDocumento.val(),
                p_apellidos_nombres : $txtApellidosNombres.val(),
                p_colegiatura : $txtColegiatura.val(),
                p_rne : $txtRne.val(),
                p_telefono_uno : $txtTelefonoUno.val(),
                p_telefono_dos : $txtTelefonoDos.val(),
                p_correo : $txtCorreo.val(),
                p_domicilio : $txtDomicilio.val(),
                p_id_especialidad : $txtEspecialidadMedico.val(),
                p_id_promotora : $txtPromotora.val(),
                p_observaciones : $txtObservaciones.val(),
                p_es_informante: $txtEsInformante.val(),
                p_tipo_personal_medico: $txtTipoPersonalMedico.val(),
                p_es_realizante: $txtEsRealizante.val()
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

    TABLA_MEDICOS  = null;
    this.cargar = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                if (TABLA_MEDICOS){
                    TABLA_MEDICOS.destroy();
                }

                $tbbMedicos.html(tplMedicos(result));
                TABLA_MEDICOS = $tblMedicos.DataTable({
                    "ordering": false
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


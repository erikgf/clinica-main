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
        $txtSede,
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

    var TR_FILA = null;

    this._sedes = [];
    this._especialidades = [];
    
    this.setInit = function(){
        tplMedicos  = _template;
        $tblMedicos  = _$tabla;
        $tbbMedicos  = _$tbody;

        this.setDOM();
        this.setEventos();
        
        this.cargarSedes();
        this.cargarEspecialidades();
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
        $txtSede = $("#txt-medico-sede");
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
            self.anular($txtIdMedico.val(), TR_FILA);
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
        TR_FILA = null;
    };

    this.leer = function(id, $tr_fila){
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
        $txtSede.val(dataMedico.id_sede);
        
        $btnEliminar.show();
    };

    this.anular = function(idMedico, $tr_fila){
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

                if (TABLA_MEDICOS){
                    TABLA_MEDICOS
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
                p_es_realizante: $txtEsRealizante.val(),
                p_id_sede : $txtSede.val()
            },
            success: function(result){
                toastr.success(result.msj);
                var arr = [].slice.call($(tplMedicos([result.registro])).find("td")),
                    dataNuevaFila = $.map(arr, function(item) {
                        return item.innerHTML;
                    });


                if (TABLA_MEDICOS){
                    if (TR_FILA){ 
                        console.log("update", dataNuevaFila);
                        TABLA_MEDICOS
                            .row(TR_FILA)
                            .data(dataNuevaFila)
                            .draw();  
                    } else {
                        console.log("insertum", dataNuevaFila);
                        TABLA_MEDICOS.row.add(dataNuevaFila).draw(false);     
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

                //console.log(find("tr").eq(0).find("td").length;)
                TABLA_MEDICOS = $tblMedicos.DataTable({
                    scrollX: true,
                    dom: 'Bfrtip',
                    buttons: [
                        'copy',
                        {
                            extend: 'excelHtml5',
                            exportOptions: {
                                columns: Array.from({length: 8}, (_, i) => i + 1)
                            },
                            title: 'Médicos DMI'
                        }
                    ],
                    
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

    this.cargarSedes = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"sede.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            success: (result) => {
                let $html = `<option value="">Seleccionar</option>`;
                result.forEach(item => {
                    $html += `<option value="${item.id}">${item.descripcion}</option>`
                });
                $txtSede.html($html);

                this._sedes = result;
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    this.cargarEspecialidades = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"especialidad.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            success: (result) => {
                let $html = `<option value="">Seleccionar</option>`;
                result.forEach(item => {
                    $html += `<option value="${item.id}">${item.descripcion}</option>`
                });

                $txtEspecialidadMedico.html($html);

                this._especialidades = result;
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


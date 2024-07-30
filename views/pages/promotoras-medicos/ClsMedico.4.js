var Medico = function(_template, _$tabla, _$tbody){
    var $mdl,   
        $frm,
        $txtIdMedico,
        $txtNumeroDocumento,
        $txtApellidosNombres,
        $txtColegiatura,
        $txtRne,
        $txtTelefonoUno,
        $txtTelefonoDos,
        $txtFechaNacimiento,
        $txtCorreo,
        $txtDomicilio,
        $txtArea,
        $txtSede,
        $txtPromotora,
        $txtObservaciones,
        $imgFirma,
        $blkFirma,
        $btnEliminar,
        $btnGuardar;

    var $txtEsInformante,
        $txtTipoPersonalMedico,
        $txtEsRealizante,
        $txtPuedeTenerUsuario;

    var tplMedicos,
        $tblMedicos,
        $tbbMedicos;

    var $btnCumpleaños, $txtMesesCumpleaños, $mdlCumpleaños;

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
        $frm = $("#frm-medico");
        $txtIdMedico = $("#txt-medico-seleccionado");
        $txtNumeroDocumento = $("#txt-medico-numerodocumento");
        $txtApellidosNombres = $("#txt-medico-apellidosnombres");
        $txtColegiatura = $("#txt-medico-colegiatura");
        $txtRne = $("#txt-medico-rne");
        $txtTelefonoUno = $("#txt-medico-telefonouno");
        $txtTelefonoDos = $("#txt-medico-telefonodos");
        $txtFechaNacimiento = $("#txt-medico-fechanacimiento");
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
        $txtPuedeTenerUsuario  = $("#txt-medico-puedetenerusuario")

        $btnCumpleaños = $("#btn-imprimir-fc-medicos");
        $txtMesesCumpleaños = $("#txt-medico-cumpleaños-mes");
        $mdlCumpleaños = $("#mdl-medico-cumpleaños");
        $imgFirma = $("#img-medico-firma");
        $blkFirma = $("#blk-medico-firma");

        const year = new Date().getFullYear();
        $txtFechaNacimiento.attr("min", year + "-01-01");
        $txtFechaNacimiento.attr("max", year + "-12-31");

        this.cargarMesesModalCumpleaños();
    };

    this.setEventos = function () {
        var self = this;

        $btnEliminar.on("click", function () {
            self.anular($txtIdMedico.val(), TR_FILA);
        });
        
        $frm.on("submit", function(e){
            e.preventDefault();
            self.guardar();
        });

        $mdl.on("hidden.bs.modal", function(e){
            $btnEliminar.hide();
            $mdl.find("form")[0].reset();
        });

        $('#tab-promotoras-medicos').on("shown.bs.tab", function(e) {
            console.log("clicked tab");
            if (TABLA_MEDICOS){
                TABLA_MEDICOS
                    .columns.adjust();
            }
        });

        $btnCumpleaños.on("click", () => {
            $mdlCumpleaños.modal("show");
        });

        $mdlCumpleaños.on("submit", "form", (e)=>{
            e.preventDefault();
            const params = new URLSearchParams({ m : $txtMesesCumpleaños.val() });
            window.open(`../../../impresiones/medicos.cumpleaños.mes.xls.php?${params.toString()}`,"_blank")
        });

        $imgFirma.on("change", (e) =>{
            this.printImagenFirma(e.currentTarget.files);            
        });

        $blkFirma.on("click", "a", (e) => {
            e.preventDefault();
            this.cleanImagenFirma();
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
        $txtFechaNacimiento.val(dataMedico.fecha_nacimiento);
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
        $txtPuedeTenerUsuario.val(dataMedico.puede_tener_usuario);

        if (Boolean(dataMedico.firma)){
            $blkFirma.find("img").prop("src", `../../../impresiones/medicos-firmas/${dataMedico.firma}`);
            $blkFirma.show();
        }

        
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

        const data = new FormData();
        if ($imgFirma[0].files){
            data.append('p_firma', $imgFirma[0].files[0]);
        }

        data.append("p_id_medico", $txtIdMedico.val());
        data.append("p_numero_documento", $txtNumeroDocumento.val());
        data.append("p_apellidos_nombres", $txtApellidosNombres.val());
        data.append("p_colegiatura", $txtColegiatura.val());
        data.append("p_rne", $txtRne.val());
        data.append("p_telefono_uno", $txtTelefonoUno.val());
        data.append("p_telefono_dos", $txtTelefonoDos.val());
        data.append("p_correo", $txtCorreo.val());
        data.append("p_fecha_nacimiento", $txtFechaNacimiento.val());
        data.append("p_domicilio", $txtDomicilio.val());
        data.append("p_id_especialidad", $txtEspecialidadMedico.val());
        data.append("p_id_promotora", $txtPromotora.val());
        data.append("p_observaciones", $txtObservaciones.val());
        data.append("p_es_informante", $txtEsInformante.val());
        data.append("p_tipo_personal_medico", $txtTipoPersonalMedico.val());
        data.append("p_es_realizante", $txtEsRealizante.val());
        data.append("p_id_sede", $txtSede.val());
        data.append("p_puede_tener_usuario", $txtPuedeTenerUsuario.val());

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=guardar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            enctype: 'multipart/form-data',
            processData: false,  // tell jQuery not to process the data
            contentType: false,
            data: data,
            success: function(result){
                toastr.success(result.msj);
                const arr = [].slice.call($(tplMedicos([result.registro])).find("td")),
                    dataNuevaFila = $.map(arr, function(item) {
                        return item.innerHTML;
                    });


                if (TABLA_MEDICOS){
                    if (TR_FILA){ 
                        TABLA_MEDICOS
                            .row(TR_FILA)
                            .data(dataNuevaFila)
                            .draw();  
                    } else {
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

    let TABLA_MEDICOS  = null;
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
                        {
                            extend: 'copy',
                            exportOptions: {
                                columns: "thead th:not(.noExport)"
                            },
                        },
                        {
                            extend: 'excelHtml5',
                            exportOptions: {
                                columns: "thead th:not(.noExport)"
                            },
                            title: 'Médicos DMI'
                        },
                        'colvis'
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

    this.cargarMesesModalCumpleaños = () => {
        let $html = `<option value="">Seleccionar</option>`;
        Util.getMeses().forEach(item => {
            $html += `<option value="${item.id}">${item.descripcion}</option>`
        });
        $txtMesesCumpleaños.html($html);
    };

    this.printImagenFirma  = (files) => {
        const file = files[0];
        let fileReader = new FileReader();
        fileReader.readAsDataURL(file);
        fileReader.onload = function (){
            $blkFirma.find("img").prop("src", fileReader.result);
            $blkFirma.show();
        }
    };

    this.cleanImagenFirma = () => {
        $blkFirma.find("img").prop("src", null);
        $blkFirma.hide();
        $imgFirma.val(null);
    };

    return this.setInit();
};

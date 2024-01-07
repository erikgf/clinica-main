var Paciente = function() {
    var $modalPaciente,
        $frmPaciente,
        $txtPacienteSeleccionado,
        $txtBuscarPaciente,
        $txtTipoDocumento,
        $txtNumeroDocumento,
        $txtNombres,
        $txtApellidosPaterno,
        $txtApellidosMaterno,
        $txtSexo,
        $txtFechaNacimiento,
        $txtOcupacion,
        $txtTipoPaciente,
        $txtEstadoCivil,
        $txtCorreo,
        $txtDomicilio,
        $txtDepartamento,
        $txtProvincia,
        $txtDistrito,
        $txtTelefono,
        $txtCelular1,
        $txtCelular2,
        $btnGuardar,
        $btnLimpiar,
        $btnEliminar;

    var EVITAR_EVENTO_CAMBIAR_UBIGEO = false;

    var getTemplates = function(){
        $.get("template.servicioagregado.php", function(result, state){
            if (state == "success"){
                tplServicioAgregado = Handlebars.compile(result);
            }
        });
    };

    this.setDOM = function(){
        $modalPaciente = $("#mdl-paciente");

        $frmPaciente = $("#frm-paciente");
        $txtPacienteSeleccionado = $("#txt-pacienteseleccionado");

        $txtBuscarPaciente = $("#txt-buscarpaciente");
        $txtTipoDocumento = $("#txt-tipodocumento");
        $txtNumeroDocumento = $("#txt-numerodocumento");
        $txtNombres = $("#txt-nombres");
        $txtApellidosPaterno = $("#txt-apellidospaterno");
        $txtApellidosMaterno = $("#txt-apellidosmaterno");
        $txtSexo = $("#txt-sexo");
        $txtFechaNacimiento = $("#txt-fechanacimiento");
        $txtOcupacion = $("#txt-ocupacion");
        $txtTipoPaciente = $("#txt-tipopaciente");
        $txtEstadoCivil = $("#txt-estadocivil");
        $txtCorreo = $("#txt-correo");
        $txtDomicilio = $("#txt-domicilio");
        $txtDepartamento = $("#txt-departamento");
        $txtProvincia = $("#txt-provincia");
        $txtDistrito = $("#txt-distrito");

        $txtTelefono = $("#txt-telefonofijo");
        $txtCelular1 = $("#txt-celular1");
        $txtCelular2 = $("#txt-celular2");


        $btnGuardar = $modalPaciente.find("#btn-guardar");
        $btnLimpiar = $modalPaciente.find("#btn-limpiar");
        $btnEliminar = $modalPaciente.find("#btn-eliminar");
    };
    
    this.setEventos = function(){
        $txtNumeroDocumento.inputFilter(function(value) {
            return /^\d*$/.test(value);    // Allow digits only, using a RegExp
        });

        $txtBuscarPaciente.on("change", function(){
            var idpaciente = this.value;
            if (idpaciente == "0" || idpaciente == "" || idpaciente == null){
                return;
            }

            $.ajax({ 
                url : VARS.URL_CONTROLADOR+"paciente.controlador.php?op=obtener_paciente_x_id_full",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: {
                    p_idpaciente : idpaciente
                },
                success: function(xhr){
                    if( xhr.rpt){
                        var datos = xhr.datos,
                            $blkAlertarEdicion = $("#blk-alertaredicion");
                        $blkAlertarEdicion.show();
                        $blkAlertarEdicion.find("#lbl-nombreusuario").html(datos.nombres_completos);
                        renderDataPaciente(datos);
                        $txtBuscarPaciente.val(null).trigger("change");
                        $txtTipoDocumento.focus();

                        $blkAlertarEdicion = null;
                    }
                    console.log(xhr);
                },
                error: function (request, status, error) {
                    toastr.error(error.responseText);
                    return;
                },
                cache: true
                }
            );

        });

        $txtDepartamento.on("change", function(){
            if (EVITAR_EVENTO_CAMBIAR_UBIGEO) return;
            $txtProvincia.val(null).trigger('change');
        });

        $txtProvincia.on("change", function(){
            if (EVITAR_EVENTO_CAMBIAR_UBIGEO) return;
            $txtDistrito.val(null).trigger('change');
        });

        $btnLimpiar.on("click", function(e){
            e.preventDefault();
            limpiarCampos();

        });

        $btnEliminar.on("click", function(e){
            e.preventDefault();
            eliminarPaciente();
        });

        $modalPaciente.on("shown.bs.modal", function(e){
            e.preventDefault();
            limpiarCampos();

            $("#chk-asignarregistro").prop("checked", true)
        });

        $btnGuardar.on("click", function(e){
            e.preventDefault();
            if (Util.validarFormulario($frmPaciente)){
                guardarPaciente();
            };
        });

        $txtTipoDocumento.on("change", function(){
            var idTipoDoc = $txtTipoDocumento.val();

            if (idTipoDoc == "1"){
                $txtNumeroDocumento.attr("maxlength", "8");
                return;
            }

            if (idTipoDoc == "6"){
                $txtNumeroDocumento.attr("maxlength", "11");
                return;
            }

            $txtNumeroDocumento.attr("maxlength", "15");
            return;
        });

        var buscandoNumeroDocumentoCliente = false,
            $spinner = $("#blk-spinner");
        $txtNumeroDocumento.on("change", function(e){
            var fnError = function(mensajeError){
                $spinner.removeClass("fa-spin fa-spinner").addClass("fa-close text-red");
                setTimeout(function(){
                    $spinner.removeClass("fa-close text-red").addClass("fa-spin fa-spinner");
                    $spinner.hide();
                },1500);
                toastr.error(mensajeError);
                $txtNumeroDocumento.select();
            };

            if (buscandoNumeroDocumentoCliente){
                return;
            }

            var idTipoDoc = $txtTipoDocumento.val(),
                numeroDocumento = $txtNumeroDocumento.val(),
                numeroDocumentoLength = numeroDocumento.length;

            if (idTipoDoc != "1" && idTipoDoc != "6"){
                return;
            }

            if (numeroDocumentoLength != 8 && numeroDocumentoLength != 11){
                return;
            }

            if ($txtPacienteSeleccionado.val() != "" && $txtNombres.val()  != ""){
                return;
            }

            buscandoNumeroDocumentoCliente = true;
            $spinner.show();
            $.ajax({ 
                url: VARS.URL_CONTROLADOR+"documento.electronico.controlador.php?op=consultar_documento_cliente",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: {
                    p_numero_documento : numeroDocumento
                },
                success: function(res){
                    buscandoNumeroDocumentoCliente = false;
                    if (res.respuesta == "error"){
                        fnError(res.mensaje);
                        return;
                    }
                    $spinner.removeClass("fa-spin fa-spinner").addClass("fa-check text-green");
                    setTimeout(function(){
                        $spinner.removeClass("fa-check text-green").addClass("fa-spin fa-spinner");
                        $spinner.hide();
                    },1500);

                    if (res.api){
                        var api = res.api;
                        $txtNombres.val(api.nombres);
                        $txtApellidosMaterno.val(api.apell_mat);
                        $txtApellidosPaterno.val(api.apell_pat);
                        $txtFechaNacimiento.val(Util.formatearFechaCorrectamente(api.fec_nacimiento));
                        $txtSexo.val(api.sexo);
                        $txtOcupacion.focus();

                        return;
                    }

                    if (res.respuesta == "ok"){
                        if (res.estado != "ACTIVO"){
                            toastr.error("Cliente está usando un RUC NO ACTIVO.");
                        }
                        $txtNombres.val(res.razon_social);
                        $txtNombres.focus();
                    }

                },
                error: function (res) {
                    buscandoNumeroDocumentoCliente = false;
                    fnError(res.responseText);
                    return;
                },
                cache: true
            });
        });

    };

    this.setFuncionesInicio = function(){
        /*Iniciando Selects*/
        $txtBuscarPaciente.select2({
            dropdownParent: $txtBuscarPaciente.parent(),
            ajax: { 
                url : VARS.URL_CONTROLADOR+"paciente.controlador.php?op=buscar_pacientes",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        p_cadenabuscar: params.term,
                    };
                },
                processResults: function (response) {
                    return {
                        results: response.datos
                    };
                },
                cache: true
            },
            minimumInputLength: 3,
            width: '100%',
            multiple: false,
            placeholder:"Buscar...",
            debug: true
        });

        $txtDepartamento.select2({
            dropdownParent: $txtDepartamento.parent(),
            ajax: { 
                url : VARS.URL_CONTROLADOR+"ubigeo.controlador.php?op=obtener_departamentos",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        p_cadenabuscar: params.term, 
                    };
                },
                processResults: function (response) {
                    return {
                        results: response.datos
                    };
                },
                cache: true
            },
            minimumInputLength: 3,
            width: '100%',
            multiple:false,
            placeholder:"Seleccionar",
            debug: true,
        });

        $txtProvincia.select2({
            dropdownParent: $txtProvincia.parent(),
            ajax: { 
                url : VARS.URL_CONTROLADOR+"ubigeo.controlador.php?op=obtener_provincias",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        p_cadenabuscar: params.term, 
                        p_iddepartamento : $txtDepartamento.val()
                    };
                },
                processResults: function (response) {
                    return {
                        results: response.datos
                    };
                },
                cache: true
            },
            minimumInputLength: 3,
            width: '100%',
            multiple:false,
            placeholder:"Seleccionar",
            debug: true,
        });

        $txtDistrito.select2({
            dropdownParent: $txtDistrito.parent(),
            ajax: { 
                url : VARS.URL_CONTROLADOR+"ubigeo.controlador.php?op=obtener_distritos",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        p_cadenabuscar: params.term, 
                        p_idprovincia : $txtProvincia.val()
                    };
                },
                processResults: function (response) {
                    return {
                        results: response.datos
                    };
                },
                cache: true
            },
            minimumInputLength: 3,
            width: '100%',
            multiple:false,
            placeholder:"Seleccionar",
            debug: true
        });
    };


    //getTemplates();
    this.setDOM();
    this.setEventos();
    this.setFuncionesInicio();

    var renderDataPaciente = function(data_paciente){        
        $txtPacienteSeleccionado.val(data_paciente.id_paciente);

        $txtTipoDocumento.val(data_paciente.id_tipo_documento);
        $txtNumeroDocumento.val(data_paciente.numero_documento).trigger("change");
        $txtNombres.val(data_paciente.nombres);
        $txtApellidosPaterno.val(data_paciente.apellidos_paterno);
        $txtApellidosMaterno.val(data_paciente.apellidos_materno);
        $txtSexo.val(data_paciente.sexo);
        $txtFechaNacimiento.val(data_paciente.fecha_nacimiento);
        $txtOcupacion.val(data_paciente.ocupacion);
        $txtTipoPaciente.val(data_paciente.id_tipo_paciente);
        $txtEstadoCivil.val(data_paciente.estado_civil);
        $txtCorreo.val(data_paciente.correo);
        $txtDomicilio.val(data_paciente.domicilio);


        EVITAR_EVENTO_CAMBIAR_UBIGEO = true;
        $txtDepartamento.append(new Option(data_paciente.departamento, data_paciente.codigo_ubigeo_departamento, true, true)).trigger('change');
        $txtProvincia.append(new Option(data_paciente.provincia, data_paciente.codigo_ubigeo_provincia, true, true)).trigger('change');
        $txtDistrito.append(new Option(data_paciente.distrito, data_paciente.codigo_ubigeo_distrito, true, true)).trigger('change');
        EVITAR_EVENTO_CAMBIAR_UBIGEO = false;

        $txtTelefono.val(data_paciente.telefono_fijo);
        $txtCelular1.val(data_paciente.celular_uno);
        $txtCelular2.val(data_paciente.celular_dos);

        Util.validarFormulario($frmPaciente);
        //$btnEliminar.show();
    }; 

    var limpiarCampos = function(){
        var  $blkAlertarEdicion = $("#blk-alertaredicion");
        $blkAlertarEdicion.hide();
        $blkAlertarEdicion.find("#lbl-nombreusuario").empty();

        $txtBuscarPaciente.focus();
        $txtBuscarPaciente.val(null).trigger("change");
        $blkAlertarEdicion = null;

        EVITAR_EVENTO_CAMBIAR_UBIGEO = true;
        $frmPaciente[0].reset();

        $txtDepartamento.val(null).trigger("change");
        $txtProvincia.val(null).trigger("change");
        $txtDistrito.val(null).trigger("change");
        EVITAR_EVENTO_CAMBIAR_UBIGEO = false;

        $txtPacienteSeleccionado.val("");
        $txtTipoDocumento.val("1").change();
        //$btnEliminar.hide();
    };

    var eliminarPaciente = function(){
        if(!confirm("¿Estás seguro de eliminar este paciente?")){
            return;
        }

        if ($txtPacienteSeleccionado.val() == ""){
            toastr.error("No hay un paciente seleccionado correctamente.");
            return;
        }

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"paciente.controlador.php?op=eliminar_paciente",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_idpaciente : $txtPacienteSeleccionado.val()
            },
            success: function(xhr){
                if( xhr.rpt){
                    toastr.success(xhr.msj);

                    limpiarCampos();
                }
            },
            error: function (request, status, error) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );

    };

    var guardarPaciente = function(){
        if(!confirm("¿Estás seguro de guardar este paciente?")){
            return;
        }

        var data_formulario = {
            p_id_paciente : $txtPacienteSeleccionado.val(),
            p_id_tipo_documento : $txtTipoDocumento.val(),
            p_numero_documento : $txtNumeroDocumento.val(),
            p_nombres : $txtNombres.val(),
            p_apellidos_paterno : $txtApellidosPaterno.val(),
            p_apellidos_materno : $txtApellidosMaterno.val(),
            p_sexo : $txtSexo.val(),
            p_fecha_nacimiento : $txtFechaNacimiento.val(),
            p_ocupacion : $txtOcupacion.val(),
            p_idtipo_paciente : $txtTipoPaciente.val(),
            p_estado_civil : $txtEstadoCivil.val(),
            p_telefono_fijo : $txtTelefono.val(),
            p_celular_uno : $txtCelular1.val(),
            p_celular_dos : $txtCelular2.val(),
            p_correo : $txtCorreo.val(),
            p_domicilio : $txtDomicilio.val(),
            p_codigo_ubigeo_distrito : $txtDistrito.val(),
            p_codigo_ubigeo_provincia : $txtProvincia.val(),
            p_codigo_ubigeo_departamento : $txtDepartamento.val()
        };

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"paciente.controlador.php?op=registrar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: data_formulario,
            success: function(xhr){
                if( xhr.rpt){
                    var datos_nuevo_paciente = xhr.paciente;
                    toastr.success(xhr.msj);

                    if ($("#chk-asignarregistro").prop("checked")){
                        $modalPaciente.modal("hide");
                        $txtPaciente.val(null);
                        $txtPacienteSeleccionado.val("");
                        $txtPaciente.append(new Option(datos_nuevo_paciente.documento_nombres_completos, datos_nuevo_paciente.id, true, true)).trigger('change');
                    } else {
                        limpiarCampos();
                    }
                    
                }
            },
            error: function (request, status, error) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );   

        /*
        */
    };

    return this;
};

$(document).ready(function(){
    objPaciente =  new Paciente(); 
});



var CanjearComprobante = function() {
    var $modalCanjearComprobante,
        $frmCanjearComprobante,
        $txtComprobante,
        $txtComprobanteCanjeado,
        $txtFechaEmision,
        $lblCantidadServicios,
        $tblServicios,
        $txtTipoDocumento,
        $txtNumeroDocumento,
        $btnGuardar;

    var $blkBoleta,
        $txtBoletaNombres,
        $txtBoletaApellidoPaterno,
        $txtBoletaApellidoMaterno,
        $txtBoletaFechaNacimiento,
        $txtBoletaSexo;

    var $blkFactura,
        $txtFacturaRazonSocial,
        $txtFacturaDireccion;

    var tplServiciosAgregados;

    var ID_ATENCION_MEDICA = null;

    var getTemplates = function(){
        $.get("../registro-atencion/template.servicioagregado.continuarpago.php", function(result, state){
            if (state == "success"){
                tplServiciosAgregados = Handlebars.compile(result);
            }
        });
    };

    this.setDOM = function(){
        $modalCanjearComprobante = $("#mdl-canjearcomprobante");

        $frmCanjearComprobante = $("#frm-canjearcomprobante");
        $txtComprobante = $("#txt-canjearcomprobante");
        $txtComprobanteCanjeado = $("#txt-canjearcomprobantecanjeado");
        $txtFechaEmision  = $("#txt-canjearfechaemision");

        $blkFactura = $("#blk-canjearfactura");
        $blkBoleta = $("#blk-canjearboleta");

        $lblCantidadServicios = $("#lbl-canjearcantidadservicios");
        $tblServicios = $("#tbl-canjearservicios");

        $txtTipoDocumento = $("#txt-canjeartipodocumento");
        $txtNumeroDocumento = $("#txt-canjearnumerodocumento");

        $txtBoletaNombres = $("#txt-boletanombres");
        $txtBoletaApellidoPaterno = $("#txt-boletaapellidopaterno");
        $txtBoletaApellidoMaterno = $("#txt-boletaapellidomaterno");
        $txtBoletaFechaNacimiento = $("#txt-boletafechanacimiento");
        $txtBoletaSexo = $("#txt-boletasexo");

        $txtFacturaRazonSocial  = $("#txt-facturarazonsocial");
        $txtFacturaDireccion  = $("#txt-facturadireccion");

        $btnCanjear = $modalCanjearComprobante.find("#btn-canjear");
    };
    
    this.setEventos = function(){
        $txtTipoDocumento.on("change", function(){
            mostrarBloqueDocumento(this.value);
        });

        $txtNumeroDocumento.on("change", function(e){
            e.preventDefault();
            buscarNumeroDocumento(this.value);
        });

        $btnCanjear.on("click", function(e){
            e.preventDefault();
            if (Util.validarFormulario($frmCanjearComprobante)){
                canjearComprobante();
            };
        });

        $modalCanjearComprobante.on("bs.modal.hidden", function(e){
            limpiarModal();
        });
    };

    var limpiarModal = function(){
        $txtTipoDocumento.val("1");
        $txtNumeroDocumento.val("");
        $txtBoletaNombres.val("");
        $txtBoletaApellidoPaterno.val("");
        $txtBoletaApellidoMaterno.val("");
        $txtBoletaFechaNacimiento.val("");
        $txtBoletaSexo.val("");

        $txtFacturaRazonSocial.val("");
        $txtFacturaDireccion.val("");

        mostrarBloqueDocumento($txtNumeroDocumento.val());
        ID_ATENCION_MEDICA = null;
    };

    this.preCanjearComprobante = function(id_atencion_medica, cliente){
        $.ajax({ 
            url: VARS.URL_CONTROLADOR+"atencion.medica.controlador.php?op=obtener_datos_atencion_comprobante",
            type: "post",
            dataType: 'json',
            data : {
                p_id_atencion_medica : id_atencion_medica
            },
            delay: 250,
            success: function(datos){
                ID_ATENCION_MEDICA = id_atencion_medica;
                $modalCanjearComprobante.find(".modal-title").html("Canjear Comprobante: "+cliente);
                $modalCanjearComprobante.modal("show");

                $txtComprobante.val(datos.comprobante);
                $tblServicios.html(tplServiciosAgregados(datos.servicios));
                $lblCantidadServicios.html(datos.servicios.length);

                Util.setFecha($txtFechaEmision, new Date());

                mostrarBloqueDocumento($txtTipoDocumento.val() );
          },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
        });
    };

    var canjearComprobante = function(){
        if (ID_ATENCION_MEDICA == null){
            toastr.error("No se ha encontrado el ID atención médica, consultar ADMINISTRADOR");
            return;
        }

        $.ajax({ 
            url: VARS.URL_CONTROLADOR+"atencion.medica.controlador.php?op=canjear_comprobante",
            type: "post",
            dataType: 'json',
            data : {
                p_id_atencion_medica : ID_ATENCION_MEDICA,
                p_id_tipo_comprobante : $txtComprobanteCanjeado.val(),
                p_fecha_emision : $txtFechaEmision.val(),
                p_id_tipo_documento : $txtTipoDocumento.val(),
                p_numero_documento : $txtNumeroDocumento.val(),
                p_boleta_nombres: $txtBoletaNombres.val(),
                p_boleta_apellido_paterno: $txtBoletaApellidoPaterno.val(),
                p_boleta_apellido_materno : $txtBoletaApellidoMaterno.val(),
                p_boleta_fecha_nacimiento : $txtBoletaFechaNacimiento.val(),
                p_boleta_sexo : $txtBoletaSexo.val(),
                p_factura_razon_social: $txtFacturaRazonSocial.val(),
                p_factura_direccion : $txtFacturaDireccion.val()
            },
            delay: 250,
            success: function(datos){
                $modalCanjearComprobante.modal("hide");
                objGestionAtenciones.listarMovimientos();

                window.open("../../../impresiones/ticket.comprobante.php?id="+datos.id_documento_electronico);
                limpiarModal();
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
        });
    };  

    var mostrarBloqueDocumento = function(numeroDocumento){
        var esRUC = numeroDocumento == "6";

        if (esRUC){
            $blkBoleta.hide();            
            $txtBoletaNombres.prop("required", false).removeClass("is-invalid").val("");
            $txtBoletaApellidoMaterno.prop("required", false).removeClass("is-invalid").val("");
            $txtBoletaApellidoPaterno.prop("required", false).removeClass("is-invalid").val("");
            $txtBoletaFechaNacimiento.prop("required", false).removeClass("is-invalid").val("");
            $txtBoletaSexo.prop("required", false).removeClass("is-invalid").val("");

            $txtNumeroDocumento.prop("maxlength", 11);
            $txtNumeroDocumento.val($txtNumeroDocumento.val().substr(0,11));

            $blkFactura.show();        
            $txtFacturaRazonSocial.prop("required", true).removeClass("is-invalid");
            $txtFacturaDireccion.removeClass("is-invalid");
        } else {
            $blkFactura.hide();
            $txtFacturaRazonSocial.prop("required", false).removeClass("is-invalid").val("");
            $txtFacturaDireccion.removeClass("is-invalid").val("");

            if ($txtTipoDocumento.val() == "1"){
                $txtNumeroDocumento.prop("maxlength", 8);
                $txtNumeroDocumento.val($txtNumeroDocumento.val().substr(0,8));    
            } else {
                $txtNumeroDocumento.prop("maxlength", 15);
            }
            
            $blkBoleta.show();
            $txtBoletaNombres.prop("required", true).removeClass("is-invalid").val("");
            $txtBoletaApellidoMaterno.prop("required", true).removeClass("is-invalid").val("");
            $txtBoletaApellidoPaterno.prop("required", true).removeClass("is-invalid").val("");
            $txtBoletaFechaNacimiento.prop("required", true).removeClass("is-invalid").val("");
            $txtBoletaSexo.prop("required", true).removeClass("is-invalid").val("");
        }
    };

    var buscandoNumeroDocumentoCliente = false;
    var buscarNumeroDocumento = function(numeroDocumento){
        var $spinner = $("#blk-spinner-numerodocumento");

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

        var numeroDocumentoLength = numeroDocumento.length,
            idTipoDocumento = $txtTipoDocumento.val();

        if (idTipoDocumento == "1" || idTipoDocumento == "6"){
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

                    if (res.respuesta == "ok"){
                        if (idTipoDocumento == "6"){
                            if (res.estado != "ACTIVO"){
                                toastr.error("Cliente está usando un RUC NO ACTIVO.");
                            }
                            console.log(res, $txtFacturaRazonSocial, $txtFacturaDireccion);
                            $txtFacturaRazonSocial.val(res.razon_social);
                            $txtFacturaDireccion.val(res.direccion.trim());
                        } else {
                           if (res.api){
                                var api = res.api;
                                $txtBoletaNombres.val(api.nombres);
                                $txtBoletaApellidoMaterno.val(api.apell_mat);
                                $txtBoletaApellidoPaterno.val(api.apell_pat);
                                $txtBoletaFechaNacimiento.val(Util.formatearFechaCorrectamente(api.fec_nacimiento));
                                $txtBoletaSexo.val(api.sexo);
                            }      
                        }
                    }

                },
                error: function (res) {
                    buscandoNumeroDocumentoCliente = false;
                    fnError(res.responseText);
                    return;
                },
                cache: true
            });  
        }
    };


    getTemplates();

    this.setDOM();
    this.setEventos();

    return this;
};

$(document).ready(function(){
    objCanjearComprobante =  new CanjearComprobante(); 
});



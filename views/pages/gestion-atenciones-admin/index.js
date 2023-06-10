var GestionAtenciones = function() {
    var $txtFechaInicio,
        $txtFechaFin,
        $btnExcel,
        $tblMovimientos;

    var tplMovimientos;
    var $btnActualizar;

    this.getTemplates = function(){
            $.get("template.movimientos.php", function(result, state){
                if (state == "success"){
                    tplMovimientos = Handlebars.compile(result);
                    listarMovimientos();
                }
            });
        };


    this.setDOM = function(){
        var hoy = new Date();
        //haceSieteDias = new Date();
        $txtFechaInicio = $("#txt-fechainicio");
        $txtFechaFin = $("#txt-fechafin");
        $btnActualizar = $("#btn-actualizarmovimientos");

        //haceSieteDias.setDate(haceSieteDias.getDate() - 7);
        //Util.setFecha($txtFechaInicio, haceSieteDias);
        Util.setFecha($txtFechaInicio, hoy);
        Util.setFecha($txtFechaFin, hoy);

        $tblMovimientos = $("#tbl-cajamovimientos");

        $btnExcel = $("#btn-excel");
    };
    
    this.setEventos = function(){
        $btnActualizar.on("click", function(e){
            listarMovimientos();
        });

        $tblMovimientos.on("click", ".btn-anularmovimiento", function (e) {
            var $btn = this,
                dataset = $btn.dataset;
            e.preventDefault();
            anularMovimiento(dataset.id, dataset.cliente);
        });

        $tblMovimientos.on("click", ".btn-cambiarmedico", function (e) {
            var $btn = this,
                dataset = $btn.dataset;
            e.preventDefault();
            initCambiarMedico(dataset.id);
        });

        $btnExcel.on("click", function(e){
            e.preventDefault();
            abrirExcel();
        });

        $tblMovimientos.on("click", ".btn-anularcomprobante", function (e) {
            var $btn = this,
                dataset = $btn.dataset;
            e.preventDefault();
            anularComprobante(dataset.id, dataset.cliente);
        });

        $tblMovimientos.on("click", ".btn-canjearcomprobante", function (e) {
            var $btn = this,
                dataset = $btn.dataset;
            e.preventDefault();
            objCanjearComprobante.preCanjearComprobante(dataset.id, dataset.cliente);
        });

        $tblMovimientos.on("click", ".btn-copiarcomprobante", function (e) {
            var $btn = this,
                dataset = $btn.dataset;
            e.preventDefault();
            copiarComprobante(dataset.id, dataset.comprobante);
        });

        $tblMovimientos.on("click", ".btn-enviarsunat", function (e) {
            const $button = $(this);
            e.preventDefault();
            enviarSUNAT($button.data("id"), $button.data("comprobante"), $button);
        });
    };

    var TABLA_MOVIMIENTOS;
    var listarMovimientos = function(){
        var tmpHtml = $btnActualizar.html();
        $btnActualizar.prop("disabled", true);
        $btnActualizar.html("<span class='fa fa-spin fa-spinner'></span>");

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.controlador.php?op=listar_atenciones_general",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_fecha_inicio : $txtFechaInicio.val(),
                p_fecha_fin : $txtFechaFin.val(),
            },
            success: function(result){
                $btnActualizar.prop("disabled", false);
                $btnActualizar.html(tmpHtml);

                if (TABLA_MOVIMIENTOS){
                    TABLA_MOVIMIENTOS.destroy();
                    TABLA_MOVIMIENTOS = null;
                }
                $tblMovimientos.find("tbody").html(tplMovimientos(result.map(r=>{
                    return {
                        ...r,
                        debo_mostrar_enviar: Boolean((r.comprobante.charAt(0) === "F") && (r.cdr_descripcion != 'ENVIADO' && r.cdr_descripcion != 'RECHAZADO')) ? 1 : 0
                    }
                })));

                if (result.length){
                    TABLA_MOVIMIENTOS = $tblMovimientos.DataTable({
                        "ordering": true
                    });
                }
            },
            error: function (request) {
                $btnActualizar.prop("disabled", false);
                $btnActualizar.html(tmpHtml);

                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    this.TABLA_MOVIMIENTOS = function(){
        return TABLA_MOVIMIENTOS;
    }

    var anularMovimiento = function(id_atencion_medica,  cliente){
        var motivo_anulacion = prompt("Ingrese el motivo de la anulación de ATENCIÓN, estoy incluirá el comprobante, del cliente "+cliente);

         if (motivo_anulacion == null){
            return;
        } 

        if (motivo_anulacion == "") {
            toastr.error("Debe ingresar un motivo de anulación.");
            return;
        }

        $.ajax({ 
            url: VARS.URL_CONTROLADOR+"atencion.medica.controlador.php?op=anular_atencion",
            type: "post",
            dataType: 'json',
            data : {
                p_id_atencion_medica : id_atencion_medica,
                p_motivo_anulacion : motivo_anulacion
            },
            delay: 250,
            success: function(datos){
                if (datos.nota_credito && datos.nota_credito.length > 0){
                    toastr.success(datos.msj+"<br><strong>Nota de Crédito generada: "+datos.nota_credito+"</strong>");    
                }
                listarMovimientos();
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
        });
    };

    var initCambiarMedico = function(idAtencion){
        objVerAtencion.obtenerAtencion(idAtencion);
    };

    var abrirExcel = function(){
        window.open("../../../impresiones/atenciones.reporte.xls.php?fi="+$txtFechaInicio.val()+"&ff="+$txtFechaFin.val(),"_blank")
    };

    var anularComprobante = function(id_atencion_medica,  cliente){
        var motivo_anulacion = prompt("Ingrese el motivo de la anulación del COMPROBANTE, la atención seguriá activa y su comprobante será el TICKET. Cliente "+cliente);


        if (motivo_anulacion == null){
            return;
        } 

        if (motivo_anulacion == "") {
            toastr.error("Debe ingresar un motivo de anulación.");
            return;
        }

        $.ajax({ 
            url: VARS.URL_CONTROLADOR+"atencion.medica.controlador.php?op=anular_solo_comprobante_atencion",
            type: "post",
            dataType: 'json',
            data : {
                p_id_atencion_medica : id_atencion_medica,
                p_motivo_anulacion : motivo_anulacion
            },
            delay: 250,
            success: function(datos){
                toastr.success(datos.msj+"<br><strong>Nota de Crédito generada: "+datos.nota_credito+"</strong>");
                listarMovimientos();
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
        });
    };
        
    this.listarMovimientos = listarMovimientos;

    var copiarComprobante = function(id_documento_electronico, comprobante){
        if (!confirm("¿Desea copiar el comprobante "+comprobante+"?")){
            return;
        }

        var observaciones = prompt("Ingrese la nueva observación agregada a este comprobante. (Recomendado). Comprobante: "+comprobante);

        if (observaciones == null){
            return;
        } 

        $.ajax({ 
            url: VARS.URL_CONTROLADOR+"documento.electronico.controlador.php?op=copiar_comprobante",
            type: "post",
            dataType: 'json',
            data : {
                p_id_documento_electronico : id_documento_electronico,
                p_observaciones : observaciones.trim()
            },
            delay: 250,
            success: function(datos){
                toastr.success(datos.msj+"<br><strong>Comprobante copiado</strong>");
                window.open("../../../impresiones/ticket.comprobante.php?id="+datos.id_documento_electronico,"_blank");
                //listarMovimientos();
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
        });
    };

    const enviarSUNAT = function(id_documento_electronico, numero_comprobante, $btnEnviar){
        if (!confirm(`¿Desea enviar el comprobante ${numero_comprobante} a SUNAT?`)){
            return;
        }
        
        const fnActualizarResultado = (resultado)=>{
            const { registro } = resultado;
            const $tdCdr = $btnEnviar.parents("tr").find(".cdrestadodescripcion");
            let cdr_estado_color, cdr_estado_descripcion;

            if (registro.cdr_estado === ""){
                cdr_estado_color = "info";
                cdr_estado_descripcion = "REENVIAR";
            } else if (registro.cdr_estado == 0){
                cdr_estado_color = "success";
                cdr_estado_descripcion = "ENVIADO";
                $btnEnviar.remove();
            } else if (registro.cdr_estado == -1){
                cdr_estado_color = "warning";
                cdr_estado_descripcion = "REVISAR";
            } else {
                cdr_estado_color = "danger";
                cdr_estado_descripcion = "RECHAZADO";
                $btnEnviar.remove();
                alert(`Comprobante rechazado: ${registro.cdr_descripcion}`);
            }

            $tdCdr.html(`<span class="badge bg-${cdr_estado_color}">${cdr_estado_descripcion}</span>`);
        };

        new EnviadorSUNAT({id_documento_electronico: id_documento_electronico, $btnEnviar: $btnEnviar})
                        .enviarSUNAT(fnActualizarResultado);
    };

    this.setDOM();
    this.setEventos();
    this.getTemplates();
    
    return this;
};

$(document).ready(function(){
    objGestionAtenciones = new GestionAtenciones(); 
});



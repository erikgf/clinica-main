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
                $tblMovimientos.find("tbody").html(tplMovimientos(result));

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

    this.setDOM();
    this.setEventos();
    this.getTemplates();
    
    return this;
};

$(document).ready(function(){
    objGestionAtenciones = new GestionAtenciones(); 
});



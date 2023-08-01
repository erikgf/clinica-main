var GestionAtenciones = function() {
    var $txtCaja,
        $txtFechaInicio,
        $txtFechaFin,
        $tblMovimientos;

    var tplMovimientos;
    var $btnActualizar;
    var KEY_LS_CAJA = "dmi_caja_seleccionada";

    this.getTemplates = function(){

        var template = _ES_ID_ROL_SUPERVISOR == "1" ? "template.movimientos.supervisor.php" : "template.movimientos.php";

        $.get(template, function(result, state){
            if (state == "success"){
                tplMovimientos = Handlebars.compile(result);
                listarMovimientos();
            }
        });
    };

    this.setDOM = function(){
        $txtCaja = $("#txt-caja");
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
        
    };
    
    this.setEventos = function(){
        $btnActualizar.on("click", function(e){
            listarMovimientos();
        });

        $txtCaja.on("change", function(e){
            e.preventDefault();
            localStorage.setItem(KEY_LS_CAJA, this.value);
            listarMovimientos();
        });

        $tblMovimientos.on("click", ".btn-anularmovimiento", function (e) {
            var $btn = this,
                dataset = $btn.dataset;
            e.preventDefault();
            anularMovimiento(dataset.id, dataset.cliente);
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
            url : VARS.URL_CONTROLADOR+"caja.controlador.php?op=listar_movimientos_general",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_fecha_inicio : $txtFechaInicio.val(),
                p_fecha_fin : $txtFechaFin.val(),
                p_id_caja : $txtCaja.val(),
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

    this.initCajas = function(){
        var self = this;
        $.ajax({ 
            url: VARS.URL_CONTROLADOR+"caja.controlador.php?op=obtener_cajas",
            type: "post",
            dataType: 'json',
            delay: 250,
            success: function(datos){
                var html = `<option value="" selected>Todas las cajas</option>`;
                for (let index = 0; index < datos.length; index++) {
                    const o = datos[index];
                    html += `<option value="${o.id_caja}">${o.descripcion}</option>`;
                }

                $("#txt-caja").html(html);

                self.setDOM();
                self.setEventos();
                self.getTemplates();
            },
            error: function (request) {
                toastr.error(request.responseText);
                return
            },
            cache: true
        });
    };

    this.initCajas();

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

    return this;
};

$(document).ready(function(){
    objGestionAtenciones = new GestionAtenciones(); 
});



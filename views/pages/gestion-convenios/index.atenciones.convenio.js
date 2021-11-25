var AtencionesConvenio = function() {
    var $tblAtencionesConvenio,
        $tbdAtencionesConvenio,
        $txtFechaInicio,
        $txtFechaFin,
        $btnActualizar;

    var $overlayTabla;

    var tplAtencionesConvenio;
    var self = this;
    var TR_FILA = null;
    var HACE_CUANTOS_DIAS = 7;

    this.getTemplates = function(){
        var $reqAtencionesConvenio =  $.get("template.atencionesconvenio.php");

        $.when($reqAtencionesConvenio)
            .done(function(resAtencionesConvenio){
                tplAtencionesConvenio = Handlebars.compile(resAtencionesConvenio);
                self.listar();
            })
            .fail(function(error){
                console.error(error);
            });
    };

    this.setDOM = function(){
        $tblAtencionesConvenio = $("#tbl-atencionesconvenio");
        $tbdAtencionesConvenio  = $("#tbd-atencionesconvenio");

        $txtFechaInicio = $("#txt-atencionesconvenio-fechainicio");
        $txtFechaFin = $("#txt-atencionesconvenio-fechafin");

        $overlayTabla = $("#overlay-tbl-atencionesconvenio");
        $btnActualizar  =  $("#btn-actualizar-atencionesconvenio");

        var hoy = new Date();
        var haceDias = new Date(hoy.getTime());
        haceDias.setDate(hoy.getDate() - HACE_CUANTOS_DIAS);
        Util.setFecha($txtFechaInicio, haceDias);
        Util.setFecha($txtFechaFin, hoy);
    };
    
    this.setEventos = function(){
        $btnActualizar.on("click", function(e){
            e.preventDefault();
            self.listar();
        });

        $tbdAtencionesConvenio.on("click", ".btn-crearcomprobante", function (e) {
            e.preventDefault();
            self.crearComprobante(this.dataset.numeroticket);
        });
    };      

    TABLA_ATENCION_CONVENIOS  = null;
    this.listar = function(){
        $btnActualizar.prop("disabled", true);
        $overlayTabla.show();

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.controlador.php?op=listar_atenciones_convenio",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_fecha_inicio: $txtFechaInicio.val(),
                p_fecha_fin : $txtFechaFin.val()
            },
            success: function(result){
                $btnActualizar.prop("disabled", false);
                $overlayTabla.hide();

                if (TABLA_ATENCION_CONVENIOS){
                    TABLA_ATENCION_CONVENIOS.destroy();
                }

                $tbdAtencionesConvenio.html(tplAtencionesConvenio(result));
                TABLA_ATENCION_CONVENIOS = $tblAtencionesConvenio.DataTable({
                    "order": [[ 1, "desc" ]],
                    "pageLength": 25,
                    "columns": [
                            { "width": "75px" },
                            { "width": "125px" },
                            { "width": "125px" },
                            null,
                            null,
                            { "width": "115px" },
                            { "width": "115px" },
                            { "width": "115px" }
                          ]
                });

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


    this.crearComprobante = function(numeroTicket){
        $('a[href="#blk-tab-facturacionconvenio"]').tab('show');
        objFacturacionConvenio.crearComprobante(numeroTicket);
    };

    this.setDOM();
    this.setEventos();
    this.getTemplates();

    //cargarTipoAfectacion();
    //cargarCategoriaServicio();

    return this;
};


$(document).ready(function(){
    objAtencionesConvenio = new AtencionesConvenio();
});



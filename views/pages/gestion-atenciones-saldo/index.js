var GestionAtencionesSaldo = function() {
    var $txtFechaInicio,
        $txtFechaFin,
        $btnExcel,
        $btnActualizar,
        $tblMovimientos;

    var tplMovimientos, tplPopoverPagos;

    this.getTemplates = function(){

        var $templateMovimientos = $.get("template.movimientos.php");
        var $templatePopoverPagos = $.get("template.popover.pagos.php");

        $.when($templateMovimientos, $templatePopoverPagos)
            .done(function(resMovimientos, resPopoverPagos){
                if (resMovimientos[1] == "success"){
                    tplMovimientos = Handlebars.compile(resMovimientos[0]);
                    listarMovimientos();
                }

                if (resPopoverPagos[1] == "success"){
                    tplPopoverPagos = Handlebars.compile(resPopoverPagos[0]);
                }
            })
            .fail(function(e1,e2){
                console.error(e1,e2);
            });
    };


    this.setDOM = function(){
        var hoy = new Date();
        //haceSieteDias = new Date();
        $txtFechaInicio = $("#txt-fechainicio");
        $txtFechaFin = $("#txt-fechafin");
        $btnActualizar = $("#btn-actualizarmovimientos");
        $txtFiltroSaldo = $("#txt-filtrosaldo");
    
        Util.setFecha($txtFechaInicio, hoy);
        Util.setFecha($txtFechaFin, hoy);

        $tblMovimientos = $("#tbl-cajamovimientos");
        $btnExcel = $("#btn-excel");
    };
    
    this.setEventos = function(){
        $btnActualizar.on("click", function(e){
            listarMovimientos();
        });

        $txtFiltroSaldo.on("change", function(){
            listarMovimientos();
        });

        $btnExcel.on("click", function(e){
            e.preventDefault();
            abrirExcel();
        });

        $tblMovimientos.on("click", ".onmostrar-pagos", function(e) {
            e.preventDefault();
            mostrarPagos(this);
       });
    };

    var TABLA_MOVIMIENTOS;
    var listarMovimientos = function(){
        var tmpHtml = $btnActualizar.html();
        $btnActualizar.prop("disabled", true);
        $btnActualizar.html("<span class='fa fa-spin fa-spinner'></span>");

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.controlador.php?op=listar_atenciones_con_saldo",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_fecha_inicio : $txtFechaInicio.val(),
                p_fecha_fin : $txtFechaFin.val(),
                p_tipo_filtro: $txtFiltroSaldo.val()
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
                        "ordering": true,
                        "columns": [
                            { "width": "75px" },
                            { "width": "100px" },
                            null,
                            { "width": "180px" },
                            { "width": "150px" },
                            { "width": "150px" },
                            { "width": "150px" },
                            { "width": "150px" },
                          ]
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

    var abrirExcel = function(){
        window.open("../../../impresiones/atenciones.reporte.xls.php?fi="+$txtFechaInicio.val()+"&ff="+$txtFechaFin.val(),"_blank")
    };

    var mostrarPagos = function(btn){
        var $btn = $(btn);

        if ($btn.data("popoveron") == "1"){
            $btn.data("popoveron", "0");
            $btn.popover("hide");
            $btn.html("<span class='fa fa-eye'></span>")
            return;
        }
        
        var idAtencionMedica = $btn.data("id");
        $btn.html("<span class='fa fa-spin fa-spinner'></span>");

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"atencion.medica.controlador.php?op=mostrar_pagos_de_atencion",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_atencion_medica : idAtencionMedica
            },
            success: function(result){
                if ($btn.data("popoveron") == "0"){
                    $btn.data("popoveron", "1");
                }

                $btn[0].dataset.content = tplPopoverPagos(result);
                $btn.popover('show');
                $btn.html("<span class='fa fa-eye-slash'></span>");
            },
            error: function (request) {
                $btn.data("popoveron", "0");
                $btn.popover("hide");
                $btn.html("<span class='fa fa-eye'></span>")
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
      
    };
        
    this.setDOM();
    this.setEventos();
    this.getTemplates();
    
    return this;
};

$(document).ready(function(){
    objGestionAtencionesSaldo = new GestionAtencionesSaldo(); 
});



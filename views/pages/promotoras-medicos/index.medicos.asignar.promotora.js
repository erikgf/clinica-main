var MedicosAsignarPromotora = function() {
    var $tbdMedicosParaAsignar, 
        $tblMedicosParaAsignar,
        $txtPromotoraAsignar,
        $tbdMedicosPromotoraAsignar, 
        $tblMedicosPromotoraAsignar;

    var $txtFechaInicio,
        $txtFechaFin,
        $btnImprimirPDF,
        $btnImprimirEXCEL;

    var $btnAsignar,
        $btnQuitar;

    var tplMedicos;

    this.getTemplates = function(){
        var $reqMedicos =  $.get("template.medicos.lista.para.promotoras.php"); 
        
        $.when($reqMedicos)
            .done(function(resMedicos){
                tplMedicos = Handlebars.compile(resMedicos);
                cargarMedicos();

            })
            .fail(function(e1){
                console.error(e1);
            });
    };

    this.setDOM = function(){    
        $tbdMedicosParaAsignar  = $("#tbd-medicosparasignar");
        $tblMedicosParaAsignar  = $("#tbl-medicosparasignar");

        $txtPromotoraAsignar = $("#txt-promotoraasignar");
        $tbdMedicosPromotoraAsignar = $("#tbd-medicospromotoraasignar"); 
        $tblMedicosPromotoraAsignar = $("#tbl-medicospromotoraasignar");

        $txtFechaInicio = $("#txt-fechainicio-asignarmedico");
        $txtFechaFin = $("#txt-fechafin-asignarmedico");
        $btnImprimirPDF = $("#btn-imprimir-asignarmedico-pdf");
        $btnImprimirEXCEL = $("#btn-imprimir-asignarmedico-excel");

        $btnAsignar =  $("#btn-asignarmedico");
        $btnQuitar =  $("#btn-quitarmedico");

        /*
        $btnActualizarPromotoras  =  $("#btn-actualizar-promotoras");
        $tblMedicos =  $("#tbl-medicos");
        $tbbMedicos =  $("#tbd-medicos");
        $btnActualizarMedicos =  $("#btn-actualizar-medicos");
        $tblEspecialidades = $("#tbl-especialidades");
        $tbbEspecialidades  = $("#tbd-especialidades");
        $btnActualizarEspecialidades =  $("#btn-actualizar-especialidades");
        */
        
    };
    
    this.setEventos = function(){

        $tbdMedicosParaAsignar.on( 'click', 'tr', function () {
            $(this).toggleClass('selected');
            verificarMedicosCantidadAsignar();
        });

        $tbdMedicosPromotoraAsignar.on( 'click', 'tr', function () {
            $(this).toggleClass('selected');
            verificarMedicosCantidadQuitar();
        });

        $btnAsignar.on("click", function(e){
            asignarMedicos();
        });
        

        $btnQuitar.on("click", function(e){
            quitarMedicos();
        });

        $txtPromotoraAsignar.on("change", function(e){
            e.preventDefault();
            cargarMedicosPromotora();
        });

        $btnImprimirPDF.on("click", function(e){
            e.preventDefault();
            window.open("../../../impresiones/medicos.promotoras.php?fi="+$txtFechaInicio.val()+"&ff="+$txtFechaFin.val()+"&idp="+$txtPromotoraAsignar.val(),"_blank")
        });
        
        $btnImprimirEXCEL.on("click", function(e){
            e.preventDefault();
            window.open("../../../impresiones/medicos.promotoras.xls.php?fi="+$txtFechaInicio.val()+"&ff="+$txtFechaFin.val()+"&idp="+$txtPromotoraAsignar.val(),"_blank")
        });
    };

    var cargarPromotoras = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"promotora.controlador.php?op=listar",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                var html = ``;

                result.forEach(promotora => {
                    html += `<option value="${promotora.id}">${promotora.descripcion}</option>`;
                });

                $txtPromotoraAsignar.html(html);
                cargarMedicosPromotora();
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    TABLA_MEDICOS_ASIGNAR = null;
    var cargarMedicos = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=listar_validos_promotoras",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {},
            success: function(result){
                if (TABLA_MEDICOS_ASIGNAR){
                    TABLA_MEDICOS_ASIGNAR.destroy();
                }

                $tbdMedicosParaAsignar.html(tplMedicos(result));
                TABLA_MEDICOS_ASIGNAR = $tblMedicosParaAsignar.DataTable({
                    "ordering": true,
                    "order": [],
                    "pageLength": 10
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

    TABLA_MEDICOS_ASIGNADOS = null;
    var cargarMedicosPromotora = function(){
        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"medico.controlador.php?op=listar_medicos_x_promotora",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_promotora : $txtPromotoraAsignar.val()
            },
            success: function(result){
                if (TABLA_MEDICOS_ASIGNADOS){
                    TABLA_MEDICOS_ASIGNADOS.destroy();
                }

                $tbdMedicosPromotoraAsignar.html(tplMedicos(result));
                if (result.length){
                    TABLA_MEDICOS_ASIGNADOS = $tblMedicosPromotoraAsignar.DataTable({
                        "ordering": true,
                        "order": [],
                        "pageLength": 10
                    });
                }
            },
            error: function (request) {
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };

    var verificarMedicosCantidadAsignar = function(){
        var cantidadSeleccionadas = parseInt(TABLA_MEDICOS_ASIGNAR.rows('.selected').data().length);
        if (cantidadSeleccionadas > 0){
            $btnAsignar.prop("disabled", false);
            return;
        }

        $btnAsignar.prop("disabled", true);
    };  

    var verificarMedicosCantidadQuitar = function(){
        var cantidadSeleccionadas = parseInt(TABLA_MEDICOS_ASIGNADOS.rows('.selected').data().length);
        if (cantidadSeleccionadas > 0){
            $btnQuitar.prop("disabled", false);
            return;
        }

        $btnQuitar.prop("disabled", true);
    };

    var asignarMedicos = function(){
        var idPromotora = $txtPromotoraAsignar.val();

        if (idPromotora == ""){
            alert("No hay PROMOTORA seleccionada.");
            return;
        }

        var arrIdMedicos = obtenerIdMedicosDesdeTabla($tblMedicosParaAsignar);

        $.ajax({ 
            url: VARS.URL_CONTROLADOR+"promotora.controlador.php?op=asignar_medicos_promotora",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: {
               p_id_promotora : idPromotora,
               p_arreglo_id_medicos : JSON.stringify(arrIdMedicos)
            },
            success: function(datos){
                toastr.success(datos.msj);
                $tbdMedicosParaAsignar.find("tr").removeClass("selected");

                verificarMedicosCantidadAsignar();
                cargarMedicosPromotora();
            },
            error: function (request) {
                toastr.error(request.responseText);
                return
            },
            cache: true
        });
    };

    var quitarMedicos = function(){
        var arrIdMedicos = obtenerIdMedicosDesdeTabla($tbdMedicosPromotoraAsignar);

        $.ajax({ 
            url: VARS.URL_CONTROLADOR+"promotora.controlador.php?op=quitar_medicos_promotora",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: {
               p_arreglo_id_medicos : JSON.stringify(arrIdMedicos)
            },
            success: function(datos){
                toastr.success(datos.msj);
                $tbdMedicosPromotoraAsignar.find("tr").removeClass("selected");

                verificarMedicosCantidadQuitar();
                cargarMedicosPromotora();
            },
            error: function (request) {
                toastr.error(request.responseText);
                return
            },
            cache: true
        });
    };


    var obtenerIdMedicosDesdeTabla = function($tbl){
        var arregloIdMedicos = [];
        $tbl.find("tr.selected").each(function(i,o){
            arregloIdMedicos.push(o.dataset.id);
        });

        return arregloIdMedicos;
    };

    this.getTemplates();
    this.setDOM();
    this.setEventos();

    var hoy = new Date();
    Util.setFecha($txtFechaInicio, hoy);
    Util.setFecha($txtFechaFin, hoy);
    hoy = null;

    cargarPromotoras();
    
    return this;
};

$(document).ready(function(){
    objMedicosAsignarPromotora = new MedicosAsignarPromotora();
});



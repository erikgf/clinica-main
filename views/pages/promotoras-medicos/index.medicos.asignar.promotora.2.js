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

    var $txtMes, $txtAnio, 
        $btnCalcular, $btnImprimir; 

    var $btnAsignar,
        $btnQuitar;

    var _tplMedicos;

    this.getTemplates = function(){
        var $reqMedicos =  $.get("template.medicos.lista.para.promotoras.php"); 
        
        $.when($reqMedicos)
            .done(function(resMedicos){
                _tplMedicos = Handlebars.compile(resMedicos);

                cargarMedicos();
                cargarPromotoras();

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

        $txtMes = $("#txt-mes-asignarmedico");
        $txtAnio = $("#txt-anio-asignarmedico");

        $btnCalcular = $("#btn-calcular-asignarmedico");
        $btnImprimir = $("#btn-imprimir-asignarmedico");
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

        $btnImprimirPDF.on("click", function(){
            //window.open("../../../impresiones/medicos.promotoras.php?fi="+$txtFechaInicio.val()+"&ff="+$txtFechaFin.val()+"&idp="+$txtPromotoraAsignar.val(),"_blank")
            window.open("../../../impresiones/medicos.promotoras.php?m="+$txtMes.val()+"&a="+$txtAnio.val()+"&idp="+$txtPromotoraAsignar.val(),"_blank")
        });
        
        $btnImprimirEXCEL.on("click", function(){
            window.open("../../../impresiones/medicos.promotoras.xls.php?m="+$txtMes.val()+"&a="+$txtAnio.val()+"&idp="+$txtPromotoraAsignar.val(),"_blank")
            //window.open("../../../impresiones/medicos.promotoras.xls.php?fi="+$txtFechaInicio.val()+"&ff="+$txtFechaFin.val()+"&idp="+$txtPromotoraAsignar.val(),"_blank")
        });

        $btnCalcular.on("click", () => {
            this.calcular();
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

                $tbdMedicosParaAsignar.html(_tplMedicos(result));
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

                $tbdMedicosPromotoraAsignar.html(_tplMedicos(result));
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

    this.renderMesesAños = async () => {
        const meses = Util.getMeses();
        const años = Util.getAños(2023);

        const resSelect = await $.get("template.select.hbs");
        const templateSelect = Handlebars.compile(resSelect);

        $txtMes.html(templateSelect(meses));
        $txtAnio.html(templateSelect(años.map( año => ({ id: año, descripcion: año }) )));

        const date = new Date();
        let mesActualBase = date.getMonth();
        let mesActual = mesActualBase === 0 ? 12 : mesActualBase;
        const anioActual = mesActualBase === 0 ? date.getFullYear() - 1 : date.getFullYear();
        mesActual = mesActual < 10 ? `0${mesActual}` : mesActual;

        $txtMes.val(mesActual);
        $txtAnio.val(anioActual);
    };


    this.calcular = async () => {
        const mes = $txtMes.val();
        const anio = $txtAnio.val();

        $btnCalcular.prop("disabled" ,true);

        try {
            const data = await $.ajax({ 
                url: VARS.URL_CONTROLADOR+"liquidacion.controlador.php?op=calcular",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: {
                   p_id_promotora : $txtPromotoraAsignar.val(),
                   p_mes: mes,
                   p_anio: anio
                },
                cache: true
            });

            if (data){
                toastr.success("Liquidaciones registradas correctamente.");
                return;
            }

            toastr.error("No se ha generado liquidaciones.");
        } catch (error) {
            toastr.error(error.responseText);
        } finally {
            $btnCalcular.prop("disabled", false);
        }
        
    };

    this.getTemplates();
    this.setDOM();
    this.setEventos();
    this.renderMesesAños();
    /*
    var hoy = new Date();
    Util.setFecha($txtFechaInicio, hoy);
    Util.setFecha($txtFechaFin, hoy);
    hoy = null;
    */
    
    return this;
};

$(document).ready(function(){
    objMedicosAsignarPromotora = new MedicosAsignarPromotora();
});



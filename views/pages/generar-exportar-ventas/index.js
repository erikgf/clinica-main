var GenerarExportarVentas = function() {
    var $txtFechaInicio,
        $txtFechaFin,
        $btnGenerar,
        $frm;

    var cadenaSisteCont = "C:/Siscontab/ImportacionesVentas/";
    var RUC_DPIROSAS = "20480718560";
    
    this.setDOM = function(){

        $txtFechaInicio = $("#txt-fechainicio");
        $txtFechaFin = $("#txt-fechafin");
        $btnGenerar = $("#btn-generar");

        $frm  = $("form");
    };  
    
    this.setEventos = function(){

        $txtFechaInicio.on("change", function(e){
            colocarNombreArchivo();
        });

        $txtFechaFin.on("change", function(e){
            colocarNombreArchivo();
        });

        $btnGenerar.on("click", function(e){
            e.preventDefault();
            generarArchivoTxt();
        });
    };

    var generarArchivoTxt = function(){
        if (!Util.validarFormulario($frm)){
            return;
        };

        var htmlBtn = $btnGenerar.html();
        $btnGenerar.html('<i class="fa fa-spin fa-spinner"></i>');

        $.ajax({ 
            url: VARS.URL_CONTROLADOR+"generar.ventas.archivo.php",
            type: "post",
            dataType: 'json',
            delay: 250,
            data:  new FormData($frm[0]),
            contentType: false,
            cache: false,
            processData:false,
            success: function(datos){
                document.getElementById("txt-archivo").value = "";
                $("#blk-archivogenerado").show();
                $("#lbl-nombrearchivogenerado").attr("href", datos.url_archivo);
                $("#lbl-nombrearchivogenerado").html(datos.nombre_archivo);

                downloadFile(datos.url_archivo);
                $btnGenerar.html(htmlBtn);
            },
            error: function (request) {
                $btnGenerar.html(htmlBtn);
                toastr.error(request.responseText);
                return
            }
        });
    };

    var  downloadFile = function(filePath){
        var link=document.createElement('a');
        link.href = filePath;
        link.download = filePath.substr(filePath.lastIndexOf('/') + 1);
        link.click();
    }
    
    var colocarNombreArchivo = function(){
        var aux, mesAnioInicio = "", mesAnioFin = "";
        
        aux = $txtFechaInicio.val().split("-");
        if (aux.length >= 3){
            mesAnioInicio = aux[1]+aux[0];
        }
        
        aux = $txtFechaFin.val().split("-");
        if (aux.length >= 3){
            mesAnioFin = aux[1]+aux[0];
        }

        if ((mesAnioFin == mesAnioInicio) && (mesAnioFin != "" && mesAnioInicio != "")){
            $("#lbl-direccionarchivoventas").html(cadenaSisteCont + mesAnioInicio + RUC_DPIROSAS + ".txt");
        } else {
            $("#lbl-direccionarchivoventas").html(cadenaSisteCont);
        }
    };

    var setFechasHoy = function () {
        var hoy = new Date();
        Util.setFecha($txtFechaInicio, hoy);
        Util.setFecha($txtFechaFin, hoy);     
    };

    this.init = function(){
        this.setDOM();
        this.setEventos();
        
        setFechasHoy();

        return this;
    };

    return this.init();
};

$(document).ready(function(){
    objGenerarExportarVentas = new GenerarExportarVentas();
});



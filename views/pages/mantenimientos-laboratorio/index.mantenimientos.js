//Areas = CategoriaServicio
var Mantenimientos = function() {
    var clasesMantenimiento = ["Unidad","Metodo","Muestra","Seccion","Abreviatura"],
        nombresTemplates = ["unidades","metodos","muestras","secciones","abreviatura"];

    var $tbl = [],
        $tbb = [],
        $btnActualizar =[];

    var tpl = {};
    var numeroClasesMantenimiento = clasesMantenimiento.length;
    var TABLAS_AJUSTADAS = false;
    var obj  = {};

    this.getTemplates = function(){
        var $reqUnidades =  $.get("template.unidades.php"),
            $reqMetodos =  $.get("template.metodos.php"),
            $reqMuestras =  $.get("template.muestras.php"),
            $reqSecciones =  $.get("template.secciones.php"),
            $reqAbreviaturas =  $.get("template.abreviaturas.php");
        
        $.when($reqUnidades, $reqMetodos, $reqMuestras, $reqSecciones, $reqAbreviaturas)
            .done(function(resUnidades, resMetodos, resMuestras, resSecciones, resAbreviaturas){
                tpl[clasesMantenimiento[0]] = Handlebars.compile(resUnidades[0]);
                tpl[clasesMantenimiento[1]] = Handlebars.compile(resMetodos[0]);
                tpl[clasesMantenimiento[2]] = Handlebars.compile(resMuestras[0]);
                tpl[clasesMantenimiento[3]] = Handlebars.compile(resSecciones[0]);
                tpl[clasesMantenimiento[4]] = Handlebars.compile(resAbreviaturas[0]);

                for (var i = 0; i < numeroClasesMantenimiento; i++) {
                    var nombreClase = clasesMantenimiento[i];
                    window["obj"+nombreClase] = new window["Lab"+nombreClase](tpl[clasesMantenimiento[i]], $tbl[i], $tbb[i]);
                };
            })
            .fail(function(e1,e2,e3,e4,e5){
                console.error(e1,e2,e3,e4,e5);
            });
    };

    this.setDOM = function(){
        for (var i = 0; i < numeroClasesMantenimiento; i++) {
            var nombreDOM = clasesMantenimiento[i].toLowerCase();
            $tbl.push($("#tbl-"+nombreDOM));
            $tbb.push($("#tbd-"+nombreDOM));
            $btnActualizar.push($("#btn-actualizar-"+nombreDOM));
        };
    };
    
    this.setEventos = function(){
        for (var i = 0; i < numeroClasesMantenimiento; i++) {
            var nombreDOM = clasesMantenimiento[i].toLowerCase();

            $btnActualizar[i].on("click", function(e){
                e.preventDefault();
                window["obj"+this.dataset.nombreclase].cargar();
            });

            $("#btn-nuevo-"+nombreDOM).on("click", function(e){
                e.preventDefault();
                window["obj"+this.dataset.nombreclase].nuevoRegistro();
            });

            $tbb[i].on("click", ".btn-editar", function (e) {
                e.preventDefault();
                window["obj"+this.dataset.nombreclase].leer(this.dataset.id, $(this).parents("tr"));
            });

            $tbb[i].on("click", ".btn-eliminar", function (e) {
                e.preventDefault();
                window["obj"+this.dataset.nombreclase].anular(this.dataset.id, $(this).parents("tr"));
            });
        };


        $("#tab-mantenimientos").on("shown.bs.tab", function(e){
            if (!TABLAS_AJUSTADAS){
                TABLA_UNIDAD.columns.adjust();
                TABLA_ABREVIATURA.columns.adjust();
                TABLA_SECCION.columns.adjust();
                TABLA_MUESTRA.columns.adjust();
                TABLA_METODO.columns.adjust();
                TABLAS_AJUSTADAS = true;
            }
        });
    };

    this.getTemplates();
    this.setDOM();
    this.setEventos();
    return this;
};


$(document).ready(function(){
    objMantenimientos = new Mantenimientos();
});



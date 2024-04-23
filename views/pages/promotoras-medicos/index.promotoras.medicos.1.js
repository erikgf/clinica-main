//Areas = CategoriaServicio
var PromotorasMedicos = function() {
    var $tblPromotoras,
        $tbbPromotoras,
        $btnActualizarPromotoras,
        $tblMedicos,
        $tbbMedicos,
        $btnActualizarMedicos,
        $tblAreas,
        $tbbAreas,
        $btnActualizarAreas;

    var tplPromotoras, 
        tplMedicos,
        tplAreas;

    this.getTemplates = function(){
        var $reqPromotoras =  $.get("template.promotoras.php"),
            $reqMedicos =  $.get("template.medicos.php"),
            $reqAreas =  $.get("template.areas.php");
        
        $.when($reqPromotoras, $reqMedicos, $reqAreas)
            .done(function(resPromotoras, resMedicos, resAreas){
                tplPromotoras = Handlebars.compile(resPromotoras[0]);
                tplMedicos = Handlebars.compile(resMedicos[0]);
                tplAreas = Handlebars.compile(resAreas[0]);

                objArea = new Area(tplAreas, $tblAreas, $tbbAreas);
                objPromotora =  new Promotora(tplPromotoras, $tblPromotoras, $tbbPromotoras);
                objMedico = new Medico(tplMedicos, $tblMedicos, $tbbMedicos);
                new MedicoAprobar({id: "#blk-medicos-aprobar"});
            })
            .fail(function(e1,e2, e3){
                console.error(e1,e2, e3);
            });
    };

    this.setDOM = function(){
        $tblPromotoras = $("#tbl-promotoras");
        $tbbPromotoras  = $("#tbd-promotoras");
        $btnActualizarPromotoras  =  $("#btn-actualizar-promotoras");
        $tblMedicos =  $("#tbl-medicos");
        $tbbMedicos =  $("#tbd-medicos");
        $btnActualizarMedicos =  $("#btn-actualizar-medicos");
        $tblAreas = $("#tbl-areas");
        $tbbAreas  = $("#tbd-areas");
        $btnActualizarAreas =  $("#btn-actualizar-areas");
        
    };
    
    this.setEventos = function(){
        $btnActualizarPromotoras.on("click", function(e){
            e.preventDefault();
            objPromotora.cargar();
        });

        $btnActualizarMedicos.on("click", function(e){
            e.preventDefault();
            objMedico.cargar();
        });

        $btnActualizarAreas.on("click", function(e){
            e.preventDefault();
            objArea.cargar();
        });

        $("#btn-nuevo-medicos").on("click", function(e){
            e.preventDefault();
            objMedico.nuevoRegistro();
        });

        $tbbMedicos.on("click", ".btn-editar", function (e) {
            e.preventDefault();
            objMedico.leer(this.dataset.id, $(this).parents("tr"));
        });

        $tbbMedicos.on("click", ".btn-eliminar", function (e) {
            e.preventDefault();
            objMedico.anular(this.dataset.id, $(this).parents("tr"));
        });


        $("#btn-nuevo-promotoras").on("click", function(e){
            e.preventDefault();
            objPromotora.nuevoRegistro();
        });


        $tbbPromotoras.on("click", ".btn-editar", function (e) {
            e.preventDefault();
            objPromotora.leer(this.dataset.id);
        });

        $tbbPromotoras.on("click", ".btn-eliminar", function (e) {
            e.preventDefault();
            objPromotora.anular(this.dataset.id);
        });

        $("#btn-nuevo-areas").on("click", function(e){
            e.preventDefault();
            objArea.nuevoRegistro();
        });

        $tbbAreas.on("click", ".btn-editar", function (e) {
            e.preventDefault();
            objArea.leer(this.dataset.id);
        });

        $tbbAreas.on("click", ".btn-eliminar", function (e) {
            e.preventDefault();
            objArea.anular(this.dataset.id);
        });

    };

    this.getTemplates();
    this.setDOM();
    this.setEventos();
    return this;
};


$(document).ready(function(){
    objPromotorasMedicos = new PromotorasMedicos();
});



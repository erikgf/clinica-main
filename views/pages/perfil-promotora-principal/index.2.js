var pMantenimientoMedicosActivar, pMantenimientoMedicos;
const PerfilPromotoraPrincipal = function (){
    const TEMPLATE_NAME_SELECT = "./template.select.hbs";
    this.template = null;
    
    this.init = () => {
        this.requestTemplates();

        new GenerarReportePromotora({id: "#blk-generar-reporte-promotora"});
        pMantenimientoMedicosActivar = new MantenimientoMedicosActivar({id: "#blk-medicos-pendientes"});
        pMantenimientoMedicos = new MantenimientoMedicos({id: "#blk-medicos-activos"});
    };

    this.requestTemplates = async () => {
        const resSelect = await $.get(TEMPLATE_NAME_SELECT);
        this.template = { 
            select: Handlebars.compile(resSelect),
        };

        this.cargarEspecialidades();
        this.cargarSedes();
    };

    this.cargarEspecialidades = async () => {
        const $txtEspecialidad = $(".especialidad");
        $txtEspecialidad.prop("disabled", true);

        try{
            const res = await $.ajax({
                url: VARS.URL_CONTROLADOR+"especialidad.controlador.php?op=listar",
                type: "post",
                dataType: 'json',
                delay: 5000,
            });

            $txtEspecialidad.html(this.template.select(res));
        } catch ( error ){
            console.error(error);
        } finally {
            $txtEspecialidad.prop("disabled", false);
        }
    };


    this.cargarSedes = async () => {
        const $txtSede = $(".sede");
        $txtSede.prop("disabled", true);

        try{
            const res = await $.ajax({
                url: VARS.URL_CONTROLADOR+"sede.controlador.php?op=listar",
                type: "post",
                dataType: 'json',
                delay: 5000,
            });

            $txtSede.html(this.template.select(res));
        } catch ( error ){
            console.error(error);
        } finally {
            $txtSede.prop("disabled", false);
        }
    };
    
    return this.init();
};

$(document).ready(function(){
    new PerfilPromotoraPrincipal();
});



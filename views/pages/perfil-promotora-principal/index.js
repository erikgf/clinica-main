
const PerfilPromotoraPrincipal = function (){
    this.init = () => {
        new GenerarReportePromotora({id: "#blk-generar-reporte-promotora"});
    };

    return this.init();
};

$(document).ready(function(){
    obj = new PerfilPromotoraPrincipal();
});



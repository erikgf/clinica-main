var Reportes = function() {
    var $txtAnio,
        $txtMes,
        $txtSede;

    this.setDOM = function(){
        $txtAnio = $("#txt-anio");
        $txtMes = $("#txt-mes");
        $txtSede = $("#txt-sede");
    };
    
    this.setEventos = function(){
        $("#btn-excel").on("click", function(e){
            e.preventDefault();
            imprimirExcel();
        });
    };

    var imprimirExcel = function(){
        var anio = $txtAnio.val(),
            mes = $txtMes.val(),
            sede = $txtSede.val();

        window.open("../../../impresiones/atenciones.sede.xls.php?a="+anio+"&m="+mes+"&sede="+sede);
    };

    this.setMeses = () =>{
        const mesActual = new Date().getMonth() + 1;
        const meses = [
            {id: 1, rotulo : "ENERO"},
            {id: 2, rotulo : "FEBRERO"},
            {id: 3, rotulo : "MARZO"},
            {id: 4, rotulo : "ABRIL"},
            {id: 5, rotulo : "MAYO"},
            {id: 6, rotulo : "JUNIO"},
            {id: 7, rotulo : "JULIO"},
            {id: 8, rotulo : "AGOSTO"},
            {id: 9, rotulo : "SETIEMBRE"},
            {id: 10, rotulo : "OCTUBRE"},
            {id: 11, rotulo : "NOVIEMBRE"},
            {id: 12, rotulo : "DICIEMBRE"},
        ];

        let $html ="";

        meses.forEach(o=>{
            $html += `<option ${o.id === mesActual ? 'selected' : ''} value="${o.id}">${o.rotulo}</option>`;
        });

        $txtMes.html($html);
    };

    this.setAños = () =>{
        const anios = [2019,2020,2021,2022,2023,2024,2025];
        const anioActual = new Date().getFullYear();
        let $html ="";

        anios.forEach(o=>{
            $html += `<option ${o === anioActual ? 'selected' : ''} value="${o}">${o}</option>`;
        });

        $txtAnio.html($html);
    };

    this.setDOM();
    this.setEventos();
    this.setMeses();
    this.setAños();

    return this;
};



$(document).ready(function(){
    objReportes = new Reportes();
});



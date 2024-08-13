var VARS = {
  URL_CONTROLADOR : "./../../../controlador/"
};

var Util = {
  validarFormulario : function($formularioValidar){
      var $inputs = Array.prototype.slice.call($formularioValidar.find(".form-control:required")),
          primerInputEnEstarErrado = null,
          camposSinCompletar = 0;

      for (let index = 0; index < $inputs.length; index++) {
          const e = $inputs[index];
          const classList = e.classList;

          if (e.value == null || e.value.trim() == ""){
              if (classList.contains("select2")){
                  $(e).next().find(".select2-selection--single").addClass(".is-invalid-select2");
              } else {
                  classList.add("is-invalid");
              }

              if (primerInputEnEstarErrado == null){
                  primerInputEnEstarErrado = e;
              }

              camposSinCompletar++;
              
          } else {
              if (classList.contains("select2")){
                  $(e).next().find(".select2-selection--single").removeClass(".is-invalid-select2");
              } else {
                  classList.remove("is-invalid");
              }
          }
      }

      if (primerInputEnEstarErrado){
          primerInputEnEstarErrado.focus();
      }

      return camposSinCompletar <= 0;
  },
  formatearFechaCorrectamente : function(fechaFormatoHumano){
    if (fechaFormatoHumano == ""){
      return "";
    }

    var arregloTemporal = fechaFormatoHumano.split("/");
    if (arregloTemporal.length != 3){
      return "";
    }

    return arregloTemporal[2]+"-"+arregloTemporal[1]+"-"+arregloTemporal[0];

  },
  setFecha : function($element, objDate){
    var mes = objDate.getMonth() + 1;
    mes = mes < 10 ? ("0"+ mes) : mes;

    var dia = objDate.getDate();
    dia = dia < 10 ? ("0"+ dia) : dia;

    var fecha  = objDate.getFullYear() + "-" + mes + "-" + dia;

    if ($element){
        $element.val(fecha);
    }

    return fecha;
  },  
  setHora : function ($element, objDate){ 
      var horas = objDate.getHours();
      horas = horas < 10 ? ("0"+ horas) : horas; 

      var minutos = objDate.getMinutes();
      minutos = minutos < 10 ? ("0"+ minutos) : minutos; 

      var segundos = objDate.getSeconds();
      segundos = segundos < 10 ? ("0"+ segundos) : segundos; 
      
      var hora = horas + ":" + minutos + ":" + segundos;
      if ($element){
        $element.val(hora);
      }

      return hora;
  },
  setFechaHora : function($element, objDate, agregarConectorDateTime = false){
    var fecha = this.setFecha(null, objDate);
    var hora = this.setHora(null, objDate);
    var fechaHora = fecha+(agregarConectorDateTime ? "T" : " ")+hora;

    if ($element){
      $element.val(fechaHora);
    }
    return fechaHora;
  },
  forceTwoDecimals : function(number, $element){
      let numberOfDecimals;
      if(number % 1 != 0){
          let str = number.toString();
          let splitAtDecimal = str.split('.');
          numberOfDecimals = splitAtDecimal[1].length;
          if(numberOfDecimals == 1){
              number = number.toString();
              number = number + "0";
          }
      }else{
          number = number.toString();
          number = number + ".00";
      }
      $element.html(number);
  },
  getMeses : function () {
    return [
      { id: '01', descripcion: 'ENERO' },
      { id: '02', descripcion: 'FEBRERO' },
      { id: '03', descripcion: 'MARZO' },
      { id: '04', descripcion: 'ABRIL' },
      { id: '05', descripcion: 'MAYO' },
      { id: '06', descripcion: 'JUNIO' },
      { id: '07', descripcion: 'JULIO' },
      { id: '08', descripcion: 'AGOSTO' },
      { id: '09', descripcion: 'SETIEMBRE' },
      { id: '10', descripcion: 'OCTUBRE' },
      { id: '11', descripcion: 'NOVIEMBRE' },
      { id: '12', descripcion: 'DICIEMBRE' },
    ];
  },
  getAños : function (desdeAño = -1, haciaAdelante = 0) {
    const año = new Date().getFullYear();
    const añosAtras = [];
    const añosAdelante = [];

    const haciaAtras = desdeAño == -1 ? 0 : (año - desdeAño);

    for (let index = 1; index <= haciaAtras; index++) {
      añosAtras.unshift(año - index);
    }

    for (let index = 1; index <= haciaAdelante; index++) {
      añosAdelante.push(año + index);
    }

    return [...añosAtras, año, ...añosAdelante];
  }
};

// Conclusión
(function() {
  /**
   * Ajuste decimal de un número.
   *
   * @param {String}  tipo  El tipo de ajuste.
   * @param {Number}  valor El numero.
   * @param {Integer} exp   El exponente (el logaritmo 10 del ajuste base).
   * @returns {Number} El valor ajustado.
   */
  function decimalAdjust(type, value, exp) {
    // Si el exp no está definido o es cero...
    if (typeof exp === 'undefined' || +exp === 0) {
      return Math[type](value);
    }
    value = +value;
    exp = +exp;
    // Si el valor no es un número o el exp no es un entero...
    if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
      return NaN;
    }
    // Shift
    value = value.toString().split('e');
    value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
    // Shift back
    value = value.toString().split('e');
    return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
  }

  // Decimal round
  if (!Math.round10) {
    Math.round10 = function(value, exp) {
      return decimalAdjust('round', value, exp);
    };
  }
  // Decimal floor
  if (!Math.floor10) {
    Math.floor10 = function(value, exp) {
      return decimalAdjust('floor', value, exp);
    };
  }
  // Decimal ceil
  if (!Math.ceil10) {
    Math.ceil10 = function(value, exp) {
      return decimalAdjust('ceil', value, exp);
    };
  }
})();

(function($) {
  $.fn.inputFilter = function(inputFilter) {
    return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
      if (inputFilter(this.value)) {
        this.oldValue = this.value;
        this.oldSelectionStart = this.selectionStart;
        this.oldSelectionEnd = this.selectionEnd;
      } else if (this.hasOwnProperty("oldValue")) {
        this.value = this.oldValue;
        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
      } else {
        this.value = "";
      }
    });
  };
}(jQuery));


class MonitoredVariable {
  constructor(initialValue) {
    this._innerValue = initialValue;
    this.beforeSet = (newValue, oldValue) => {};
    this.beforeChange = (newValue, oldValue) => {};
    this.afterChange = (newValue, oldValue) => {};
    this.afterSet = (newValue, oldValue) => {};
  }

  set val(newValue) {
    const oldValue = this._innerValue;
    // newValue, oldValue may be the same
    this.beforeSet(newValue, oldValue);
    if (oldValue !== newValue) {
      this.beforeChange(newValue, oldValue);
      this._innerValue = newValue;
      this.afterChange(newValue, oldValue);
    }
    // newValue, oldValue may be the same
    this.afterSet(newValue, oldValue);
  }

  get val() {
    return this._innerValue;
  }
}


$(document).ready(function(){
  $("input[type=money]").inputFilter(function(value) {
      return /^\d*\.?\d*$/.test(value);
  }); 
});
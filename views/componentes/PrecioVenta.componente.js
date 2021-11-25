var PrecioVentaComponente = function(data){
	var $txtPrecioVenta = data.$txtPrecioVenta ?? null;
	var $txtValorVenta = data.$txtValorVenta ?? null;
	var $txtIdTipoAfectacion = data.$txtIdTipoAfectacion ?? null;

	var actualizarValorVenta = function(){
        var valor = $txtPrecioVenta.val();
        if (valor == "" || parseFloat(valor) <= 0.00){
            valor = "0.00";
            $txtPrecioVenta.val(valor);
        }

		var tipoAfectacion = $txtIdTipoAfectacion ? $txtIdTipoAfectacion.val() : "10";

        $txtValorVenta.val(parseFloat(valor / (tipoAfectacion == "10" ? 1.18 : 1)).toFixed(4));    
    };

	this.evento = function(){
		if ($txtPrecioVenta){
			$txtPrecioVenta.on("change", function(){
				 actualizarValorVenta();
			});
		}

		if ($txtIdTipoAfectacion){
			$txtIdTipoAfectacion.on("change", function(){
				 actualizarValorVenta();
			});
		}
	};

	this.evento();
	return this;
};
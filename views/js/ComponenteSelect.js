var ComponenteSelect = function($select){
	this.$select = $select;

	this.render = function(data){
	    var $html = `<option value="">Seleccionar</option>`;
	    for (var i = data.length - 1; i >= 0; i--) {
	        let o = data[i];
	        $html += `<option value="${o.id}">${o.descripcion}</option>`;
	    };

	    this.$select.html($html);
	};

	return this;
};
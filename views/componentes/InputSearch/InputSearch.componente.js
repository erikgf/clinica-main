var InputSearch = function(data){
    this.id = null;
	this.$ = null;
    this.placeholder = null;
    this.registros = null;
    this.isOpen = false;
    this.isSearching = false;
    this.limitShowing = 6;

    const STRING_ID = "id";
    const STRING_TEXT = "text";

    this.onSelect = null;

    this.init = () => {
        this.id = data?.id ?? null;
        if (!Boolean(this.id)){
            return null;
        }

        this.$ = $("#"+this.id);
        this.placeholder =  data?.placeholder ?? "Buscar opción..."
        this.registros = data?.registros ?? [];
        this.onSelect = data?.onSelect ?? (() => {});

        this.render();
        this.setEventos();

        return this;
    };

    this.render = () => {
        this.$.addClass("___input-search");
        this.$.html(`
            <input type="text" class="form-control" placeholder="${this.placeholder}"/>   
            <div class="___input-search-results"></div>
        `);
    };

    this.setEventos = () => {
        this.$.on("keyup", "input", (e)=>{
            this.buscar(e.currentTarget.value);
        });

        this.$.on("focusout", ()=>{
            if (this.$.find("li:hover").length <= 0){
                this.cerrar();
            }
        });

        this.$.on("click", ".___input-search-results li", (e)=>{
            const $li = e.currentTarget;
            this.onSelect({
                [STRING_ID] : $li.dataset.id,
                [STRING_TEXT] : $li.innerHTML
            });
            //this.cerrar();
        });
    }

    this.updateRegistros = (_registros) => {
        this.registros = _registros;
        this.cerrar();
    }

    this.cerrar = () => {
        this.isOpen = false;
        this.$.find(".___input-search-results").empty();
        this.$.find("input").val("");
    };

	this.buscar = (stringBuscar) => {
        const lowStringBuscar = stringBuscar?.toLowerCase();
        const registrosMostrar = this.registros.filter( item => {
            return item[STRING_TEXT].toLowerCase().indexOf(lowStringBuscar) != -1
        }).slice(0, this.limitShowing);
        const $searching = this.$.find(".___input-search-results");
        if (registrosMostrar.length > 0){
            this.isOpen = true;
            $searching.html(registrosMostrar.map( item => (`<li data-id="${item[STRING_ID]}">${item[STRING_TEXT]}</li>`)).join(""));
            return;
        }

        this.isOpen = false;
        $searching.empty();
        
	};

	return this.init();
};
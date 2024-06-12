const Paginador = function(initData){
    this.$tbody = null;
    this.$paginador = null;
    this.$txtBuscador = null;
    this.template = null;
    this.keysParaBuscar = null;

    this.data = [];
    this._dataFiltrada = [];
    this._actualPage = -1;
    this._maxPages = 0;
    this.registrosPorPagina = 30;

    this.init = ({
        $tbody,
        $paginador,
        $txtBuscador,
        template,
        keysParaBuscar = []
    })=> {
        this.$tbody = $tbody;
        this.$paginador = $paginador;
        this.$txtBuscador = $txtBuscador;
        this.template = template;
        this.keysParaBuscar = keysParaBuscar;
    };

    this.start = (registros) => {
        this._data = registros;
        if (this.$txtBuscador && this.$txtBuscador.val()?.length > 0 && this.keysParaBuscar.length > 0){
            this.calcularDataFiltrada(this.$txtBuscador.val());
        } else {
            this._dataFiltrada = registros;
        }

        this.renderBloquePaginacion();
        this.renderPagina(0);

        this.setEventos();
    };


    this.setEventos = () => {
        this.$tbody && this.$tbody.off();
        this.$paginador && this.$paginador.off();
        this.$txtBuscador && this.$txtBuscador.off();

        this.$paginador.on("click", "li.page-item", (e) => {
            e.preventDefault();
            let { index } = e.currentTarget.dataset;
           
            if ( index  == "<"){
                if (this._actualPage <= 0){
                    return;
                }

                this.renderPagina(this._actualPage - 1);
                return;
            }

            if ( index  == ">"){
                if (this._actualPage >= this._maxPages - 1){
                    return;
                }
                this.renderPagina(this._actualPage + 1);
                return;
            }

            this.renderPagina(parseInt( index ));
        });

        if (this.$txtBuscador){
            this.$txtBuscador.on("keypress", (e)=> {
                if (e.keyCode === 13){
                    this.buscar(this.$txtBuscador.val());
                }
            });
        }
    }

    this.calcularDataFiltrada = (strBuscar) => {
        const strBuscarLower = strBuscar.toLowerCase();
        this._dataFiltrada = this._data.filter( registro => {
            let filtra = false;
            this.keysParaBuscar.forEach(key => {
                if (Boolean(registro[key])){
                    filtra = filtra || registro[key].toLowerCase().indexOf(strBuscarLower) != -1;
                }
                
            });

            return filtra;
        });
    }
    
    this.buscar = (strBuscar) => {
        this.calcularDataFiltrada(strBuscar);
        this.renderBloquePaginacion();
        this.renderPagina(0);
    };

    this.renderPagina = (actualPage) => {
        this._actualPage = actualPage;
        const actualCursorInPage = this._actualPage * this.registrosPorPagina;
        const dataPaginada = this._dataFiltrada.slice(actualCursorInPage, actualCursorInPage + this.registrosPorPagina);
        this.$tbody.html(this.template(dataPaginada));

        this.$paginador.find("li").removeClass("active");
        this.$paginador.find(`li[data-index=${this._actualPage}]`).addClass("active");
    };

    this.renderBloquePaginacion = () => {
        const cantidadTotal = this._dataFiltrada.length;
        this._maxPages  = Math.ceil(cantidadTotal / this.registrosPorPagina);

        if (this._maxPages <= 0 ){
            return;
        }

        let $html = "";
        for (let index = 0; index < this._maxPages; index++) {
            $html += `<li data-index="${index}" class="page-item"><a class="page-link" href="javascript:;">${index + 1}</a></li>`;
        }

        this.$paginador.html(`<ul class="pagination pagination-sm m-0 mb-3 float-right">
                        <li data-index="<" class="page-item"><a class="page-link" href="javascript:;">«</a></li>
                        ${$html}
                        <li data-index=">"class="page-item"><a class="page-link" href="javascript:;">»</a></li>
                </ul>`);
    };

    this.updateItem = ( id, objCambio ) => {
        const fnMapp = (item) => {
            if ( item.id === id ){
                return {
                    ...item,
                    ...objCambio
                }
            }
            
            return item;
        }

        this._data = this._data.map(fnMapp);
        this._dataFiltrada = this._data.map(fnMapp);
    };

    this.deleteItems = ( arrayId ) => {
        const fnFilter = (item) => {
            return !(arrayId.includes(item.id));
        };

        this._data = this._data.filter(fnFilter);
        this._dataFiltrada = this._dataFiltrada.filter(fnFilter);

        this.renderBloquePaginacion();
        this.renderPagina(this._actualPage);
    };

    this.getData = () => {
        return this._data;
    }

    return this.init(initData);
};
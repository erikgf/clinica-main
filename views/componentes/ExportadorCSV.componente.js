const ExportadorCSV = function(data){
    this.init = ({
        fileName = 'my_data.csv'
    })=> {
        this._fileName = fileName;
    };

    this.exportar = ({nombreColumnas = [], filas = []}) => {
        const fullData = [nombreColumnas].concat(filas);
        const encoding = "data:text/csv;charset=utf-8,";  
        const csvContent = fullData.map(e => e.join(";")).join("\n");
       // const encodedUri = encodeURI(csvContent);

        const link = document.createElement("a");
        link.setAttribute("href", encoding + escape(csvContent));
        link.setAttribute("download", this._fileName);
        document.body.appendChild(link); // Required for FF

        link.click();
    };

    return this.init(data);
};
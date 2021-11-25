var ConsultarDocumento = function(data){
    var self = this;
    this.buscandoNumeroDocumento = false;

    this.init = function(){
        this.$loader = data.$blkLoader ?? null;
        this.idTipoDoc = data.idTipoDoc ?? null;
        this.$txtNumeroDocumento = data.$txtNumeroDocumento ?? "";
    
        this.fnSuccessCallback = data.fnOK  ?? function(res){ console.log(res)};
        this.fnErrorCallBack = data.fnError ?? function(error){ console.error(error)};
        return this;
    }

    this.buscar = function(){
        if (this.buscandoNumeroDocumento){
            console.log("Buscando...");
            return;
        }

        if (this.idTipoDoc != "1" && this.idTipoDoc != "6"){
            console.log("nonn Tipo Documento...");
            return;
        }
        
        var numeroDocumento = this.$txtNumeroDocumento.val();
        var numeroDocumentoLength = numeroDocumento.length;
        if (numeroDocumentoLength != 8 && numeroDocumentoLength != 11){
            console.log("Non logintud valida...");
            return;
        }

        var $loader =this.$loader;

        this.buscandoNumeroDocumento = true;
        if ($loader) $loader.show();
            $.ajax({ 
                url: VARS.URL_CONTROLADOR+"documento.electronico.controlador.php?op=consultar_documento_cliente",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: {
                    p_numero_documento : numeroDocumento
                },
                success: function(res){
                    self.buscandoNumeroDocumentoCliente = false;
                    if ($loader){
                        $loader.removeClass("fa-spin fa-spinner").addClass("fa-check text-green");
                        setTimeout(function(){
                            $loader.removeClass("fa-check text-green").addClass("fa-spin fa-spinner");
                            $loader.hide();
                        },1500);
                    }

                    self.fnSuccessCallback(res);
                },
                error: function(error){
                    self.buscandoNumeroDocumentoCliente = false;
                    if ($loader) {
                        $loader.removeClass("fa-spin fa-spinner").addClass("fa-close text-red");
                        setTimeout(function(){
                            $loader.removeClass("fa-close text-red").addClass("fa-spin fa-spinner");
                            $loader.hide();
                        },1500);
                    }
                    $txtNumeroDocumento.select();
                    self.fnErrorCallBack(error.responseText);
                },
                cache: true
            });
    

    };


    return this.init();    
};
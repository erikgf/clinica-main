const EnviadorSUNAT = function(data){
    this.enviandoSUNAT = false;
    this.id_documento_electronico = data.id_documento_electronico;
    this.$btnEnviar = data.$btnEnviar;

    this.enviarSUNAT = function(fnPost = null){
        if (this.enviandoSUNAT){
            return;
        }
        this.enviandoSUNAT = true;
        const tempHTML = this.$btnEnviar.html();
        this.$btnEnviar.html("<p>Cargando....</p>");
    
        setTimeout(()=>{
           
            this.enviandoSUNAT = false;
            const resultado = {
                cdr_estado: 222,
                cdr_descripcion: "Comprobante XUZ, ha sido aceptado.",
                estado_anulado : 0
            };

            fnPost(resultado);
        }, 1000);

        $.ajax({ 
            url : VARS.URL_CONTROLADOR+"documento.electronico.controlador.php?op=enviar_sunat_x_id",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: {
                p_id_documento_electronico : this.id_documento_electronico
            },
            success: function(result){
                this.$btnEnviar.html(tempHTML);
                self.enviandoSUNAT = false;
                fnPost(result);
            },
            error: function (request) {
                this.$btnEnviar.html(tempHTML);
                self.enviandoSUNAT = false;
                toastr.error(request.responseText);
                return;
            },
            cache: true
            }
        );
    };


    return this;
};
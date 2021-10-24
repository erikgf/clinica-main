var ClsRecibo = function(dataInit){
    var self = this;
    var buscandoRecibo;
    var $btnBuscarRecibo,
        $txtBuscarRecibo;

    this.operacionAjax = "";

    var init = function(){
        buscandoRecibo = false;
        if (dataInit){
            $btnBuscarRecibo = dataInit.$btnBuscarRecibo;        
            $txtBuscarRecibo = dataInit.$txtBuscarRecibo;

            if (dataInit.tipo_movimiento == "ingreso"){
                self.operacionAjax = "obtener_atencion_medica_para_saldos";
            } else {
                self.operacionAjax = "obtener_atencion_medica_para_egreso";
            }
        }
        

        return self;
    };

    this.buscarReciboParaSaldos = function(fnOk, fnError){
        this.buscarRecibo($btnBuscarRecibo, $txtBuscarRecibo, fnOk, fnError)
    };

    this.buscarReciboParaSaldos = function(fnOk, fnError ){
        this.buscarRecibo($btnBuscarRecibo, $txtBuscarRecibo,  fnOk, fnError)
    };

    this.buscarRecibo = function(fnOk, fnError){
        $btnBuscarRecibo.prop("disabled", true);

        if (buscandoRecibo == true){
            return;
        }

        buscandoRecibo = true;

        $.ajax({ 
            url: VARS.URL_CONTROLADOR+"atencion.medica.controlador.php?op="+self.operacionAjax,
            type: "post",
            dataType: 'json',
            delay: 250,
            data: {
                p_numero_acto_medico : $txtBuscarRecibo.val()
            },
            success: function(res){
                buscandoRecibo = false;
                $btnBuscarRecibo.prop("disabled", false);
                $txtBuscarRecibo.val("");

                if (res == false){
                    toastr.error("Atención no encontrada.");
                    return;
                }

                fnOk(res);
            },
            error: function (res) {
                buscandoRecibo = false;
                $btnBuscarRecibo.prop("disabled", false);
                if (fnError){
                    fnError(res.responseText);
                    return;
                }

                console.error(res.responseText);
                return;
            },
            cache: true
        });
    };

    return init();
};
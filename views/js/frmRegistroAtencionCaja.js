
/*
Variable que permite inicializar la página a lo largo de toda la app,
todas las varaibles que representen a una página deben comenzar con el
formato frm + el nombred del archivo php en formato nombre objeto.


Al pasar por showContent, se iniotializará obligatoriamente con "init", ahi si es primera
vez que se carga, se marcará con initialized = 1 y se procederá a CREAR + EJECUTAR (al momento de CREAR
    debemos colocar solo las cosas que serán EJECUTADAS 1 VEZ, por ejemplo EVENTOS o carga de datos que 
    serán muy estáticos.).
Caso contrario, solo se EJECUTA,  lo que implica realizar acciones acorde al input ( hacer refresh, o cosas
    parecidas)

Queda pendiente la función de eliminación, ya que tendremos varias páginas que no estan en el front, q esten escondidas
pero que los eventos asociados seguirán activos, consumienod memoria, esto estaría genial en aplicaciones de pocas páginas,
pero este no es el caso.

Todas las funciones grandes deberia ser declaradas parte de la página para optimizar aun mas su acceso (en memopria) dentro del app.

*/
var frmRegistroAtencionCaja = {
    initialized : 0,
    init: function(){
        if (this.initialized == 0){
            this.initialized = 1;
            this.create();
            return;
        }

        this.execute();
    },
    execute: function(){
        let retrievedCaja = localStorage.getItem('caja');
        let paciente_info = localStorage.getItem('paciente');
        paciente_info = JSON.parse(paciente_info);
        let orders = localStorage.getItem('orders');
        orders = JSON.parse(orders);

        if(retrievedCaja!=undefined){
            retrievedCaja = JSON.parse(retrievedCaja)
            if(retrievedCaja=="Caja I - DPI I"){
                $("#caja-select").val('1');
            }else{
                
                $("#caja-select").val('2');
            }
        }

        $('#fecha-cancelar').val(paciente_info.fecha);
        $('#caja-name').html(paciente_info.nombres_completos);
        $('#caja-historia').html(paciente_info.numero_historia+' - '+ paciente_info.nombres_completos);
        $('#caja-medicorealizante').html(paciente_info.medico_realizante);
        $('#caja-medicoordenante').html(paciente_info.medico_ordenante);
        $('#caja-direccion').html(paciente_info.direccion);
        $('#caja-documento').html(paciente_info.numero_documento);
        $('#caja-observaciones').html(paciente_info.observaciones);
        $('#caja-orders').html(function(){
            var i,  text;
            for(i = 0;i<Object.keys(orders).length;i++){
                const o = orders[i];
                text += `<tr><td>${o.servicio}</td><td class='caja-price'>${o.price}</td></tr>`;
            }
            return text
        });

        let totalCost = 0
        $('.caja-price').each(function(){
            totalCost = parseFloat($( this ).html()) + totalCost
            parseFloat(totalCost).toFixed(2)
        })

        this.forceTwoDecimals(totalCost, $("#caja-total"));

        this.cajaResultadoTotal();

        new Scroller(this.$ctn, "#blk-facturando-a").createScroll();
    },
    create : function(){
        this.$ctn = $("#content-registro-atencion-caja");
        var self = this;
        var setEventos = function($ctn){
            $ctn.on("change", "#caja-select", function () {
                let selectedCaja = $(this).val()
                localStorage.setItem('caja', JSON.stringify(selectedCaja));
            });

            $ctn.on("keyup", "#caja-pago-efectivo", function () {
                var $cajaPago = $(this);

                if ($cajaPago.val() == ""){
                    $cajaPago.val("0.00");
                    $cajaPago.select();
                }
            });

            $ctn.on("keyup", "#caja-pago-deposito", function () {
                var $cajaPago = $(this);

                if ($cajaPago.val() == ""){
                    $cajaPago.val("0.00");
                    $cajaPago.select();
                }

                if(parseFloat($cajaPago.val()) > 0.00){
                    $('#deposito').removeClass("hide");
                }else{
                    $('#deposito').addClass("hide");
                }
            });

            $ctn.on("keyup", "#caja-pago-tarjeta", function () {
                var $cajaPago = $(this);

                if ($cajaPago.val() == ""){
                    $cajaPago.val("0.00");
                    $cajaPago.select();
                }

                if(parseFloat($cajaPago.val()) > 0.00){
                    $('#tarjeta-cont').removeClass("hide")
                }else{
                    $('#tarjeta-cont').addClass("hide")
                }
            });

            $ctn.on("change", "#caja-pago-efectivo", function () {
                self.setTwoNumberDecimal($(this));
                $('#caja-efectivo').html($('#caja-pago-efectivo').val());
                self.cajaResultadoTotal();
            });

            $ctn.on("change", "#caja-pago-deposito", function () {
                self.setTwoNumberDecimal($(this))
                $('#caja-deposito').html($('#caja-pago-deposito').val())
                self.cajaResultadoTotal();
            });

            $ctn.on("change", "#caja-pago-tarjeta", function () {
                self.setTwoNumberDecimal($(this))
                $('#caja-tarjeta').html($('#caja-pago-tarjeta').val())
                self.cajaResultadoTotal();
            });
        };

        setEventos(this.$ctn);
        this.execute();
    },
    setTwoNumberDecimal : function(elemt) {
        elemt[0].value = parseFloat(elemt[0].value).toFixed(2);
    },
    forceTwoDecimals : function(number, resultContainer){
        if(number % 1 != 0){
            let str = number.toString()
            let splitAtDecimal = str.split('.')
            numberOfDecimals = splitAtDecimal[1].length
            if(numberOfDecimals == 1){
                number = number.toString()
                number = number + "0"
            }
        }else{
            number = number.toString()
            number = number + ".00"
        }
        resultContainer.html(number);
    },
    cajaResultadoTotal : function(){
        let efectivo = parseFloat($('#caja-efectivo').html());
        let deposito = parseFloat($('#caja-deposito').html());
        let tarjeta = parseFloat($('#caja-tarjeta').html());

        let total = parseFloat($('#caja-total').html());
        let pagoDe = efectivo + deposito + tarjeta;

        if(pagoDe>total){
            this.forceTwoDecimals(pagoDe - total, $('#caja-vuelto'));
            this.forceTwoDecimals("0", $('#caja-credito'));

            $("#caja-result-vuelto").removeClass("hide");
        } else {
            this.forceTwoDecimals("0", $('#caja-vuelto'));
            this.forceTwoDecimals(total - pagoDe, $('#caja-credito'));

            $("#caja-result-vuelto").addClass("hide");
        }
     
        this.forceTwoDecimals(total, $("#caja-total"));
    }
};


/*Objeto externo, auxiliar que permite  hacer que un item, se mueva a par con el scroll de la pantalla
    recibe el $content de la página,  y el id del elemento a mover.
*/
var Scroller = function($ctn, idElementoMover){
    let lastKnownScrollPosition = 0;
    let ticking = false;
    const limitPixels = 280;    

    var functionScroll = function(e) {
        lastKnownScrollPosition = window.scrollY;
        if (!ticking) {
            window.requestAnimationFrame(function() {
                let $blk = $ctn.find(idElementoMover);
                let pixelesAMover = 0;

                if (lastKnownScrollPosition >= limitPixels){
                    pixelesAMover = lastKnownScrollPosition - limitPixels + 35;
                } 

                $blk.css({"margin-top": pixelesAMover+"px"});
                ticking = false;
            });

            ticking = true;
        }
    };

    this.createScroll = function(){
        document.addEventListener('scroll', functionScroll);   
    };
    
    this.killScroll = function(){
        lastKnownScrollPosition = null;
        ticking = false;
        document.removeEventListener("scroll", functionScroll);
    };
    return this;
};

frmRegistroAtencionCaja.init();
document.documentElement.scrollTop = 0;

/*



// $('#procesar-pago').click(function(){
    
// }) */
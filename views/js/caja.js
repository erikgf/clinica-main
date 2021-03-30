let retrievedCaja = localStorage.getItem('caja');
let paciente_info = localStorage.getItem('paciente');
paciente_info = JSON.parse(paciente_info)
let orders = localStorage.getItem('orders');
orders = JSON.parse(orders)
$("#caja-select").change(function () {
    let selectedCaja = $(this).val()
    localStorage.setItem('caja', JSON.stringify(selectedCaja));
})
if(retrievedCaja!=undefined){
    retrievedCaja = JSON.parse(retrievedCaja)
    if(retrievedCaja=="Caja I - DPI I"){
        $("#caja-select").val('1');
    }else{
        
        $("#caja-select").val('2');
    }
}
$('#fecha-cancelar').val(paciente_info.fecha)
$('#caja-name').html("NOMBRE DEL PACIENTE")
$('#caja-historial').html(paciente_info.Historia)
//$('#caja-realizante').html("")
//$('#caja-ordenante').html("")
//$('#caja-direccion').html("")
$('#caja-documento').html(paciente_info.Documento)
$('#caja-observaciones').html(paciente_info.observaciones)
$('#caja-orders').html(function(){
    var i
    let text
    for(i = 0;i<Object.keys(orders).length;i++){
        text += "<tr>" + "<td>" + orders[i].servicio + "</td>" + "<td class='caja-price'>" + orders[i].price + "</td>" + "</tr>"
    }
    return text
})
$('#caja-pago-deposito').keyup(function(){
    if($(this).val()!=''){
        $('#deposito').removeClass("hide")
    }else{
        $('#deposito').addClass("hide")
    }
})
$('#caja-pago-tarjeta').keyup(function(){
    if($(this).val()!=''){
        $('#tarjeta-cont').removeClass("hide")
    }else{
        $('#tarjeta-cont').addClass("hide")
    }
})

$('#caja-pago-efectivo').change(function(){
    setTwoNumberDecimal($(this))
    $('#caja-efectivo').html($('#caja-pago-efectivo').val())
    cajaResultadoTotal()
})
$('#caja-pago-deposito').change(function(){
    setTwoNumberDecimal($(this))
    $('#caja-deposito').html($('#caja-pago-deposito').val())
    cajaResultadoTotal()
})
$('#caja-pago-tarjeta').change(function(){
    setTwoNumberDecimal($(this))
    $('#caja-tarjeta').html($('#caja-pago-tarjeta').val())
    cajaResultadoTotal()
})
// $('.deposito-input').change(function(){
//     setTwoNumberDecimal($(this))
//     cajaResultadoTotal()
// })


function setTwoNumberDecimal(elemt) {
    elemt[0].value = parseFloat(elemt[0].value).toFixed(2);
}
let totalCost = 0
$('.caja-price').each(function(){
    totalCost = parseFloat($( this ).html()) + totalCost
    parseFloat(totalCost).toFixed(2)
})
forceTwoDecimals(totalCost, $("#caja-total"))
function forceTwoDecimals(number, resultContainer) {
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
    resultContainer.html(number)
}
function cajaResultadoTotal(){
    let efectivo = $('#caja-efectivo').html()
    efectivo = parseFloat(efectivo)
    let deposito = $('#caja-deposito').html()
    deposito = parseFloat(deposito)
    let tarjeta = $('#caja-tarjeta').html()
    tarjeta = parseFloat(tarjeta)

    let total = $('#caja-total').html()
    total = parseFloat(total)
    let pagoDe = efectivo + deposito + tarjeta
    if(total>pagoDe){
        $('#caja-result-cont').removeClass('callout-warning')
        $('#caja-result-cont').addClass('callout-info')
        $('#caja-vuelot-o-credito').html('Crédito S./')
        forceTwoDecimals(total-pagoDe, $('#caja-vuelto'))
    }
    if(total<pagoDe){
        $('#caja-result-cont').removeClass('callout-info')
        $('#caja-result-cont').addClass('callout-warning')
        $('#caja-vuelot-o-credito').html('Vuelto S./')
        forceTwoDecimals(pagoDe-total, $('#caja-vuelto'))
    }
    forceTwoDecimals(total, $("#caja-total"))
}cajaResultadoTotal()

var Scroller = function(){
    let lastKnownScrollPosition = 0;
    let ticking = false;
    const limitPixels = 280;

    var functionScroll = function(e) {
        lastKnownScrollPosition = window.scrollY;
        if (!ticking) {
            window.requestAnimationFrame(function() {
                let $blk = $("#content-cancelar #blk-facturando-a");
                let pixelesAMover = 0;

                if (lastKnownScrollPosition >= limitPixels){
                    pixelesAMover = lastKnownScrollPosition - limitPixels;
                } 

                console.log(lastKnownScrollPosition, pixelesAMover, limitPixels);
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

document.documentElement.scrollTop = 0;

var objScroll = new Scroller();
objScroll.createScroll();
// $('#procesar-pago').click(function(){
    
// })
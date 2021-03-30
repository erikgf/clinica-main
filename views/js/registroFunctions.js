// $.fn.modal.Constructor.prototype._enforceFocus = function() {}; fix for modals and select2
// $(document).ready(function () { $.fn.modal.Constructor.prototype.enforceFocus = function () { }; });
//Variables editables

let catID, servicioName, newCounter

var pacientesDDBBHistorial = [
    {id: 0, text: "Buscar o Crear usuario"},
    {id: 1, text: "Ninguno"},
    {id: 2, text: "47137833", tipoDocumento: 'Pasaporte',historial: 'xxx-xxx-xxx',
    nombres: 'David', apellidoP: 'Malca', apellidoM: 'Chavez',
    sexo: 'Masculino', DoB: 'xx/xx/xx', deuda:'xx.xx',
    ocupacion:'programacion',tipo: '1',estadoCivil:'Soltero',
    DNItitular: 'NaN',Ntitular:'NaN',parentesco:'NaN',
    telFijo:'074 xx xx xx', celUno: 'xxx-xxx-xxx', celDos:'xxx-xxx-xxx',
    correo: 'mi@correo.com', domicilio: 'Av. Mi casa', distrito:'Chiclayo',
    provincia: 'Chiclayo', region: 'Lambayeque'},

    {id: 3, text: "47853689", tipoDocumento: 'Pasaporte', historial: 'xxx-xxx-xxx',
    nombres: 'Érik', apellidoP: 'De Ur', apellidoM: 'Guillen Flores',
    sexo: 'Masculino', DoB: 'xx/xx/xx', deuda:'xx.xx',
    ocupacion:'programacion',tipo: '1',estadoCivil:'Soltero',
    DNItitular: 'NaN',Ntitular:'NaN',parentesco:'NaN',
    telFijo:'074 xx xx xx', celUno: 'xxx-xxx-xxx', celDos:'xxx-xxx-xxx',
    correo: 'mi@correo.com', domicilio: '', distrito:'1',
    provincia: '', region: ''},

    {id: 4, text: "47852145", tipoDocumento: 'Pasaporte', historial: 'xxx-xxx-xxx',
    nombres: 'Juanjosé', apellidoP: 'García', apellidoM: 'Urrutia',
    sexo: 'Masculino', DoB: 'xx/xx/xx', deuda:'xx.xx',
    ocupacion:'programacion',tipo: '1',estadoCivil:'Soltero',
    DNItitular: '48993322',Ntitular:'Titular1',parentesco:'Parentesco 1',
    telFijo:'074 xx xx xx', celUno: 'xxx-xxx-xxx', celDos:'xxx-xxx-xxx',
    correo: 'mi@correo.com', domicilio: '', distrito:'1',
    provincia: '', region: ''},

    {id: 5, text: "78383877", tipoDocumento: 'Pasaporte', historial: 'xxx-xxx-xxx',
    nombres: 'David Antonio', apellidoP: 'Malca', apellidoM: 'Chávez',
    sexo: 'Masculino', DoB: 'xx/xx/xx', deuda:'xx.xx',
    ocupacion:'programacion',tipo: '1',estadoCivil:'Soltero',
    DNItitular: '48993322',Ntitular:'Titular1',parentesco:'Parentesco 1',
    telFijo:'074 xx xx xx', celUno: 'xxx-xxx-xxx', celDos:'xxx-xxx-xxx',
    correo: 'mi@correo.com', domicilio: '', distrito:'1',
    provincia: '', region: ''},
    {id: 6, text: "47137833", tipoDocumento: 'Pasaporte',historial: 'xxx-xxx-xxx',
    nombres: 'Ana', apellidoP: 'Mejía', apellidoM: 'Baca',
    sexo: 'Masculino', DoB: 'xx/xx/xx', deuda:'xx.xx',
    ocupacion:'programacion',tipo: '1',estadoCivil:'Soltero',
    DNItitular: 'NaN',Ntitular:'NaN',parentesco:'NaN',
    telFijo:'074 xx xx xx', celUno: 'xxx-xxx-xxx', celDos:'xxx-xxx-xxx',
    correo: 'mi@correo.com', domicilio: '', distrito:'1',
    provincia: '', region: ''},

    {id: 7, text: "47995522", tipoDocumento: 'Pasaporte', historial: 'xxx-xxx-xxx',
    nombres: 'Jesús', apellidoP: 'Martinez', apellidoM: 'Vasquez',
    sexo: 'Masculino', DoB: 'xx/xx/xx', deuda:'xx.xx',
    ocupacion:'programacion',tipo: '1',estadoCivil:'Soltero',
    DNItitular: 'NaN',Ntitular:'NaN',parentesco:'NaN',
    telFijo:'074 xx xx xx', celUno: 'xxx-xxx-xxx', celDos:'xxx-xxx-xxx',
    correo: 'mi@correo.com', domicilio: '', distrito:'1',
    provincia: '', region: ''},

    {id: 8, text: "47856629", tipoDocumento: 'Pasaporte', historial: 'xxx-xxx-xxx',
    nombres: 'Carlos Juan', apellidoP: 'Zaapata', apellidoM: 'Castillo',
    sexo: 'Masculino', DoB: 'xx/xx/xx', deuda:'xx.xx',
    ocupacion:'programacion',tipo: '1',estadoCivil:'Soltero',
    DNItitular: '48993322',Ntitular:'Titular1',parentesco:'Parentesco 1',
    telFijo:'074 xx xx xx', celUno: 'xxx-xxx-xxx', celDos:'xxx-xxx-xxx',
    correo: 'mi@correo.com', domicilio: '', distrito:'1',
    provincia: '', region: ''},
];
let pacientesDDBB = [
    {id: 0, text: "Nombres y DNI"},
    {id: 1, text: "David Antonio Malca Chávez - 47137833"},
    {id: 2, text: "Érik De Ur Guillen Flores - 47853689"},
    {id: 3, text: "Juanjosé García Urrutia - 47852145"},
    {id: 4, text: "David Antonio Malca Chávez - 47137883"},
];

let autorizantes = [
    {id: 0, text: ""},
    {id: 4, text: "Autorizante 1 - 454512gteer345"},
    {id: 1, text: "Autorizante 2 - 5634g43tgvg6s2"},
    {id: 2, text: "Autorizante 3 - 563h4563h3h36h"},
    {id: 5, text: "Autorizante 4 - 3h4563h4533h45"},
]

let validadores = [
    {id: 0, text: ""},
    {id: 4, text: "Validador 1 - 454512gteer345"},
    {id: 1, text: "Validador 2 - 5634g43tgvg6s2"},
    {id: 2, text: "Validador 3 - 563h4563h3h36h"},
    {id: 5, text: "Validador 4 - 3h4563h4533h45"},
]

let serviciosCat1 = ['Añadir servicio', 'Servicio 1 - Categoría 1','Servicio 2 - Categoría 1']

let medicosOrdenantes = ['Escoger', 'Médico Ordenante 1', 'Médico Ordenante 2', 'Médico Ordenante 3']
let medicosRealizantes = ['Escoger', 'Médico Realizante 1', 'Médico Realizante 2', 'Médico Realizante 3']

let servicio1Cat1 = {
    title: 'Servicio 1 - Categoría 1',
    desc: 'Lorem ipsum dolor sit, amet consectetur adipisicing elit.',
    unitPrice: '10.00'
};

let servicio2Cat1 = {
    title: 'Servicio 2 - Categoría 1',
    desc: 'Lorem ipsum dolor sit, amet consectetur adipisicing elit.',
    unitPrice: '12.00'
}
let servicio1Cat2 = {
    title: 'Servicio 1 - Categoría 2',
    desc: 'Lorem ipsum dolor sit, amet consectetur adipisicing elit.',
    unitPrice: '55.00'
}
let servicio2Cat2 = {
    title: 'Servicio 2 - Categoría 2',
    desc: 'Lorem ipsum dolor sit, amet consectetur adipisicing elit.',
    unitPrice: '99.00'
}
let servicio1Cat3 = {
    title: 'Servicio 1 - Categoría 3',
    desc: 'Lorem ipsum dolor sit, amet consectetur adipisicing elit.',
    unitPrice: '48.00'
}
let servicio2Cat3 = {
    title: 'Servicio 2 - Categoría 3',
    desc: 'Lorem ipsum dolor sit, amet consectetur adipisicing elit.',
    unitPrice: '322.00'
}

function func_registro_de_servicios() {
    let subtotal = document.querySelector('#sub-total');
    let detailsCont;

    var setDOM = function(){
        /*NOT IMPLEMENTEED */
    };
    
    var setEventos = function(){
        $("#content-registroatencion").on("click", "#plus", function(e){
            let number = parseInt(e.target.previousElementSibling.innerHTML);
            e.target.previousElementSibling.innerHTML = number+1;
            getSubtotalOfEach();
            getSubtotal();
            firstTry=false;
        });
    
        $("#content-registroatencion").on("click", "#minus", function(e){
            let number = parseInt(e.target.nextElementSibling.innerHTML);
            if(e.target.nextElementSibling.innerHTML>1){
                e.target.nextElementSibling.innerHTML = number-1;
            }
            getSubtotalOfEach();
            getSubtotal();
        });
    
        $("#content-registroatencion").on("click", "#close-servicio", function(e){
            let element = e.target.parentElement.parentElement.parentElement.parentElement;
            element.parentNode.removeChild(element);
            if($('#order-cont').children().length==0){
                $('#subtotal-cont').addClass("hide");
                canContinue();
            }
            getSubtotalOfEach();
            getSubtotal();
        });
    
        $("#content-registroatencion").on("click", "#descuento-btn", function(e){
            detailsCont = e.target.nextElementSibling;
    
            if(detailsCont.querySelector('#autorizadoPor').innerHTML != ''){
                if(detailsCont.nextElementSibling.children[0].innerHTML=='Gratuito'){
                    $('#gratuitoCheck').prop('checked', true);
                    $('#descuentoInput')[0].value = '';
                    $("#descuentoInput").prop('disabled', true);
                }else{
                    $('#descuentoInput')[0].value = detailsCont.nextElementSibling.children[0].innerHTML;
                }
    
                $('#motivoTextArea').val(detailsCont.querySelector('#motivo').innerHTML);
                nameInput[3].innerHTML = detailsCont.querySelector('#autorizadoPor').innerHTML;
                nameInput[4].innerHTML = detailsCont.querySelector('#validadoPor').innerHTML;
                $('#eliminarDescuento').removeClass('hide');
            }else{
                $('#eliminarDescuento').addClass('hide');
                clearInputs();
            }
        });

        $("#content-registroatencion").on("change", "#selUser", function(){
           canContinue();
        });

        $("#content-registroatencion").on("change", "#txt-medicoordenante", function(){
            canContinue();
         });

         $("#content-registroatencion").on("change", "#txt-medicorealizante", function(){
            canContinue();
         });

        $("#content-registroatencion").on("change", "#categorias-select", function(e){
            let $selAgregadorServicios = $("#agregador-de-servicios");
            if(k==0){
                $selAgregadorServicios.prop('disabled', false);
                $("#categorias-select option[value='NaN']").remove();

                $selAgregadorServicios.select2({
                    ajax: { 
                        url: "./mySQL/connect-servicios.php",
                        type: "post",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                searchTerm: params.term, // search term
                                id: $("#categorias-select").val()
                            };
                        },
                        processResults: function (response) {
                            return {results: response};
                        }
                    },
                    minimumInputLength: 1,
                    width: '100%',
                    multiple:false,
                    placeholder:"Seleccionar",
                    debug: true,
                    tags: false,
                    cache: true
                });
                k=1
            }
            
            if($("#categorias-select").find(":selected").val()=="NaN"){
                $selAgregadorServicios.prop('disabled', true).empty()
            }else if($("#categorias-select").find(":selected").val()=="cat-1"){

                    $selAgregadorServicios.prop('disabled', false).empty()

                    $selAgregadorServicios.prop('disabled', false).empty()
                    .append(new Option("Agregar Servicio", "add-service"))
                    .append(new Option("Servicio 1 - Categoría 1", "servicio1-cat1"))
                    .append(new Option("Servicio 2 - Categoría 1", "servicio2-cat1"))
                    
                    return
                
            }else if($("#categorias-select").find(":selected").val()=="cat-2"){
                
                    $selAgregadorServicios.prop('disabled', false).empty()
                    .append(new Option("Agregar Servicio", "add-service"))
                    .append(new Option("Servicio 1 - Categoría 2", "servicio1-cat2"))
                    .append(new Option("Servicio 2 - Categoría 2", "servicio2-cat2"))
                    

                    return
                
            }else if($("#categorias-select").find(":selected").val()=="cat-3"){
                
                $selAgregadorServicios.prop('disabled', false).empty()
                .append(new Option("Agregar Servicio", "add-service"))
                .append(new Option("Servicio 1 - Categoría 3", "servicio1-cat3"))
                .append(new Option("Servicio 2 - Categoría 3", "servicio2-cat3"))
                return;
            
            }
        });

        var variableParaEvitarSeguirSegundoChangeAlVaciar = false;
        $("#content-registroatencion").on("change", "#agregador-de-servicios", function(e){
            if(variableParaEvitarSeguirSegundoChangeAlVaciar){
                variableParaEvitarSeguirSegundoChangeAlVaciar = false;
                return;
            }

            var $txtAgregadorServicios = $(this);
            var textInsideThisSelect2 = $txtAgregadorServicios.find("option:selected").html();
            $.ajax({ 
                url: "./mySQL/connect-servicios-check.php",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: {
                   idservicio : this.value
                },
                success: function(result){
                    if(result.length==1){
                        agregarServicio(result[0]);
                        $txtAgregadorServicios[0].selectedIndex = 0;
                        variableParaEvitarSeguirSegundoChangeAlVaciar = true;
                        $txtAgregadorServicios.val(null).trigger('change');

                        canContinue();
                        getSubtotalOfEach();
                        getSubtotal();
                    }
                },
                error: function (request, status, error) {
                    toastr["error"]("Ese usuario no existe");
                    return
                },
                cache: true
            });
            /*
            let textInsideThisSelect2 = $('#select2-agregador-de-servicios-container').html();
           ;*/
        });
    
        $("#content-registroatencion").on("click", '#ctn-historial',function(e){
            if(localStorage.getItem('SavedName')){
                localStorage.removeItem('SavedName');
            }

            let savedText = nameInput[0].innerHTML;
            function hasNumber(myString) {
                return /\d/.test(myString);
            };

            let result = hasNumber(savedText);
            let savedName;
    
            if(!result){
                savedText = savedText.replace(/(^\s*)|(\s*$)/gi,"");
                savedText = savedText.replace(/[ ]{2,}/gi," ");
                let wordArray = savedText.split(' ');

                switch(wordArray.length){
                    case 1:
                        name = wordArray[0];
                        savedName = { 'Names': name};
                    break;
                    case 2:
                        name = wordArray[0];
                        firstLastname = wordArray[1];
                        savedName = { 'Names': name, 'First lastname': firstLastname};
                    break;
                    case 3:
                        name = wordArray[0];
                        firstLastname = wordArray[1];
                        secondLastname = wordArray[2];
                        savedName = { 'Names': name, 'First lastname': firstLastname, 'Second lastname': secondLastname};
                    break;
                    default:
                        name = wordArray[0] + ' ' + wordArray[1];
                        firstLastname = wordArray[2];
                        for(i = 3;i<wordArray.length;i++){
                            (i==3)?secondLastname = wordArray[i]:secondLastname += ' ' + wordArray[i];
                        }
                        savedName = { 'Names': name, 'First lastname': firstLastname, 'Second lastname': secondLastname};
                    break;
                };
            }else{
                savedName = { 'DNI': savedText};
            }
            
            localStorage.setItem('SavedName', JSON.stringify(savedName));
        });   


        var gratuito = Boolean;
        $('#content-registroatencion gratuitoCheck').change(function() {
            if(this.checked) {
                $("#descuentoInput").prop('disabled', true);
                $("#descuentoInput").removeClass('is-invalid')
            }else{
                $("#descuentoInput").prop('disabled', false);
                $('#descuentoInput')[0].value = ''
            }
        });

        $('#content-registroatencion cancelarDescuentoBtn').click(function(){
            clearInputs()
        });

        $('#content-registroatencion autorizar-btn').click(function(){
            let montoDeDescuento

            
            if($('#gratuitoCheck').is(':checked')){
                gratuito = true
                $('#descuentoInput').removeClass('is-invalid')
                montoDeDescuento = 0
            }else{
                gratuito = false
                montoDeDescuento = $('#descuentoInput')[0].value
            }
            let currentQuant = detailsCont.parentElement.querySelector('#quant').innerHTML
            let currentPrecioUnitario = detailsCont.parentElement.querySelector('#precio-unitario').innerHTML
            currentQuant = parseInt(currentQuant)
            currentPrecioUnitario = parseFloat(currentPrecioUnitario)
            let currentSubtotal = currentQuant*currentPrecioUnitario
            currentSubtotal = parseFloat(currentSubtotal)
            montoDeDescuento = parseFloat(montoDeDescuento)
            if(montoDeDescuento>currentSubtotal){
            alert("el monto de descuento no puede ser mayor al subtotal del servicio. Subtotal del servicio actual es : S/."+currentSubtotal.toFixed(2))
            return
            }

            //check if all fields have been filled
            let isEmpty = false
            if(!gratuito){
                if($('#descuentoInput')[0].value==''){
                    $('#descuentoInput').addClass('is-invalid')
                    
                    $('#descuentoInput').keydown(function(){
                        if($(this).val() != ''){
                            $(this).removeClass('is-invalid')
                        }
                    })
                    isEmpty=true
                }
            }
            if($('#motivoTextArea').val()==''){
                $('#motivoTextArea').addClass('is-invalid')
                $('#motivoTextArea').keydown(function(){
                    if($(this).val() != ''){
                        $(this).removeClass('is-invalid')
                    }
                })
                isEmpty=true
            }
            if(nameInput[3].innerHTML==''){
                // nameInput[3].(function(){
                //     if($(this).val() != ''){
                //         $(this).removeClass('is-invalid')
                //     }
                // })
                nameInput[3].parentElement.style.cssText = `width:100%;border: solid 1px;border-color: #dc3545;padding-right: 2.25rem;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");background-repeat: no-repeat;background-position: right calc(.375em + .6875rem) center;background-size: calc(.75em + .375rem) calc(.75em + .375rem)`
                isEmpty=true
            }
            if(nameInput[4].innerHTML==''){
                nameInput[4].parentElement.style.cssText = `width:100%;border: solid 1px;border-color: #dc3545;padding-right: 2.25rem;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");background-repeat: no-repeat;background-position: right calc(.375em + .6875rem) center;background-size: calc(.75em + .375rem) calc(.75em + .375rem)`
                isEmpty=true
            }
            if($('#autorizarPW').val()==''){
                $('#autorizarPW').addClass('is-invalid')
                $('#autorizarPW').keydown(function(){
                    if($(this).val() != ''){
                        $(this).removeClass('is-invalid')
                    }
                })
                isEmpty=true
            }
            if(isEmpty==true){
                
                $('#llenar-campos-warning').removeClass('hide')
                $('.modal-footer').css("background-color", "#e02929");
                return
            }


            let codigoDeAutorizante = nameInput[3].innerHTML
            let codigoDeValidador = nameInput[4].innerHTML
            let motivo = $('#motivoTextArea').val()
            detailsCont.querySelector('#autorizadoPor').innerHTML = codigoDeAutorizante
            detailsCont.querySelector('#validadoPor').innerHTML = codigoDeValidador
            detailsCont.querySelector('#motivo').innerHTML = motivo
            if(gratuito){
                detailsCont.nextElementSibling.children[0].style.color = "red"
                detailsCont.nextElementSibling.children[0].innerHTML = "Gratuito"
            }else{
                // detailsCont.nextElementSibling.children[0].style.color = "black"
                detailsCont.nextElementSibling.children[0].innerHTML = montoDeDescuento
            }
            

            $("#myModal").modal('hide');
            getSubtotalOfEach()
            getSubtotal()
            detailsCont.classList.remove( "hide" )
            detailsCont.previousElementSibling.innerHTML= 'Editar descuento'
            detailsCont.previousElementSibling.classList = 'btn btn-outline-primary col-lg-2'
            clearInputs()
            $('#eliminarDescuento').removeClass('hide')
        });  
    };

    var setFuncionesInicio = function(){
        $("#content-registroatencion #datetimepicker1").datetimepicker({
            format: 'LT',
            date: new Date()
        });
        

        $("#content-registroatencion #datetimepicker2").datetimepicker({
            format: 'L',
            date: new Date()
        });

        /*Iniciando Selects*/
        $("#content-registroatencion #selUser").select2({
            ajax: { 
                url: "./mySQL/connect-pacientes.php",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        searchTerm: params.term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            },
            minimumInputLength: 3,
            width: '100%',
            multiple:false,
            placeholder:"Seleccionar",
            debug: true,
            tags: true
        });


        $("#content-registroatencion #selUser").select2('open');

        $("#content-registroatencion #categorias-select").select2({
            ajax: { 
                url: "./mySQL/connect-categorias.php",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        searchTerm: params.term, 
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            },
            width: '100%',
            multiple:false,
            placeholder:"Seleccionar categoría",
            debug: true,
            tags: false
        });

        $("#content-registroatencion #txt-medicorealizante").select2({
            ajax: { 
            url: "./mySQL/connect-doctores.php",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
            return {
                searchTerm: params.term, // search term
            };
            },
            processResults: function (response) {
            return {
                results: response
            };
            },
            cache: true
            },
            minimumInputLength: 1,
            width: '100%',
            multiple:false,
            placeholder:"Seleccionar",
            debug: true,
            tags: false
        });

        $("#content-registroatencion #txt-medicoordenante").select2({
                ajax: { 
                    url: "./mySQL/connect-doctores.php",
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params.term, // search term
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1,
                width: '100%',
                multiple:false,
                placeholder:"Seleccionar",
                debug: true,
                tags: false
        });

        $("#content-registroatencion #autorizante").select2({
            dropdownParent: $("#autorizante").parent(),
            data:autorizantes,
            width: '100%'
        });

        $("#content-registroatencion #validador").select2({
            dropdownParent: $("#validador").parent(),
            data:validadores,
            width: '100%',
        });

        
    };

    setFuncionesInicio();
    setEventos();

    var j = 0;
    let nameInput = document.querySelectorAll('span.select2-selection__rendered');
    
    if(nameInput.length== 0){
        location.reload();
    }
    
    let name;
    let firstLastname;
    let secondLastname;
    
	nameInput[3].addEventListener('DOMSubtreeModified', ()=>{
        if (j != 0){
            nameInput[3].parentElement.style.cssText = `width:100%;`
            
            nameInput[4].innerHTML = nameInput[3].innerHTML
        }else j = 1
    })

var k=0;
var z=0;

function agregarServicio(Servicio){
    //<p>${Servicio.desc}</p>
    $('#order-cont').append(`
            <div class="row each-order" style="width:100%" tabindex=0>
                <div class="col-sm-12" style="padding-left:8px;">
                    <div class="callout callout-warning" style="margin-top:20px;">
                        <div class="card-cont">
                            <div class="servicio-text-cont">
                                <h5>${Servicio.nombre}</h5>
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info" id="minus">-</button>
                                <button type="button" class="btn btn-info" id="quant">1</button>
                                <button type="button" class="btn btn-info"id="plus">+</button>
                            </div>


                            <p style="margin:0" class="col-lg-1">P.Unitario <span id="precio-unitario">${Servicio.precio}</span></p>
                            
                            <button type="button" class="btn btn-block btn-outline-warning col-lg-2" id="descuento-btn" style="width:200px"  data-toggle="modal" data-target="#myModal">
                                <img src="views/img/coin.svg" style="width:25px;display:inline-block;pointer-events:none">
                                <i style="pointer-events:none">Sin Descuento</i>
                            </button>


                            <span id="descuentoDetails" class="hide col-lg-2">
                                <strong>Autorizado por: </strong>
                                <span id="autorizadoPor"></span>
                                <br>
                                <strong>Validado por: </strong>
                                <span id="validadoPor"></span>
                                <br>
                                <span style="max-width:350px;display:inline-block">
                                    <strong>Motivo: </strong>
                                    <span id="motivo"></span>
                                </span>
                                <br>
                            </span>
                                
                            <span class="col-lg-2">
                                Descuento por: <span id="monto-de-descuento" style="color:red">Sin descuento</span><br>
                                Subtotal: <h5 id=subtotalPorItem style="color:green;display:inline-block">0</h5>
                            </span>
                        
                            <button type="button" class="btn btn-block btn-outline-danger" style="width:35px;margin-top:0" id='close-servicio'>&times;</button>
                        </div>
                        
                    </div>
                </div>
            </div>
            `);
};

function getSubtotal(){

    let currentSum = 0
    let subTotales = document.querySelectorAll('#subtotalPorItem')
    subTotales.forEach((eachSubtotal)=>{
        let parsedSubtotal = parseFloat(eachSubtotal.innerHTML)
        currentSum = currentSum + parsedSubtotal
        subtotal.innerHTML = currentSum.toFixed(2)
    })
};

function getSubtotalOfEach(){

    let cards = document.querySelectorAll('.card-cont')
    cards.forEach((card)=>{
        let currentQuantity = card.querySelector('#quant').innerHTML
        let precioUnitario = card.querySelector('#precio-unitario').innerHTML
        let subtotalPlaceHolder = card.querySelector('#subtotalPorItem')
        let montoDeDescuento = card.querySelector('#monto-de-descuento').innerHTML
        precioUnitario = parseFloat(precioUnitario)
        currentQuantity = parseInt(currentQuantity)

        if(montoDeDescuento == 'Sin descuento'){
            montoDeDescuento = 0
        }else if(montoDeDescuento == 'Gratuito'){
            montoDeDescuento = (currentQuantity*precioUnitario)
        }else{
            parseFloat(montoDeDescuento)
        }
        let result = (currentQuantity*precioUnitario)-montoDeDescuento

        result = result.toFixed(2);
        subtotalPlaceHolder.innerHTML = result
    })
};


function clearInputs(){
    $('#descuentoInput').removeClass('is-invalid')
    $('#motivoTextArea').removeClass('is-invalid')
    nameInput[3].parentElement.style.cssText = 'width:100%'
    nameInput[4].parentElement.style.cssText = 'width:100%'
    $('#autorizarPW').removeClass('is-invalid')
    $('.modal-footer').css('background-color', '')
    $('#llenar-campos-warning').removeClass('hide')
    $('#gratuitoCheck').prop('checked', false)
    $("#descuentoInput").prop('disabled', false)
    $('#descuentoInput')[0].value = ''
    $('#motivoTextArea').val("")
    $('#autorizante').val(null).trigger('change');
    $('#validador').val(null).trigger('change');
    $('#autorizarPW').val("")
    
};

$('#eliminarDescuento').click(function() {
    detailsCont.previousElementSibling.innerHTML = `<img src="views/img/coin.svg" style="width:25px;display:inline-block;pointer-events:none">
                                <i style="pointer-events:none">Sin Descuento</i>`;
    detailsCont.previousElementSibling.classList = 'btn btn-outline-warning';

    detailsCont.querySelector('#autorizadoPor').innerHTML = '';
    detailsCont.querySelector('#validadoPor').innerHTML = '';
    detailsCont.querySelector('#motivo').innerHTML = '';
    detailsCont.classList.add('hide');

    detailsCont.nextElementSibling.children[0].innerHTML = 'Sin descuento';
    $("#myModal").modal('hide');
    getSubtotalOfEach();
    getSubtotal();
})

if(nameInput.length== 0){
    location.reload();
}
/*
nameInput[0].addEventListener('DOMSubtreeModified', ()=>{
    canContinue()
})

nameInput[1].addEventListener('DOMSubtreeModified', ()=>{canContinue()})
nameInput[2].addEventListener('DOMSubtreeModified', ()=>{canContinue()})
*/;


function canContinue(){
    let $btnContinuar = $("#continue-btn");
    let ordersCount = $('#order-cont').children().length;
    let idPaciente = $("#selUser").val();
    let idMedicoRealizante = $("#txt-medicorealizante").val();
    let idMedicoOrdenante = $("#txt-medicoordenante").val();

    if (ordersCount <= 0){
        $btnContinuar.prop("disabled", true);
        return;
    }

    if (idPaciente == "0" || idPaciente == "" || idPaciente == null){
        $btnContinuar.prop("disabled", true);
        return;
    }

    if (idMedicoRealizante == "0" || idMedicoRealizante == "" || idMedicoRealizante == null){
        $btnContinuar.prop("disabled", true);
        return;
    }

    if (idMedicoOrdenante == "0" || idMedicoOrdenante == "" || idMedicoOrdenante == null ){
        $btnContinuar.prop("disabled", true);
        return;
    }

    console.log("nondead");
    $btnContinuar.prop("disabled", false);
};

$('#continue-btn').click(function(){
    let patientInfo = []
    //check if user selected exists
    
    //get text from select user <select />
    let text = nameInput[0].innerHTML
    $.ajax({ 
        url: "./mySQL/connect-pacientes.php",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: {searchTerm: text // search term
        },
        success: function(result){
            if(result.length>0){
                patientInfo = result
                cont(patientInfo)
            }else{
                toastr["error"]("Ese usuario no existe")
                return
            }
            
        },
        error: function (request, status, error) {
        toastr["error"]("Ese usuario no existe")
        return
        },
        cache: true
        }
    )


function cont(patientInfo){
    let date = $('#datetimepicker2').children('input').eq(0).val()
    let name = nameInput[0].innerHTML
    let orders = {}
    $('.each-order').each(function( index ){
        let servicioName = document.querySelectorAll('.servicio-text-cont')[index].children[0].innerHTML
        let subtotal = $( this).find('#subtotalPorItem').html()
        orders[index] = { "servicio":servicioName,"price": subtotal};
    })
    let DNI = name.replace( /^\D+/g, '');
    let numberToSubtract = DNI.length + 3
    numberToSubtract = -Math.abs(numberToSubtract);
    name = name.slice(0, numberToSubtract);
    function quickCheck(key) {
        return key.text == DNI
    }
    // let result = pacientesDDBBHistorial.find(quickCheck)
    let historia = patientInfo[0].historial + ' ' + name
    let direccion  = patientInfo[0].domicilio + ', ' + patientInfo[0].distrito + ', ' + patientInfo[0].provincia + ', ' + patientInfo[0].region
    let documento = patientInfo[0].DNI
    let observaciones = $('#registro-observaciones').val()

    let paciente = {}
    let realizante = nameInput[1].innerHTML
    let ordenante = nameInput[2].innerHTML
    paciente = { "Name":name,"Historia":historia,"Direccion": direccion,"Documento":documento, "Realizante":realizante, "Ordenante":ordenante, 'fecha':date, "observaciones":observaciones};
    localStorage.setItem('paciente', JSON.stringify(paciente));
    localStorage.setItem('orders', JSON.stringify(orders));
    showContent('cancelar')
}

})

};

func_registro_de_servicios();


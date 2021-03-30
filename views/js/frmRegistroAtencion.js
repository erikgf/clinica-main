// $.fn.modal.Constructor.prototype._enforceFocus = function() {}; fix for modals and select2
// $(document).ready(function () { $.fn.modal.Constructor.prototype.enforceFocus = function () { }; });
//Variables editables

let catID, servicioName, newCounter

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

function func_registro_de_servicios() {
    let subtotal = document.querySelector('#sub-total');
    let detailsCont;

    var setDOM = function(){
        /*NOT IMPLEMENTEED */
    };
    
    var setEventos = function(){
        $("#content-registro-atencion").on("click", "#plus", function(e){
            let number = parseInt(e.target.previousElementSibling.innerHTML);
            e.target.previousElementSibling.innerHTML = number+1;
            getSubtotalOfEach();
            getSubtotal();
            firstTry=false;
        });
    
        $("#content-registro-atencion").on("click", "#minus", function(e){
            let number = parseInt(e.target.nextElementSibling.innerHTML);
            if(e.target.nextElementSibling.innerHTML>1){
                e.target.nextElementSibling.innerHTML = number-1;
            }
            getSubtotalOfEach();
            getSubtotal();
        });
    
        $("#content-registro-atencion").on("click", "#close-servicio", function(e){
            let element = e.target.parentElement.parentElement.parentElement.parentElement;
            element.parentNode.removeChild(element);
            if($('#order-cont').children().length==0){
                $('#subtotal-cont').addClass("hide");
                canContinue();
            }
            getSubtotalOfEach();
            getSubtotal();
        });
    
        $("#content-registro-atencion").on("click", "#descuento-btn", function(e){
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

        $("#content-registro-atencion").on("change", "#selUser", function(){
           canContinue();
        });

        $("#content-registro-atencion").on("change", "#txt-medicoordenante", function(){
            canContinue();
         });

         $("#content-registro-atencion").on("change", "#txt-medicorealizante", function(){
            canContinue();
         });

        $("#content-registro-atencion").on("change", "#categorias-select", function(e){
            let $selAgregadorServicios = $("#agregador-de-servicios");

            $selAgregadorServicios.prop('disabled', false);
            $("#categorias-select option[value='NaN']").remove();

            $selAgregadorServicios.select2({
                ajax: { 
                    //url: "./mySQL/connect-servicios.php",
                    url : "./controlador/servicio.controlador.php?op=buscar",
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            p_cadenabuscar: params.term, // search term
                            p_idcategoria: $("#categorias-select").val()
                        };
                    },
                    processResults: function (response) {
                        return {results: response.datos};
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
            
            if($("#categorias-select").find(":selected").val()==""){
                $selAgregadorServicios.prop('disabled', true).empty()
            }
        });

        var variableParaEvitarSeguirSegundoChangeAlVaciar = false;
        $("#content-registro-atencion").on("change", "#agregador-de-servicios", function(e){
            if(variableParaEvitarSeguirSegundoChangeAlVaciar){
                variableParaEvitarSeguirSegundoChangeAlVaciar = false;
                return;
            }

            var $txtAgregadorServicios = $(this);
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
        });
    
        $("#content-registro-atencion").on("click", '#ctn-historial',function(e){
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
        $('#content-registro-atencion gratuitoCheck').change(function() {
            if(this.checked) {
                $("#descuentoInput").prop('disabled', true);
                $("#descuentoInput").removeClass('is-invalid')
            }else{
                $("#descuentoInput").prop('disabled', false);
                $('#descuentoInput')[0].value = ''
            }
        });

        $('#content-registro-atencion cancelarDescuentoBtn').click(function(){
            clearInputs()
        });

        $('#content-registro-atencion autorizar-btn').click(function(){
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
        });

        $('#continue-btn').click(function(){
            let idPacienteSeleccionado = $("#selUser").val();
            $.ajax({ 
                //url: "./mySQL/connect-paciente-x-id.php",
                url : "./controlador/paciente.controlador.php?op=obtener_paciente_x_id",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: {
                    p_idpaciente : idPacienteSeleccionado
                },
                success: function(result){
                    if(result.rpt){
                        continuar(result.datos)
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
            );

            function continuar(objPaciente){
                let orders = {};
                $('.each-order').each(function( index ){
                    let servicioName = document.querySelectorAll('.servicio-text-cont')[index].children[0].innerHTML
                    let subtotal = $( this).find('#subtotalPorItem').html()
                    orders[index] = { "servicio":servicioName,"price": subtotal};
                });

                let medicoRealizante = $("#txt-medicorealizante").find("option:selected").html();
                let medicoOrdenante = $("#txt-medicoordenante").find("option:selected").html();
                let pacienteListoParaPagar = {
                         "nombres_completos": objPaciente.nombres_completos,
                         "numero_historia" :objPaciente.numero_historia,
                         "direccion": objPaciente.direccion,
                         "numero_documento": objPaciente.numero_documento,
                         "medico_realizante": medicoRealizante, 
                         "medico_ordenante":medicoOrdenante, 
                         "fecha": $("#txt-fechaatencion").val(), 
                         "hora": $("#txt-horaatencion").val(),
                         "observaciones": $('#registro-observaciones').val()
                        };
                
                localStorage.setItem('paciente', JSON.stringify(pacienteListoParaPagar));
                localStorage.setItem('orders', JSON.stringify(orders));
                 
                showContent('registro-atencion-caja');

                if (window.frmRegistroAtencionCaja){
                    window.frmRegistroAtencionCaja.execute();
                }
            };
        });

    };

    var setFuncionesInicio = function(){
        $("#content-registro-atencion #datetimepicker1").datetimepicker({
            format: 'LT',
            date: new Date()
        });
        
        $("#content-registro-atencion #datetimepicker2").datetimepicker({
            format: 'L',
            date: new Date()
        });

        /*Iniciando Selects*/
        $("#content-registro-atencion #selUser").select2({
            ajax: { 
                //url: "./mySQL/connect-pacientes.php",
                url : "./controlador/paciente.controlador.php?op=buscar_pacientes",
                type: "POST",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        p_cadenabuscar: params.term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response.datos
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


        $("#content-registro-atencion #selUser").select2('open');

        $("#content-registro-atencion #categorias-select").select2({
            ajax: { 
                url : "./controlador/categoria.servicio.controlador.php?op=buscar",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        p_cadenabuscar: params.term, 
                    };
                },
                processResults: function (response) {
                    return {
                        results: response.datos
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

        $("#content-registro-atencion #txt-medicorealizante").select2({
            ajax: { 
                url : "./controlador/medico.controlador.php?op=buscar",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        p_cadenabuscar: params.term, // search term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response.datos
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

        $("#content-registro-atencion #txt-medicoordenante").select2({
            ajax: { 
                url : "./controlador/medico.controlador.php?op=buscar",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        p_cadenabuscar: params.term, // search term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response.datos
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

        $("#content-registro-atencion #autorizante").select2({
            dropdownParent: $("#autorizante").parent(),
            data:autorizantes,
            width: '100%'
        });

        $("#content-registro-atencion #validador").select2({
            dropdownParent: $("#validador").parent(),
            data:validadores,
            width: '100%',
        });

        
    };

    setFuncionesInicio();
    setEventos();
/*
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
*/
    
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

        $btnContinuar.prop("disabled", false);
    };

};

func_registro_de_servicios();


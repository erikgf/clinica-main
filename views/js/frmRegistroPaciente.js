var frmRegistroPaciente = {
    initialized : 0,
    changeableInput : null,
    currentId : null,
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
        this.$ctn = $("#content-registro-paciente");
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
    autofill : function(input){
        if(this.changeableInput!=input){
            this.changeableInput=input
            this.currentId=0
        }
        $.ajax({ 
            url: "./mySQL/connect-pacientes-historial-strict.php",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: {
                searchTerm: input.innerHTML, // search term
            },
            success: function(result){
                if(input.innerHTML.length>7){
                    value = result[0]
                    console.log(value)
                    if(value){
                        counterToStopInfiniteLoop=1
                        activarAlerta(value.nombres + ' ' + value.apellidoP + ' ' + value.apellidoM)
                        cont()
                    }else {
                        // clearHistorialInputs(dniInput[7])
                        cambiarARegistrar()
                    }
                }
            },
            error: function (request, status, error) {
            console.log('error niggs')
            return
            },
            cache: true
        });
    },
    clearHistorialInputs : function(input){
        if(input == dniInput[7]){
            $('#collapseOne').removeClass('show')
            $('#lookupPorTitular').val(null).trigger('change');
            dniInput[5].innerHTML = ''
        }else if(input == 'Limpiar-Campos-Btn'){
            $('#collapseOne').removeClass('show')
            $('#lookupPorTitular').val(null).trigger('change');
            $('#dniInput').val(null).trigger('change');
            $('#dniInput').empty();
        }
        $('#multipleChildrenSelect').prop('disabled', true).empty()
            .css({"background-color":"white","color":"black"})
    
        $('#tipoDeDocumentoSelect').val('1')
        $('#historialSelect').val('')
        $('#nombresSelect').val('')
        $('#apellidoPaternoSelect').val('')
        $('#apellidoMaternoSelect').val('')
        $('#sexoSelect').val('')
        $('#nacimientoSelect').val('')
        $('#deudaSelect').val('')
        $('#ocupacionSelect').val('')
        $('#tipoSelect').val('')
        $('#estadoCivilSelect').val('')
        
        $('#titularCheckbox').prop('checked', false)
        $('#titularNumberDeDocumentoSelect').val('')
        $('#nombresTitularSelect').val('')
        $('#parentescoSelect').val('')
        $('#titularCheck').addClass('hide')
        
        $('#telefonoSelect').val('')
        $('#celUnoSelect').val('')
        $('#celDosSelect').val('')
        $('#correoSelect').val('')
        $('#domicilioSelect').val('')
        $('#distritoSelect').val('')
        $('#provinciaSelect').val('')
        $('#regionSelect').val('')
    }
};


let value, patientInfo;
$("#lookupPorTitular").select2({
    data:'busquedaPorTitularDDBB',
    minimumInputLength: 1,
    width: '100%',
    multiple:false,
    placeholder:"Seleccionar",
    debug: true,
    language: {
        inputTooShort: function() {
            return 'Digite al menos dos caracteres';
        }
    }
});
$("#dniInput").select2({
    ajax: { 
    url: "./mySQL/connect-pacientes-historial.php",
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
    minimumInputLength: 3,
    width: '100%',
    multiple:false,
    placeholder:"Seleccionar",
    debug: true,
    tags: true,
    minimumInputLength: 1,
    width: '100%',
    multiple:false,
    placeholder:"Buscar o Crear usuario",
    tags: true,
    debug: true,
    language: {
        inputTooShort: function() {
            return 'Digite al menos dos caracteres';
        }
    }
});
let counterToStopInfiniteLoop = 0
dniInput = document.querySelectorAll('span.select2-selection__rendered')
let currentId = 0
$( document ).ready(function() {
    dniInput[7].addEventListener('DOMSubtreeModified', (e)=>{
        if(counterToStopInfiniteLoop==0){
            input=dniInput[7]
            autofill(e.target)
        }
        
    })
});
let changeableInput
function autofill(input){
}
function cont(){
    // if(input==dniInput[5]){
        
    //     dniInput[6].innerHTML = value.dniNino
    // }else
     if(input==dniInput[7]){
        clearHistorialInputs(dniInput[7])
        // $('#dniInput').val(savedValue).trigger('change');
    }
    cambiarAGuardarCambios()
    $('#tipoDeDocumentoSelect').val(value.tipoDocumento)
    // if(value.tipoDocumento=='Ninguno'){
    //     $('#dniInput').val('').trigger('change')
    //     .prop('disabled', true).empty()
    // }
    $('#historialSelect').val(value.historial)
    $('#nombresSelect').val(value.nombres)
    $('#apellidoPaternoSelect').val(value.apellidoP)
    $('#apellidoMaternoSelect').val(value.apellidoM)
    $('#sexoSelect').val(value.sexo)
    $('#nacimientoSelect').val(value.DoB)
    $('#deudaSelect').val(value.deuda)
    $('#ocupacionSelect').val(value.ocupacion)
    $('#tipoSelect').val(value.tipo)
    $('#estadoCivilSelect').val(value.estadoCivil)
    if(value.DNItitular!='NaN'){
        if(value.DNItitular!=undefined){
            $('#titularCheckbox').prop('checked', true)
            $('#titularCheckbox').prop('disabled', true)
            $('#titularNumberDeDocumentoSelect').val(value.DNItitular)
            $('#nombresTitularSelect').val(value.Ntitular)
            $('#parentescoSelect').val(value.parentesco)
            $('#titularCheck').removeClass('hide')
        }
    }else{
        $('#titularCheckbox').prop('checked', false)
        $('#titularNumberDeDocumentoSelect').val('')
        $('#nombresTitularSelect').val('')
        $('#parentescoSelect').val('')
        $('#titularCheck').addClass('hide')
    }
    
    $('#telefonoSelect').val(value.telFijo)
    $('#celUnoSelect').val(value.celUno)
    $('#celDosSelect').val(value.celDos)
    $('#correoSelect').val(value.correo)
    $('#domicilioSelect').val(value.domicilio)
    $('#distritoSelect').val(value.distrito)
    $('#provinciaSelect').val(value.provincia)
    $('#regionSelect').val(value.region)

    counterToStopInfiniteLoop = 0
}
function clearHistorialInputs(input){
    if(input == dniInput[7]){
        $('#collapseOne').removeClass('show')
        $('#lookupPorTitular').val(null).trigger('change');
        dniInput[5].innerHTML = ''
    }else if(input == 'Limpiar-Campos-Btn'){
        $('#collapseOne').removeClass('show')
        $('#lookupPorTitular').val(null).trigger('change');
        $('#dniInput').val(null).trigger('change');
        $('#dniInput').empty();
    }
    $('#multipleChildrenSelect').prop('disabled', true).empty()
        .css({"background-color":"white","color":"black"})

    $('#tipoDeDocumentoSelect').val('1')
    $('#historialSelect').val('')
    $('#nombresSelect').val('')
    $('#apellidoPaternoSelect').val('')
    $('#apellidoMaternoSelect').val('')
    $('#sexoSelect').val('')
    $('#nacimientoSelect').val('')
    $('#deudaSelect').val('')
    $('#ocupacionSelect').val('')
    $('#tipoSelect').val('')
    $('#estadoCivilSelect').val('')
    
    $('#titularCheckbox').prop('checked', false)
    $('#titularNumberDeDocumentoSelect').val('')
    $('#nombresTitularSelect').val('')
    $('#parentescoSelect').val('')
    $('#titularCheck').addClass('hide')
    
    $('#telefonoSelect').val('')
    $('#celUnoSelect').val('')
    $('#celDosSelect').val('')
    $('#correoSelect').val('')
    $('#domicilioSelect').val('')
    $('#distritoSelect').val('')
    $('#provinciaSelect').val('')
    $('#regionSelect').val('')
}
$('#titularCheckbox').change(function() {
    if(this.checked) {
        $('#titularCheck').removeClass('hide')
        // $("#descuentoInput").prop('disabled', true);
        // $("#descuentoInput").removeClass('is-invalid')
    }else{
        $('#titularCheck').addClass('hide')
        // $("#descuentoInput").prop('disabled', false);
        // $('#descuentoInput')[5].value = ''
    }
})
$('#limpiar-campos').click(function(){
    clearHistorialInputs('Limpiar-Campos-Btn')
    cambiarARegistrar()
    $('#alerta').addClass('hide')
    $('#mensaje').addClass('hide')
})
function cambiarAGuardarCambios(){
    $('#saveBtn').removeClass('btn-success')
    $('#saveBtn').addClass('btn-primary')
    $('#saveBtn').html('Guardar Cambios')
}
function cambiarARegistrar(){
    $('#saveBtn').removeClass('btn-primary')
    $('#saveBtn').addClass('btn-success')
    $('#saveBtn').html('Registrar')
}
function desactivarAlerta(){
    $('#mensaje').removeClass('hide')
    $('#alerta').addClass('hide')
}
function activarAlerta(nombre){
    $('#mensaje').addClass('hide')
    $('#alerta').removeClass('hide')
    if(nombre!=undefined){
        $('#nombreUsuario').html(nombre)
    }
}
let textInputToFocus = document.querySelectorAll(".select2-selection__rendered");

$('#saveBtn').click(function(){
    // ready es para decir que estamos listos para continuar, si a continuación detectamos que algún campo obligatorio está vacío, 
    //cambiará ready de truthy a falsey para que no continuemos y adicionalmente resaltemos el campo obligatorio que necesita ser editado
    let ready = true
    // Revisar si campos obligatorios están vacíos
    // if($('#tipoDeDocumentoSelect option:selected').text()=='NINGUNO'){
    //     llenarCampo($('#tipoDeDocumentoSelect'))
    //     llenarCampo(dniInput[6])
    //     ready = false
    // }
    if($('#historialSelect').val()==''){
        llenarCampo($('#historialSelect'))
        ready = false
    }
    if($('#nombresSelect').val()==''){
        llenarCampo($('#nombresSelect'))
        ready = false
    }
    if($('#apellidoPaternoSelect').val()==''){
        llenarCampo($('#apellidoPaternoSelect'))
        ready = false
    }
    if($('#apellidoMaternoSelect').val()==''){
        llenarCampo($('#apellidoMaternoSelect'))
        ready = false
    }
    if($('#tipoSelect option:selected').text()==''||$('#tipoSelect option:selected').text()==''){
        llenarCampo($('#tipoSelect'))
        ready = false
    }
    if($('#sexoSelect option:selected').text()==''||$('#sexoSelect option:selected').text()==''){
        llenarCampo($('#sexoSelect'))
        ready = false
    }
    if($('#nacimientoSelect').val()==''){
        llenarCampo($('#nacimientoSelect'))
        ready = false
    }
    if($('#deudaSelect').val()==''){
        llenarCampo($('#deudaSelect'))
        ready = false
    }
    // if($('#ocupacionSelect').val()==''){
    //     llenarCampo($('#ocupacionSelect'))
    //     ready = false
    // }
    if($('#estadoCivilSelect option:selected').text()==''||$('#estadoCivilSelect option:selected').text()==''){
        llenarCampo($('#estadoCivilSelect'))
        ready = false
    }
    if($('#titularCheckbox').checked == true){
        llenarCampo($('#titularNumberDeDocumentoSelect'))
        llenarCampo($('#nombresTitularSelect'))
        llenarCampo($('#parentescoSelect'))
        ready = false
    }

    if(ready){
        let name = $('#nombresSelect').val() + ' ' + $('#apellidoPaternoSelect').val() + ' ' + $('#apellidoMaternoSelect').val()
        let dni = $('#select2-dniInput-container').html()
        
        getInputFieldData()
        console.log(patientInfo)
        $.ajax({ 
            url: "./mySQL/connect-editar_paciente.php",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: patientInfo,
            success: function(result){
                if(result=="RegistroOK"){
                    toastr["success"]("Usuario registrado exitósamente")
                }else if(result=="EdicionOK"){
                    toastr["success"]("Información editada exitósamente")
                }
                console.log(result)
            },
            error: function (request, status, error) {
                console.log(error)
                toastr["error"]("Error, no se pudo registrar al paciente, favor de contactarse con el administrador")
                return
            },
            cache: true
            })


        
        // debugger
        clearHistorialInputs('Limpiar-Campos-Btn')
        cambiarARegistrar()
        $('#alerta').addClass('hide')
        $('#mensaje').addClass('hide')
        $('#dniInput').val(null).trigger('change');
        if(areYouComingWithDataToAutofill){
            showContent('registrar_servicios')
            let nameInput = document.querySelectorAll('span.select2-selection__rendered')
            // debugger
            nameInput[0].innerHTML = name + ' ' + '-' + ' ' + dni
        }else{
            clearHistorialInputs('Limpiar-Campos-Btn')
        }
    }

    
})
function llenarCampo(selectInput){
    if(selectInput==dniInput[6]){
        selectInput.style.cssText = `
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(.375em + .1875rem) center;
        background-size: calc(.75em + .375rem) calc(.75em + .375rem);`
        selectInput.parentElement.style.border = 'red solid 1px'
        return
    }
    selectInput.addClass('is-invalid')
    selectInput.removeClass('is-warning')
}

$('#tipoDeDocumentoSelect').change(function(){
    if($(this).val()=='Ninguno'){
        $('#dniInput').val('').trigger('change')
        .prop('disabled', true).empty()
        $('#titularCheckbox').prop('disabled', true)
        .prop('checked', true)
        $('#titularCheck').removeClass('hide')
    }else{
        $('#dniInput').prop('disabled', false)
        $('#titularCheckbox').prop('disabled', false)
        .prop('checked', false)
        $('#titularCheck').addClass('hide')
    }
})

let areYouComingWithDataToAutofill
function loadFields(){
    areYouComingWithDataToAutofill = true
    function hasNumber(myString) {
        return /\d/.test(myString);
      }
    
    let retrievedName = localStorage.getItem('SavedName');
    if(retrievedName){
        retrievedName = JSON.parse(retrievedName)
        let result = hasNumber(retrievedName.DNI)
    // console.log(Object.keys(retrievedName).length)
        if(retrievedName.Names=="Nombres"){
            areYouComingWithDataToAutofill = false
        }else if(Object.keys(retrievedName).length==1){
            clearHistorialInputs('Limpiar-Campos-Btn')
            $('#nombresSelect').val(retrievedName.Names)
            // $('#apellidoPaternoSelect').val('')
            // $('#apellidoMaternoSelect').val('')
        }else if(Object.keys(retrievedName).length==2){
            clearHistorialInputs('Limpiar-Campos-Btn')
            $('#nombresSelect').val(retrievedName.Names)
            $('#apellidoPaternoSelect').val(retrievedName['First lastname'])
            // $('#apellidoMaternoSelect').val('')
        }else if(Object.keys(retrievedName).length>2){
            clearHistorialInputs('Limpiar-Campos-Btn')
            $('#nombresSelect').val(retrievedName.Names)
            $('#apellidoPaternoSelect').val(retrievedName['First lastname'])
            $('#apellidoMaternoSelect').val(retrievedName['Second lastname'])
        }else{
            dniInput[7].innerHTML=retrievedName.DNI
            $('#nombresSelect').val('')
            $('#apellidoPaternoSelect').val('')
            $('#apellidoMaternoSelect').val('')
        }
    }
}loadFields()
$('#historial-btn').click(function(){
    loadFields()
})

function getInputFieldData(){
    patientInfo = {}
    if(value) patientInfo.id=value.id
    patientInfo.tipoDocumento = $('#tipoDeDocumentoSelect').val()
    patientInfo.dni = dniInput[7].innerHTML
    patientInfo.historial = $('#historialSelect').val()
    patientInfo.nombres = $('#nombresSelect').val()
    patientInfo.apellidoP = $('#apellidoPaternoSelect').val()
    patientInfo.apellidoM = $('#apellidoMaternoSelect').val()
    patientInfo.sexo = $('#sexoSelect').val()
    patientInfo.nacimiento = $('#nacimientoSelect').val()
    patientInfo.deuda = $('#deudaSelect').val()
    patientInfo.ocupacion = $('#ocupacionSelect').val()
    patientInfo.tipo = $('#tipoSelect').val()
    patientInfo.estadoCivil = $('#estadoCivilSelect').val()

    patientInfo.telFijo = $('#telefonoSelect').val()
    patientInfo.celUno = $('#celUnoSelect').val()
    patientInfo.celDos = $('#celDosSelect').val()
    patientInfo.correo = $('#correoSelect').val()
    patientInfo.domicilio = $('#domicilioSelect').val()
    patientInfo.distrito = $('#distritoSelect').val()
    patientInfo.provincia = $('#provinciaSelect').val()
    patientInfo.region = $('#regionSelect').val()
}
let serviciosDDBB = [
    {id:0, servicio: "Servicio 1",categoria: "Categoría 1",precioUnitario:	10},
    {id:1, servicio: "Servicio 2",categoria: "Categoría 1",precioUnitario:	15},
    {id:2, servicio: "Servicio 1",categoria: "Categoría 2",precioUnitario:	20},
    {id:3, servicio: "Servicio 2",categoria: "Categoría 2",precioUnitario:	25},
    {id:4, servicio: "Servicio 1",categoria: "Categoría 3",precioUnitario:	30},
    {id:5, servicio: "Servicio 2",categoria: "Categoría 3",precioUnitario:	35}
]
let categoriasDDBB = [
    {id:0, name: ""},
    {id:1, name: "Categoría 1"},
    {id:2, name: "Categoría 2"},
    {id:3, name: "Categoría 3"},
    {id:999, name: "Crear nueva categoría"}
]

//Make teh checker select all or deselect all
$('#checkAll').change(function(){
    if($(this).prop('checked')!=true){
        $('#servicios-tbody' > $('.select-servicio').each(function(){
            $(this).prop('checked', false)
        }))
    }else{
        $('#servicios-tbody' > $('.select-servicio').each(function(){
            $(this).prop('checked', true)
        }))
    }
})

function loadServicios(){

    for(let i=0;i<serviciosDDBB.length;i++){
        
        $('#checkAll').prop('checked', false)
        let newTR = document.createElement('tr')
        let newTRdataset = document.createAttribute('dataset-id')
        newTRdataset.value = serviciosDDBB[i].id
        newTR.setAttributeNode(newTRdataset);

        let newTDcheck = document.createElement('td')
        newTDcheck.innerHTML = `<input type="checkbox" class="select-servicio">`
        newTR.appendChild(newTDcheck)

        let newTD1 = document.createElement('td')
        newTD1.id = 'row-servicio-name'
        newTD1.innerHTML = serviciosDDBB[i].servicio
        newTR.appendChild(newTD1)

        let newTD2 = document.createElement('td')
        newTD2.id = 'row-servicio-category'
        newTD2.innerHTML = serviciosDDBB[i].categoria
        newTR.appendChild(newTD2)

        let newTD3 = document.createElement('td')
        newTD3.id = 'row-servicio-price'
        newTD3.innerHTML = serviciosDDBB[i].precioUnitario
        newTR.appendChild(newTD3)

        let newTD4 = document.createElement('td')
        let precioUnitario = serviciosDDBB[i].precioUnitario
        let igv = precioUnitario * 0.18
        igv= igv.toFixed(4)
        igv = parseFloat(igv)
        newTD4.innerHTML = precioUnitario - igv
        newTR.appendChild(newTD4)

        let newTD5 = document.createElement('td')
        newTD5.innerHTML = `<button type="button" class="btn btn-outline-info btn-sm">editar</button>`
        newTR.appendChild(newTD5)

        let newTD6 = document.createElement('td')
        newTD6.innerHTML = `<button type="button" class="btn btn-outline-danger btn-sm">eliminar</button>`
        newTR.appendChild(newTD6)

        document.querySelector('#servicios-tbody').appendChild(newTR)
    }
}loadServicios()

$(document).ready( function () {
    $('#table_id').DataTable({
        "search": {
          "smart": false
        }
      });
} );

let rowsId
document.querySelector('#servicios-tbody').addEventListener('click', (e)=>{

    //Get id from the row from the button we clicked on
    rowsId = e.target.parentElement.parentElement.getAttribute('dataset-id')

    if(e.target.classList.contains("btn-outline-danger")){
          
        //Write servicio name to h3 element inside modal
        $('#modal-servicio-name').html(e.target.parentElement.parentElement.querySelector('#row-servicio-name').innerHTML)
        //show modal
        $("#modal-eliminar-servicio").modal()
    }else if(e.target.classList.contains("btn-outline-info")){ //clicked on edit
        loadCategorias()
        $("#modal-editar-o-crear").modal()
        //Fill servicio name input inside modal
        $('#modal-servicio-descripcion').val(e.target.parentElement.parentElement.querySelector('#row-servicio-name').innerHTML)
        $('#modal-servicio-categoria').val(e.target.parentElement.parentElement.querySelector('#row-servicio-category').innerHTML)
        $('#modal-servicio-precio').val(e.target.parentElement.parentElement.querySelector('#row-servicio-price').innerHTML)
        sinIGVfourDigits(e.target.parentElement.parentElement.querySelector('#row-servicio-price').innerHTML)
    }
})
function searchDDBB(DDBBid) {
    return DDBBid.id == rowsId;
    }
function findRow() {

    let result = serviciosDDBB.find(searchDDBB);
    return serviciosDDBB.indexOf(result);
}
function reloadTable(){
    let numberOfRowsToShow = $( "select[name*='table_id_length']" ).val()
    $('#table_id').DataTable().destroy();
    $("#servicios-tbody").empty();
    loadServicios()
    $('#table_id').DataTable({
        "search": {
          "smart": false
        },
        "pageLength": numberOfRowsToShow
      });
}
function deleteRow(){

    serviciosDDBB.splice(findRow(), 1);
    reloadTable()
    
}

$('#eleminar-servicio').click(function(){
    deleteRow()
    $("#modal-eliminar-servicio").modal('hide')
})
$('#modal-servicio-precio').keyup(function(){
    let currentInput = $(this).val()
    sinIGVfourDigits(currentInput)
})
function sinIGVfourDigits(valueToChange){
    let IGV = valueToChange*0.18
    valueToChange = valueToChange - IGV
    valueToChange = parseFloat(valueToChange).toFixed(4)
    $('#modal-precio-sin-IGV').html(valueToChange)
}
$('#modal-editar-o-crear-btn').click(function(){
    
    //get index of row we clicked on on the table
    let currentRowIndex = findRow()

    //Check if user is creating a category
    if($('#modal-servicio-categoria').is("input")){
        //IS creating. Search to see if category exists:

        function searchCategoryDDBB(DDBBid) {
            return DDBBid.name == $('#modal-servicio-categoria').val();
            }
        function findCategory() {
            return categoriasDDBB.find(searchCategoryDDBB);
        }
        //Check if category was found

        if(!findCategory()){ //Category was not found, create it!
            //insert at index, delete 0 (if left empty like below, it's as if you had put 0), item to insert
            let newItemToInsert = {id:categoriasDDBB[(categoriasDDBB.length-2)].id+1, name:$('#modal-servicio-categoria').val()}
            //insert it at the right place in the categoriasDDBB
            categoriasDDBB.splice((categoriasDDBB.length-1), 0, newItemToInsert)
        }else{//Category WAS found, Throw error!
            if($('#modal-servicio-categoria').html()==''){
                checkForEmptyFieldsEditarOcrear()
            }else{
                $('#categoría-lable').html('Esta categoría ya existe:')
                $('#categoría-lable').css({'color':'red'});
                return
            }
            
        }
    }

    //fill out the text fields and selects
    let shouldIcont = checkForEmptyFieldsEditarOcrear()
    if(shouldIcont=='stop'){
        return
    }
    if(currentRowIndex!=-1){
        serviciosDDBB[currentRowIndex].servicio = $('#modal-servicio-descripcion').val()
        serviciosDDBB[currentRowIndex].categoria = $('#modal-servicio-categoria').val()
        serviciosDDBB[currentRowIndex].precioUnitario = $('#modal-servicio-precio').val()
    }else{
        let newServicio = {id:serviciosDDBB[(serviciosDDBB.length-1)].id+1, servicio: $('#modal-servicio-descripcion').val(),categoria: $('#modal-servicio-categoria').val(),precioUnitario:$('#modal-servicio-precio').val()}
        serviciosDDBB.push(newServicio)
    }
    reloadTable()
    $("#modal-editar-o-crear").modal('hide')
}) 

function listenForCategoriasSelecChanges() {
    $('#modal-servicio-categoria').change(function(){
        if($(this).val()=='Crear nueva categoría'){
            let parent = $(this).parent()
            parent.empty()
            let newInput = `<input type="text" class="form-control " id="modal-servicio-categoria" placeholder='Llenar'>`
            parent.append(newInput)
            parent.append(`<button type="button" class="btn btn-outline-secondary btn-xs" id="cancel-categoria-creation">Cancelar</button>`)
            $('#cancel-categoria-creation').click(function(){
                revertCategoriaSelect()
            })
        }
    })
}listenForCategoriasSelecChanges()
function loadCategorias(){
    //If font color is red from error message, turn it back to black
    $('#categoría-lable').css({'color':'black'});
    //Also, make sure that the text isn't displaying the error, revert it!
    $('#categoría-lable').html('Categoría:')
    $('#modal-servicio-categoria').empty()
    let categorias = ""
    for(i=0;i<categoriasDDBB.length;i++){
        categorias = categorias + `<option value="`+ categoriasDDBB[i].name +`">`+ categoriasDDBB[i].name +`</option>`
    }
    $("#modal-servicio-categoria").append(categorias)
}
function revertCategoriaSelect(){
    $('#categoria-holder').empty()
    $('#categoria-holder').append(`<select type="text" class="form-control " id="modal-servicio-categoria" placeholder='Categoría'></select>`)
    loadCategorias()
    listenForCategoriasSelecChanges()
}
//make sure categorias input goes back to select when we hit cancel editing or creating
$('#cancelar-crear-o-editar').click(function(){
    revertCategoriaSelect()
})

//crear un nuevo servicio btn
$('#crear-servicio').click(function(){
    $("#modal-editar-o-crear").modal()
    //empty out every field
    $('#modal-servicio-descripcion').val('')
    loadCategorias()
    $('#modal-servicio-precio').val('')
    $('#modal-precio-sin-IGV').html(0)
})

//check all fields are filled
function checkForEmptyFieldsEditarOcrear(){
    let isEmpty = false
    if($('#modal-servicio-descripcion').val()==''){
        $('#modal-servicio-descripcion').css({'border-color':'red'})
        isEmpty=true
    }if($('#modal-servicio-categoria').val()==''){
        $('#modal-servicio-categoria').css({'border-color':'red'})
        isEmpty=true
    }if($('#modal-servicio-precio').val()==''){
        $('#modal-servicio-precio').css({'border-color':'red'})
        isEmpty=true
    }
    if(isEmpty){
        setListenerOnCreateOrEditServiceToRemoveAlert()
        return 'stop'
    }
}
function setListenerOnCreateOrEditServiceToRemoveAlert(){
    $('#modal-servicio-descripcion').keyup(function(){
        if(!$('#modal-servicio-descripcion').val()==''){
            $('#modal-servicio-descripcion').css({'border-color':'#ced4da'})
        }
    })
    if($('#modal-servicio-categoria').is("select")){
        $('#modal-servicio-categoria').change(function(){
            if(!$('#modal-servicio-categoria').val()==''){
                $('#modal-servicio-categoria').css({'border-color':'#ced4da'})
            }
        })
    }else{
        $('#modal-servicio-categoria').keyup(function(){
            if(!$('#modal-servicio-precio').val()==''){
                $('#modal-servicio-precio').css({'border-color':'#ced4da'})
            }
        })
    }
    $('#modal-servicio-precio').keyup(function(){
        if(!$('#modal-servicio-precio').val()==''){
            $('#modal-servicio-precio').css({'border-color':'#ced4da'})
        }
    })
    
}

$('#pos-or-neg').click(function(){
    let currentValue = $('#precio-masivo').val()
    currentValue>=0?currentValue=-Math.abs(currentValue):currentValue=Math.abs(currentValue)
    $('#precio-masivo').val(currentValue)
})

$('#apply-precio-masivo').click(function(){
    //revisar que campo no esté vacío
    if($('#precio-masivo').val()==''){
        toastr["error"]("No ha especificado monto")
        return
    }
    let nothingChecked = true
    $('#servicios-tbody').find( 'tr' ).each(function(){
        if($(this).find( 'input' ).is(':checked')){
            nothingChecked = false
            let currentIndex = $(this).attr('dataset-id')
            let precioParaAgregar = $('#precio-masivo').val()
            precioParaAgregar = parseFloat(precioParaAgregar)
            let precioActual = serviciosDDBB[currentIndex].precioUnitario
            precioActual = parseFloat(precioActual)
            serviciosDDBB[currentIndex].precioUnitario = precioActual + precioParaAgregar
        }
    })
    if(nothingChecked){
        toastr["error"]("No ha seleccionado ningún servicio")
        return
    }
    reloadTable()
    toastr["success"]("Se actualizaron las tarifas")
})

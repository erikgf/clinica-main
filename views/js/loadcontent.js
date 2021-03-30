function load_content(container,content,title){
    $('#datetimepicker4').datetimepicker('destroy');
    $('#datetimepicker4').removeClass('hasDatepicker');
    $('#customScriptsCont').empty()
    $('#'+container).load(content);
    $('h1.m-0').html(title)
    if(title == 'Bienvenido al registro de servicios'){
        $('#newScriptCont').empty();
        let myScript = document.createElement('script')
        myScript.setAttribute("src", "views/js/registroFunctions.js")
        document.body.appendChild(myScript)
        // $('#newScriptCont').append( "<script src=views/js/customFunctions.js defer></script>" )
    }else if(title == 'Ver, editar, o crear nuevo historial'){
        $('#newScriptCont').empty();
        let myScript = document.createElement('script')
        myScript.setAttribute("src", "views/js/historialFunctions.js")
        document.body.appendChild(myScript)
    }
}
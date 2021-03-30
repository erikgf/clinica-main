


<div class="card-body" style="max-width: 1250px;padding-left:0;">
            <div id="accordion">
                <!-- we are adding the .class so bootstrap.js collapse plugin detects it -->
                <div class="card card-primary">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" class="" aria-expanded="true" style="background-color: #007bff;color: #fff;border-radius: 5px 5px 0 0;">
                    <div class="card-header"> 
                        <h4 class="card-title">
                            Búsqueda por Titular
                        </h4>
                    </div>
                </a>
                <div id="collapseOne" class="panel-collapse in collapse" style="">
                <form role="form">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                            <div class="form-group">
                                <label>Tipo de Documento</label>
                                <select class="form-control">
                                <option value='1'>DNI</option>
                                <option value='2'>Pasaporte</option>
                                </select>
                            </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                <label>Número de Documento del Titular</label>
                                <select type="text" class="form-control" placeholder="Obligatorio" id="lookupPorTitular"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <div class="form-group">
                            <label>Resultado de búsqueda</label>
                            <select class="form-control" id="multipleChildrenSelect" disabled>
                            <option></option>
                            </select>
                        </div>
                    </div>
                </form>
                </div>
                </div>
                
        </div>
</div>
<!-- </div> -->
<div class="row hide" id="mensaje">
    <div class="col-sm-5" style="margin-bottom:40px">
        <div class="alert alert-success alert-dismissible">
            <!-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> -->
            <h5><i class="icon fas fa-check"></i>Creando un nuevo usuario</h5>
            Para buscar un usuario existente, escribir el número de identificación en el casillero de «Número de Documento»
            después de seleccionar el tipo de documento (DNI o pasaporte) o dirigirse a «Búsqueda por Titular» en la parte superior
        </div>
    </div>
</div>

<div class="row hide" id="alerta">
    <div class="col-sm-5" style="margin-bottom:40px">
        <div class="alert alert-warning alert-dismissible">
            <!-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> -->
            <h5><i class="icon fas fa-exclamation-triangle"></i>Editando la información del usuario <span id="nombreUsuario"></span></h5>
            Para crear un nuevo usuario, darle click al botón de limpiar campos
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-3" style="margin-bottom:40px">
        <button type="button" class="btn btn-block btn-secondary" id="limpiar-campos">Limpiar campos</button>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
    <div class="form-group">
        <label>Tipo de Documento (<span style="color:red">*</span>)</label>
        <select class="form-control uppercase" id="tipoDeDocumentoSelect" style="background-position: right calc(0.875em + .1875rem) center;">
        <!-- <option value="Ninguno">Ninguno</option> -->
        <option value='1'>DNI</option>
        <option value='2'>Pasaporte</option>
        </select>
    </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <label>Número de Documento (<span style="color:red">*</span>)</label>
            <select type="text" class="form-control uppercase" placeholder="Obligatorio" id="dniInput"></select>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
        <label>Número de Historial (<span style="color:red">*</span>)</label>
        <input type="text" class="form-control uppercase" placeholder="Obligatorio" id="historialSelect">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-3">
        <!-- text input -->
        <div class="form-group">
        <label>Nombres (<span style="color:red">*</span>)</label>
        <input type="text" class="form-control uppercase" placeholder="Obligatorio" id="nombresSelect">
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
        <label>Apellido Paterno (<span style="color:red">*</span>)</label>
        <input type="text" class="form-control uppercase" placeholder="Obligatorio" id="apellidoPaternoSelect">
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
        <label>Apellido Materno (<span style="color:red">*</span>)</label>
        <input type="text" class="form-control uppercase" placeholder="Obligatorio" id="apellidoMaternoSelect">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-3">
        <label>Sexo (<span style="color:red">*</span>)</label>
        <select class="form-control uppercase" id="sexoSelect" placeholder="Obligatorio" style="background-position: right calc(0.875em + .1875rem) center;">
            <option>Obligatorio</option>
            <option value="1">Masculino</option>
            <option value="2">Femenino</option>
        </select>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
        <label>Fecha de nacimiento (<span style="color:red">*</span>)</label>
        <input type="text" class="form-control uppercase" placeholder="Obligatorio" id="nacimientoSelect">
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
        <label>Saldo de deuda (<span style="color:red">*</span>)</label>
        <input type="text" class="form-control uppercase" placeholder="Obligatorio" id="deudaSelect">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-3">
        <div class="form-group">
            <div class="form-group">
                <label>Ocupación</label>
                <input type="text" class="form-control uppercase" placeholder="Opcional" id="ocupacionSelect">
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <label>Tipo de paciente (<span style="color:red">*</span>)</label>
            <select class="form-control uppercase" id="tipoSelect" placeholder="Obligatorio" style="background-position: right calc(0.875em + .1875rem) center;">
            <option>Obligatorio</option>
            <option>1</option>
            <option>2</option>
            </select>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <label>Estado Civil (<span style="color:red">*</span>)</label>
            <select class="form-control uppercase"id="estadoCivilSelect" placeholder="Obligatorio" style="background-position: right calc(0.875em + .1875rem) center;">
            <option>Obligatorio</option>
            <option value="1">Soltero</option>
            <option value="2">Conviviente</option>
            <option value="3">Casado</option>
            <option value="4">Divorciado</option>
            <option value="5">Viudo</option>
            </select>
        </div>
    </div>
</div>
<!-- <br> -->
<div class="form-check" style="display:none">
    <input class="form-check-input uppercase" type="checkbox" id="titularCheckbox">
    <label style="font-size:16px">¿Posee titular?</label>
</div>
<!-- <br> -->

<div class="row hide" id="titularCheck" style="display:none">
    <div class="col-sm-3">
        <!-- text input -->
        <div class="form-group">
        <label>Número de documento del Titular (<span style="color:red">*</span>)</label>
        <input type="text" class="form-control uppercase" placeholder="Obligatorio" id="titularNumberDeDocumentoSelect">
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
        <label>Nombres y apellidos del Titular (<span style="color:red">*</span>)</label>
        <input type="text" class="form-control uppercase" placeholder="Obligatorio" id="nombresTitularSelect">
        </div>
    </div>
    <div class="col-sm-3" style="display:none">
        <div class="form-group">
            <label>Parentesco (<span style="color:red">*</span>)</label>
            <select class="form-control uppercase" id="parentescoSelect" style="background-position: right calc(0.875em + .1875rem) center;">
                <option>Seleccionar</option>
                <option>Parentesco 1</option>
                <option>Parentesco 2</option>
                <option>Parentesco 3</option>
            </select>
        </div>
    </div>
</div>
<!-- <br> -->
<div class="row">
    <div class="col-sm-3">
        <!-- text input -->
        <div class="form-group">
        <label>Teléfono fijo</label>
        <input type="text" class="form-control uppercase" placeholder="Opcional" id="telefonoSelect">
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
        <label>Celular uno</label>
        <input type="text" class="form-control uppercase" placeholder="Opcional" id="celUnoSelect">
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
        <label>Celular dos</label>
        <input type="text" class="form-control uppercase" placeholder="Opcional" id="celDosSelect">
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-3">
        <!-- text input -->
        <div class="form-group">
        <label>Correo</label>
        <input type="text" class="form-control" placeholder="Opcional" id="correoSelect">
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
        <label>Domicilio</label>
        <input type="text" class="form-control uppercase" placeholder="Opcional" id="domicilioSelect">
        </div>
    </div>

    <div class="col-sm-3">
        <div class="form-group">
            <label>Región</label>
            <select class="form-control uppercase" id="regionSelect" placeholder="Opcional">
            <option>Opcional</option>
            <option value="1">1</option>
            <option value="2">2</option>
            </select>
        </div>
    </div>
    
</div>

<div class="row">
    <div class="col-sm-3">
        <div class="form-group">
            <label>Provincia</label>
            <select class="form-control uppercase" id="provinciaSelect" placeholder="Opcional">
            <option>Opcional</option>
            <option value="1">1</option>
            <option value="2">2</option>
            </select>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <label>Distrito</label>
            <select class="form-control uppercase" id="distritoSelect" placeholder="Opcional">
            <option>Opcional</option>
            <option value="1">1</option>
            <option value="2">2</option>
            </select>
        </div>
    </div>
    
    
</div>

<div class="row" style="margin-top:40px;margin-bottom:40px;">
    <div class="col-lg-10 col-xl-4">
        <button type="button" class="btn btn-danger btn-lg" style="width:200px; margin-right:20px" id="deleteBtn">Eliminar</button>
        <button type="button" class="btn btn-success btn-lg" style="width:200px" id="saveBtn">Registrar</button>
    </div>
</div>
<script src="views/js/historialFunctions.js"></script>
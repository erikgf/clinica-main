<div class="card">
    <div class="card-header">
        <h3 class="card-title">Paciente</h3>
    </div>
    <div class="card-body">
        <div class="card-body hide" style="max-width: 1250px;padding-left:0;">
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
            <div class="col-sm-12">
                <div class="alert alert-success alert-dismissible">
                    <!-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> -->
                    <h5><i class="icon fas fa-check"></i>Creando un nuevo usuario</h5>
                    Para buscar un usuario existente, escribir el número de identificación en el casillero de «Número de Documento»
                    después de seleccionar el tipo de documento (DNI o pasaporte) o dirigirse a «Búsqueda por Titular» en la parte superior
                </div>
            </div>
        </div>

        <div class="row hide" id="alerta">
            <div class="col-sm-12">
                <div class="alert alert-warning alert-dismissible">
                    <!-- <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> -->
                    <h5><i class="icon fas fa-exclamation-triangle"></i>Editando la información del usuario <span id="nombreUsuario"></span></h5>
                    Para crear un nuevo usuario, darle click al botón "LIMPIAR CAMPOS"
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3" style="margin-bottom:40px">
                <button type="button" class="btn btn-block btn-secondary" id="limpiar-campos">LIMPIAR CAMPOS</button>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2">
                <div class="form-group">
                    <label>Tipo de Documento (<span style="color:red">*</span>)</label>
                    <select class="form-control uppercase" id="tipoDeDocumentoSelect" style="background-position: right calc(0.875em + .1875rem) center;">
                        <option value='1'>DNI</option>
                        <option value='2'>Pasaporte</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label>Número de Documento (<span style="color:red">*</span>)</label>
                    <select type="text" class="form-control uppercase" id="dniInput"></select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-4">
                <!-- text input -->
                <div class="form-group">
                    <label>Nombres (<span style="color:red">*</span>)</label>
                    <input type="text" class="form-control uppercase" id="nombresSelect">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Apellido Paterno (<span style="color:red">*</span>)</label>
                    <input type="text" class="form-control uppercase" id="apellidoPaternoSelect">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Apellido Materno (<span style="color:red">*</span>)</label>
                    <input type="text" class="form-control uppercase" id="apellidoMaternoSelect">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-2">
                <label>Sexo (<span style="color:red">*</span>)</label>
                <select class="form-control uppercase" id="sexoSelect"  style="background-position: right calc(0.875em + .1875rem) center;">
                    <option value="">Seleccionar</option>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                </select>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                <label>Fecha de Nac. (<span style="color:red">*</span>)</label>
                <input type="date" class="form-control uppercase" required id="nacimientoSelect">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <div class="form-group">
                        <label>Ocupación</label>
                        <input type="text" class="form-control uppercase" required id="ocupacionSelect">
                    </div>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label>Tipo de paciente (<span style="color:red">*</span>)</label>
                    <select class="form-control uppercase" required id="tipoSelect" style="background-position: right calc(0.875em + .1875rem) center;">
                        <option value="1">PARTICULAR</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <label>Estado Civil (<span style="color:red">*</span>)</label>
                    <select class="form-control uppercase" id="estadoCivilSelect" required style="background-position: right calc(0.875em + .1875rem) center;">
                        <option value="">Seleccionar</option>
                        <option value="1">SOLTERO</option>
                        <option value="2">CASADO</option>
                        <option value="3">VIUDO</option>
                        <option value="4">DIVORCIADO</option>
                        <option value="5">CONVIVIENTE</option>
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
            <div class="col-sm-2">
                <!-- text input -->
                <div class="form-group">
                <label>Número de documento del Titular (<span style="color:red">*</span>)</label>
                <input type="text" class="form-control uppercase" placeholder="Obligatorio" id="titularNumberDeDocumentoSelect">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                <label>Nombres y apellidos del Titular (<span style="color:red">*</span>)</label>
                <input type="text" class="form-control uppercase" placeholder="Obligatorio" id="nombresTitularSelect">
                </div>
            </div>
            <div class="col-sm-3" style="display:none">
                <div class="form-group">
                    <label>Parentesco (<span style="color:red">*</span>)</label>
                    <select class="form-control uppercase" id="parentescoSelect" style="background-position: right calc(0.875em + .1875rem) center;">
                        <option value="">Seleccionar</option>
                        <option>Parentesco 1</option>
                        <option>Parentesco 2</option>
                        <option>Parentesco 3</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-sm-3">
                <!-- text input -->
                <div class="form-group">
                <label>Correo</label>
                <input type="email" class="form-control"  id="correoSelect">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                <label>Domicilio</label>
                <input type="text" class="form-control uppercase" id="domicilioSelect">
                </div>
            </div>
            
        </div>

        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Región</label>
                    <select class="form-control uppercase" id="regionSelect">
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Provincia</label>
                    <select class="form-control uppercase" id="provinciaSelect">
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Distrito</label>
                    <select class="form-control uppercase" id="distritoSelect">
                    </select>
                </div>
            </div>

        </div>

        <!-- <br> -->
        <div class="row">
            <div class="col-sm-2">
                <!-- text input -->
                <div class="form-group">
                <label>Teléfono fijo</label>
                <input type="cel" class="form-control uppercase" id="telefonoSelect">
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                <label>Celular 1</label>
                <input type="cel" class="form-control uppercase" id="celUnoSelect">
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                <label>Celular 2</label>
                <input type="cel" class="form-control uppercase" id="celDosSelect">
                </div>
            </div>
        </div>

        <div class="row form-group">
            <div class="offset-6 col-sm-3">
                <button type="button" class="btn btn-lg btn-danger btn-block">ELIMINAR</button>
            </div>
            <div class="col-sm-3">
                <button type="button" id="saveBtn" class="btn btn-lg btn-success btn-block">REGISTRAR</button>
            </div>
        </div>
   
    </div>
</div>
<script type="text/javascript"  src="views/js/frmRegistroPaciente.js"></script>
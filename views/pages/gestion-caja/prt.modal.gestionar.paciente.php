<div class="modal fade" id="mdl-paciente"  role="dialog" aria-labelledby="mdl-pacientelabel">
    <div class="modal-dialog modal-xl" role="document">
        <form class="modal-content" id="frm-paciente">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-pacientelabel">Gestionar Paciente</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <input type="hidden" id="txt-pacienteseleccionado">
                        <!--
                        <div class="card-body hide" style="max-width: 1250px;padding-left:0;">
                                    <div id="accordion">
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
                        -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Buscar Paciente <i class="fa fa-search"></i></label>
                                    <select class="form-control uppercase" id="txt-buscarpaciente" style="background-position: right calc(0.875em + .1875rem) center;">
                                        <option value=''>Buscar...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="row" style="display:none" id="blk-alertaredicion">
                            <div class="col-sm-12">
                                <div class="alert alert-warning alert-dismissible">
                                    <h5><i class="icon fas fa-exclamation-triangle"></i>Editando la información del usuario <b id="lbl-nombreusuario"></b></h5>
                                    Para crear un nuevo usuario, darle click al botón "LIMPIAR CAMPOS"
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tipo de Documento</label>
                                    <select class="form-control uppercase" name="txt-tipodocumento" required id="txt-tipodocumento" style="background-position: right calc(0.875em + .1875rem) center;">
                                        <option value='0'>SIN DOC</option>
                                        <option value='1' selected>DNI</option>
                                        <option value='4'>CARNET EXTRANJERÍA</option>
                                        <option value='6'>RUC</option>
                                        <option value='7'>PASAPORTE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Número de Documento (<span style="color:red">*</span>)</label>
                                    <input type="text" name="txt-numerodocumento" class="form-control uppercase" id="txt-numerodocumento" required maxlength="15"/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Nombres (<span style="color:red">*</span>)</label>
                                    <input type="text" class="form-control uppercase" required id="txt-nombres">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Apellido Paterno (<span style="color:red">*</span>)</label>
                                    <input type="text" class="form-control uppercase" required id="txt-apellidospaterno">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Apellido Materno (<span style="color:red">*</span>)</label>
                                    <input type="text" class="form-control uppercase" required id="txt-apellidosmaterno">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <label>Sexo (<span style="color:red">*</span>)</label>
                                <select class="form-control uppercase" id="txt-sexo"  style="background-position: right calc(0.875em + .1875rem) center;">
                                    <option value="">Seleccionar</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                <label>Fecha de Nac. (<span style="color:red">*</span>)</label>
                                <input type="date" class="form-control uppercase" required id="txt-fechanacimiento">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label>Ocupación</label>
                                        <input type="text" class="form-control uppercase" name="txt-ocupacion" id="txt-ocupacion">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Tipo de paciente (<span style="color:red">*</span>)</label>
                                    <select class="form-control uppercase" required id="txt-tipopaciente" style="background-position: right calc(0.875em + .1875rem) center;">
                                        <option value="1">PARTICULAR</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Estado Civil</label>
                                    <select class="form-control uppercase" id="txt-estadocivil" style="background-position: right calc(0.875em + .1875rem) center;">
                                        <option value="1">SOLTERO</option>
                                        <option value="2">CASADO</option>
                                        <option value="3">VIUDO</option>
                                        <option value="4">DIVORCIADO</option>
                                        <option value="5">CONVIVIENTE</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!--
                        <div class="form-check" style="display:none">
                            <input class="form-check-input uppercase" type="checkbox" id="titularCheckbox">
                            <label style="font-size:16px">¿Posee titular?</label>
                        </div>

                        <div class="row hide" id="titularCheck" style="display:none">
                            <div class="col-sm-2">
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
                        -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                <label>Correo</label>
                                <input type="email" class="form-control"  id="txt-correo">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                <label>Domicilio</label>
                                <input type="text" class="form-control uppercase" id="txt-domicilio">
                                </div>
                            </div>
                            
                        </div>

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Departamento</label>
                                    <select class="form-control uppercase select2" id="txt-departamento">
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Provincia</label>
                                    <select class="form-control uppercase select2" id="txt-provincia">
                                    </select>
                                </div>
                                </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Distrito</label>
                                    <select class="form-control uppercase select2" id="txt-distrito">
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
                                <input type="tel" class="form-control uppercase" id="txt-telefonofijo">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                <label>Celular 1</label>
                                <input type="tel" class="form-control uppercase" id="txt-celular1">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                <label>Celular 2</label>
                                <input type="tel" class="form-control uppercase" id="txt-celular2">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-left">
                    <input type="checkbox" id="chk-asignarregistro"> Asignar a la atención tras GUARDAR.
                </div>

                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-secondary" id="btn-limpiar">LIMPIAR CAMPOS</button>
                <button type="button" class="btn btn-danger" id="btn-eliminar" style="display:none">ELIMINAR</button>
                <button type="button" class="btn btn-success" id="btn-guardar">GUARDAR</button>
            </div>
        </div>
    </div>
</div>



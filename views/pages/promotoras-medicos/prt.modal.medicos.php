<div class="modal fade" id="mdl-medico"  role="dialog" aria-labelledby="mdl-medicolabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" id="frm-medico">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-medicolabel">Gestionar Médico</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <input type="hidden" id="txt-medico-seleccionado">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-medico-numerodocumento">Número de Documento</label>
                                    <input type="text" class="form-control uppercase" id="txt-medico-numerodocumento" maxlength="11"/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="txt-medico-apellidosnombres">Apellidos y Nombres (<span style="color:red">*</span>)</label>
                                    <input type="text" class="form-control uppercase" required id="txt-medico-apellidosnombres">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-medico-colegiatura">Colegiatura</label>
                                    <input type="text" class="form-control uppercase" id="txt-medico-colegiatura">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-medico-rne">RNE</label>
                                    <input type="text" class="form-control uppercase" id="txt-medico-rne">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-medico-telefonouno">Teléfono 1</label>
                                    <input type="text" class="form-control uppercase" id="txt-medico-telefonouno" maxlength="9">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-medico-telefonodos">Teléfono 2</label>
                                    <input type="text" class="form-control uppercase" id="txt-medico-telefonodos" maxlength="9">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-medico-fechanacimiento">Cumpleaños</label>
                                    <input type="date" class="form-control"  id="txt-medico-fechanacimiento" style="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-medico-correo">Correo</label>
                                    <input type="email" class="form-control"  id="txt-medico-correo" style="">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txt-medico-domicilio">Domicilio</label>
                                    <textarea class="form-control uppercase"  id="txt-medico-domicilio"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-medico-sede">Sede (<span style="color:red">*</span>)</label>
                                    <select type="text" class="form-control" required id="txt-medico-sede">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-medico-especialidad">Especialidad</label>
                                    <select type="text" class="form-control uppercase" id="txt-medico-especialidad">
                                        <option value="1">GENÉRICA</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="txt-medico-promotora">Promotora</label>
                                    <select type="text" class="form-control uppercase" id="txt-medico-promotora"></select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-medico-tipomedico">Tipo Personal Médico</label>
                                    <select type="text" class="form-control uppercase" id="txt-medico-tipomedico">
                                        <option selected value="0">MÉDICO</option>
                                        <option value="1">TECNÓLOGO MÉDICO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="txt-medico-esinformante">¿Es Informante?</label>
                                    <select type="text" class="form-control uppercase" id="txt-medico-esinformante">
                                        <option value="1">INFORMANTE</option>
                                        <option selected value="0">NO INFORMANTE</option>
                                        <option value="2">AMBOS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="txt-medico-esrealizante">¿Es Realizante?</label>
                                    <select type="text" class="form-control uppercase" id="txt-medico-esrealizante">
                                        <option value="1">REALIZANTE</option>
                                        <option selected value="0">NO REALIZANTE</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-medico-puedetenerusuario">¿Puede tener usuario sistema?</label>
                                    <select type="text" class="form-control" id="txt-medico-puedetenerusuario">
                                        <option selected value="0">NO</option>
                                        <option value="1">SÍ</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="txt-medico-observaciones">Observaciones</label>
                                    <textarea class="form-control uppercase" id="txt-medico-observaciones"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-danger" id="btn-medico-eliminar" style="display:none">ELIMINAR</button>
                <button type="button" class="btn btn-success" id="btn-medico-guardar">GUARDAR</button>
            </div>
        </div>
    </div>
</div>



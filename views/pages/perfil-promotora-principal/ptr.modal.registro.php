<div class="modal fade" id="mdl-medico"  role="dialog" aria-labelledby="mdl-medicolabel">
    <div class="modal-dialog modal-lg" role="document">
        <form class="modal-content" id="frm-medico">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-medicolabel">Gestionar Médico</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <input type="hidden" name="id_medico_seleccionado" id="txt-medico-seleccionado">
                        <div class="row">
                            <!--
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-medico-numerodocumento">Núm. Documento (<span style="color:red">*</span>)</label>
                                    <input type="text" required name="numero_documento" class="form-control uppercase" id="txt-medico-numerodocumento" maxlength="11"/>
                                </div>
                            </div>
                            -->
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-medico-colegiatura">Colegiatura (<span style="color:red">*</span>)</label>
                                    <input type="text" required name="colegiatura" class="form-control uppercase" id="txt-medico-colegiatura">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-medico-fn">Día/Mes Nacimiento (<span style="color:red">*</span>)</label>
                                    <input type="date" required name="fecha_nacimiento" class="form-control uppercase" id="txt-medico-fn">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="txt-medico-especialidad">Especialidad (<span style="color:red">*</span>)</label>
                                    <select  name="especialidad" required class="form-control uppercase especialidad" id="txt-medico-especialidad"></select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="txt-medico-apellidosnombres">Apellidos y Nombres (<span style="color:red">*</span>)</label>
                                    <input type="text" required name="apellidos_nombres" class="form-control uppercase" required id="txt-medico-apellidosnombres">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="txt-medico-celular">Celular</label>
                                    <input type="text" name="celular"  class="form-control uppercase celular" id="txt-medico-celular"/>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="txt-medico-direccion">Dirección</label>
                                    <textarea  name="direccion"  class="form-control uppercase direccion" id="txt-medico-direccion"></textarea>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-medico-sede">Sede  (<span style="color:red">*</span>)</label>
                                    <select  name="sede"  class="form-control uppercase sede" required ="txt-medico-sede"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="submit" class="btn btn-success on-medico-guardar">GUARDAR</button>
            </div>
        </form>
    </div>
</div>



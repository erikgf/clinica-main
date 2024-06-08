<div class="modal fade" id="mdl-colaborador"  role="dialog" aria-labelledby="mdl-colaboradorlabel">
    <div class="modal-dialog modal-lg" role="document">
        <form class="modal-content" id="frm-colaborador">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-colaboradorlabel">Gestionar colaborador</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt-colaborador-seleccionado">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="txt-colaborador-tipodocumento">Tipo Doc. (<span style="color:red">*</span>)</label>
                            <select id="txt-colaborador-tipodocumento" class="form-control">
                                <option selected value="1" selected>DNI</option>
                                <option value="4">CARNÉ EXTRANJERÍA</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="txt-colaborador-numerodocumento">Número Doc. (<span style="color:red">*</span>)</label>
                            <input type="text" class="form-control" minlength="8" maxlength="8" required id="txt-colaborador-numerodocumento"/>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="txt-colaborador-nombres">Nombres (<span style="color:red">*</span>)</label>
                            <input type="text" class="form-control uppercase" required id="txt-colaborador-nombres"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="txt-colaborador-apellidopaterno">Ap. Paterno (<span style="color:red">*</span>)</label>
                            <input type="text" class="form-control uppercase" required id="txt-colaborador-apellidopaterno"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="txt-colaborador-apellidomaterno">Ap. Materno (<span style="color:red">*</span>)</label>
                            <input type="text" class="form-control uppercase" required id="txt-colaborador-apellidomaterno"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="txt-colaborador-correo">Correo</label>
                            <input type="email" class="form-control"  id="txt-colaborador-correo"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="txt-colaborador-celular">Celular</label>
                            <input type="tel" class="form-control"  id="txt-colaborador-celular"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="txt-colaborador-idrol">Rol</label>
                            <select id="txt-colaborador-idrol" class="form-control" required></select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="chk-colaborador-accesosistema">¿Acceso Sistema?</label>
                            <input type="checkbox"  id="chk-colaborador-accesosistema"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-danger" id="btn-colaborador-eliminar" style="display:none">ELIMINAR</button>
                <button type="button" class="btn btn-success" id="btn-colaborador-guardar">GUARDAR</button>
            </div>
        </form>
    </div>
</div>



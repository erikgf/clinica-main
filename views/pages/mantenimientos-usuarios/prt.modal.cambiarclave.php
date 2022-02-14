<div class="modal fade" id="mdl-colaboradorcambiarclave"  colaboradorcambiarclavecambiarclavee="dialog" aria-labelledby="mdl-colaboradorcambiarclavelabel">
    <div class="modal-dialog" colaboradorcambiarclavee="document">
        <form class="modal-content" id="frm-colaboradorcambiarclave">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-colaboradorcambiarclavelabel">Cambiar Clave Colaborador</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt-colaboradorcambiarclave-seleccionado">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="txt-colaboradorcambiarclave-nombres">Nombres y Apellidos</label>
                            <input type="text" class="form-control uppercase" readonly id="txt-colaboradorcambiarclave-nombres"/>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="txt-colaboradorcambiarclave-clave">Nueva Clave (<span style="color:red">*</span>)</label>
                            <input type="password" class="form-control" minlength="6" required id="txt-colaboradorcambiarclave-clave"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-success" id="btn-colaboradorcambiarclave-guardar">GUARDAR</button>
            </div>
        </form>
    </div>
</div>



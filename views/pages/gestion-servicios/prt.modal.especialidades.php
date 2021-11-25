<div class="modal fade" id="mdl-especialidad"  role="dialog" aria-labelledby="mdl-especialidadlabel">
    <div class="modal-dialog modal-xl" role="document">
        <form class="modal-content" id="frm-especialidad">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-especialidadlabel">Gestionar Especialidad</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt-especialidad-seleccionado">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="txt-especialidad-descripcion">Descripción (<span style="color:red">*</span>)</label>
                            <input type="text" class="form-control uppercase" required id="txt-especialidad-descripcion"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-danger" id="btn-especialidad-eliminar" style="display:none">ELIMINAR</button>
                <button type="button" class="btn btn-success" id="btn-especialidad-guardar">GUARDAR</button>
            </div>
        </form>
    </div>
</div>



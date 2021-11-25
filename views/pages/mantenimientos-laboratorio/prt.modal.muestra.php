<div class="modal fade" id="mdl-muestra"  role="dialog" aria-labelledby="mdl-muestralabel">
    <div class="modal-dialog" role="document">
        <form class="modal-content" id="frm-muestra">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-muestralabel">Gestionar Muestra</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt-muestra-seleccionado">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="txt-muestra-descripcion">Descripción (<span style="color:red">*</span>)</label>
                            <input type="text" class="form-control uppercase" required id="txt-muestra-descripcion"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-danger" id="btn-muestra-eliminar" style="display:none">ELIMINAR</button>
                <button type="button" class="btn btn-success" id="btn-muestra-guardar">GUARDAR</button>
            </div>
        </form>
    </div>
</div>



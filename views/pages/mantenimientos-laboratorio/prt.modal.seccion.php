<div class="modal fade" id="mdl-seccion"  role="dialog" aria-labelledby="mdl-seccionlabel">
    <div class="modal-dialog " role="document">
        <form class="modal-content" id="frm-seccion">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-seccionlabel">Gestionar Sección</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt-seccion-seleccionado">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="txt-seccion-descripcion">Descripción (<span style="color:red">*</span>)</label>
                            <input type="text" class="form-control uppercase" required id="txt-seccion-descripcion"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-danger" id="btn-seccion-eliminar" style="display:none">ELIMINAR</button>
                <button type="button" class="btn btn-success" id="btn-seccion-guardar">GUARDAR</button>
            </div>
        </form>
    </div>
</div>



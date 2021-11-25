<div class="modal fade" id="mdl-metodo"  role="dialog" aria-labelledby="mdl-metodolabel">
    <div class="modal-dialog" role="document">
        <form class="modal-content" id="frm-metodo">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-metodolabel">Gestionar Método</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt-metodo-seleccionado">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="txt-metodo-descripcion">Descripción (<span style="color:red">*</span>)</label>
                            <input type="text" class="form-control " required id="txt-metodo-descripcion"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-danger" id="btn-metodo-eliminar" style="display:none">ELIMINAR</button>
                <button type="button" class="btn btn-success" id="btn-metodo-guardar">GUARDAR</button>
            </div>
        </form>
    </div>
</div>



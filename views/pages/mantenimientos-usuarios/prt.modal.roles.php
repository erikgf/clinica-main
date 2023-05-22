<div class="modal fade" id="mdl-rol"  role="dialog" aria-labelledby="mdl-rollabel">
    <div class="modal-dialog modal-xl" role="document">
        <form class="modal-content" id="frm-rol">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-rollabel">Gestionar Rol</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt-rol-seleccionado">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="txt-rol-descripcion">Descripción (<span style="color:red">*</span>)</label>
                            <input type="text" class="form-control uppercase" required id="txt-rol-descripcion"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" >
                        <h6>Permisos Disponibles </h6>
                        <div class="row" id="blk-interfaces">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-success" id="btn-rol-guardar">GUARDAR</button>
            </div>
        </form>
    </div>
</div>



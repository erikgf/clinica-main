<div class="modal fade" id="mdl-promotora"  role="dialog" aria-labelledby="mdl-promotoralabel">
    <div class="modal-dialog modal-xl" role="document">
        <form class="modal-content" id="frm-promotora">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-promotoralabel">Gestionar Promotora</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt-promotora-seleccionado">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="txt-promotora-numerodocumento">Número de Documento</label>
                            <input type="text" class="form-control uppercase" id="txt-promotora-numerodocumento" maxlength="15"/>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group">
                            <label for="txt-promotora-descripcion">Descripción (<span style="color:red">*</span>)</label>
                            <input type="text" class="form-control uppercase" required id="txt-promotora-descripcion">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="txt-promotora-comision">Comisión</label>
                            <input type="number" required step="0.01" class="form-control uppercase" id="txt-promotora-comision"/>
                        </div>
                    </div>
                </div>

                <div class="row">
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-danger" id="btn-promotora-eliminar" style="display:none">ELIMINAR</button>
                <button type="button" class="btn btn-success" id="btn-promotora-guardar">GUARDAR</button>
            </div>
        </form>
    </div>
</div>



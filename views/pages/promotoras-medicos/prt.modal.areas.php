<div class="modal fade" id="mdl-area"  role="dialog" aria-labelledby="mdl-arealabel">
    <div class="modal-dialog modal-xl" role="document">
        <form class="modal-content" id="frm-area">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-arealabel">Gestionar Área</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt-area-seleccionado">
                <div class="row">
                    <div class="col-md-9 col-sm-7">
                        <div class="form-group">
                            <label for="txt-area-descripcion">Descripción (<span style="color:red">*</span>)</label>
                            <input type="text" class="form-control uppercase" required id="txt-area-descripcion"/>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-5">
                        <h5>Comisiones Por Sede</h5>
                        <div id="blk-area-comisiones"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-danger" id="btn-area-eliminar" style="display:none">ELIMINAR</button>
                <button type="button" class="btn btn-success" id="btn-area-guardar">GUARDAR</button>
            </div>
        </form>
    </div>
</div>



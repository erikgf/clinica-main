<div class="modal fade" id="mdl-promotora"  role="dialog" aria-labelledby="mdl-promotoralabel">
    <div class="modal-dialog modal-lg" role="document">
        <form class="modal-content" id="frm-promotora">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-promotoralabel">Gestionar promotora</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt-promotora-seleccionado">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="txt-promotora-numerodocumento">Número Doc. (<span style="color:red">*</span>)</label>
                            <input type="text" class="form-control" length="15" readonly required id="txt-promotora-numerodocumento"/>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="txt-promotora-nombres">Nombres / Razón Social(<span style="color:red">*</span>)</label>
                            <input type="text" class="form-control uppercase" readonly required id="txt-promotora-nombres"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="chk-promotora-accesosistema">¿Acceso Sistema?</label>
                            <input type="checkbox"  id="chk-promotora-accesosistema"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-success" id="btn-promotora-guardar">GUARDAR</button>
            </div>
        </form>
    </div>
</div>



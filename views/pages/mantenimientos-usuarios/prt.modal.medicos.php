<div class="modal fade" id="mdl-medico"  role="dialog" aria-labelledby="mdl-medicolabel">
    <div class="modal-dialog modal-lg" role="document">
        <form class="modal-content" id="frm-medico">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-medicolabel">Gestionar medico</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt-medico-seleccionado">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="txt-medico-numerodocumento">Número Doc. (<span style="color:red">*</span>)</label>
                            <input type="text" class="form-control" length="15" readonly required id="txt-medico-numerodocumento"/>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="txt-medico-nombres">Nombres y Apellidos(<span style="color:red">*</span>)</label>
                            <input type="text" class="form-control uppercase" readonly required id="txt-medico-nombres"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="chk-medico-accesosistema">¿Acceso Sistema?</label>
                            <input type="checkbox"  id="chk-medico-accesosistema"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-success" id="btn-medico-guardar">GUARDAR</button>
            </div>
        </form>
    </div>
</div>
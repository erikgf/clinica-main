<div class="modal fade" id="mdl-empresaconvenio"  role="dialog" aria-labelledby="mdl-empresaconveniolabel">
    <div class="modal-dialog modal-lg" role="document">
        <form class="modal-content" id="frm-empresaconvenio">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-empresaconveniolabel">Gestionar Empresaconvenio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt-empresaconvenio-seleccionado">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="txt-empresaconvenio-numerodocumento">Número Documento (RUC) (<span style="color:red">*</span>)</label>
                            <input maxlength="11" type="text" class="form-control " required id="txt-empresaconvenio-numerodocumento"/>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="txt-empresaconvenio-razonsocial">Razón Social (<span style="color:red">*</span>)</label>
                            <input type="text" class="form-control " required id="txt-empresaconvenio-razonsocial"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="txt-empresaconvenio-mensajeticket">Mensaje en Ticket</label>
                            <textarea class="form-control" id="txt-empresaconvenio-mensajeticket"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-danger" id="btn-empresaconvenio-darbaja" style="display:none">DAR BAJA</button>
                <button type="button" class="btn btn-success" id="btn-empresaconvenio-daralta" style="display:none">DAR ALTA</button>
                <button type="button" class="btn btn-success" id="btn-empresaconvenio-guardar">GUARDAR</button>
            </div>
        </form>
    </div>
</div>



<div class="modal fade" id="mdl-informe"  role="dialog" aria-labelledby="mdl-informelabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" id="frm-informe">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-informelabel">Redactar Informe</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <input type="hidden" id="txt-informe-seleccionado">

                        <h6><strong>Cabecera del Informe</strong></h6>
                        <table class="table table-sm mb-3" width="100%">
                            <tbody id="blk-informecabecera"></tbody>
                        </table>
                        
                        <h6><strong>Contenido del Informe</strong></h6>
                        <div id="editor" style="min-height: 450px;"></div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-success" id="btn-informeguardar">GUARDAR</button>
            </div>
        </div>
    </div>
</div>



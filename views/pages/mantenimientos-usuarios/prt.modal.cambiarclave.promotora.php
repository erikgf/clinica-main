<div class="modal fade" id="mdl-promotoracambiarclave"  role="dialog" aria-labelledby="mdl-promotoracambiarclavelabel">
    <div class="modal-dialog" role="document">
        <form class="modal-content" id="frm-promotoracambiarclave">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-promotoracambiarclavelabel">Cambiar Clave Promotora</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt-promotoracambiarclave-seleccionado">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="txt-promotoracambiarclave-nombres">Nombres / Razón social</label>
                            <input type="text" class="form-control uppercase" readonly id="txt-promotoracambiarclave-nombres"/>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="txt-promotoracambiarclave-clave">Nueva Clave (<span style="color:red">*</span>)</label>
                            <input type="password" class="form-control" minlength="6" required id="txt-promotoracambiarclave-clave"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-success" id="btn-promotoracambiarclave-guardar">GUARDAR</button>
            </div>
        </form>
    </div>
</div>



<div class="modal fade" id="mdl-abrircaja"  role="dialog" aria-labelledby="mdl-abrircajalabel">
    <div class="modal-dialog modal-lg" role="document">
        <form class="modal-content" id="frm-abrircaja">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-abrircajalabel">Abrir Caja</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt-cajainstancia">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Caja</label>
                            <select required class="form-control" id="txt-cajaabrir">
                                <option value="">Seleccionar caja</option>
                                <option value='1'>Caja I - DPI I</option>
                                <option value='2'>Caja II - DPI II</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fecha Apertura</label>
                            <input type="date" name="txt-fechaapertura" class="form-control uppercase" id="txt-fechaapertura" required/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Monto Apertura</label>
                            <input type="number" name="txt-montoapertura" value="0.00" class="form-control uppercase" id="txt-montoapertura" required/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CANCELAR</button>  
                <button type="button" class="btn btn-success" id="btn-guardarabrir">ABRIR CAJA</button>
            </div>
        </form>
    </div>
</div>



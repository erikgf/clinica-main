<div class="modal fade" id="mdl-descuento"  role="dialog" aria-labelledby="mdl-descuentoLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="mdl-descuentoLabel">Autorizar Descuento</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="chk-gratuitodescuento">
                            <label class="form-check-label" for="chk-gratuitodescuento"><strong>¿Gratuito?</strong></label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Total Actual</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">S./</span>
                                    </div>
                                    <input required type="money" class="form-control " readonly id="txt-importetotaldescuento">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Monto Descuento (<span style="color:red">*</span>) </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">S./</span>
                                    </div>
                                    <input required type="money" class="form-control " id="txt-montodescuento" step=".01" min="0" value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Motivo (<span style="color:red">*</span>)</label>
                        <textarea class="form-control" rows="3" placeholder="Campo obligatorio ..." id="txt-motivodescuento"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="txt-autorizadordescuento">Validador/Autorizador (<span style="color:red">*</span>)</label>
                        <select required class="form-control" id="txt-autorizadordescuento">
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="txt-clavedescuento">Contraseña (<span style="color:red">*</span>)</label>
                        <input required type="password" class="form-control" id="txt-clavedescuento" placeholder="Contraseña">
                    </div>
                </div>
                <div class="modal-footer">
                    <span id="llenar-campos-warning" class="hide" style="position:absolute;left:10px;color:white">Llenar todos<br>los campos</span>
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="cancelarDescuentoBtn">CANCELAR</button>
                    <button type="button" class="btn btn-danger hide" id="btn-eliminardescuento">ELIMINAR DESCUENTO</button>
                    <button type="button" class="btn btn-success" id="btn-autorizardescuento">AUTORIZAR</button>
                </div>
            </div>
        </div>
    </div>
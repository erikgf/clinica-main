<div class="modal fade" id="mdl-validacion"  role="dialog" aria-labelledby="mdl-validacion">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="mdl-validacion">Autorizar Atención</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>Se ha detectado que esta atención requiere una confirmación de un usuario autorizador. Debido a que <b>esta atención ha recibido un descuento y se está utilizando tipo(s) de pago(s) NO EFECTIVO.</b></p>
                    <form>   
                        <div class="form-group">
                            <label for="txt-motivodescuentovalidacion">Motivo (<span style="color:red">*</span>)</label>
                            <textarea required class="form-control" rows="3" placeholder="Campo obligatorio ..." id="txt-motivodescuentovalidacion"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="txt-autorizadordescuentovalidacion">Validador/Autorizador (<span style="color:red">*</span>)</label>
                            <select required class="form-control" id="txt-autorizadordescuentovalidacion">
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="txt-clavedescuentovalidacion">Contraseña (<span style="color:red">*</span>)</label>
                            <input required type="password" class="form-control" id="txt-clavedescuentovalidacion" placeholder="Contraseña">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="btn-guardarvalidacion">CONFIRMAR GUARDAR</button>
                </div>
            </div>
        </div>
    </div>
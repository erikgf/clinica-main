<div class="modal fade" id="mdl-veratencion" role="dialog" aria-labelledby="mdl-veratencionlabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" id="frm-veratencion">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-veratencionlabel">Atención  <b id="lbl-atencion"></b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Información básica</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="txt-idatencion" />
                        <div class="row">
                            <div class="col-md-5" >
                                <div class="form-group">
                                    <label for="txt-paciente">Paciente</label>
                                    <input type="text" id="txt-paciente" class="form-control" readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-2 col-sm-3" id="blk-comprobante">
                                <div class="form-group">
                                    <label for="txt-comprobante">Comprobante</label>
                                    <input type="text" id="txt-comprobante" class="form-control" readonly>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-6">
                                <div class="form-group">
                                    <label for="txt-fechaatencion">Fecha Atención</label>
                                    <input type="date" id="txt-fechaatencion" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div  id="blk-descuentoenpago" class="row" style="display:none">
                            <div class="form-group col-md-3 col-sm-6">
                                <label for="txt-descuentoenpago" class="text-red">Descuento</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text text-red">S./</span>
                                    </div>
                                    <input type="money" readonly class="form-control text-red" id="txt-descuentoenpago" value="0.00" step=".01" min="0">
                                </div>
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <label for="txt-descuentomotivo" class="text-red">Motivo Descuento</label>
                                <textarea readonly class="form-control text-red" id="txt-descuentomotivo"></textarea>
                            </div>
                            <div class="form-group col-md-3 col-sm-6">
                                <label for="txt-descuentousuarioautorizador" class="text-red">Autorizador Descuento</label>
                                <textarea readonly class="form-control text-red" id="txt-descuentousuarioautorizador"></textarea>
                            </div>
                        </div>

                        <div class="row" id="blk-facturaveratencion" style="display:none">
                            <div class="col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label for="txt-facturaruc-veratencion">RUC (<span style="color:red">*</span>)  <span id="blk-spinner-ruc" style="display:none;" class="fa fa-spin fa-spinner"></label>
                                    <input type="text" class="form-control uppercase"  id="txt-facturaruc-veratencion" maxlength = "11">
                                </div>
                            </div>
                            <div class="col-md-5 col-sm-12">
                                <div class="form-group">
                                    <label for="txt-facturarazonsocial-veratencion">Razón Social (<span style="color:red">*</span>)</label>
                                    <input type="text" class="form-control uppercase"  id="txt-facturarazonsocial-veratencion">
                                </div>
                            </div>
                            <div class="col-md-5 col-sm-12">
                                <div class="form-group">
                                    <label for="txt-facturadireccion-veratencion">Dirección (<span style="color:red">*</span>)</label>
                                    <textarea class="form-control uppercase"  id="txt-facturadireccion-veratencion"> </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">Servicios <b id="lbl-cantidadservicios"></b></h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="card-body"  style="display: none;">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th style="max-width:135px;" class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="tbl-servicios">
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Médicos</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="txt-medicoordenante">Médico Ordenante</label>
                                    <select class="select-style" id="txt-medicoordenante">
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for ="txt-medicorealizante">Médico Realizante</label>
                                    <select class="select-style" id="txt-medicorealizante">
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <br>    
                                <button class="btn btn-success" id="btn-cambiarmedico">CAMBIAR MÉDICO</button>
                            </div>
                        </div>
                    </div>
                </div>
                        
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Forma de Pago</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="form-group col-md-8">
                                        <label for="txt-pagoefectivo">Efectivo</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">S./</span>
                                            </div>
                                            <input readonly type="money" class="form-control " id="txt-pagoefectivo" value="0.00" step=".01" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="txt-pagodeposito">Depósito</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">S./</span>
                                            </div>
                                            <input readonly type="money" class="form-control " id="txt-pagodeposito" value="0.00" step=".01" min="0">
                                        </div>
                                    </div>
                                </div>
                                <div  id="blk-deposito" class="row" style="display:none;">
                                    <div class="form-group col-sm-4">
                                        <div for="txt-banco" class="form-check-label">Banco</div>
                                        <input class="form-control" readonly type="text" id="txt-banco"/>
                                    </div>

                                    <div class="form-group col-sm-8">
                                        <label for="txt-numerooperacion"  class="form-check-label">N° Operación</label>
                                        <input id="txt-numerooperacion" readonly  type="text" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="row">
                                    <div class="form-group col-md-5">
                                        <label for="">Tarjeta</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">S./</span>
                                            </div>
                                            <input readonly type="money" class="form-control " id="txt-pagotarjeta" value="0.00" step=".01" min="0">
                                        </div>
                                    </div>
                                </div>

                                <div id="blk-tarjeta" style="display:none;">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input disabled type="radio" name="rad-tarjeta" checked="">
                                                <label  class="form-check-label">T. Débito</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input disabled type="radio" name="rad-tarjeta">
                                                <label  class="form-check-label">T. Crédito</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="form-check-label">Número de Tarjeta</label>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <input readonly type="text" data-ntarjeta="1" class="numero-tarjeta form-control text-center" maxlength="4">
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input readonly type="text" data-ntarjeta="2" class="numero-tarjeta form-control text-center" maxlength="4">
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input readonly type="text" data-ntarjeta="3" class="numero-tarjeta form-control text-center" maxlength="4">
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input readonly type="text" data-ntarjeta="4" class="numero-tarjeta form-control text-center" maxlength="4">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>    
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <div class="form-group">
                                                <label  class="form-check-label" for="txt-numerovoucher">N° de Voucher</label>
                                                <input  readonly class="form-control" type="text" maxlength="8" id="txt-numerovoucher">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <div class="callout callout-success">
                            <h5>Efectivo S./</h5> 
                            <h2 id="lbl-cajaefectivo">0.00</h2>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="callout callout-info">
                            <h5>Depósito S./</h5> 
                            <h2 id="lbl-cajadeposito">0.00</h2>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="callout callout-alert">
                            <h5>Tarjeta S./</h5> 
                            <h2 id="lbl-cajatarjeta">0.00</h2>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="callout callout-warning bg-warning">
                            <h5>Saldo S./</h5> 
                            <h2 id="lbl-cajacredito">0.00</h2>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="callout callout-danger bg-danger hide" id="blk-cajavuelto">
                            <h5>Vuelto S./</h5> 
                            <h2 id="lbl-cajavuelto">0.00</h2>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="callout callout-info bg-gradient-info">
                            <h5>Total S./</h5>
                            <h2 id="lbl-cajatotal">0.00</h2>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Observaciones</label>
                            <textarea readonly class="form-control" rows="2" placeholder="Observaciones" id="txt-observaciones"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
            </div>
        </div>
    </div>
</div>



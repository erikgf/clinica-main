<div class="modal fade" id="mdl-continuarpago" role="dialog" aria-labelledby="mdl-continuarpagolabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" id="frm-continuarpago">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-continuarpagolabel">Facturando a:   <b id="lbl-pacientepagar"></b></h4>
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
                        <div class="row">
                            <div class="col-md-3" >
                                <div class="form-group">
                                    <label for="txt-caja">Caja</label>
                                    <select required class="form-control bg-gradient-info" id="txt-caja">
                                        <option value='1'>Caja I - DPI I</option>
                                        <option value='2'>Caja II - DPI II</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-3">
                                <div class="form-group">
                                    <label for="txt-fechaemision">Fecha de Emisión</label>
                                    <input required type="date" id="txt-fechaemision" class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        <label>Comprobante Pago</label>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="radio" value="00" name="rad-tipocomprobantepago">
                                    <label class="form-check-label">Sólo TICKET</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="radio" value="03" name="rad-tipocomprobantepago" checked="">
                                    <label class="form-check-label">BOLETA</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="radio" value="01" name="rad-tipocomprobantepago">
                                    <label class="form-check-label">FACTURA</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select required class="form-control" name="txt-serie" id="txt-serie">
                                    <option value="">Seleccionar SERIE</option>
                                </select>
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
                    <div class="card-body" style="display: none;">
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
                                        <input id="txt-pagoefectivo" required type="money" class="form-control"  placeholder="0.00" step=".01" min="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="txt-pagodeposito">Depósito</label>
                                        <input id="txt-pagodeposito" required type="money" class="form-control"  placeholder="0.00" step=".01" min="0">
                                    </div>
                                </div>
                                <div  id="blk-deposito" class="row" style="display:none;">
                                    <div class="form-group col-sm-4">
                                        <div for="txt-banco" class="form-check-label">Banco</div>
                                        <select id="txt-banco" class="form-control">
                                            <option value="1">BCP</option>
                                            <option value="2">INTERBANK</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-sm-8">
                                        <label for="txt-numerooperacion"  class="form-check-label">N° Operación</label>
                                        <input id="txt-numerooperacion"  type="text" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <div class="row">
                                    <div class="form-group col-md-5">
                                        <label for="">Tarjeta</label>
                                        <input id="txt-pagotarjeta" class="form-control" type="money" required placeholder="0.00" step=".01" min="0">
                                    </div>
                                </div>

                                <div id="blk-tarjeta" style="display:none;">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="radio" name="rad-tarjeta" checked="">
                                                <label class="form-check-label">T. Débito</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="radio" name="rad-tarjeta">
                                                <label class="form-check-label">T. Crédito</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="form-check-label">Número de Tarjeta</label>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <input type="text" data-ntarjeta="1" class="numero-tarjeta form-control text-center" maxlength="4">
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="text" data-ntarjeta="2" class="numero-tarjeta form-control text-center" maxlength="4">
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="text" data-ntarjeta="3" class="numero-tarjeta form-control text-center" maxlength="4">
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="text" data-ntarjeta="4" class="numero-tarjeta form-control text-center" maxlength="4">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>    
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <div class="form-group">
                                                <label  class="form-check-label" for="txt-numerovoucher">N° de Voucher</label>
                                                <input  class="form-control" type="text" maxlength="8" id="txt-numerovoucher">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <hr class="m-0"> -->
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
                            <h5>Crédito S./</h5> 
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-success" id="btn-guardarpago">GUARDAR</button>
            </div>
        </div>
    </div>
</div>



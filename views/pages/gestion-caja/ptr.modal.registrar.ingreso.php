<div class="modal fade" id="mdl-registraringreso" role="dialog" aria-labelledby="mdl-registraringresolabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" id="frm-registraringreso">
            <div class="modal-header  bg-success">
                <h4 class="modal-title" id="mdl-registraringresolabel">Registrar Ingreso</h4>
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
                            <div class="col-md-3 " >
                                <div class="form-group">
                                    <label for="txt-caja">Caja</label>
                                    <select required class="form-control text-red font-weight-bold txt-cajas-movimientos" id="txt-caja"></select>
                                </div>
                            </div>

                            <div class="col-md-3" >
                                <div class="form-group">
                                    <label for="txt-tipoingreso">Tipo de Ingreso</label>
                                    <select required class="form-control" id="txt-tipoingreso">
                                        <option value="">Seleccionar</option>
                                        <option value="4">SALDOS A PACIENTES</option>
                                        <option value="10">OTROS INGRESOS</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label for="txt-fecharegistro">Fecha Registro</label>
                                    <input required type="date" id="txt-fecharegistro" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card" id="blk-pagosaldopaciente" style="display:none">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="txt-buscarrecibo">Núm. Recibo</label>
                                <div class="input-group mb-3">
                                  <input type="text" class="form-control rounded-0" id="txt-buscarrecibo"/>
                                  <span class="input-group-append">
                                    <button type="button" class="btn btn-info" id="btn-buscarrecibo">BUSCAR</button>
                                  </span>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Paciente</th>
                                            <th>Fecha Atención</th>
                                            <th>Importe Total</th>
                                            <th>Adeudado</th>
                                            <th>Pendiente</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="txt-atencionpaciente">-</td>
                                            <td id="txt-atencionfecha">-</td>
                                            <td id="txt-atenciontotal">-</td>
                                            <td class="font-weight-bold" id="txt-atencionadeudado">-</td>
                                            <td class="font-weight-bold" id="txt-atencionpendiente">-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card-body" id="blk-comprobantepagoingreso" style="display:none">
                        <label>Comprobante de Pago</label>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="radio" value="00" name="rad-tipocomprobantepago">
                                    <label class="form-check-label">Sólo TICKET</label>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <input type="radio" value="03" name="rad-tipocomprobantepago" checked="">
                                    <label class="form-check-label">BOLETA</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="checkbox" id="chk-pacienteboleta">
                                    <label class="form-check-label">Boleta tiene diferente paciente.</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="radio" value="01" name="rad-tipocomprobantepago">
                                    <label class="form-check-label">FACTURA</label>
                                </div>
                            </div>
                        </div>

                        <div id="blk-boleta" style="display:none">
                            <div class="row">
                                <div class="col-md-2 col-sm-12">
                                    <!-- text input -->
                                    <div class="form-group">
                                        <label for="txt-boletatipodocumento">Tipo Doc. (<span style="color:red">*</span>) </label>
                                        <select class="form-control" id="txt-boletatipodocumento">
                                            <option value='1' selected>DNI</option>
                                            <option value='4'>CARNET EXTRANJERÍA</option>
                                            <option value='6'>RUC</option>
                                            <option value='7'>PASAPORTE</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-12">
                                    <!-- text input -->
                                    <div class="form-group">
                                        <label for="txt-boletanumerodocumento">Núm Doc. (<span style="color:red">*</span>)  <span id="blk-spinner-numerodocumento" style="display:none;" class="fa fa-spin fa-spinner"></label>
                                        <input type="text" class="form-control uppercase"  id="txt-boletanumerodocumento" maxlength = "15">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="txt-boletanombres">Nombres (<span style="color:red">*</span>)</label>
                                        <input type="text" class="form-control uppercase"  id="txt-boletanombres">
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="txt-boletaapellidopaterno">Ap. Paterno (<span style="color:red">*</span>)</label>
                                        <input type="text" class="form-control uppercase"  id="txt-boletaapellidopaterno"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label for="txt-boletaapellidomaterno">Ap. Materno (<span style="color:red">*</span>)</label>
                                        <input type="text" class="form-control uppercase"  id="txt-boletaapellidomaterno"/>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <label for="txt-boletafechanacimiento">Fecha Nacimiento (<span style="color:red">*</span>)</label>
                                        <input type="date" class="form-control uppercase"  id="txt-boletafechanacimiento">
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <label for="txt-boletasexo">Sexo (<span style="color:red">*</span>)</label>
                                        <select class="form-control uppercase"  id="txt-boletasexo">
                                            <option value="">Seleccionar</option>
                                            <option value="M">Masculino</option>
                                            <option value="F">Femenino</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="blk-factura" style="display:none">
                            <div class="col-md-2 col-sm-12">
                                <!-- text input -->
                                <div class="form-group">
                                    <label for="txt-facturaruc">RUC (<span style="color:red">*</span>)  <span id="blk-spinner-ruc" style="display:none;" class="fa fa-spin fa-spinner"></label>
                                    <input type="text" class="form-control uppercase"  id="txt-facturaruc" maxlength = "11">
                                </div>
                            </div>
                            <div class="col-md-5 col-sm-12">
                                <div class="form-group">
                                    <label for="txt-facturarazonsocial">Razón Social (<span style="color:red">*</span>)</label>
                                    <input type="text" class="form-control uppercase"  id="txt-facturarazonsocial">
                                </div>
                            </div>
                            <div class="col-md-5 col-sm-12">
                                <div class="form-group">
                                    <label for="txt-facturadireccion">Dirección (<span style="color:red">*</span>)</label>
                                    <textarea class="form-control uppercase"  id="txt-facturadireccion"> </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="txt-ingresos-observaciones">Observaciones: </label>
                                <textarea class="form-control " id="txt-ingresos-observaciones"></textarea>
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
                                            <input required type="money" class="form-control " id="txt-pagoefectivo" value="0.00" step=".01" min="0">
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
                                            <input required type="money" class="form-control " id="txt-pagodeposito" value="0.00" step=".01" min="0">
                                        </div>
                                    </div>
                                </div>
                                <div  id="blk-deposito" class="row" style="display:none;">
                                    <div class="form-group col-sm-4">
                                        <div for="txt-banco" class="form-check-label">Banco</div>
                                        <select id="txt-banco" class="form-control">
                                            <option value="1">BCP</option>
                                            <option value="2">SCOTIABANK</option>
                                            <option value="3">BBVA</option>
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
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">S./</span>
                                            </div>
                                            <input required type="money" class="form-control " id="txt-pagotarjeta" value="0.00" step=".01" min="0">
                                        </div>
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
                        <!--
                        <div class="callout callout-warning bg-warning">
                            <h5>Saldo S./</h5> 
                            <h2 id="lbl-cajacredito">0.00</h2>
                        </div>
                        -->
                    </div>
                    <div class="col-md-2">
                        <div class="callout callout-danger bg-danger hide" id="blk-cajavuelto">
                            <h5>Excedente S./</h5> 
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
                <button type="button" class="btn btn-success" id="btn-guardaringreso">GUARDAR</button>
            </div>
        </div>
    </div>
</div>



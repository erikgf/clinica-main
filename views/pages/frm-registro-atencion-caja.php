<div class="card">
    <div class="card-header">
        <h3 class="card-title">Realizar Pago de Atención</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="card col-sm-12">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2" >
                            <div class="form-group">
                                <label>Caja</label>
                                <select class="form-control bg-gradient-info" id="caja-select">
                                    <option value='1'>Caja I - DPI I</option>
                                    <option value='2'>Caja II - DPI II</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="">Fecha de Emisión</label>
                                <input type="text" id="fecha-cancelar" class="form-control" readonly placeholder="Fecha">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="lbl-usuario">Atención Creada Por: </label>
                                <h4 id="lbl-usuario">[Usuario            ]</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- /.card-body -->
            <div class="col-sm-6" id="blk-facturando-a">
                <table class="table table-bordered">
                    <thead>                  
                        <tr>
                            <th>Facturando a: <strong id="caja-name">NOMBRE DEL PACIENTE</strong></th>
                            <th style="width: 40px">Costo</th>
                        </tr>
                    </thead>
                    <tbody id="caja-orders"></tbody>
                </table>
            </div>
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Datos del Paciente</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tbody>
                            <tr>
                                <td>Historia</td>
                                <td id="caja-historia"></td>
                            </tr>
                            <tr>
                                <td>Tipo Paciente</td>
                                <td>01 PARTICULAR</td>
                            </tr>
                            <tr>
                                <td>Medico Realiza</td>
                                <td id="caja-medicorealizante"></td>
                            </tr>
                            <tr>
                                <td>Medico Ordena</td>
                                <td id="caja-medicoordenante"></td>
                            </tr>
                            <tr>
                                <td>Emp. Convenio: </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Compañía</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Dirección</td>
                                <td id="caja-direccion"></td>
                            </tr>
                            <tr>
                                <td>Documento</td>
                                <td id="caja-documento"></td>
                            </tr>
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
                            <div class="form-group col-sm-4">
                                <label for="">Efectivo</label>
                                <input id="caja-pago-efectivo" type="money" class="form-control"  placeholder="0.00" step=".01" min="0">
                            </div>
                        </div>
                        <hr class="m-0">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label for="">Depósito</label>
                                <input id="caja-pago-deposito" type="money" class="form-control"  placeholder="0.00" step=".01" min="0">
                            </div>
                            <div id="deposito" class="col-sm-8 hide">
                                <div class="row">
                                    <div class="form-group col-sm-4">
                                        <div class="form-check-label">Banco</div>
                                        <select class="form-control">
                                            <option>BCP</option>
                                            <option>INTERBANK</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-8">
                                        <label class="form-check-label">N° Operación</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="m-0">
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label for="">Tarjeta</label>
                                <input id="caja-pago-tarjeta" class="form-control" type="money" placeholder="0.00" step=".01" min="0">
                            </div>
                            <div class="form-group col-sm-8 hide" id="tarjeta-cont">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="radio" name="radio1" checked="">
                                            <label class="form-check-label">T. Débito</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <input type="radio" name="radio1">
                                            <label class="form-check-label">T. Crédito</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-check-label">Número de tarjeta</label>
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <input type="text" class="form-control" maxlength="4">
                                                </div>
                                                <div class="col-sm-3">
                                                    <input type="text" class="form-control" maxlength="4">
                                                </div>
                                                <div class="col-sm-3">
                                                    <input type="text" class="form-control" maxlength="4">
                                                </div>
                                                <div class="col-sm-3">
                                                    <input type="text" class="form-control" maxlength="4">
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                </div>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <label  class="form-check-label">N° de Voucher</label>
                                            <input  class="form-control" type="text" maxlength="8">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <div class="form-group">
                            <label>Observaciones</label>
                            <textarea class="form-control" rows="3" placeholder="Enter ..." id="caja-observaciones"></textarea>
                        </div>
                    </div>
                </div>
            
                <div class="row">
                    <div class="col-sm-4">
                        <div class="callout callout-success">
                            <h5>Efectivo S./</h5> 
                            <h2 id="caja-efectivo">0.00</h2>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="callout callout-info">
                            <h5>Depósito S./</h5> 
                            <h2 id="caja-deposito">0.00</h2>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="callout callout-alert">
                            <h5>Tarjeta S./</h5> 
                            <h2 id="caja-tarjeta">0.00</h2>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-4">
                        <div class="callout callout-warning bg-warning">
                            <h5>Crédito S./</h5> 
                            <h2 id="caja-credito">0.00</h2>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="callout callout-danger bg-danger hide" id="caja-result-vuelto">
                            <h5>Vuelto S./</h5> 
                            <h2 id="caja-vuelto">0.00</h2>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="callout callout-info bg-gradient-info">
                            <h5>Total S./</h5>
                            <h2 id="caja-total">0.00</h2>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                    <h3 class="card-title">Comprobante de Pago</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <input type="radio" name="radtipocomprobantepago">
                                    <label class="form-check-label">Sólo Ticket</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <input type="radio" name="radtipocomprobantepago" checked="">
                                    <label class="form-check-label">Boleta</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <input type="radio" name="radtipocomprobantepago">
                                    <label class="form-check-label">Factura</label>
                                </div>
                            </div>
                            <div class="col-sm-3" id="blk-serie">
                                <select class="form-control" name="txt-serie" id="txt-serie">
                                    <option value="">Serie</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="offset-6 col-sm-3">
                <button type="button" class="btn btn-lg btn-default btn-block">CANCELAR</button>
            </div>
            <div class="col-sm-3">
                <button type="button" id="procesar-pago" class="btn btn-lg btn-success btn-block">GUARDAR</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript"  src="views/js/frmRegistroAtencionCaja.js"></script>
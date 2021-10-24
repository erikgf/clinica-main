<div class="modal fade " id="mdl-registraregreso" role="dialog" aria-labelledby="mdl-registraregresolabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" id="frm-registraregreso">
            <div class="modal-header bg-danger">
                <h4 class="modal-title" id="mdl-registraregresolabel">Registrar Egreso</h4>
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
                                    <label for="txt-egresos-caja">Caja</label>
                                    <select required class="form-control text-red font-weight-bold txt-cajas-movimientos" id="txt-egresos-caja"></select>
                                </div>
                            </div>

                            <div class="col-md-3" >
                                <div class="form-group">
                                    <label for="txt-tipoegreso">Tipo de Egreso</label>
                                    <select required class="form-control" id="txt-tipoegreso">
                                        <option value="">Seleccionar</option>
                                        <option value="2">GASTOS</option>
                                        <option value="7">VUELTOS A PACIENTES</option>
                                        <option value="8">DEVOLUCIÓN DE PACIENTES</option>
                                        <option value="11">OTROS EGRESOS</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <div class="form-group">
                                    <label for="txt-egresos-fecharegistro">Fecha Registro</label>
                                    <input required type="date" id="txt-egresos-fecharegistro" class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card" id="blk-egresos-atencionmedicamostrar" style="display:none">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="txt-egresos-buscarrecibo">Núm. Recibo</label>
                                <div class="input-group mb-3">
                                  <input type="text" class="form-control rounded-0" id="txt-egresos-buscarrecibo"/>
                                  <span class="input-group-append">
                                    <button type="button" class="btn btn-info" id="btn-egresos-buscarrecibo">BUSCAR</button>
                                  </span>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th style="width:70px">N° Recibo</th>
                                            <th>Paciente</th>
                                            <th style="width:100px">Fecha Atención</th>
                                            <th style="width:100px">Monto Devuelto</th>
                                            <th style="width:100px">Monto Vuelto</th>
                                            <th style="width:100px">Importe Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="txt-egresos-atencionrecibo"></td>
                                            <td id="txt-egresos-atencionpaciente">-</td>
                                            <td id="txt-egresos-atencionfecha">-</td>
                                            <td id="txt-egresos-atenciondevuelto">-</td>
                                            <td id="txt-egresos-atencionvuelto">-</td>
                                            <td id="txt-egresos-atenciontotal">-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="txt-egresos-observaciones">Observaciones: </label>
                                <textarea class="form-control " id="txt-egresos-observaciones"></textarea>
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
                                        <label for="txt-egresos-pagoefectivo">Efectivo</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">S./</span>
                                            </div>
                                            <input required type="money" class="form-control " id="txt-egresos-pagoefectivo" value="0.00" step=".01" min="0">
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
                            <h2 id="lbl-egresos-cajaefectivo">0.00</h2>
                        </div>
                    </div>

                    <div class="offset-md-8 col-md-2">
                        <div class="callout callout-info bg-gradient-info">
                            <h5>Total S./</h5>
                            <h2 id="lbl-egresos-cajatotal">0.00</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-success" id="btn-guardaregreso">GUARDAR</button>
            </div>
        </div>
    </div>
</div>



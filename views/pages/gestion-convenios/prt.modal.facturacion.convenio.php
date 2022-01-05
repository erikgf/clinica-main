<div class="modal fade" id="mdl-facturacionconvenio"  role="dialog" aria-labelledby="mdl-facturacionconveniolabel">
    <div class="modal-dialog modal-xl" role="document">
        <form class="modal-content" id="frm-facturacionconvenio">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-facturacionconveniolabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group input-group-sm">
                            <label for="txt-facturacionconvenio-numeroticket">Número Ticket <input type="checkbox"  id="chk-facturacionconvenio-numeroticket"/>  </label>
                            <input maxlength="15" type="text" readonly class="form-control "  id="txt-facturacionconvenio-numeroticket"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <br>
                            <label id="txt-facturacionconvenio-numeroticketres" class="badge badge-success"></label>
                        </div>
                    </div>
                    <div class="offset-md-5 col-md-2">
                        <div class="form-group input-group-sm">
                            <label for="txt-facturacionconvenio-montocubierto">Monto Cubierto </label>
                            <input maxlength="15" type="text" readonly class="form-control "  id="txt-facturacionconvenio-montocubierto"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group input-group-sm">
                            <label for="txt-facturacionconvenio-idtipocomprobante">Tipo Comprobante (<span style="color:red">*</span>)</label>
                            <select  class="form-control" required id="txt-facturacionconvenio-idtipocomprobante">
                                    <option value="01" selected>FACTURA</option>
                                    <option value="07">NOTA CRÉDITO</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1 col-sm-6">
                        <div class="form-group input-group-sm">
                            <label for="txt-facturacionconvenio-serie">Serie (<span style="color:red">*</span>)</label>
                            <select class="form-control " required id="txt-facturacionconvenio-serie">
                                <option selected value="">Seleccionar</option>
                                <option value="F004">F004</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row" id="blk-facturacionconvenio-mod" style="display:none">
                    <div class="col-md-2">
                        <div   class="form-group input-group-sm">
                            <label for="txt-facturacionconvenio-razonsocial">Factura Serie Mod. </label>
                            <select type="text" class="form-control " id="txt-facturacionconvenio-seriecomprobantemod">
                                <option selected value="">Seleccionar</option>
                                <option  value="F004">F004</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div   class="form-group input-group-sm">
                            <label for="txt-facturacionconvenio-numerocomprobantemod">Factura Número Mod. </label>
                            <input type="text" class="form-control " id="txt-facturacionconvenio-numerocomprobantemod"/>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div   class="form-group input-group-sm">
                            <label for="txt-facturacionconvenio-motivomod">Motivo de Nota </label>
                            <select class="form-control " id="txt-facturacionconvenio-motivomod">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div   class="form-group input-group-sm">
                            <label for="txt-facturacionconvenio-descripcionmotivomod">Descripción Motivo </label>
                            <textarea type="text" class="form-control " id="txt-facturacionconvenio-descripcionmotivomod"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group input-group-sm">
                            <label for="txt-facturacionconvenio-numerodocumento">N. Documento(<span style="color:red">*</span>) <span id="blk-facturacionconvenio-spinner" style="display:none;" class="fa fa-spin fa-spinner"></span>  </label>
                            <input maxlength="11" type="text" class="form-control " required id="txt-facturacionconvenio-numerodocumento"/>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group input-group-sm">
                            <label for="txt-facturacionconvenio-razonsocial">Razón Social (<span style="color:red">*</span>)</label>
                            <input type="text" class="form-control " required id="txt-facturacionconvenio-razonsocial"/>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group input-group-sm">
                            <label for="txt-facturacionconvenio-direccion">Dirección</label>
                            <textarea class="form-control " id="txt-facturacionconvenio-direccion"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group input-group-sm">
                            <label for="txt-facturacionconvenio-fechaemision">F. Emisión(<span style="color:red">*</span>)</label>
                            <input type="date" class="form-control " required id="txt-facturacionconvenio-fechaemision"/>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div  class="form-group input-group-sm">
                            <label for="txt-facturacionconvenio-fechavencimiento">F. Vencimiento (<span style="color:red">*</span>)</label>
                            <input type="date" class="form-control " required id="txt-facturacionconvenio-fechavencimiento"/>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div  class="form-group input-group-sm">
                            <label for="txt-facturacionconvenio-formapago">Condición de Pago (<span style="color:red">*</span>)</label>
                            <select class="form-control" id="txt-facturacionconvenio-formapago">
                                <option selected value="1">CONTADO</option>
                                <option value="0">CRÉDITO</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
                <h4>Agregar Detalle</h4>
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group input-group-sm">
                            <label for="txt-facturacionconvenio-servicio">Servicio</label>
                            <select class="form-control " id="txt-facturacionconvenio-servicio"></select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group input-group-sm">
                            <label for="txt-facturacionconvenio-cantidad">Cant.</label>
                            <input class="form-control " id="txt-facturacionconvenio-cantidad" value="1"/>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group input-group-sm">
                            <label for="txt-facturacionconvenio-precio">Precio</label>
                            <input class="form-control " id="txt-facturacionconvenio-precio" value="0.00"/>
                        </div>
                    </div>
                    <div class="offset-md-3 col-md-1">
                        <div class="form-group">
                            <br>
                            <button type="button" id="btn-agregar-item" class="btn btn-primary btn-sm btn-block">AGREGAR</button>  
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-sm" id="tbl-facturacionconvenio-detalle" width="100%" >
                            <thead>
                                <tr>
                                    <th style="width: 75px">Opc.</th>
                                    <th>Servicio</th>
                                    <th  class="text-left" style="width: 135px" title="Precio de Venta">P.V.</th>
                                    <th  class="text-left" style="width: 125px">Cant.</th>
                                    <th  class="text-right" style="width: 135px">Total</th>
                                </tr>
                            </thead>
                            <tbody id="tbd-facturacionconvenio-detalle">
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group input-group-sm">
                            <label for="txt-facturacionconvenio-observaciones">Observaciones</label>
                            <textarea class="form-control " id="txt-facturacionconvenio-observaciones"></textarea>
                        </div>
                    </div>
                    <div class="col-sm-6 text-right">
                        <h5>Subtotal : <b id="lbl-facturacionconvenio-subtotal">0.00</b></h5>
                        <h5>IGV : <b id="lbl-facturacionconvenio-igv">0.00</b></h5>
                        <h4>Total : <b id="lbl-facturacionconvenio-total">0.00</b></h4>
                    </div>
                </div>

                <div class="row" id="blk-facturacionconvenio-cuotascredito" style="display:none">
                    <div class="col-sm-6">
                        <p class="font-weight-bold">Cuotas de Pago a Crédito</p>
                        <table class="table" id="tbl-facturacionconvenio-cuotascredito">
                            <thead>
                                <tr>
                                    <th style="width:60px" class="text-center"></th>
                                    <th style="width:90px" class="text-center">Cuota</th>
                                    <th style="width:120px">Importe Cuota</th>
                                    <th style="width:120px">Fecha de Pago</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="10"><a href="#" class="btn-agregarcuota">Agregar Cuota...</a></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-success" id="btn-facturacionconvenio-guardar">GUARDAR</button>
            </div>
        </form>
    </div>
</div>



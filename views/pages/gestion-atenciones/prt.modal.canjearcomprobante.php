<div class="modal fade" id="mdl-canjearcomprobante"  role="dialog" aria-labelledby="mdl-canjearcomprobantelabel">
    <div class="modal-dialog modal-lg" role="document">
        <form class="modal-content" id="frm-canjearcomprobante">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-canjearcomprobantelabel"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt-cajainstancia">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="txt-canjearcomprobante">Comprobante ANTERIOR</label>
                            <input type="text" name="txt-canjearcomprobante" class="form-control uppercase" id="txt-canjearcomprobante" readonly/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="txt-canjearcomprobantecanjeado">Tipo Comprobante NUEVO </label>
                            <select required name="txt-canjearcomprobantecanjeado" class="form-control" id="txt-canjearcomprobantecanjeado">
                                <option value="03" selected>BOLETA</option>
                                <option value='01'>FACTURA</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fecha Emisión</label>
                            <input type="date" name="txt-canjearfechaemision" class="form-control uppercase" id="txt-canjearfechaemision"  required/>
                        </div>
                    </div>
                </div>

                <div >
                    <div class="row">
                        <div class="col-md-3 col-sm-12">
                            <!-- text input -->
                            <div class="form-group">
                                <label for="txt-canjeartipodocumento">Tipo Doc. (<span style="color:red">*</span>) </label>
                                <select class="form-control" id="txt-canjeartipodocumento">
                                    <option value='1' selected>DNI</option>
                                    <option value='4'>CARNET EXTRANJERÍA</option>
                                    <option value='6'>RUC</option>
                                    <option value='7'>PASAPORTE</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <!-- text input -->
                            <div class="form-group">
                                <label for="txt-canjearnumerodocumento">Núm Doc. (<span style="color:red">*</span>)  <span id="blk-spinner-numerodocumento" style="display:none;" class="fa fa-spin fa-spinner"></label>
                                <input type="text" class="form-control uppercase"  id="txt-canjearnumerodocumento" maxlength = "15">
                            </div>
                        </div>
                    </div>
                    <div id="blk-canjearboleta" style="display:none">
                        <div class="row">
                            <div class="col-md-4 col-sm-12">
                                <div class="form-group">
                                    <label for="txt-boletanombres">Nombres (<span style="color:red">*</span>)</label>
                                    <input required type="text" class="form-control uppercase"  id="txt-boletanombres">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <div class="form-group">
                                    <label for="txt-boletaapellidopaterno">Ap. Paterno (<span style="color:red">*</span>)</label>
                                    <input required type="text" class="form-control uppercase"  id="txt-boletaapellidopaterno"/>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <div class="form-group">
                                    <label for="txt-boletaapellidomaterno">Ap. Materno (<span style="color:red">*</span>)</label>
                                    <input required type="text" class="form-control uppercase"  id="txt-boletaapellidomaterno"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="txt-boletafechanacimiento">Dirección (<span style="color:red">*</span>)</label>
                                    <textarea required rows="2" class="form-control uppercase"  id="txt-boletadireccion"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-sm-12">
                                <div class="form-group">
                                    <label for="txt-boletafechanacimiento">F. Nacimiento (<span style="color:red">*</span>)</label>
                                    <input required type="date" class="form-control uppercase"  id="txt-boletafechanacimiento">
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-12">
                                <div class="form-group">
                                    <label for="txt-boletasexo">Sexo (<span style="color:red">*</span>)</label>
                                    <select required class="form-control uppercase"  id="txt-boletasexo">
                                        <option value="">Seleccionar</option>
                                        <option value="M">Masculino</option>
                                        <option value="F">Femenino</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                     </div>

                     <div class="row" id="blk-canjearfactura" style="display:none">
                        <div class="col-md-7 col-sm-12">
                            <div class="form-group">
                                <label for="txt-facturarazonsocial">Razón Social (<span style="color:red">*</span>)</label>
                                <input type="text" class="form-control uppercase"  id="txt-facturarazonsocial">
                            </div>
                        </div>
                        <div class="col-md-5 col-sm-12">
                            <div class="form-group">
                                <label for="txt-facturadireccion">Dirección</label>
                                <textarea class="form-control uppercase"  id="txt-facturadireccion"> </textarea>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Servicios Registrados: <b id="lbl-canjearcantidadservicios"></b></h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th style="max-width:135px;" class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="tbl-canjearservicios">
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CANCELAR</button>  
                <button type="button" class="btn btn-success" id="btn-canjear">CANEJAR COMPROBANTE</button>
            </div>
        </form>
    </div>
</div>



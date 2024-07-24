<div class="modal fade" id="mdl-paquete"  role="dialog" aria-labelledby="mdl-paquetelabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="frm-paquete">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-paquetelabel">Gestionar Paquete</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <input type="hidden" id="txt-paquete-seleccionado">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="txt-paquete-descripcion">Nombre Paquete (<span style="color:red">*</span>)</label>
                                    <input type="text" required class="form-control uppercase" id="txt-paquete-descripcion"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-paquete-precioventa">Precio Venta (<span style="color:red">*</span>)</label>
                                    <input type="number" step="0.0001" required class="form-control uppercase" id="txt-paquete-precioventa"/>
                                </div>
                            </div>
                        </div>

                        <h5>Examenes en este Paquete</h5>
                        <table class="table table-sm" id="tbl-paquete-examenes">
                            <thead>
                                <th style="width:75px">OPC.</th>
                                <th>Descripción Examen</th>
                                <th style="width:120px">Valor Venta</th>
                                <th style="width:120px">Precio Venta</th>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-right" colspan="2"><b>TOTAL</b></td>
                                    <td class="text-right font-weight-bold" id="lbl-paquete-valorventa"></td>
                                    <td class="text-right font-weight-bold" id="lbl-paquete-total"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-danger" id="btn-paquete-eliminar" style="display:none">ELIMINAR</button>
                <button type="button" class="btn btn-success" id="btn-paquete-guardar">GUARDAR</button>
            </div>
        </div>
    </div>
</div>



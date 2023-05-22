<div class="modal fade" id="mdl-serviciogeneral"  role="dialog" aria-labelledby="mdl-serviciogenerallabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content" id="frm-serviciogeneral">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-serviciogenerallabel">Gestionar Servicio</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <input type="hidden" id="txt-serviciogeneral-seleccionado">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="txt-serviciogeneral-descripcion">Descripción / Nombre Servicio (<span style="color:red">*</span>)</label>
                                    <input type="text" required class="form-control uppercase" id="txt-serviciogeneral-descripcion"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-serviciogeneral-cantidadexamenes">Cant. Exámenes (<span style="color:red">*</span>)</label>
                                    <input type="number"  step="1" required class="form-control" id="txt-serviciogeneral-cantidadexamenes"/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="txt-serviciogeneral-descripciondetallada">Descripción Detallada </label>
                                    <textarea rows="3" class="form-control uppercase"  id="txt-serviciogeneral-descripciondetallada"></textarea>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-serviciogeneral-categoriaservicio">Área/Categoría (<span style="color:red">*</span>)</label>
                                    <select  class="form-control" id="txt-serviciogeneral-categoriaservicio"></select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-serviciogeneral-comision">Comisión</label>
                                    <input type="number" step="0.0001"  class="form-control" id="txt-serviciogeneral-comision"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-serviciogeneral-tipoafectacion">Tipo Afectación(<span style="color:red">*</span>)</label>
                                    <select required class="form-control" id="txt-serviciogeneral-tipoafectacion"></select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-serviciogeneral-valorventa">Valor Venta (<span style="color:red">*</span>)</label>
                                    <input type="number" step="0.0001" readonly required class="form-control uppercase" id="txt-serviciogeneral-valorventa"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-serviciogeneral-precioventa">Precio Venta (<span style="color:red">*</span>)</label>
                                    <input type="number" step="0.0001" required class="form-control uppercase" id="txt-serviciogeneral-precioventa"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-danger" id="btn-serviciogeneral-eliminar" style="display:none">ELIMINAR</button>
                <button type="button" class="btn btn-success" id="btn-serviciogeneral-guardar">GUARDAR</button>
            </div>
        </div>
    </div>
</div>



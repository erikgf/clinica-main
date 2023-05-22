<div class="modal fade" id="mdl-campaña"  campañae="dialog" aria-labelledby="mdl-campañalabel">
    <div class="modal-dialog modal-xl" campañae="document">
        <form class="modal-content" id="frm-campaña">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-campañalabel">Gestionar campaña</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt-campaña-seleccionado">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="txt-campaña-nombre">Nombre (<span style="color:red">*</span>)</label>
                            <input type="text" class="form-control uppercase" required id="txt-campaña-nombre"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="txt-campaña-descripcion">Descripción</label>
                            <textarea class="form-control"  id="txt-campaña-descripcion"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="txt-campaña-fechainicio">Fecha Inicio (<span style="color:red">*</span>)</label>
                            <input type="date" class="form-control " required id="txt-campaña-fechainicio"/>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="txt-campaña-fechafin">Fecha Fin (<span style="color:red">*</span>)</label>
                            <input type="date" class="form-control " required id="txt-campaña-fechafin"/>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="txt-campaña-fechafin">Sede (<span style="color:red">*</span>)</label>
                            <select class="form-control" id="txt-campaña-sede" required>
                                <option value="1">CHICLAYO</option>
                                <option value="2">LAMBAYEQUE</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
                <h4 class="subtitle"> Descuentos</h4>

                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="txt-campaña-tipo">Tipo</label>
                            <select class="form-control" id="txt-campaña-tipo">
                                <option value="servicio">Servicio</option>
                                <option value="categoria">Categoría</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="txt-campaña-serviciocategoria">Servicio/Categoría</label>
                            <select class="form-control" id="txt-campaña-serviciocategoria"></select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="txt-campaña-clase">Clase</label>
                            <select class="form-control" id="txt-campaña-clase">
                                <option value="1">Porcentaje</option>
                                <option value="0">Monto Fijo</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="txt-campaña-valor">Valor (-)</label>
                            <input type="number" class="form-control" value="0.00" id="txt-campaña-valor"/>
                        </div>
                    </div>

                    <div class="col-sm-12 form-group">
                        <button class="btn btn-primary btn-sm btn-block" id="btn-campaña-agregaritem">AGREGAR ITEM</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th>OPC.</th>
                                    <th>Tipo</th>
                                    <th>Servicio/Categoría</th>
                                    <th>Clase</th>
                                    <th>Dscto</th>
                                </tr>
                            </thead>
                            <tbody id="tbl-descuentos">


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-danger" id="btn-campaña-eliminar" style="display:none">ELIMINAR</button>
                <button type="button" class="btn btn-success" id="btn-campaña-guardar">GUARDAR</button>
            </div>
        </form>
    </div>
</div>



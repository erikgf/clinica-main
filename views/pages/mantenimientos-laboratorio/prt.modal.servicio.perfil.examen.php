<div class="modal fade" id="mdl-perfilexamen"  role="dialog" aria-labelledby="mdl-perfilexamenlabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="frm-perfilexamen">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-perfilexamenlabel">Gestionar Perfil Examen Lab.</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <input type="hidden" id="txt-perfilexamen-seleccionado">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="txt-perfilexamen-descripcion">Descripción / Nombre Perfil (<span style="color:red">*</span>)</label>
                                    <input type="text" required class="form-control" id="txt-perfilexamen-descripcion"/>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="txt-perfilexamen-precioventa">Precio Venta (<span style="color:red">*</span>)</label>
                                    <input type="number" step="0.0001" readonly required class="form-control " id="txt-perfilexamen-precioventa"/>
                                </div>
                            </div>
                        </div>

                        <h5>Examenes en este Perfil</h5>
                        <table class="table table-sm" id="tbl-perfilexamen-examenes">
                            <thead>
                                <th>Descripción Examen</th>
                                <th style="width:120px">Valor Venta</th>
                                <th style="width:120px">Precio Venta</th>
                                <th style="width:60px">OPC.</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-danger" id="btn-perfilexamen-eliminar" style="display:none">ELIMINAR</button>
                <button type="button" class="btn btn-success" id="btn-perfilexamen-guardar">GUARDAR</button>
            </div>
        </div>
    </div>
</div>



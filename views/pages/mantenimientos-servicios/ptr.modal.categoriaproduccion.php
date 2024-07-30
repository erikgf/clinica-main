<div class="modal fade" id="mdl-categoriaproduccion"  role="dialog" aria-labelledby="mdl-categoriaproduccionlabel">
    <div class="modal-dialog" role="document">
        <form class="modal-content" id="frm-categoriaproduccion">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-categoriaproduccionlabel">Gestionar Categoría Producción Médico</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="txt-categoriaproduccion-seleccionado">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="txt-categoriaproduccion-descripcion">Descripción (<span style="color:red">*</span>)</label>
                            <input type="text" required class="form-control uppercase" id="txt-categoriaproduccion-descripcion"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-danger" id="btn-categoriaproduccion-eliminar" style="display:none">ELIMINAR</button>
                <button type="submit" class="btn btn-success" id="btn-categoriaproduccion-guardar">GUARDAR</button>
            </div>
        </form>
    </div>
</div>



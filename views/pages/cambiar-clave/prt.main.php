
<div class="card">
    <form>
        <div class="card-header">
            <h3 class="card-title">Cambiar Clave de Usuario</h3>
        </div>
        <div class="card-body">
            <p>Tu clave está <b>encriptada</b> ni siquiera los administradores la conocen, en caso de pérdida; consultar al administrador para un reseteo de clave.</p>
            <div class="row">
                <div class="col-md-2 col-sm-6">
                    <div class="form-group">
                        <label for="txt-antiguaclave">Antigua Clave</label>
                        <input type="password"  required  id="txt-antiguaclave" class="form-control"/>
                        <small class="pull-right ver-clave"> <span class="fa fa-eye"></span>  Ver</small>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="form-group">
                        <label for="txt-nuevaclave">Nueva Clave</label>
                        <input type="password" required id="txt-nuevaclave" class="form-control"/>
                        <small class="pull-right ver-clave"> <span class="fa fa-eye"></span>  Ver</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="pull-right">
                <button type="button" class="btn btn-success btn-lg" id="btn-cambiarclave">CAMBIAR CLAVE</button>
            </div>
        </div>
    </form>

</div>




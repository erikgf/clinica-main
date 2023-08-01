
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Gestión de Cajas</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <select required class="form-control" id="txt-caja">
                                <option value='1'>Caja I - DPI I</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <button class="btn btn-success btn-block" title="Abrir Caja" id="btn-abrircaja"><i class="fa fa-archive"></i> ABRIR CAJA</button>                            
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="txt-fechainicio">Fecha Inicio</label>
                            <input required type="date" id="txt-fechainicio" value="" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="txt-fechafin">Fecha Fin</label>
                            <input required type="date" id="txt-fechafin" value="" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <br>
                            <button class="btn btn-success btn-block" title="Actualizar" id="btn-actualizarinstancias"><i class="fa fa-refresh"></i></button>                            
                        </div>
                    </div>
                </div>

                <div class="row"  style="max-height: 475px;">
                    <table class="table table-sm col-md-12">
                        <thead>
                            <tr>
                                <th class="text-center">OPC.</th>
                                <th >Usuario Responsable</th>
                                <th class="text-center">F. Apertura</th>
                                <th class="text-center">Monto Apertura</th>
                                <th class="text-center">F. Cierre</th>
                                <th class="text-center">Monto Cierre</th>
                            </tr>
                        </thead>
                        <tbody id="tbl-cajainstancias">
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title text-red font-weight-bold lbl-cajaseleccionada">[CAJA : NO SELECCIONADA]</h3>        
                <div class="card-tools">
                    <button type="button" id="btn-cerrarcaja" style="display:none;" class="btn btn-sm bg-gradient-blue"><i class="fa fa-key"></i> CERRAR</button>
                    <button type="button" id="btn-ingresocaja" class="btn btn-sm bg-gradient-green"><i class="fa fa-plus"></i> INGRESO</button>
                    <button type="button" id="btn-egresocaja" style="display:none;" class="btn btn-sm bg-gradient-red"><i class="fa fa-minus"></i> EGRESO</button>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" id="blk-montos">
                
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Movimientos: <b class=" text-red lbl-cajaseleccionada">[CAJA : NO SELECCIONADA]</b></h3>        
                <div class="card-tools">
                    <button type="button" id="btn-actualizarmovimientos" class="btn btn-sm bg-green"><i class="fa fa-refresh"></i> ACTUALIZAR</button>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-sm" id="tbl-cajamovimientosmain">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Movimiento</th>
                            <th>T. Movimiento</th>
                            <th>Cliente</th>
                            <th>Mto. Efectivo</th>
                            <th>Mto. Depósito</th>
                            <th>Mto. Tarjeta</th>
                            <th>Mto. Crédito</th>
                        </tr>
                    </thead>
                    <tbody id="tbl-cajamovimientos">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
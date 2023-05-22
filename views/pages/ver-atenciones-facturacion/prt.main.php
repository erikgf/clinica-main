
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Gestión de Atenciones</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-2 col-sm-6">
                <div class="form-group">
                    <label for="txt-fechainicio">Fecha Inicio</label>
                    <input required type="date" id="txt-fechainicio" value="" class="form-control"/>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="form-group">
                    <label for="txt-fechafin">Fecha Fin</label>
                    <input required type="date" id="txt-fechafin" value="" class="form-control"/>
                </div>
            </div>
            <div class="col-md-2 col-sm-12">
                <div class="form-group">
                    <br>
                    <button class="btn btn-success btn-block" title="Actualizar" id="btn-actualizarmovimientos"><i class="fa fa-refresh"></i> ACTUALIZAR</button>                            
                </div>
            </div>
            <div class="col-md-2 col-sm-12">
                <div class="form-group">
                    <br>
                    <button class="btn btn-block btn-info" title="Excel" id="btn-excel"><i class="fa fa-file-excel"></i> EXPORTAR EXCEL</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-group.input-left{
        width:200px;display: inline-block;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Atenciones</h3>        
                <div class="card-tools">
                    <div class="form-group input-left">
                        <label for="">Total Gravadas</label>
                        <input readonly type="text" class="form-control" value="" id="txt-totalgravadas">
                    </div>
                    <div class="form-group input-left">
                        <label for="">Importe IGV</label>
                        <input readonly type="text" class="form-control" value="" id="txt-totaligv">
                    </div>
                    <div class="form-group input-left">
                        <label for="">Importe Total</label>
                        <input readonly type="text" class="form-control" value="" id="txt-importetotal">
                    </div>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-sm"  id="tbl-cajamovimientos">
                    <thead style="font-size:small">
                        <tr>
                            <th>SUNAT</th>
                            <th>Tipo Doc</th>
                            <th>Comprobante</th>
                            <th>Fecha Emisión</th>
                            <th>Cliente</th>
                            <th>Núm. Doc</th>
                            <th>Tipo Pago</th>
                            <th>Porc. IGV</th>
                            <th>Total Gravadas</th>
                            <th>Total IGV</th>
                            <th>Total</th>
                            <th>Fecha Doc. Modificado</th>
                            <th>Tipo Doc. Modificado</th>
                            <th>Serie Modificado</th>
                            <th>Núm. Correlativo Modificado</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-sm-2">
                        <div class="control-group">
                            <label for="txt-efectivo">Total Efectivo</label>
                            <input readonly type="text" id="txt-efectivo" class="form-control" value="0.00"/>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="control-group">
                            <label for="txt-tarjeta">Total Tarjeta</label>
                            <input readonly type="text" id="txt-tarjeta" class="form-control" value="0.00"/>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="control-group">
                            <label for="txt-deposito">Total Depósito</label>
                            <input readonly type="text" id="txt-deposito" class="form-control" value="0.00"/>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="control-group">
                            <label for="txt-credito">Total Crédito</label>
                            <input readonly type="text" id="txt-credito" class="form-control" value="0.00"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card card-tabs">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tab-atencionesconvenio" data-toggle="pill" href="#blk-tab-atencionesconvenio" role="tab" aria-controls="blk-tab-atencionesconvenio" aria-selected="true">
                    Atenciones con Convenio
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-facturacionconvenio" data-toggle="pill" href="#blk-tab-facturacionconvenio" role="tab" aria-controls="blk-tab-facturacionconvenio" aria-selected="false">
                    Facturación Convenios
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-empresaconvenio" data-toggle="pill" href="#blk-tab-empresaconvenio" role="tab" aria-controls="blk-tab-empresaconvenio" aria-selected="false">
                    Empresas con Convenio
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
    <div class="tab-content" id="tabs-content">
        <div class="tab-pane fade show active" id="blk-tab-atencionesconvenio" role="tabpanel" aria-labelledby="tab-atencionesconvenio">
             <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Atenciones con Convenio</h3>
                            <div class="pull-right">
                                <button class="btn btn-success" title="Actualizar" id="btn-actualizar-atencionesconvenio"><i class="fa fa-refresh"></i> ACTUALIZAR</button>                            
                            </div>
                        </div>
                        <div class="card-body overlay-wrapper">
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label for="txt-atencionesconveniofechainicio">Fecha Inicio</label>
                                        <input required type="date" id="txt-atencionesconvenio-fechainicio" value="" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label for="txt-atencionesconvenio-fechafin">Fecha Fin</label>
                                        <input required type="date" id="txt-atencionesconvenio-fechafin" value="" class="form-control"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="overlay" id="overlay-tbl-atencionesconvenio" style="display:none;"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Cargando...</div></div>
                                    <table class="table table-sm" id="tbl-atencionesconvenio">
                                        <thead>
                                            <tr>
                                                <th style="width: 75px">Opc.</th>
                                                <th style="width: 125px">N. Recibo</th>
                                                <th style="width: 125px">Fecha Registro</th>
                                                <th>Empresa Convenio</th>
                                                <th>Paciente</th>
                                                <th style="width: 115px">% Convenio</th>
                                                <th style="width: 115px">Monto Cliente</th>
                                                <th style="width: 115px">Monto Cubierto</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbd-atencionesconvenio">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             </div>
        </div>

        <div class="tab-pane fade" id="blk-tab-facturacionconvenio" role="tabpanel" aria-labelledby="tab-facturacionconvenio">
             <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Facturación</h3>
                            <div class="pull-right">
                                <button class="btn btn-success" title="Actualizar" id="btn-actualizar-facturacionconvenio"><i class="fa fa-refresh"></i> ACTUALIZAR</button>                            
                                <button class="btn btn-primary" title="Nuevo" id="btn-nuevo-facturacionconvenio"><i class="fa fa-plus"></i> NUEVO</button>                            
                            </div>
                        </div>
                        <div class="card-body overlay-wrapper">
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label for="txt-facturacionconvenio">Fecha Inicio</label>
                                        <input required type="date" id="txt-facturacionconvenio-fechainicio" value="" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label for="txt-facturacionconvenio-fechafin">Fecha Fin</label>
                                        <input required type="date" id="txt-facturacionconvenio-fechafin" value="" class="form-control"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="overlay" id="overlay-tbl-facturacionconvenio" style="display:none;"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Cargando...</div></div>
                                    <table class="table table-sm" id="tbl-facturacionconvenio" width="100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 75px">Opc.</th>
                                                <th style="width: 125px">N. Doc.</th>
                                                <th>Razón Social</th>
                                                <th style="width: 125px" title="Tipo de Comprobante">T.C.</th>
                                                <th style="width: 165px">Comprobante</th>
                                                <th class="text-center" style="width: 135px">Fecha Emisión</th>
                                                <th class="text-center" style="width: 135px">Valor Venta</th>
                                                <th class="text-center" style="width: 135px">IGV</th>
                                                <th class="text-center" style="width: 135px">Importe Total</th>
                                                <th class="text-center" style="width: 135px">SUNAT</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbd-facturacionconvenio">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             </div>
        </div>

        <div class="tab-pane fade" id="blk-tab-empresaconvenio" role="tabpanel" aria-labelledby="tab-empresaconvenio">
             <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Empresas con Convenio</h3>
                            <div class="pull-right">
                                <button class="btn btn-success" title="Actualizar" id="btn-actualizar-empresaconvenio"><i class="fa fa-refresh"></i> ACTUALIZAR</button>                            
                                <button class="btn btn-primary" title="Nuevo" id="btn-nuevo-empresaconvenio"><i class="fa fa-plus"></i> NUEVO</button>                            
                            </div>
                        </div>
                        <div class="card-body overlay-wrapper">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="overlay" id="overlay-tbl-empresaconvenio" style="display:none;"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Cargando...</div></div>
                                    <table class="table table-sm" id="tbl-empresaconvenio" width="100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 75px">Opc.</th>
                                                <th style="width: 125px">Número Documento</th>
                                                <th>Razón Social</th>
                                                <th>Fecha Alta</th>
                                                <th>Fecha Baja</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbd-empresaconvenio">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             </div>
        </div>
    </div>
    </div>
    <!-- /.card -->
</div>
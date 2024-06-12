<style rel="stylesheet">
    .blk-flotante{
        max-width: 360px;
        border: 1px solid #dac5c5;
        border-radius: 1em;
        padding: 12px 16px;
        z-index: 2;
        position: absolute;
        top: 30px;
        right: 15px;
        background: white;
    }
</style>
<div class="card card-tabs">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tabs-liquidacionsinsobre" data-toggle="pill" href="#blk-tabs-liquidacionsinsobre" role="tab" aria-controls="tabs-liquidacionsinsobre" aria-selected="true">
                Liquidaciones sin Sobre
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link " id="tabs-entregasobres" data-toggle="pill" href="#blk-tabs-entregasobres" role="tab" aria-controls="tabs-entregasobres" aria-selected="true">
                Entregas de Sobres
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="tabs-content">
            <div class="tab-pane fade show active" id="blk-tabs-liquidacionsinsobre" role="tabpanel" aria-labelledby="tabs-liquidacionsinsobre">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filtrar por mes de liquidación</h3>
                    </div>
                    <form class="card-body row" id="frm-liquidacionsinsobre">
                        <div class="col-lg-2 col-md-3 col-sm-6">
                            <div class="form-group">
                                <label for="txt-liquidacionsinsobre-mes">Mes</label>
                                <select required name="txt-liquidacionsinsobre-mes" id="txt-liquidacionsinsobre-mes" class="form-control txt-mes"></select>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-2 col-sm-6">
                            <div class="form-group">
                                <label for="txt-liquidacionsinsobre-año">Año</label>
                                <select required name="txt-liquidacionsinsobre-año" id="txt-liquidacionsinsobre-año" class="form-control txt-año"></select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-5 col-sm-12">
                            <div class="form-group">
                                <label for="txt-liquidacionsinsobre-promotora">Promotora</label>
                                <select name="txt-liquidacionsinsobre-promotora" id="txt-liquidacionsinsobre-promotora" class="form-control txt-promotora"></select>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-2 col-sm-6">
                            <div class="form-group">
                                <label for="txt-liquidacionsinsobre-montomin">Monto Mín.</label>
                                <input type="number" value="100.00" required name="txt-liquidacionsinsobre-montomin" id="txt-liquidacionsinsobre-montomin" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <br>
                            <button type="submit" class="btn btn-primary"><span class="fa fa-search"></span> BUSCAR</button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Registros</h3>
                        <div class="card-tools" style="display:flex; align-items:center;gap:20px;">
                            <div style="min-width:300px" class="input-group input-group-sm">
                                <input type="text" name="table_search" id="txt-liquidacionsinsobre-buscar" class="form-control float-right" placeholder="Buscar por médico (Presionar ENTER)...">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-success btn-block" id="btn-liquidacionsinsobre-guardar">
                                <i class="fa fa-save"></i>
                                <span>GUARDAR</span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="blk-liquidacionsinsobre-paginacion"></div>
                        <table class="table table-sm" id="tbl-liquidacionsinsobre-registros">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Médico</th>
                                    <th>Promotora</th>
                                    <th class="text-center" style="width:125px">Mes Actual</th>
                                    <th class="text-center" style="width:135px">Acumulado</th>
                                    <th style="width:200px">Fecha Entrega</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbd-liquidacionsinsobre-registros"> 
                                <tr>
                                    <td colspan="99">
                                        <p class="text-center">No hay registros que mostrar.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="blk-liquidacionsinsobre-verdetalle" style="display: none;" class="blk-flotante"></div>
            </div>

            <div class="tab-pane fade show" id="blk-tabs-entregasobres" role="tabpanel" aria-labelledby="tabs-entregasobres">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filtrar por Fecha Entregado</h3>
                    </div>
                    <form class="card-body row" id="frm-entregasobres">
                        <div class="col-lg-2 col-md-3 col-sm-6">
                            <div class="form-group">
                                <label for="txt-entregasobres-mes">Mes</label>
                                <select required name="txt-entregasobres-mes" id="txt-entregasobres-mes" class="form-control txt-mes"></select>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-2 col-sm-6">
                            <div class="form-group">
                                <label for="txt-entregasobres-año">Año</label>
                                <select required name="txt-entregasobres-año" id="txt-entregasobres-año" class="form-control txt-año"></select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-5 col-sm-12">
                            <div class="form-group">
                                <label for="txt-entregasobres-promotora">Promotora</label>
                                <select name="txt-entregasobres-promotora" id="txt-entregasobres-promotora" class="form-control txt-promotora"></select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <br>
                            <button type="submit" class="btn btn-primary"><span class="fa fa-search"></span> BUSCAR</button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Registros</h3>
                        <div class="card-tools" style="display:flex; align-items:center;gap:20px;">
                            <div style="min-width:300px" class="input-group input-group-sm">
                                <input type="text" name="table_search" id="txt-entregasobres-buscar" class="form-control float-right" placeholder="Buscar por médico (Presionar ENTER)...">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-success btn-block" id="btn-entregasobres-guardar">
                                <i class="fa fa-save"></i>
                                <span>GUARDAR CAMBIOS</span>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="blk-entregasobres-paginacion"></div>
                        <table class="table table-sm" id="tbl-entregasobres-registros">
                            <thead>
                                <tr>
                                    <th title="Seleccionar para eliminar" class="text-center"><i class="fa fa-trash fa-2x text-danger"></i></th>
                                    <th>ID</th>
                                    <th>Médico</th>
                                    <th>Promotora</th>
                                    <th class="text-center" style="width:150px">Total</th>
                                    <th style="width:200px">Fecha Entrega</th>
                                    <th style="width:200px">Fecha Aceptado</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody id="tbd-entregasobres-registros"> 
                                <tr>
                                    <td colspan="99">
                                        <p class="text-center">No hay registros que mostrar.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="blk-entregasobres-verdetalle" style="display: none;" class="blk-flotante"></div>
            </div>

        </div>
    </div>
    <!-- /.card -->
</div>
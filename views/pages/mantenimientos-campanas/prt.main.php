<div class="card card-tabs">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tab-campañas" data-toggle="pill" href="#blk-tab-campañas" role="tab" aria-controls="blk-tab-campañas" aria-selected="true">
                    Campañas
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
    <div class="tab-content" id="tabs-content">
        <div class="tab-pane fade show active" id="blk-tab-campañas" role="tabpanel" aria-labelledby="tab-campañas">
             <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Campañas</h3>
                            <div class="card-tools m-0">
                                <button id="btn-actualizar-campañas" class="btn btn-sm btn-success"><span class="fa fa-refresh"></span> ACTUALIZAR </button>
                                <button id="btn-nuevocampañas" class="btn btn-sm btn-primary"><span class="fa fa-plus"></span> NUEVO REGISTRO </button>
                            </div>
                        </div>
                        <div class="card-body overlay-wrapper">
                            <div class="overlay" id="overlay-tbl-campañas" style="display:none;"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Cargando...</div></div>
                            <table class="table table-sm" id="tbl-campañas">
                                <thead>
                                    <tr>
                                        <th style="width: 75px">Opc.</th>
                                        <th>Sede</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Fin</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="tbd-campañas">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
             </div>
        </div>
    </div>
    </div>
    <!-- /.card -->
</div>
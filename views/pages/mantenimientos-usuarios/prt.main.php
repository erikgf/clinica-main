<div class="card card-tabs">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tab-colaboradores" data-toggle="pill" href="#blk-tab-colaboradores" role="tab" aria-controls="blk-tab-colaboradores" aria-selected="true">
                    Colaborador
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tab-roles" data-toggle="pill" href="#blk-tab-roles" role="tab" aria-controls="blk-tab-roles" aria-selected="false">
                    Roles
                </a>
            </li>
            <!--
            <li class="nav-item">
                <a class="nav-link" id="tab-accesos" data-toggle="pill" href="#blk-tab-accesos" role="tab" aria-controls="blk-tab-accesos" aria-selected="false">
                    Accesos
                </a>
            </li>
            -->
        </ul>
    </div>
    <div class="card-body">
    <div class="tab-content" id="tabs-content">
        <div class="tab-pane fade show active" id="blk-tab-colaboradores" role="tabpanel" aria-labelledby="tab-colaboradores">
             <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Colaboradores</h3>
                            <div class="card-tools m-0">
                                <button id="btn-actualizar-colaboradores" class="btn btn-sm btn-success"><span class="fa fa-refresh"></span> ACTUALIZAR </button>
                                <button id="btn-nuevocolaboradores" class="btn btn-sm btn-primary"><span class="fa fa-plus"></span> NUEVO REGISTRO </button>
                            </div>
                        </div>
                        <div class="card-body overlay-wrapper">
                            <div class="overlay" id="overlay-tbl-colaboradores" style="display:none;"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Cargando...</div></div>
                            <table class="table table-sm" id="tbl-colaboradores">
                                <thead>
                                    <tr>
                                        <th style="width: 75px">Opc.</th>
                                        <th>N. Documento</th>
                                        <th>Nombres y Apellidos</th>
                                        <th>Correo</th>
                                        <th>Teléfono</th>
                                        <th>Rol</th>
                                        <th>Sistema</th>
                                    </tr>
                                </thead>
                                <tbody id="tbd-colaboradores">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
             </div>
        </div>
        <div class="tab-pane fade show" id="blk-tab-roles" role="tabpanel" aria-labelledby="tab-roles">
             <div class="row">
                <div class="col-md-7 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Roles</h3>
                            <div class="card-tools m-0">
                                <button id="btn-actualizar-roles" class="btn btn-sm btn-success"><span class="fa fa-refresh"></span> ACTUALIZAR </button>
                                <!-- <button id="btn-nuevoroles" class="btn btn-sm btn-primary"><span class="fa fa-plus"></span> NUEVO REGISTRO </button> -->
                            </div>
                        </div>
                        <div class="card-body overlay-wrapper">
                            <div class="overlay" id="overlay-tbl-roles" style="display:none;"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Cargando...</div></div>
                            <table class="table table-sm" id="tbl-roles">
                                <thead>
                                    <tr>
                                        <th style="width: 75px">Opc.</th>
                                        <th>Descripción</th>
                                        <th>Interfaz de Inicio<th>
                                    </tr>
                                </thead>
                                <tbody id="tbd-roles">
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
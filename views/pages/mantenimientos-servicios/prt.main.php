<div class="card card-tabs">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="tabs" role="tablist">
            <li class="nav-item">
            <a class="nav-link active" id="tab-servicios" data-toggle="pill" href="#blk-tab-servicios" role="tab" aria-controls="blk-tab-promotoras-servicios" aria-selected="false">
                Servicios
            </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
    <div class="tab-content" id="tabs-content">
        <div class="tab-pane fade show active" id="blk-tab-servicios" role="tabpanel" aria-labelledby="tab-servicios">
            <div class="card collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">Filtros</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="card-body row" style="display:none;">
                    <div class="col-md-2 col-sm-12">
                        <div class="form-group">
                            <label for="">Tipo Servicio</label>
                            <select class="form-control" id="txt-filtro-tiposervicio">
                                <option value="">Todos</option>
                                <option selected  value="1">Servicio</option>
                                <option value="2">Examen Lab.</option>
                                <option value="3">Perfil Lab.</option>
                                <option value="4">Paquetes</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

             <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Servicios</h3>
                            <div class="card-tools m-0">
                                <button id="btn-actualizar-servicios" class="btn btn-sm btn-success"><span class="fa fa-refresh"></span> ACTUALIZAR </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary btn-sm  dropdown-toggle dropdown-icon"  data-toggle="dropdown"><span class="fa fa-plus"></span> NUEVO REGISTRO</button>
                                        <span class="sr-only">Toggle Dropdown</span>
                                        <div class="dropdown-menu" role="menu">
                                            <button class="dropdown-item" id="btn-nuevoservicio"> SERVICIO</button>
                                            <button class="dropdown-item" id="btn-nuevoexamenlab"> EXAMEN LAB.</button>
                                            <button class="dropdown-item" id="btn-nuevoperfillab"> PERFIL LAB.</button>
                                            <button class="dropdown-item" id="btn-nuevopaquete"> PAQUETE</button>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body overlay-wrapper">
                            <div class="overlay" id="overlay-tbl-servicios" style="display:none;"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Cargando...</div></div>
                            <table class="table table-sm" id="tbl-servicios">
                                <thead>
                                    <tr>
                                        <th style="width: 75px">Opc.</th>
                                        <th>Descripción</th>
                                        <th>Área/Categoría</th>
                                        <th>Tipo Servicio</th>
                                        <th>Valor Venta</th>
                                        <th>Precio Venta</th>
                                    </tr>
                                </thead>
                                <tbody id="tbd-servicios">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Promotoras</h3>  
                            <div class="card-tools m-0">
                                <button id="btn-actualizar-promotoras" class="btn btn-sm btn-success"><span class="fa fa-refresh"></span> ACTUALIZAR </button>
                                <button id="btn-nuevo-promotoras" class="btn btn-sm bg-gradient-blue"><span class="fa fa-plus"></span> NUEVO </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm" style="font-size: .85em;" id="tbl-promotoras">
                                <thead>
                                    <tr>
                                        <th style="width: 75px">Opc.</th>
                                        <th>Descripción</th>
                                        <th style="width: 120px">% Comisión</th>
                                    </tr>
                                </thead>
                                <tbody id="tbd-promotoras">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Áreas</h3>
                            <div class="card-tools m-0">
                                <button id="btn-actualizar-areas" class="btn btn-sm btn-success"><span class="fa fa-refresh"></span> ACTUALIZAR </button>
                                <button id="btn-nuevo-areas" class="btn btn-sm bg-gradient-blue"><span class="fa fa-plus"></span> NUEVO </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm" style="font-size: .85em;" id="tbl-areas">
                                <thead>
                                    <tr>
                                        <th style="width: 75px">Opc.</th>
                                        <th>Descripción</th>
                                        <th style="width: 120px">% Comisión</th>
                                    </tr>
                                </thead>
                                <tbody id="tbd-areas">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            -->

             </div>
        </div>
    </div>
    </div>
    <!-- /.card -->
</div>
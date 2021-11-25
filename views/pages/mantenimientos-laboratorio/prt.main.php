<div class="card card-tabs">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="tabs" role="tablist">
            <li class="nav-item">
            <a class="nav-link active" id="tab-servicios" data-toggle="pill" href="#blk-tab-servicios" role="tab" aria-controls="blk-tab-promotoras-servicios" aria-selected="true">
                Servicios
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link" id="tab-mantenimientos" data-toggle="pill" href="#blk-tab-mantenimientos" role="tab" aria-controls="blk-tab-promotoras-servicios" aria-selected="false">
                Mantenimientos
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
                                <option selected value="2">Examen Lab.</option>
                                <option value="3">Perfil Lab.</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

             <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Examenes y Perfiles</h3>
                            <div class="card-tools m-0">
                                <button id="btn-actualizar-servicios" class="btn btn-sm btn-success"><span class="fa fa-refresh"></span> ACTUALIZAR </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary btn-sm  dropdown-toggle dropdown-icon"  data-toggle="dropdown"><span class="fa fa-plus"></span> NUEVO REGISTRO</button>
                                        <span class="sr-only">Toggle Dropdown</span>
                                        <div class="dropdown-menu" role="menu">
                                            <button class="dropdown-item" id="btn-nuevoexamenlab"> EXAMEN LAB.</button>
                                            <button class="dropdown-item" id="btn-nuevoperfillab"> PERFIL LAB.</button>
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
             </div>
        </div>
        <div class="tab-pane fade" id="blk-tab-mantenimientos" role="tabpanel" aria-labelledby="tab-mantenimientos">
             <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Unidades</h3>
                            <div class="card-tools m-0">
                                <button data-nombreclase="Unidad" id="btn-actualizar-unidad" class="btn btn-sm btn-success"><span class="fa fa-refresh"></span> ACTUALIZAR </button>
                                <button data-nombreclase="Unidad" id="btn-nuevo-unidad" class="btn btn-sm bg-gradient-blue"><span class="fa fa-plus"></span> NUEVO </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm" id="tbl-unidad" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 75px">Opc.</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody id="tbd-unidad">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Métodos</h3>  
                            <div class="card-tools m-0">
                                <button data-nombreclase="Metodo" id="btn-actualizar-metodo" class="btn btn-sm btn-success"><span class="fa fa-refresh"></span> ACTUALIZAR </button>
                                <button data-nombreclase="Metodo" id="btn-nuevo-metodo" class="btn btn-sm bg-gradient-blue"><span class="fa fa-plus"></span> NUEVO </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm" id="tbl-metodo"  style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 75px">Opc.</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody id="tbd-metodo">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
             </div>

             <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Secciones</h3>
                            <div class="card-tools m-0">
                                <button data-nombreclase="Seccion"  id="btn-actualizar-seccion" class="btn btn-sm btn-success"><span class="fa fa-refresh"></span> ACTUALIZAR </button>
                                <button data-nombreclase="Seccion"  id="btn-nuevo-seccion" class="btn btn-sm bg-gradient-blue"><span class="fa fa-plus"></span> NUEVO </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm" id="tbl-seccion"  style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 75px">Opc.</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody id="tbd-seccion">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Muestras</h3>  
                            <div class="card-tools m-0">
                                <button data-nombreclase="Muestra"  id="btn-actualizar-muestra" class="btn btn-sm btn-success"><span class="fa fa-refresh"></span> ACTUALIZAR </button>
                                <button data-nombreclase="Muestra"  id="btn-nuevo-muestra" class="btn btn-sm bg-gradient-blue"><span class="fa fa-plus"></span> NUEVO </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm" id="tbl-muestra"  style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 75px">Opc.</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody id="tbd-muestra">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Abreviaturas</h3>  
                            <div class="card-tools m-0">
                                <button data-nombreclase="Abreviatura"  id="btn-actualizar-abreviatura" class="btn btn-sm btn-success"><span class="fa fa-refresh"></span> ACTUALIZAR </button>
                                <button data-nombreclase="Abreviatura"  id="btn-nuevo-abreviatura" class="btn btn-sm bg-gradient-blue"><span class="fa fa-plus"></span> NUEVO </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm" id="tbl-abreviatura"  style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 75px">Opc.</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody id="tbd-abreviatura">
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
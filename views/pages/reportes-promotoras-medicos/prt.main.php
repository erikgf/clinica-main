<div class="card card-tabs">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tabs-promotoras" data-toggle="pill" href="#blk-tabs-promotoras" role="tab" aria-controls="tabs-promotoras" aria-selected="false">
                Reporte Promotoras y Médicos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tabs-mensualpromotoras" data-toggle="pill" href="#blk-tabs-mensualpromotoras" role="tab" aria-controls="tabs-mensualpromotoras" aria-selected="false">
                Reporte Médicos y Promotoras Mensual
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
    <div class="tab-content" id="tabs-content">
        <div class="tab-pane fade show active" id="blk-tabs-promotoras" role="tabpanel" aria-labelledby="tabs-promotoras">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Médicos de Promotoras</h3>
                        </div>
                        <div class="card-body">
                        
                            <div class="form-group">
                                <label for="">Promotora seleccionada</label>
                                <select id="txt-promotoraasignar" type="search" class="form-control"> 
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label for="">Fecha Inicio</label>
                                        <input type="date" id="txt-fechainicio-asignarmedico" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label for="">Fecha Fin</label>
                                        <input type="date" id="txt-fechafin-asignarmedico" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <br>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info  dropdown-toggle dropdown-icon"  data-toggle="dropdown"><span class="fa fa-print"></span> GENERAR</button>
                                            <span class="sr-only">Toggle Dropdown</span>
                                            <div class="dropdown-menu" role="menu">
                                                <button class="dropdown-item" id="btn-imprimir-asignarmedico-pdf"> PDF</button>
                                                <button class="dropdown-item" id="btn-imprimir-asignarmedico-excel"> EXCEL</button>
                                            </div>
                                        </button>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="tab-pane fade show" id="blk-tabs-mensualpromotoras" role="tabpanel" aria-labelledby="tabs-mensualpromotoras">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Reporte Médicos y Promotoras Mensual</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label for="">Fecha Inicio</label>
                                        <input type="date" id="txt-fechainicio-mensualpromotoras" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label for="">Fecha Fin</label>
                                        <input type="date" id="txt-fechafin-mensualpromotoras" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <br>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info  dropdown-toggle dropdown-icon"  data-toggle="dropdown"><span class="fa fa-print"></span> GENERAR</button>
                                            <span class="sr-only">Toggle Dropdown</span>
                                            <div class="dropdown-menu" role="menu">
                                                <button class="dropdown-item" id="btn-imprimir-mensualpromotoras-excel"> EXCEL</button>
                                            </div>
                                        </button>
                                    </div>
                                    
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
<div class="card card-tabs">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tabs-reporteexamenes" data-toggle="pill" href="#blk-tabs-reporteexamenes" role="tab" aria-controls="tabs-liquidacionindividualmedico" aria-selected="true">
                Reportes de Examenes
                </a>
            </li>
            <!--
            <li class="nav-item">
            <a class="nav-link" id="tab-promotoras-medicos" data-toggle="pill" href="#blk-tab-promotoras-medicos" role="tab" aria-controls="blk-tab-promotoras-medicos" aria-selected="false">
                Mantenimientos
            </a>
            </li>
            -->
        </ul>
    </div>

    <div class="card-body">
    <div class="tab-content" id="tabs-content">
        <div class="tab-pane fade show active" id="blk-tabs-reporteexamenes" role="tabpanel" aria-labelledby="tabs-reporteexamenes">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Reporte Examenes</h3>
                        </div>
                        <div class="card-body row">
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group input-group-sm">
                                    <label for="">Fecha Inicio</label>
                                    <input type="date" id="txt-fechainicio" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group input-group-sm">
                                    <label for="">Fecha Fin</label>
                                    <input type="date" id="txt-fechafin" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <br>
                                <button class="btn btn-success btn-block" id="btn-excel"><span class="fa fa-file-excel"></span> EXCEL</button>
                            </div>
                            <div class="col-md-1">
                                <br>
                                <button class="btn btn-danger btn-block" id="btn-pdf"><span class="fa fa-file-pdf"></span> PDF</button>
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
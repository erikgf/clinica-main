<div class="card card-tabs">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tabs-reporteexamenes" data-toggle="pill" href="#blk-tabs-reporteexamenes" role="tab" aria-controls="tabs-liquidacionindividualmedico" aria-selected="true">
                Reportes Atenciones Sede
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">
    <div class="tab-content" id="tabs-content">
        <div class="tab-pane fade show active" id="blk-tabs-reporteexamenes" role="tabpanel" aria-labelledby="tabs-reporteexamenes">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Reportes Atenciones Sede</h3>
                        </div>
                        <div class="card-body row">
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group input-group-sm">
                                    <label for="txt-sede">Sede</label>
                                    <select name="txt-sede" class="form-control" id="txt-sede">
                                        <option value="*" selected>TODAS</option>
                                        <option value="1">CHICLAYO</option>
                                        <option value="2">LAMBAYEQUE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group input-group-sm">
                                    <label for="txt-mes">Mes</label>
                                    <select id="txt-mes" class="form-control"></select>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group input-group-sm">
                                    <label for="txt-anio">Año</label>
                                    <select id="txt-anio" class="form-control"></select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <br>
                                <button class="btn btn-success btn-block" id="btn-excel"><span class="fa fa-file-excel"></span> EXCEL</button>
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
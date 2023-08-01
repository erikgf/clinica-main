<div class="card card-tabs">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tabs-reporteexamenes" data-toggle="pill" href="#blk-tabs-reporteexamenes" role="tab" aria-controls="tabs-liquidacionindividualmedico" aria-selected="true">
                Reportes de Cajas
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">
    <div class="tab-content" id="tabs-content">
        <div class="tab-pane fade show active" id="blk-tabs-reporteexamenes" role="tabpanel" aria-labelledby="tabs-reporteexamenes">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <form class="card" id="frm-excel">
                        <div class="card-header">
                            <h3 class="card-title">Reporte Cajas</h3>
                        </div>
                        <div class="card-body row">
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group input-group-sm">
                                    <label for="txt-fecha">Fecha</label>
                                    <input type="date" id="txt-fecha"  required class="form-control"/>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group input-group-sm">
                                    <label for="txt-cajasdisponibles">Cajas Disponibles <span id="ldn-cajasdisponibles"></span></label>
                                    <select multiple id="txt-cajasdisponibles" required class="form-control" style="height: 150px"></select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <br>
                                <button type="submit" class="btn btn-success btn-block" id="btn-excel"><span class="fa fa-file-excel"></span> EXCEL</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- /.card -->
</div>
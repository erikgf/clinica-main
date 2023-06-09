<div class="card card-tabs">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tabs-reporteexamenes" data-toggle="pill" href="#blk-tabs-reporteexamenes" role="tab" aria-controls="tabs-liquidacionindividualmedico" aria-selected="true">
                Reporte General Seguimiento de Médicos
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
                            <h3 class="card-title">Reporte General Seguimiento de Médicos</h3>
                        </div>
                        <form class="card-body">
                            <div class="row">
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
                                
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label for="txt-area">Área</label>
                                        <select id="txt-area" required name="txt-area[]" multiple class="form-control"></select>
                                    </div>
                                </div>
                                
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label for="txt-promotora">Promotores</label>
                                        <select id="txt-promotora" required name="txt-promotora[]" multiple class="form-control"></select>
                                    </div>
                                </div>

                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label for="txt-sede">Sedes</label>
                                        <select id="txt-sede" required name="txt-sede[]" multiple class="form-control"></select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="txt-monto">Monto mayor a</label>
                                        <input type="number" id="txt-monto" value="100.00" step="0.01" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <br>
                                    <button class="btn btn-primary btn-block" type="submit" id="btn-listar"><span class="fa fa-search"></span> LISTAR</button>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <br>
                                    <button class="btn btn-success btn-block" id="btn-excel"><span class="fa fa-file-excel"></span> EXCEL</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Lista de Registros</h3>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm nowrap display" id="tbl-examenes" style="width:100%">
                                                
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- /.card -->
</div>
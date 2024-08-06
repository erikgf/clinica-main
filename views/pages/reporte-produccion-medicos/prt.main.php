<div class="card card-tabs">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tabs-reporte" data-toggle="pill" href="#blk-tabs-reporte" role="tab" aria-controls="tabs-reporte" aria-selected="true">
                Reporte Producción de Médicos
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">
    <div class="tab-content" id="tabs-content">
        <div class="tab-pane fade show active" id="blk-tabs-reporte" role="tabpanel" aria-labelledby="tabs-reporte">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Reporte Producción de Médicos</h3>
                        </div>
                        <form id="frm-listar" class="card-body">
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="txt-fechainicio">Fecha Inicio</label>
                                        <input type="date" required name="txt-fechainicio"  id="txt-fechainicio" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="txt-fechafin">Fecha Fin</label>
                                        <input type="date" required name="txt-fechafin" id="txt-fechafin" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <div class="form-group">
                                            <label for="txt-medicoinformante">Médicos</label>
                                            <select required id="txt-medicoinformante"class="form-control"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <br>
                                    <button class="btn btn-primary btn-block" type="submit" id="btn-listar"><span class="fa fa-search"></span> LISTAR</button>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <br>
                                    <button class="btn btn-success btn-block" id="btn-excel"><span class="fa fa-file-excel"></span> EXCEL</button>
                                </div>
                            </div>
                        </form>
                    </div>
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
                                <thead style="font-size:small">
                                    <tr>
                                        <th>ESTADO</th>
                                        <th>FECHA</th>
                                        <th>RECIBO</th>
                                        <th>PACIENTE</th>
                                        <th>AREA</th>
                                        <th>EXAMEN</th>
                                        <th>MONTO PROD.</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size:.9em;">
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
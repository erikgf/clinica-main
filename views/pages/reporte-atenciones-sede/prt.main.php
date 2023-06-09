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
                        <form class="card-body">
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="txt-fechainicio">Fecha Inicio</label>
                                        <input type="date" name="txt-fechainicio" id="txt-fechainicio" required class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="txt-fechafin">Fecha Fin</label>
                                        <input type="date" name="txt-fechafin" id="txt-fechafin" required class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="txt-estado">Estado</label>
                                        <select id="txt-estado" required name="txt-estado" class="form-control">
                                            <option value="*">AMBOS</option>
                                            <option value="1">REALIZADO</option>
                                            <option value="0">PENDIENTE</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label for="txt-area">Área</label>
                                        <select id="txt-area" required name="txt-area[]" multiple class="form-control"></select>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="txt-sede">Sede</label>
                                        <select name="txt-sede" required class="form-control" id="txt-sede">
                                        </select>
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
                                        <th>Sede</th>
                                        <th>Estado</th>
                                        <th>F. Registro</th>
                                        <th>Recibo</th>
                                        <th>Comprobante</th>
                                        <th>Paciente</th>
                                        <th>Área</th>
                                        <th>Examen</th>
                                        <th>Monto Examen</th>
                                        <th>Monto Recibo Examen</th>
                                        <th>Médico Realizante</th>
                                        <th>Médico Informante</th>
                                        <th>Médico Ordenante</th>
                                        <th>Observaciones</th>
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
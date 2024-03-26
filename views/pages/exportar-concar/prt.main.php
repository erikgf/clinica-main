<div class="card card-tabs">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tabs-exportarconcar" data-toggle="pill" href="#blk-tabs-exportarconcar" role="tab" aria-controls="tabs-liquidacionindividualmedico" aria-selected="true">
                Exportar a CONCAR
                </a>
            </li>
        </ul>
    </div>

    <div class="card-body">
    <div class="tab-content" id="tabs-content">
        <div class="tab-pane fade show active" id="blk-tabs-exportarconcar" role="tabpanel" aria-labelledby="tabs-exportarconcar">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Exportar a CONCAR</h3>
                        </div>
                        <div class="card-body">
                            <form id="frm-ventasanexos">
                                <h4>Ventas y Anexos</h4>
                                <div class="row">
                                    <div class="col-md-2 col-lg-1 col-sm-6">
                                        <div class="form-group input-group-sm">
                                            <label for="txt-fechainicio">Fecha Inicio</label>
                                            <input type="date" name="txt-fechainicio" id="txt-fechainicio" required class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-lg-1 col-sm-6">
                                        <div class="form-group input-group-sm">
                                            <label for="txt-fechafin">Fecha Fin</label>
                                            <input type="date" name="txt-fechafin" id="txt-fechafin" required class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-lg-1 col-sm-6">
                                        <div class="form-group input-group-sm">
                                            <label for="txt-correlativo-inicio">Correlativo</label>
                                            <input type="numeric" name="txt-correlativo-inicio" placeholder="Ejemplo: 1" required id="txt-correlativo-inicio" required class="form-control"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2 col-lg-1 col-sm-6">
                                        <button type="submit" class="btn btn-success btn-block" id="btn-exportar"><span class="fa fa-file"></span> EXPORTAR</button>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <form id="frm-cancelaciones">
                                <h4>Cancelaciones de Ventas</h4>
                                <div class="row">
                                    <div class="col-md-2 col-lg-1 col-sm-6">
                                        <div class="form-group input-group-sm">
                                            <label for="txt-fechainiciocancelaciones">Fecha Inicio</label>
                                            <input type="date" name="txt-fechainiciocancelaciones" id="txt-fechainiciocancelaciones" required class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-lg-1 col-sm-6">
                                        <div class="form-group input-group-sm">
                                            <label for="txt-fechafincancelaciones">Fecha Fin</label>
                                            <input type="date" name="txt-fechafincancelaciones" id="txt-fechafincancelaciones" required class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-lg-1 col-sm-6">
                                        <div class="form-group input-group-sm">
                                            <label for="txt-correlativocancelaciones-inicio">Correlativo</label>
                                            <input type="numeric" name="txt-correlativocancelaciones-inicio" placeholder="Ejemplo: 1" required id="txt-correlativocancelaciones-inicio" required class="form-control"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2 col-lg-1 col-sm-6">
                                        <button type="submit" class="btn btn-success btn-block" id="btn-exportarcancelaciones"><span class="fa fa-file"></span> EXPORTAR</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

            <!--
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
            -->
        </div>
    </div>
    </div>
    <!-- /.card -->
</div>
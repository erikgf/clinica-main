<div class="card card-tabs">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tabs-reporteexamenes" data-toggle="pill" href="#blk-tabs-reporteexamenes" role="tab" aria-controls="tabs-liquidacionindividualmedico" aria-selected="true">
                Reportes Descuentos de Atenciones
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
                            <h3 class="card-title">Reporte Descuentos de Atenciones</h3>
                        </div>
                        <form class="card-body">
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
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group input-group-sm">
                                        <label for="txt-sede">Sede</label>
                                        <select id="txt-sede" required name="txt-sede" class="form-control"></select>
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
                            <table class="table table-sm nowrap display" id="tbl-atenciones" style="width:100%">
                                <thead style="font-size:small">
                                    <tr>
                                        <th>CAJA</th>
                                        <th>ID RECIBO</th>
                                        <th>FECHA</th>
                                        <th>HORA</th>
                                        <th>PACIENTE</th>
                                        <th>USU. REGISTRO</th>
                                        <th>USU. VALIDADOR</th>
                                        <th>MOTIVO DESCUENTO</th>
                                        <th>SERVICIO</th>
                                        <th>IMPORTE TOTAL</th>
                                        <th>MONTO DESCUENTO</th>
                                        <th>MONTO CANCELADO</th>
                                        <th>MONTO DEUDA</th>
                                        <th>SEDE</th>
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

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Gestión de Atenciones con Saldo</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-2 col-sm-6">
                <div class="form-group">
                    <label for="txt-fechainicio">Fecha Inicio</label>
                    <input required type="date" id="txt-fechainicio" value="" class="form-control"/>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="form-group">
                    <label for="txt-fechafin">Fecha Fin</label>
                    <input required type="date" id="txt-fechafin" value="" class="form-control"/>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="form-group">
                    <label for="txt-filtrosaldo">Filtro Saldos Cancelados</label>
                    <select required id="txt-filtrosaldo" class="form-control">
                        <option value="*">Todos</option>
                        <option value="C">Cancelados</option>
                        <option value="P" select>Pendientes</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2 col-sm-12">
                <div class="form-group">
                    <br>
                    <button class="btn btn-success btn-block" title="Actualizar" id="btn-actualizarmovimientos"><i class="fa fa-refresh"></i> ACTUALIZAR</button>                            
                </div>
            </div>
            <!--
            <div class="col-md-2 col-sm-12">
                <div class="form-group">
                    <br>
                    <button class="btn btn-block btn-info" title="Excel" id="btn-excel"><i class="fa fa-file-excel"></i> EXPORTAR EXCEL</button>
                </div>
            </div>
            -->
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Atenciones</h3>        
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-sm" id="tbl-cajamovimientos">
                    <thead>
                        <tr>
                            <th style="witdh:75px">Nro Recibo</th>
                            <th style="witdh:100px">Fecha</th>
                            <th>Paciente</th>
                            <th style="witdh:180px">Comprobante</th>
                            <th style="witdh:150px">A Cuenta</th>
                            <th style="witdh:150px">Caja A Cuenta</th>
                            <th style="witdh:150px">Saldo</th>
                            <th style="witdh:150px">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
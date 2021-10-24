
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Ver Exámenes a Revisar</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body" id="frm-busqueda">
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
            <div class="col-md-3 col-sm-6">
                <div class="form-group">
                    <label for="txt-area">Área</label>
                    <select required id="txt-area" class="form-control"></select>
                </div>
            </div>
            <div class="col-md-2 col-sm-12">
                <div class="form-group">
                    <br>
                    <button class="btn btn-success btn-block" title="Buscar" id="btn-buscar"><i class="fa fa-search"></i> BUSCAR</button>                            
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Exámenes |
                <small>
                    <div class="squares"></div>
                    Pendiente
                    <div class="squares bg-gradient-success"></div>
                    Realizado
                    <div class="squares bg-gradient-red"></div>
                    Cancelado
                </small> 
                </h3>        
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-sm" id="tbl-examenes">
                    <thead style="font-size: small;">
                        <tr>
                            <th>Fecha Registro</th>
                            <th>Recibo</th>
                            <th>Paciente</th>
                            <th>Examen</th>
                            <th>Deuda</th>
                            <th>Médico Realizante</th>
                            <th>Médico Informante</th>
                            <th>Observaciones</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 1em;">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<style>
    @media (max-width: 480px) {
        .card-tabs > .card-body{
            padding: 0px;
        }

        .btn-responsiver {
            margin: 8px 0px;
            width: 100%;
        }
    }
</style>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Ver Resultados Laboratorio</h3>
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
            <div class="col-md-2 col-sm-12">
                <div class="form-group">
                    <br>
                    <button class="btn btn-success btn-block" title="Actualizar" id="btn-actualizarmovimientos"><i class="fa fa-refresh"></i> ACTUALIZAR</button>                            
                </div>
            </div>
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
                <table class="table table-sm" id="tbl-atenciones">
                    <thead>
                        <tr>
                            <th></th>
                            <th style="width:120px"></th>
                            <th style="width:100px">N° Recibo</th>
                            <th>Paciente</th>
                            <th style="width:120px">Edad</th>
                            <th style="width:100px">Sexo</th>
                            <th style="width:130px">Fecha Atención</th>
                            <th style="width:100px">¿Validado?</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
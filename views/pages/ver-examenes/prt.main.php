
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Ver Exámenes</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="txt-fechainicio">Fecha Inicio</label>
                            <input required type="date" id="txt-fechainicio" value="" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="txt-fechafin">Fecha Fin</label>
                            <input required type="date" id="txt-fechafin" value="" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="form-group">
                            <label for="txt-area">Área</label>
                            <select required id="txt-area"class="form-control"></select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="txt-medicoinformante">M. Informantes</label>
                            <select required id="txt-medicoinformante"class="form-control"></select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="txt-medicorealizante">M. Realizantes</label>
                            <select required id="txt-medicorealizante"class="form-control"></select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <br>
                            <button class="btn btn-success btn-block" title="Actualizar" id="btn-actualizarexamenes"><i class="fa fa-refresh"></i> ACTUALIZAR</button>                            
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <br>
                            <button class="btn btn-info btn-block" title="Excel" id="btn-excel"><i class="fa fa-file-excel"></i> EXCEL</button>                            
                        </div>
                    </div>
                </div>                
            </div>
            
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <style type="text/css">
                    .cuadrado-estado{
                        cursor: pointer;
                        display: inline-block;
                        font-size: large;
                    }

                    .cuadrado-estado-seleccionado{
                        font-weight: bold;
                        text-decoration: underline;
                    }
                </style>
                <h3 class="card-title">Listado de Exámenes |
                <small>
                    <div data-valor="*" class="cuadrado-estado cuadrado-estado-seleccionado">   
                        <div class="squares bg-gradient-dark"></div>
                        TODOS
                    </div>
                    <div data-valor="P"  class="cuadrado-estado">   
                        <div class="squares"></div>
                        PENDIENTE
                    </div>
                    <div data-valor="R"  class="cuadrado-estado" >   
                        <div class="squares bg-gradient-success"></div>
                        REALIZADO
                    </div>
                    <div data-valor="C"  class="cuadrado-estado" >   
                        <div class="squares bg-gradient-red"></div>
                        CANCELADO
                    </div>
                </small> 
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-sm nowrap display" id="tbl-examenes" style="width:100%">
                    <thead style="font-size:small">
                        <tr>
                            <th>F. Registro</th>
                            <th>Recibo</th>
                            <th>Paciente</th>
                            <th>Área</th>
                            <th>Examen</th>
                            <th>Monto Examen</th>
                            <th>Monto</th>
                            <th>Deuda</th>
                            <th>Medio Pago</th>
                            <th>F. Realizado</th>
                            <th>Médico Realizante</th>
                            <th>Médico Informante</th>
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

<div class="row">
    <div class="col-md-12" id="blk-resumenes-areas">
    </div>
</div>
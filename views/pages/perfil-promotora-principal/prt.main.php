<div class="row">
    <div class="col-sm-7">
        <div class="card collapsed-card" id="blk-medicos-pendientes" >
            <div class="card-header">
                <h3 class="card-title">Mis Médicos: Pendientes por activar</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-md btn-success on-refresh" title="Actualizar"><i class="fa fa-refresh"></i></button>
                    <button type="button" class="btn btn-md btn-primary on-new-record" title="Nuevo Médico"><i class="fa fa-plus"></i> <span>NUEVO MÉDICO</span></button>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body"style="display: none;position:relative">
                <div class="zona-loader"></div>
                <div class="overflow-auto">
                    <table class="table" style="max-height: 300px">
                        <thead>
                            <tr>
                                <th>OPC</th>
                                <th>Estado</th>
                                <th>Médico</th>
                                <th>CMP</th>
                                <th>F. Nacimiento</th>
                                <th>Especialidad</th>
                                <th>Celular</th>
                                <th>Dirección</th>
                            </tr>
                        </thead> 
                        <tbody>
                            
                        </tbody>   
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-5">
        <div class="card" id="blk-generar-reporte-promotora">
            <div class="card-header">
                <h3 class="card-title">Generar Reporte Promotora</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <form class="card-body row">
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label for="">Fecha Inicio</label>
                        <input type="date" required id="txt-fechainicio" class="form-control">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label for="">Fecha Fin</label>
                        <input type="date" required id="txt-fechafin"  class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <br>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info  dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="fa fa-print"></span> IMPRIMIR</button>
                            <span class="sr-only">Toggle Dropdown</span>
                            <div class="dropdown-menu" role="menu">
                                <button type="submit" class="dropdown-item" id="btn-imprimir-pdf"> PDF</button>
                                <button type="submit" class="dropdown-item" id="btn-imprimir-excel"> EXCEL</button>
                            </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card"  id="blk-medicos-activos">
            <div class="card-header">
                <h3 class="card-title">Mis Médicos: Activos</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-md btn-success on-refresh" title="Actualizar"><i class="fa fa-refresh"></i></button>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body"style="position:relative">
                <div class="zona-loader"></div>
                <table class="table" style="min-height: 300px">
                    <thead>
                        <tr>
                            <th>OPC</th>
                            <th>Estado</th>
                            <th>Médico</th>
                            <th>CMP</th>
                            <th>F. Nacimiento</th>
                            <th>Sede</th>
                            <th>Especialidad</th>
                            <th>Celular</th>
                            <th>Dirección</th>
                        </tr>
                    </thead> 
                    <tbody> </tbody>   
                </table>
            </div>
        </div>
    </div>
</div>
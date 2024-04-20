<div class="row">
    <div class="col-sm-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Mis Médicos</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">


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
            <div class="card-body row">
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label for="">Fecha Inicio</label>
                        <input type="date" id="txt-fechainicio" class="form-control">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="form-group">
                        <label for="">Fecha Fin</label>
                        <input type="date" id="txt-fechafin"  class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <br>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info  dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="fa fa-print"></span> IMPRIMIR</button>
                            <span class="sr-only">Toggle Dropdown</span>
                            <div class="dropdown-menu" role="menu">
                                <button class="dropdown-item" id="btn-imprimir-pdf"> PDF</button>
                                <button class="dropdown-item" id="btn-imprimir-excel"> EXCEL</button>
                            </div>
                        
                    </div>
                    
                </div>
            </div>
        </div>

    </div>
</div>
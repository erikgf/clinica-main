<div class="card card-tabs">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tabs-liquidacionindividualmedico" data-toggle="pill" href="#blk-tabs-liquidacionindividualmedico" role="tab" aria-controls="tabs-liquidacionindividualmedico" aria-selected="true">
                Liquidación Detallada de Médicos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link " id="tabs-liquidacionmedicos" data-toggle="pill" href="#blk-tabs-liquidacionmedicos" role="tab" aria-controls="tabs-liquidacionmedicos" aria-selected="true">
                Liquidación de Médicos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tabs-asignarpromotora" data-toggle="pill" href="#blk-tabs-asignarpromotora" role="tab" aria-controls="tabs-asignarpromotora" aria-selected="false">
                Promotoras y Médicos
                </a>
            </li>
            <li class="nav-item">
            <a class="nav-link" id="tab-promotoras-medicos" data-toggle="pill" href="#blk-tab-promotoras-medicos" role="tab" aria-controls="blk-tab-promotoras-medicos" aria-selected="false">
                Mantenimientos <b></b>
            </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
    <div class="tab-content" id="tabs-content">
        <div class="tab-pane fade show active" id="blk-tabs-liquidacionindividualmedico" role="tabpanel" aria-labelledby="tabs-liquidacionindividualmedico">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Filtrar</h3>
                        </div>
                        <div class="card-body ">
                            <div class="row">
                                <div class="col-md-3 col-lg-2 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="">Fecha Inicio</label>
                                        <input type="date" class="txt-fechainicio form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-2 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="">Fecha Fin</label>
                                        <input type="date" class="txt-fechafin form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-2 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="">Comisión Mayor a:</label>
                                        <input class="form-control" value="100.00" id="txt-totalesmayores-liquidacion"/>
                                    </div>    
                                </div>
                                <div class="col-md-3 col-lg-2 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="">Sede</label>
                                        <select class="form-control" id="txt-sede-liquidacion"></select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-lg-3 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="">Seleccionar Médico(s)</label>
                                        <select class="form-control" id="txt-medicos-liquidacion"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-sm-6 col-xs-12 form-group">
                                    <button class="btn btn-block btn-primary btn-verresultados"><span class="fa fa-search"></span> VER RESULTADOS</button>
                                </div>
                                <div class="col-md-2 col-sm-6 col-xs-12 form-group">
                                    <button class="btn btn-block btn-info btn-imprimir"><span class="fa fa-print"></span> IMPRIMIR</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Atenciones</h3>
                        </div>
                        <div class="card-body overflow-auto">
                            <table class="table table-sm" id="tbl-atenciones">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Servicio</th>
                                        <th>Cantidad</th>
                                        <th>Total SIN IGV</th>
                                        <th>Comisión</th>
                                    </tr>
                                </thead>
                                <tbody id="tbd-atenciones"> 
                                    
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="text-right" colspan="3">Total</th>
                                        <th id="txt-total-totalsinigv" class="text-right">0.00</th>
                                        <th id="txt-total-comision" class="text-right">0.00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>   
                </div>
            </div>
        </div>

        <div class="tab-pane fade show" id="blk-tabs-liquidacionmedicos" role="tabpanel" aria-labelledby="tabs-liquidacionmedicos">
            <div class="row">
                <div class="col-md-12 col-lg-9 col-xs-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Filtrar</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-lg-2 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="">Fecha Inicio</label>
                                        <input type="date" class="txt-fechainicio-liquidaciontotal form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-2 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="">Fecha Fin</label>
                                        <input type="date" class="txt-fechafin-liquidaciontotal form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-2 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="">Totales Mayor a:</label>
                                        <input class="form-control" value="0.00" id="txt-totalesmayores-liquidaciontotal"/>
                                    </div>    
                                </div>
                                <div class="col-md-3 col-lg-2 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="">Sede</label>
                                        <select class="form-control" id="txt-sede-liquidaciontotal"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-sm-6 col-xs-12 form-group">
                                    <button class="btn btn-block btn-primary btn-verresultados-liquidaciontotal"><span class="fa fa-search"></span> VER RESULTADOS</button>
                                </div>
                                <div class="col-md-2 col-sm-6 col-xs-12 form-group">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info  dropdown-toggle dropdown-icon"  data-toggle="dropdown"><span class="fa fa-print"></span> IMPRIMIR</button>
                                            <span class="sr-only">Toggle Dropdown</span>
                                            <div class="dropdown-menu" role="menu">
                                                <button class="dropdown-item btn-imprimir-liquidaciontotal-pdf"> PDF</button>
                                                <button class="dropdown-item btn-imprimir-liquidaciontotal-excel"> EXCEL</button>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Médicos</h3>
                        </div>
                        <div class="card-body overflow-auto">
                            <table class="table table-sm" id="tbl-medicos-liquidaciontotal">
                                <thead>
                                    <tr>
                                        <th style="width:135px">Código</th>
                                        <th style="width:160px">Sede</th>
                                        <th>Apellidos y Nombres</th>
                                        <th style="width:135px" class="text-right">S/ Total</th>
                                    </tr>
                                </thead>
                                <tbody id="tbd-medicos-liquidaciontotal"> 
                                    
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="text-right" colspan="3">Total</th>
                                        <th style="width:135px" id="txt-total-comision-liquidaciontotal" class="text-right">0.00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>   
                </div>
            </div>
            
        </div>

        <div class="tab-pane fade show" id="blk-tabs-asignarpromotora" role="tabpanel" aria-labelledby="tabs-asignarpromotora">
            <div class="row">
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Todos los médicos</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm" id="tbl-medicosparasignar">
                                <thead>
                                    <tr>
                                        <th>Médico</th>
                                        <th>Celulares</th>
                                    </tr>
                                </thead>
                                <tbody id="tbd-medicosparasignar">
                                    <tr>
                                        <td>Sin nada que mostrar</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mt-lg-5">
                        <button id="btn-asignarmedico"  disabled class="btn btn-block btn-primary">
                            <i class="fa fa-3x fa-arrow-circle-right"></i>
                        </button>
                        <button id="btn-quitarmedico"  disabled class="btn btn-block btn-danger">
                            <i class="fa fa-3x fa-arrow-circle-left"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Médicos de Promotoras</h3>
                        </div>
                        <div class="card-body">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <a data-toggle="collapse" data-parent=".card-body" href="#collapseOne">
                                        GENERAR REPORTE PROMOTORA
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseOne" class="panel-collapse collapse in">
                                <div class="card-body row">
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label for="">Fecha Inicio</label>
                                            <input type="date" id="txt-fechainicio-asignarmedico" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label for="">Fecha Fin</label>
                                            <input type="date" id="txt-fechafin-asignarmedico" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <br>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-info  dropdown-toggle dropdown-icon"  data-toggle="dropdown"><span class="fa fa-print"></span> IMPRIMIR</button>
                                                <span class="sr-only">Toggle Dropdown</span>
                                                <div class="dropdown-menu" role="menu">
                                                    <button class="dropdown-item" id="btn-imprimir-asignarmedico-pdf"> PDF</button>
                                                    <button class="dropdown-item" id="btn-imprimir-asignarmedico-excel"> EXCEL</button>
                                                </div>
                                            </button>
                                        </div>
                                        
                                    </div>
                                </div>
                                </div>
                            </div>
                        
                            <div class="form-group">
                                <label for="">Promotora seleccionada</label>
                                <select id="txt-promotoraasignar" type="search" class="form-control"> 
                                </select>
                                <small>Las comisiones asignadas a los médicos en este mes (+ 1 semana del siguiente mes) serán automáticamente actualizadas tras alguna ASIGNACIóN.</small>
                            </div>

                            <table class="table table-sm" id="tbl-medicospromotoraasignar">
                                <thead>
                                    <tr>
                                        <th>Médico</th>
                                        <th>Celulares</th>
                                    </tr>
                                </thead>
                                <tbody id="tbd-medicospromotoraasignar">
                                    <tr>
                                        <td>Sin nada que mostrar</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <div class="tab-pane fade" id="blk-tab-promotoras-medicos" role="tabpanel" aria-labelledby="tab-promotoras-medicos">
             <div class="row">
                <div class="col-md-8">
                    <div class="card collapsed-card" id="blk-medicos-aprobar">
                        <div class="card-header">
                            <h3 class="card-title">Médicos para aprobar (<b id="lbl-cantidad-medicosaprobar"></b>)</h3>
                            <div class="card-tools">
                                <select name="txt-medicos-estado" id="txt-medico-estado">
                                    <option selected value="P">PENDIENTES</option>
                                    <option value="A">APROBADOS</option>
                                    <option value="R">RECHAZADOS</option>
                                </select>
                                <button type="button" class="btn btn-sm on-refresh bg-success" title="Actualizar">
                                    <i class="fas fa-refresh"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body"style="display: none;position:relative">
                            <div class="zona-loader"></div>
                            <table class="table" style="min-height: 300px">
                                <thead>
                                    <tr>
                                        <th>OPC</th>
                                        <th>Estado</th>
                                        <th>Médico</th>
                                        <th>CMP</th>
                                        <th>Cumpleaños</th>
                                        <th>Especialidad</th>
                                        <th>Celular</th>
                                        <th>Dirección</th>
                                    </tr>
                                </thead> 
                                <tbody></tbody>   
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Médicos</h3>
                            <div class="card-tools m-0">
                                <button id="btn-actualizar-medicos" class="btn btn-sm btn-success"><span class="fa fa-refresh"></span> ACTUALIZAR </button>
                                <button id="btn-nuevo-medicos" class="btn btn-sm bg-gradient-blue"><span class="fa fa-plus"></span> NUEVO </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm" style="font-size: .85em;" id="tbl-medicos" cellspacing="0" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 75px">Opc.</th>
                                        <th>Sede</th>
                                        <th>Médico</th>
                                        <th>Colegiatura</th>
                                        <th>RNE</th>
                                        <th>Teléfonos</th>
                                        <th>Cumpleaños</th>
                                        <th>Domicilio</th>
                                        <th>Promotora</th>
                                        <th>Especialidad</th>
                                    </tr>
                                </thead>
                                <tbody id="tbd-medicos">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Promotoras</h3>  
                            <div class="card-tools m-0">
                                <button id="btn-actualizar-promotoras" class="btn btn-sm btn-success"><span class="fa fa-refresh"></span> ACTUALIZAR </button>
                                <button id="btn-nuevo-promotoras" class="btn btn-sm bg-gradient-blue"><span class="fa fa-plus"></span> NUEVO </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm" style="font-size: .85em;" id="tbl-promotoras">
                                <thead>
                                    <tr>
                                        <th style="width: 75px">Opc.</th>
                                        <th>Descripción</th>
                                        <th style="width: 120px">% Comisión</th>
                                    </tr>
                                </thead>
                                <tbody id="tbd-promotoras">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Áreas</h3>
                            <div class="card-tools m-0">
                                <button id="btn-actualizar-areas" class="btn btn-sm btn-success"><span class="fa fa-refresh"></span> ACTUALIZAR </button>
                                <button id="btn-nuevo-areas" class="btn btn-sm bg-gradient-blue"><span class="fa fa-plus"></span> NUEVO </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm" style="font-size: .85em;" id="tbl-areas">
                                <thead>
                                    <tr>
                                        <th style="width: 75px">Opc.</th>
                                        <th>Descripción</th>
                                        <th style="width: 120px">% Comisión</th>
                                    </tr>
                                </thead>
                                <tbody id="tbd-areas">
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
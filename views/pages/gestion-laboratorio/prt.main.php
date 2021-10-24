<div class="card card-tabs">
    <div class="card-header p-0 pt-1">
        <ul class="nav nav-tabs" id="tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tabs-muestrasresultados" data-toggle="pill" href="#blk-tabs-muestrasresultados" role="tab" aria-controls="tabs-liquidacionindividualmedico" aria-selected="true">
                Registro de Muestras y Resultados
                </a>
            </li>
            <!--
            <li class="nav-item">
            <a class="nav-link" id="tab-promotoras-medicos" data-toggle="pill" href="#blk-tab-promotoras-medicos" role="tab" aria-controls="blk-tab-promotoras-medicos" aria-selected="false">
                Mantenimientos
            </a>
            </li>
            -->
        </ul>
    </div>

    <style>
        .subdescripciones{
            padding: 0px !important;
            padding-left: 28px !important;
            font-size: .78em;
        }

        .subdescripciones-titulo{
            padding-top: 0px  !important;
            padding-bottom: 0px  !important;
            padding-left: 8px  !important;
            font-weight: bold;
            font-size: .86em;
        }

        .blk-buscador{
            position: absolute;
            width: 100%;
            max-height: 330px;
            overflow: auto;
            z-index: 9999;
        }

        .blk-buscador ul{
            padding-left: 0px;
            font-size: 14px;
            background: white;
        }

        .blk-buscador ul li{
            list-style-type: none;
            padding: 4px 8px;
            border: .5px #dee2e6 solid;
            cursor: pointer;
        }

        .blk-buscador ul li:hover{
            background: #80bdff;
        }
    </style>
    <div class="card-body">
    <div class="tab-content" id="tabs-content">
        <div class="tab-pane fade show active" id="blk-tabs-muestrasresultados" role="tabpanel" aria-labelledby="tabs-muestrasresultados">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Atenciones</h3>
                        </div>
                        <div class="card-body row">
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group input-group-sm">
                                    <label for="">Fecha Inicio</label>
                                    <input type="date" id="txt-fechainicio" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="form-group input-group-sm">
                                    <label for="">Fecha Fin</label>
                                    <input type="date" id="txt-fechafin" class="form-control"/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group input-group-sm">
                                    <label for="txt-tipofiltro">Tipo Filtro</label>
                                    <select class="form-control" id="txt-tipofiltro">
                                        <option value="*">Todos</option>
                                        <option value="M" selected>Pendientes</option>
                                        <option value="R">Faltan Resultados</option>
                                        <option value="V">Faltan Validar</option>
                                        <option value="C">Completados</option>
                                        <option value="I">Completados - Faltan Imprimir</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <br>
                                <button class="btn btn-success btn-sm" id="btn-actualizaratenciones"><span class="fa fa-refresh"></span> ACTUALIZAR</button>
                            </div>
                            <div style="overflow-y:scroll;max-height:350px;width:100%">
                                <table class="table table-sm nowrap display" id="tbl-atenciones" style="width:100%;">
                                    <thead style="font-size:small">
                                        <tr>
                                            <th class="text-center" style="width:100px">Fecha</th>
                                            <th class="text-center" style="width:80px">Recibo</th>
                                            <th>Paciente</th>
                                            <th class="text-center" style="width:60px">Edad</th>
                                            <th class="text-center" style="width:60px">Sexo</th>
                                            <th class="text-center" style="width:160px">Fecha Muestra</th>
                                            <th class="text-center" style="width:160px">Fecha Resultados</th>
                                            <th class="text-center" style="width:160px">Fecha Validado</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbd-atenciones"> 
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12" id="blk-registromuestra" style="display:none;">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Registro de Muestras</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-2 form-group input-group-sm">
                                    <label>N. Recibo: </label>
                                    <h4 class="lbl-recibo font-weight-bold"></h4>
                                </div>
                                <div class="col-sm-10 form-group input-group-sm">
                                    <label>Paciente: </label>
                                    <h4 class="lbl-paciente font-weight-bold"></h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-sm nowrap display" id="tbl-atenciondetalle" style="width:100%;">
                                        <thead style="font-size:small">
                                            <tr>
                                                <th style="width:70px">OPC</th>
                                                <th style="width:70px">Imprimir</th>
                                                <th style="width:70px">¿Muestra?</th>
                                                <th>Examen/Grupo Examenes</th>
                                                <th style="width:120px">F. Muestra</th>
                                                <th style="width:120px">F. Entrega</th>
                                                <th style="width:120px">F. Resultados</th>
                                                <th style="width:120px">F. Validado</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbd-atenciondetalle">                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="pull-right">
                                <button class="btn btn-default" id="btn-cancelarmuestra" type="button"><i class="fa fa-ban"></i> CANCELAR</button>
                                <div class="btn-group">
                                    <button id="btn-imprimirmuestratodojunto"  type="button" class="btn btn-primary  dropdown-toggle dropdown-icon"  data-toggle="dropdown"><span class="fa fa-print"></span> IMPRIMIR TODO JUNTO</button>
                                        <span class="sr-only">Toggle Dropdown</span>
                                        <div class="dropdown-menu" role="menu">
                                            <button class="dropdown-item" id="btn-imprimirmuestra-todojunto-logo"> CON LOGO</button>
                                            <button class="dropdown-item" id="btn-imprimirmuestra-todojunto-sinlogo"> SIN LOGO</button>
                                        </div>
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button id="btn-imprimirmuestra"  type="button" class="btn btn-info  dropdown-toggle dropdown-icon"  data-toggle="dropdown"><span class="fa fa-print"></span> IMPRIMIR</button>
                                        <span class="sr-only">Toggle Dropdown</span>
                                        <div class="dropdown-menu" role="menu">
                                            <button class="dropdown-item" id="btn-imprimirmuestra-logo"> CON LOGO</button>
                                            <button class="dropdown-item" id="btn-imprimirmuestra-sinlogo"> SIN LOGO</button>
                                        </div>
                                    </button>
                                </div>
                                <button class="btn btn-success" id="btn-guardarmuestra" type="button"><i class="fa fa-save"></i> GUARDAR</button>
                            </div>
                        </div>
                    </div>   
                </div>

                <div class="col-sm-12" id="blk-registroresultados" style="display:none">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Registro de Resultados </h3>
                            <div class="pull-right">
                                <button class="btn btn-default btn-cancelarresultados" type="button"><i class="fa fa-arrow-circle-left"></i> ATRÁS</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <input type="hidden" id="txt-idatencionmedicoservicioseleccionado"/>
                            <div class="row">
                                <div class="col-sm-2 form-group input-group-sm">
                                    <label>N. Recibo: </label>
                                    <h4 class="lbl-recibo font-weight-bold"></h4>
                                </div>
                                <div class="col-sm-5 form-group input-group-sm">
                                    <label>Paciente: </label>
                                    <h4 class="lbl-paciente font-weight-bold"></h4>
                                </div>
                                <div class="col-sm-5 form-group input-group-sm">
                                    <label>Examen/Grupo Examenes</label>
                                    <h4 id="lbl-servicioatencion" class="font-weight-bold"></h4>
                                </div>
                                <!--
                                <div class="col-sm-4 form-group input-group-sm">
                                    <label>Grupo Lab.</label>
                                    <h5 id="lbl-grupolaboratorio" class="font-weight-bold"></h5>
                                </div>
                                -->
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-sm nowrap display" id="tbl-examenes" style="width:100%;">
                                        <thead style="font-size:small">
                                            <tr>
                                                <th>Examen</th>
                                                <th class="text-center" style="width:180px">Resultado</th>
                                                <th class="text-center" style="width:100px">Unidad</th>
                                                <th class="text-center" style="width:220px">Valores de Referencia</th>
                                                <th class="text-center" style="width:120px">Método</th>
                                                <th class="text-center" style="width:80px">OPC</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbd-examenes"> 
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="pull-left">
                                <button class="btn bg-blue" id="btn-validarresultados" type="button"><i class="fa fa-check"></i>  VALIDAR RESULTADOS</button>
                                <button class="btn bg-danger" id="btn-cancelarvalidacion" type="button"><i class="fa fa-close"></i>  CANCELAR VALIDACIÓN</button>
                            </div>
                            <div class="pull-right">
                                <button class="btn btn-default btn-cancelarresultados" type="button"><i class="fa fa-arrow-circle-left"></i> ATRÁS</button>
                                <button class="btn btn-success" id="btn-guardarresultados" type="button"><i class="fa fa-save"></i>  GUARDAR</button>
                            </div>
                        </div>
                    </div>   
                </div>
            </div>
            
        </div>
        <!--
        <div class="tab-pane fade" id="blk-tab-mantenimientos" role="tabpanel" aria-labelledby="tab-mantenimientos">
             <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Médicos</h3>
                            <div class="card-tools m-0">
                                <button id="btn-actualizar-medicos" class="btn btn-sm btn-success"><span class="fa fa-refresh"></span> ACTUALIZAR </button>
                                <button id="btn-nuevo-medicos" class="btn btn-sm bg-gradient-blue"><span class="fa fa-plus"></span> NUEVO </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm" style="font-size: .85em;" id="tbl-medicos">
                                <thead>
                                    <tr>
                                        <th style="width: 75px">Opc.</th>
                                        <th>Médico</th>
                                        <th>Colegiatura</th>
                                        <th>RNE</th>
                                        <th>Teléfonos</th>
                                        <th>Correo</th>
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
                            <h3 class="card-title">Especialidades</h3>
                            <div class="card-tools m-0">
                                <button id="btn-actualizar-especialidades" class="btn btn-sm btn-success"><span class="fa fa-refresh"></span> ACTUALIZAR </button>
                                <button id="btn-nuevo-especialidades" class="btn btn-sm bg-gradient-blue"><span class="fa fa-plus"></span> NUEVO </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm" style="font-size: .85em;" id="tbl-especialidades">
                                <thead>
                                    <tr>
                                        <th style="width: 75px">Opc.</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody id="tbd-especialidades">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

             </div>
        </div>
        -->
    </div>
    </div>
    <!-- /.card -->
</div>
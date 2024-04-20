
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Registro de Atenciones</h3>
        <div class="card-tools" style="display:flex">
            <div style="padding: 0px 16px;display:none">
                <h3>
                    <span id="lbl-nombrecampaña" class="badge bg-primary">ACTIVA: CAMPAÑA ACTIVA</span>
                </h3>
            </div>
            <div>
                <h3>
                    <span id="lbl-nombrecaja" class="badge bg-red">SELECCIONAR CAJA</span>
                    <select id="txt-seleccionadorcaja" class="form-control" style="display:none">
                        <option value="" selected>Seleccionar Caja</option>
                        <option value="1">CAJA 1</option>
                        <option value="2">CAJA 2</option>
                        <option value="3">CAJA 3</option>
                        <option value="4">CAJA 4</option>
                        <option value="5">CAJA 5 - LAMBAYEQUE</option>
                        <option value="6">CAJA INGRESOS Y TRANSFER</option>
                        <option value="7">CAJA ADMISIóN</option>
                        <!-- <option value="100">CAJA TEMPORAL</option>-->
                        <option value="0">Cancelar</option>
                    </select>
                </h3>
            </div>
        </div>
    </div>
    <div class="card-body">
            <div class="row">
                <div class="col-md-2 col-sm-6">
                    <div class="form-group">
                        <label for="">Hora</label>
                        <input type="time"  id="txt-horaatencion" class="form-control" readonly/>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="form-group">
                        <label for="">Fecha</label>
                        <input type="date" id="txt-fechaatencion" readonly class="form-control"/>
                    </div>
                </div>
                <div class="col-md-3"></div>
                <div class="col-md-5 col-sm-12" id="blk-historial-paciente"></div>
            </div>
            <div class="row">
                <div class="col-md-5 col-sm-12">
                    <div class="form-group">
                        <label for="">Paciente</label>
                        <select id='txt-paciente'>
                            <option value='0'>Seleccionar a paciente</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2 col-sm-12">
                    <div class="form-group">
                        <br>    
                        <button class="btn btn-info" data-toggle="modal" data-target="#mdl-paciente">GESTIONAR PACIENTES</button>
                    </div>     
                </div>
                
            </div>    
            <br>
                <!-- -----------------SELECT CATEGORY--------------------- -->
            <div class="row">
                <div class="col-md-2 col-sm-12">
                    <div class="form-group">
                        <label for="">Seleccionar Categoría</label>
                        <select id="txt-categoria" class="select-style">
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                    <label for="">Agregar Servicios</label>
                        <select id="txt-servicio" class="select-style">
                            <option value="0">Agregar un Servicio</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="">Lista de Servicios</label>
                        <!-- ------------------------------CONTENEDOR DE ORDENES------------------------ -->
                        <div id="blk-servicios" style="box-shadow: 2px 2px 5px 1px rgba(179,179,179,1)">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row" id="blk-descuento"  style="display:none;">
                <div class="col-sm-12">
                    <div class="offset-lg-9 col-lg-3 offset-md-9 col-md-3"> 
                        <button class="btn-warning btn-block btn btn-sm text-right" id="btn-descuento" data-toggle="modal" data-target="#mdl-descuento">
                            SIN DESCUENTO                            
                        </button>
                        <!-- <span id="lbl-rotulocampañadescuento" style="display:none" class="btn-block btn-sm font-weight-bold text-right">CON DESCUENTO POR CAMPAÑA</span>-->
                        <div class="color-palette-set" id="blk-mostrardescuento" style="display:none;"  data-toggle="modal" data-target="#mdl-descuento">
                            <div class="bg-warning color-palette" style="padding: 10px;display:flex; justify-content: flex-end;padding-right: 30px;">
                                <span style="margin-right:15px">Descuento</span>
                                <h5 id="lbl-descuento" class="m-0 text-red">0.00</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row hide"  id="blk-subtotal">
                <div class="col-sm-12">
                    <div class="offset-lg-9 col-lg-3 offset-md-9 col-md-3">
                        <div class="color-palette-set">
                            <div class="bg-lightblue color-palette" style="padding: 10px;display:flex; justify-content: flex-end;padding-right: 30px;">
                                <span style="margin-right:15px">Total</span>
                                <h5 id="lbl-subtotal" style="margin:0">0.00</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="txt-medicoordenante">Médico Ordenante</label>
                        <select class="select-style" id="txt-medicoordenante">
                            <option value="1">PARTICULAR</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for ="txt-medicorealizante">Médico Realizante</label>
                        <select class="select-style" id="txt-medicorealizante">
                            <option value="2">ROSAS DPI</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea class="form-control" rows="2" placeholder="Observaciones" id="txt-observaciones"></textarea>
                    </div>
                </div>
            </div>
            
    </div>
    <div class="card-footer">
        <div class="pull-right">
            <button type="button" class="btn btn-success btn-lg" id="btn-continuar" disabled>CONTINUAR</button>
        </div>
        
    </div>

    <!---------------------------------- Modal ------------------------------>
    <!-- <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"> -->
    
</div>




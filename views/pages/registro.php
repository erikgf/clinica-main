<div class="card">
    <div class="card-header">
        <h3 class="card-title">Registro de Atenciones</h3>
    </div>
    <div class="card-body">
            <div class="row">
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="">Hora</label>
                        <div class="input-group date" id="datetimepicker1" data-target-input="nearest">
                            <input type="text" class="form-control datetimepicker-input" data-target="#datetimepicker1"/>
                            <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="">Fecha</label>
                        <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                            <input type="text" class="form-control datetimepicker-input" data-target="#datetimepicker2"/>
                            <div class="input-group-append" data-target="#datetimepicker2" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="fa fa-clock-o"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                <!-- -----------------SELECT PATIENT--------------------- -->
            
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="">Paciente</label>
                        <select id='selUser'>
                            <option value='0'>Seleccionar a paciente</option>
                        </select>
                    </div>
                </div>
            </div>    
            <br>
                <!-- -----------------SELECT CATEGORY--------------------- -->
            <div class="row">
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="">Seleccionar Categoría</label>
                        <select id="categorias-select" class="select-style">
                        </select>
                    </div>
                </div>
                <div class="col-sm-4" style="padding-left:8px">
                    <div class="form-group">
                    <label for="">Agregar Servicios</label>
                        <select id="agregador-de-servicios" class="select-style">
                            <option value="add-service">Agregar un Servicio</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="">Lista de Servicios</label>
                        <!-- ------------------------------CONTENEDOR DE ORDENES------------------------ -->
                        <div id="order-cont" style="min-height:10vh;box-shadow: 2px 2px 5px 1px rgba(179,179,179,1);background-color:#f7faff">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-12 subtotal-cont hide" id="subtotal-cont">
                <div class="col-sm-2">
                    <div class="color-palette-set">
                    <div class="bg-lightblue color-palette" style="padding: 10px;display:flex;    justify-content: flex-end;padding-right: 30px;"><span style="margin-right:10px">Sub Total </span><h5 id="sub-total" style="margin:0"></h5></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Médico Realizante</label>
                        <select class="select-style" id="medico-realizante">
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Médico Ordenante</label>
                        <select class="select-style" id="medico-ordenante"></select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Observaciones</label>
                        <textarea class="form-control" rows="2" placeholder="Observaciones" id="registro-observaciones"></textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="offset-9 col-sm-3">
                    <button type="button" class="btn btn-block btn-success btn-lg" id="continue-btn" disabled>REALIZAR PAGO</button>
                </div>
            </div>
            <!---------------------------------- Modal ------------------------------>
            <!-- <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"> -->
            <div class="modal fade" id="myModal"  role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">Autorizar descuento</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="gratuitoCheck">
                            <label class="form-check-label" for="exampleCheck1"><strong>¿Gratuito?</strong></label>
                        </div>
                        <br>
                        <label class="form-check-label"><strong>Descuento por el monto de:</strong></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                            <span class="input-group-text">S./</span>
                            </div>
                            <input type="text" class="form-control " id="descuentoInput" onkeypress="return isNumberKey(event)">

                            <script>
                                function isNumberKey(evt)
                                {
                                    var charCode = (evt.which) ? evt.which : event.keyCode
                                    if (charCode > 31 && (charCode < 48 || charCode > 57)){
                                        if(charCode==46){
                                            return true;
                                        }else{
                                            return false;
                                        }
                                    }else{
                                        return true
                                    }
                                }
                            </script>
                            <!-- <div class="input-group-append">
                            <span class="input-group-text">.00</span>
                            </div> -->
                        </div>
                        <br>
                        <div class="form-group">
                                        <label>Motivo</label>
                                        <textarea class="form-control" rows="3" placeholder="Campo obligatorio ..." id="motivoTextArea"></textarea>
                        </div>
                        <br>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Código de Autorizante</label>
                            <select type="text" class="form-control" id="autorizante" placeholder="Nombres y Código"></select>
                                    
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Código de Validador</label>
                            <select type="text" class="form-control" id="validador" placeholder="Nombres y Código"></select>
                                    
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Contraseña</label>
                            <input type="password" class="form-control" id="autorizarPW" placeholder="Contraseña">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <span id="llenar-campos-warning" class="hide" style="position:absolute;left:10px;color:white">Llenar todos<br>los campos</span>
                        <button type="button" class="btn btn-danger hide" id="eliminarDescuento">Eliminar descuento</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal" id="cancelarDescuentoBtn">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="autorizar-btn">Autorizar</button>
                    </div>
                    </div>
                </div>
            </div>
    </div>
</div>


<div class="modal fade" id="mdl-examenlaboratorio"  role="dialog" aria-labelledby="mdl-examenlaboratoriolabel">
    <style type="text/css">
        .select2-container .select2-selection--single {
            height: 32px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 22px;
        }

        textarea {
            resize: none;
            overflow: hidden;
            min-height: 50px;
            max-height: 100px;
        }
    </style>
    <script type="text/javascript">
        function auto_grow(element) {
            element.style.height = "5px";
            element.style.height = (element.scrollHeight)+"px";
        };
    </script>
    <div class="modal-dialog modal-xxl" role="document">
        <div class="modal-content" id="frm-examenlaboratorio">
            <div class="modal-header">
                <h4 class="modal-title" id="mdl-examenlaboratoriolabel">Gestionar Examen Lab.</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <input type="hidden" id="txt-examenlaboratorio-seleccionado">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="txt-examenlaboratorio-descripcion">Descripción / Nombre Servicio (<span style="color:red">*</span>)</label>
                                    <input type="text" required class="form-control uppercase" id="txt-examenlaboratorio-descripcion"/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="txt-examenlaboratorio-comision">Comisión</label>
                                    <input type="number" step="0.0001"  class="form-control" id="txt-examenlaboratorio-comision"/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="txt-examenlaboratorio-tipoafectacion">Tipo Afectación(<span style="color:red">*</span>)</label>
                                    <select required class="form-control" id="txt-examenlaboratorio-tipoafectacion"></select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="txt-examenlaboratorio-valorventa">Valor Venta (<span style="color:red">*</span>)</label>
                                    <input type="number" step="0.0001" readonly required class="form-control uppercase" id="txt-examenlaboratorio-valorventa"/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="txt-examenlaboratorio-precioventa">Precio Venta (<span style="color:red">*</span>)</label>
                                    <input type="number" step="0.0001" required class="form-control uppercase" id="txt-examenlaboratorio-precioventa"/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="txt-examenlaboratorio-muestra">Tipo Muestra (<span style="color:red">*</span>)</label>
                                    <select required class="form-control" id="txt-examenlaboratorio-muestra"></select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="txt-examenlaboratorio-seccion">Tipo Sección (<span style="color:red">*</span>)</label>
                                    <select required class="form-control" id="txt-examenlaboratorio-seccion"></select>
                                </div>
                            </div>
                        </div>

                        <h5>
                            Descripción Examen Laboratorio
                            <div class="pull-right">
                                <button class="btn btn-sm btn-primary" id="btn-exportarcsv" type="button">Exportar CSV</button>
                            </div>
                        </h5>
                        <table class="table table-sm" id="tbl-examenlaboratorio-examenes">
                            <thead>
                                <th style="width:65px">Tab.</th>
                                <th>Descripción Examen</th>
                                <th style="width:150px">Abrev.</th>
                                <th style="width:150px">Unidad</th>
                                <th style="width:200px">Valores referenciales</th>
                                <th style="width:160px">Método</th>
                                <th style="width:60px">OPC.</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>  
                <button type="button" class="btn btn-danger" id="btn-examenlaboratorio-eliminar" style="display:none">ELIMINAR</button>
                <button type="button" class="btn btn-success" id="btn-examenlaboratorio-guardar">GUARDAR</button>
            </div>
        </div>
    </div>
</div>



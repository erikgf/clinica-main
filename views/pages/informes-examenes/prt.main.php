<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informes de Exámanes</h3>
            </div>
            <div class="card-body">
                <form class="row" id="frm-buscar">
                    <div class="col-sm-6 col-md-2">
                        <div class="form-group">
                            <label for="txtfechainicio">Fecha Inicio</label>
                            <input type="date" required class="form-control" name="txtfechainicio" id="txt-fechainicio"/>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <div class="form-group">
                            <label for="txtfechafin">Fecha Fin</label>
                            <input type="date" required class="form-control" name="txtfechafin" id="txt-fechafin"/>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <div class="form-group">
                            <br>
                            <button type="submit" class="btn btn-success btn-block" id="btn-buscar"><span class="fa fa-search"></span> BUSCAR</button>
                        </div>
                    </div>
                </form>

                <hr>
                
                <div class="row">
                    <div class="col-md-5 col-sm-12">
                        <div class="form-group">
                            <label for="txtmedico">Seleccionar Médico</label>
                            <select name="txtmedico" class="form-control" id="txt-medico">
                                <option value="">Ninguno</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5 col-sm-12 text-right">
                        <br>
                        <div class="form-group">
                            <button id="btn-guardarorden" type="button" class="btn btn-success" style="display: none;"><span class="fa fa-save"></span> GUARDAR ORDEN</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10 col-sm-12 overlay-wrapper">
                        <div class="overlay" id="overlay-tbl-informes" style="display:none;"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Cargando...</div></div>
                        <table class="table table-sm" id="tbl-informes">
                            <thead>
                                <tr>
                                    <th style="width: 80px">Opc.</th>
                                    <th style="width: 80px">Mover</th>
                                    <th>F. Atención</th>
                                    <th>Paciente</th>
                                    <th>Examen</th>
                                    <th>Área</th>
                                    <th>Última Actualización</th>
                                </tr>
                            </thead>
                            <tbody id="tbd-informes">
                                <tr>
                                    <td colspan="10" class="text-center"><i>Sin registros</i></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
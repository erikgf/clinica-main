
<div class="card">
    <form>
        <div class="card-header">
            <h3 class="card-title">Generar Archivo Importación de Ventas</h3>
        </div>
        <div class="card-body">
            <p>El archivo inicial se encuentra en <b id="lbl-direccionarchivoventas">C:/Siscontab/ImportacionesVentas/</b></p>
            <div class="row">
                <div class="col-md-2 col-sm-6">
                    <div class="form-group">
                        <label for="txt-fechainicio">Fecha Inicio</label>
                        <input type="date" name="txt-fechainicio" required  id="txt-fechainicio" class="form-control"/>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="form-group">
                        <label for="txt-fechafin">Fecha Fin</label>
                        <input type="date" name="txt-fechafin" required  id="txt-fechafin" class="form-control"/>
                    </div>
                </div>
                
                <div class="col-md-2 col-sm-6">
                    <div class="form-group">
                        <label for="txt-archivo">Archivo Inicial</label>
                        <input type="file" name="txt-archivo" id="txt-archivo" class="form-control"/>
                    </div>
                </div>
            </div>

            <br>
            <div id="blk-archivogenerado" style="display:none">
                <b>Archivo generado: <a download id="lbl-nombrearchivogenerado" href="#"></a></b>
                <p>Se debe proceder a reemplazar el archivo nuevo con el anterior (archivo inicial).</p>
            </div>
        </div>
        <div class="card-footer">
            <div class="pull-right">
                <button type="submit" class="btn btn-success btn-lg" id="btn-generar">GENERAR ARCHIVO TXT</button>
            </div>
        </div>
    </form>

</div>




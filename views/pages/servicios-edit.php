
<div class="col-md-6">
<button type="button" class="btn btn-primary" style="margin: 25px 0" id="crear-servicio">Crear un nuevo servicio</button>

<div class="card">
	<div class="card-header">
	<h3 class="card-title p-3">Aumentar tarifas a seleccionadas</h3>
	</div><!-- /.card-header -->
	<div class="card-body">
		<div class="tab-content">
			<button type="button" class="btn btn-default" id="pos-or-neg">+/-</button><input type="text" id="precio-masivo" onkeypress="return isNumberKey(event)" style="margin-right:20px;margin-left:20px;transform: translateY(2px);width: 100px;">
			<button id="apply-precio-masivo" type="button" class="btn btn-warning" style="margin-right:20px;">Confirmar</button>
		</div>
		
		<!-- /.tab-content -->
	</div><!-- /.card-body -->
</div>
	<div class="card">
		<div class="card-header">
			<h3 class="card-title">Edición de servicios</h3>
		</div>
		<!-- /.card-header -->
		<div class="card-body p-0">
			<table id="table_id" class="table table-sm display">
				<thead>
					<tr>
						<th style="width: 10px"><input type="checkbox" id="checkAll"></th>
						<th>Descripción</th>
						<th>Categoría</th>
						<th>Con IGV</th>
						<th>Sin IGV</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody id="servicios-tbody">
				</tbody>
			</table>
		</div>
		<!-- /.card-body -->
	</div>
	<!-- /.card -->
</div>
<!---------------------------------- Modal eliminar ------------------------------>
<div class="modal fade" id="modal-eliminar-servicio"  role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Eliminar servicio</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
         
			<label class="form-check-label">¿Seguro que desea eliminar el siguiente servicio?</label>
			<h3 id="modal-servicio-name"></h3>
	
      </div>
		<div class="modal-footer">
			<button type="button" class="btn btn-danger" id="eleminar-servicio">Eliminar Servicio</button>
			<button type="button" class="btn btn-default" data-dismiss="modal" id="">Cancelar</button>
		</div>
    </div>
  </div>
</div>


<!---------------------------------- Modal editar o crear------------------------------>

<div class="modal fade" id="modal-editar-o-crear"  role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Editar o crear servicio</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<br>
				<label class="form-check-label"><strong>Descripción:</strong></label>
				<div class="input-group">
					<input type="text" class="form-control " id="modal-servicio-descripcion" placeholder='Descripción'>
				</div>
				<br>
				<label class="form-check-label"><strong id="categoría-lable">Categoría:</strong></label>
				<div class="input-group" id="categoria-holder">
					<select type="text" class="form-control " id="modal-servicio-categoria" placeholder='Categoría'>
						
					</select>
				</div>
				<br>
				<div class="input-group">
					<div class="input-group-prepend">
					<span class="input-group-text">S./</span>
					</div>
					<input type="text" id="modal-servicio-precio" onkeypress="return isNumberKey(event)">
					<lable style="margin-left:20px;margin-top:6px">Sin IGV = <span id="modal-precio-sin-IGV"></span></lable>
				</div>
				<br>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal" id="cancelar-crear-o-editar">Cancelar</button>
					<button type="button" class="btn btn-success" id="modal-editar-o-crear-btn">Confirmar</button>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="views/js/DPIcrud.js" defer></script>
<div id="divMensajeError{!! $entidad !!}"></div>
<div id="my-div-errors" class="hidden"><h5 class="text-center p-0 m-0"><span class="badge badge-danger"></span></h5></div>
{!! Form::model($docmat, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="row">
	<div class="col-lg-4">
		{!! Form::label('numero', 'Numero:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('numero', $numero, array('class' => 'form-control input-xs', 'id' => 'numero','readonly'=>'true')) !!}
		</div>
	</div>
	<div class="col-lg-4">
		{!! Form::label('fecha', 'Fecha:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::date('fecha', null, array('class' => 'form-control input-xs', 'id' => 'fecha')) !!}
		</div>
	</div>
	<div class="col-lg-4">
		{!! Form::label('fechanecesidad', 'Fecha Necesidad:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::date('fechanecesidad', null, array('class' => 'form-control input-xs', 'id' => 'fechanecesidad')) !!}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-4">
		{!! Form::label('codigo', 'Código Oracle:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('codigo', null, array('class' => 'form-control input-xs', 'id' => 'codigo')) !!}
		</div>
	</div>
	<div class="col-lg-3 u-search-ua">
		<label for="ua" class="pl-3">Código Ua</label>
		<div class="u-ua-style js-vehiculo-desc" style="margin-top:20px">{{$placa}}</div>
		<input class="js-vehiculo-hiddenid" id="vehiculo_id" name="vehiculo_id" type="hidden" value="{{$vehiculo_id}}">
		<input class="js-equipo-hiddenid" id="equipo_id" name="equipo_id" type="hidden" value="{{$equipo_id}}">
		<input class="form-control input-xs js-vehiculo-id" style="margin-left: 20px" id="uades" name="uades" type="text" value="{{$uades}}">
		
		<small id="autoComplete_listve" class="text-danger"></small>
	</div>
	<div class="col-lg-1">
	</div>
	<div class="col-lg-4">
		{!! Form::label('km', 'Km:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('km', null, array('class' => 'form-control input-xs', 'id' => 'km')) !!}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-4">
		{!! Form::label('presupuestado', 'Presupuestado:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('presupuestado', $cboPresupuestado, null, array('class' => 'form-control input-xs', 'id' => 'presupuestado')) !!}
		</div>
	</div>
	<div class="col-lg-4">
		{!! Form::label('tipooperacion', 'Tipo Operación:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('tipooperacion', $cboTipo, null, array('class' => 'form-control input-xs', 'id' => 'tipooperacion')) !!}
		</div>
	</div>
	<div class="col-lg-4">
		{!! Form::label('condicionentrega', 'Cond. Entrega:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('condicionentrega', $cboCondicion, null, array('class' => 'form-control input-xs', 'id' => 'condicionentrega')) !!}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-4">
		{!! Form::label('tipocompra', 'Tipo Compra:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('tipocompra', $cboTipoCompra, null, array('class' => 'form-control input-xs', 'id' => 'tipocompra')) !!}
		</div>
	</div>
	<div class="col-lg-4">
		{!! Form::label('trabajo', 'Trabajo a Realizar:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::textarea('trabajo', null, array('class' => 'form-control input-xs', 'id' => 'trabajo','rows'=>'3')) !!}
		</div>
	</div>
	<div class="col-lg-2">
		{!! Form::label('condicionmercado', 'Condición Entrega:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('condicionmercado', $cboCondicionMercado, null, array('class' => 'form-control input-xs', 'id' => 'condicionmercado','onchange'=>'mostrarCondicion();')) !!}
		</div>
	</div>
	<div class="col-lg-2">
		{!! Form::label('dias', 'Días:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12 hidden" id="divNacional">
			{!! Form::select('dias1', $cboDiasNacional, null, array('class' => 'form-control input-xs', 'id' => 'dias1')) !!}
		</div>
		<div class="col-lg-12 col-md-12 col-sm-12 hidden" id="divInternacional">
			{!! Form::select('dias2', $cboDiasInternacional, null, array('class' => 'form-control input-xs', 'id' => 'dias2')) !!}
		</div>
	</div>
</div>
<br />
<div class="row">
	<div class="col-lg-4">
		{!! Form::label('tipomantenimiento', 'Tipo Mant.:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('tipomantenimiento', $cboTipoMantenimiento, null, array('class' => 'form-control input-xs', 'id' => 'tipomantenimiento')) !!}
		</div>
	</div>
	<div class="col-lg-4">
		{!! Form::label('taller_id', 'Taller:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('taller_id', $cboTaller, null, array('class' => 'form-control input-xs', 'id' => 'taller_id')) !!}
		</div>
	</div>
	{!! Form::button('<i class="fa fa-plus fa-lg table-add"></i>', array('class' => 'btn btn-success btn-sm', 'id' => 'btnAgregar', 'onclick' => 'agregarfila();')) !!}
</div>
<br />
<div class="table-responsive col-lg-12" style="max-height: 400px;overflow-y: auto;">
	<table id="example1" class="table table-bordered table-striped table-condensed table-hover">
		<thead>
			<tr>
				<th colspan="1"  class="text-center" >#</th>
				<th colspan="1" width="8%"  class="text-center" >CANT.</th>
				<th colspan="1" width="5%"  class="text-center" >UND</th>
				<th colspan="1" width="5%" class="text-center" >CODIGO</th>
				<th colspan="1" width="30%"  class="text-center" >DESCRIPCION</th>
				<th colspan="1"  class="text-center" >PROVEEDOR 1</th>
				<th colspan="1"  class="text-center" >PROVEEDOR 2</th>
				<th colspan="1"  class="text-center" >PROVEEDOR 3</th>
				<th colspan="1"  class="text-center" >Elim.</th>
			</tr>
		</thead>
		<tbody id="tbody">
			<?php
			$contador = 1;
			if(count($detalle)<=1)$oObservaciones=array();
			?>
			@foreach ($detalle as $key => $value)
				<tr>
					<td name="nnn" style="text-align:center;">{{ $contador }}</td>
					<td><input type='text' value='{{$value->cantidad}}' class='form-control input-xs' name='txtCantidad[]' /></td>
					<td><input type='text' value='{{$value->unidad}}' class='form-control input-xs' name='txtUnidad[]' /></td>
					<td><input type='text' value='{{$value->codigo}}' class='form-control input-xs' name='txtCodigo[]' /></td>
					<td><textarea class='form-control input-xs' name='txtDescripcion[]' rows='3'>{{$value->descripcion}}</textarea></td>
					<td><input type='text' value='{{$value->proveedor1}}' class='form-control input-xs' name='txtProveedor1[]' /></td>
					<td><input type='text' value='{{$value->proveedor2}}' class='form-control input-xs' name='txtProveedor2[]' /></td>
					<td><input type='text' value='{{$value->proveedor3}}' class='form-control input-xs' name='txtProveedor3[]' /></td>
					<td onclick="if(confirm('¿Desea Eliminar el item?')){borrarfila({{ $value->id }});deleteRow(this);}" style="color:#ff0000;text-align:center;width:50px;cursor: pointer;"><i class="material-icons text-center">close</i>
					<input type="hidden" name="iddetalle[]" value="{{ $value->id }}">
					</td>
				</tr>
				<?php
				$contador = $contador + 1;
				?>
			@endforeach
		</tbody>
	</table>
	<?php $items=''; if(count($detalle)>=1){ $items='1';}?>
	<input type="hidden" name="cuantasdescripciones" id="cuantasdescripciones" value="{{ $items }}"></p>
</div>

<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}

<style>
	.solo-lectura:read-only {
		background-color: transparent;
		cursor: not-allowed;
	}
	.hidden {
		display: none;
	}
	
	.dt-row-bordered-selectable{
		border: 2px solid blueviolet !important;
		cursor: pointer;
	}
	.dt-row-bordered{
		border: 2px solid blueviolet !important;
	}
	.showTable{
		display: block !important;
	}
	.data-table{
		float: left;
		position: absolute;
		display: none;
		z-index: 1000;
	}
</style>
<script type="text/javascript">
	var a = <?php echo $contador?> -1;
	$(document).ready(function() {
		configurarAnchoModal('1100');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		doSearchVehiculo();
	
	}); 

function agregarfila(){
	a++;
	document.getElementById('cuantasdescripciones').value='1';
	var tr = document.createElement("tr");
	tr.innerHTML = `
		<tr>
			<td name="nnn" style="text-align:center;">${a}</td>
			<td><input type='text' value='' class='form-control input-xs' name='txtCantidad[]' /></td>
			<td><input type='text' value='' class='form-control input-xs' name='txtUnidad[]' /></td>
			<td><input type='text' value='' class='form-control input-xs' name='txtCodigo[]' /></td>
			<td><textarea value='' class='form-control input-xs' name='txtDescripcion[]' rows='3'></textarea></td>
			<td><input type='text' value='' class='form-control input-xs' name='txtProveedor1[]' /></td>
			<td><input type='text' value='' class='form-control input-xs' name='txtProveedor2[]' /></td>
			<td><input type='text' value='' class='form-control input-xs' name='txtProveedor3[]' /></td>
			<td onclick="if(confirm('¿Desea Eliminar el item?')){deleteRow(this);}" style="color:#ff0000;text-align:center;width:50px;cursor: pointer;"><i class="material-icons text-center">close</i>
			<p><input type="hidden" name="iddetalle[]" id="ididid" value="-1"></p>
			</td>
		</tr>`;
	document.getElementById("tbody").appendChild(tr);
}

function deleteRow(btn) {
	a--;
	if (document.getElementsByName('personaId[]').length>1) {
		document.getElementById('cuantasdescripciones').value='1';
	}else{
		document.getElementById('cuantasdescripciones').value='';
	}
	var row = btn.parentNode;
  	row.parentNode.removeChild(row);
}

function borrarfila(iddd){

		var input = document.createElement("input");

	input.setAttribute("type", "hidden");

	input.setAttribute("name", "idseliminados[]");

	input.setAttribute("value", iddd);

	document.getElementById("tbody").appendChild(input);


	var fff=document.getElementsByName('datos'+iddd);
	for (var i = 0; i < fff.length; i++) {
		fff[i].innerHTML='';
	}
}

function mostrarCondicion(){
	if($("#condicionmercado").val()=="Nacional"){
		$("#divNacional").removeClass("hidden");
		$("#divInternacional").addClass("hidden");
	}else{
		$("#divNacional").addClass("hidden");
		$("#divInternacional").removeClass("hidden");
	}
}
mostrarCondicion();
</script>
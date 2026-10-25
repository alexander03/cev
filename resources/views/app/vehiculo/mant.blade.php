<?php 
$icono = '';
if ($vehiculo !== NULL) {
	$icono = $vehiculo->icono;
	$concesionaria_id = $vehiculo->concesionaria_id;
}
?>

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($vehiculo, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class='form-row'>
	<div class="form-group col-md-6 mt-4">

		{!! Form::label('ua_id', 'Ua :', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="u-ua-style js-ua-desc">
				<?php if($vehiculo && !is_null($vehiculo->ua)) echo $vehiculo ->ua->descripcion; ?>
			</div>
			<input type="text" name="ua_id" id="ua_id" class="form-control js-ua-id input-xs" 
			value="@if($vehiculo && !is_null($vehiculo->ua)){{$vehiculo->ua->codigo}}@endif">
			<small id="autoComplete_list1" class="text-danger"></small>
		</div>
	</div>
	<div class="form-group col-md-6 mt-4">
		{!! Form::label('modelo', 'Modelo:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('modelo', null, array('class' => 'form-control input-xs', 'id' => 'modelo')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 mt-4">
		{!! Form::label('anio', 'Año de fabricación:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('anio', null, array('class' => 'form-control input-xs', 'id' => 'anio')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 mt-4">
		{!! Form::label('placa', 'Placa:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('placa', null, array('class' => 'form-control input-xs', 'id' => 'placa')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 mt-4">
		{!! Form::label('motor', 'Motor:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('motor', null, array('class' => 'form-control input-xs', 'id' => 'motor')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 mt-4">
		{!! Form::label('chasis', 'Chasis:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('chasis', null, array('class' => 'form-control input-xs', 'id' => 'chasis')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 mt-4">
		{!! Form::label('color', 'Color:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('color', null, array('class' => 'form-control input-xs', 'id' => 'color')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 mt-4">
		{!! Form::label('asientos', 'Asientos:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('asientos', null, array('class' => 'form-control input-xs', 'id' => 'asientos')) !!}
		</div>
	</div>
	<div class="form-group col-md-6  ">
		{!! Form::label('area_id', 'Area:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('area_id', $cboArea, null, array('class' => 'form-control input-xs', 'id' => 'area_id')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('marca_id', 'Marca:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('marca_id', $cboMarca, null, array('class' => 'form-control input-xs', 'id' => 'marca_id')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('contratista_id', 'Propiedad de:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('contratista_id', $cboContratista, null, array('class' => 'form-control input-xs', 'id' => 'contratista_id')) !!}
		</div>
	</div>
	<div class="form-group col-md-6">
		{!! Form::label('carroceria_id', 'Carrocería:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('carroceria_id', $cboCarroceria ,null, array('class' => 'form-control input-xs', 'id' => 'carroceria_id')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('kilometraje_id', 'Regla:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('kilometraje_id', $cboKilometraje ,null, array('class' => 'form-control input-xs', 'id' => 'kilometraje_id')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('tipocombustible_id', 'Tipo Combustible:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('tipocombustible_id', $cboTipocombustible, null, array('class' => 'form-control input-xs', 'id' => 'tipocombustible_id')) !!}
		</div>
	</div>
	<div class="form-group col-md-6">
		{!! Form::label('kilometraje_ini', 'Kilometraje: inicial', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('kilometraje_ini', null, array('class' => 'form-control input-xs', 'id' => 'kilometraje_ini')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('precio', 'Precio Alquiler:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::number('precio', null, array('class' => 'form-control input-xs', 'id' => 'precio')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 mt-4">
		{!! Form::label('uatrabajo_id', 'Ua Trabajo:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="u-ua-style js-ua-trabajo-desc">
				<?php if($vehiculo && !is_null($vehiculo->uatrabajo)) echo $vehiculo ->uatrabajo->descripcion; ?>
			</div>
			<input type="text" name="uatrabajo_id" id="uatrabajo_id" class="form-control js-ua-trabajo-id input-xs" 
			value="@if($vehiculo && !is_null($vehiculo->uatrabajo) ){{$vehiculo->uatrabajo->codigo}}@endif">
			<small id="autoComplete_listTrabajo" class="text-danger"></small>
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('decimales', 'Decimales CheckList:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::number('decimales', null, array('class' => 'form-control input-xs', 'id' => 'decimales')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('concesionaria_id', 'Concesionaria:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('concesionaria_id', $cboConcesionaria, $concesionaria_id, array('class' => 'form-control input-xs', 'id' => 'concesionaria_id')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('estado', 'Estado:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
			<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('estado', $cboEstado, null, array('class' => 'form-control input-xs', 'id' => 'estado')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('saltarkm', 'Saltar Km:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
			<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('saltarkm', $cboSaltarkm, null, array('class' => 'form-control input-xs', 'id' => 'saltarkm')) !!}
		</div>
	</div>
	<div class="form-group col-md-6" style="display: none;">
		{!! Form::label('kilometraje_act', 'Kilometraje: actual', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('kilometraje_act', null, array('class' => 'form-control input-xs', 'id' => 'kilometraje_act')) !!}
		</div>
	</div>
</div>
<div class="form-group ">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('500');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		doSearchUA();
		doSearchUATrabajo();
	}); 
</script>

<?php 
$icono = '';
if ($equipo !== NULL) {
	$icono = $equipo->icono;
	$concesionaria_id = $equipo->concesionaria_id;
}
?>

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($equipo, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="row">
	<div class="form-group col-md-6 ">

		{!! Form::label('ua_id', 'Ua :', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="u-ua-style js-ua-desc">
				<?php if($equipo && $equipo->ua_id) echo $equipo ->ua->descripcion; ?>
			</div>
			<input type="text" name="ua_id" id="ua_id" class="form-control js-ua-id input-xs" 
			value="@if($equipo && $equipo->ua_id ){{$equipo->ua->codigo}}@endif">
			<small id="autoComplete_list1" class="text-danger"></small>
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('descripcion', 'Descripcion:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('modelo', 'Modelo:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('modelo', null, array('class' => 'form-control input-xs', 'id' => 'modelo')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('anio', 'Año de fabricación:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('anio', null, array('class' => 'form-control input-xs', 'id' => 'anio')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('placa', 'Placa:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('placa', null, array('class' => 'form-control input-xs', 'id' => 'placa')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('capacidad_carga', 'Capac. Carga(m3):', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::number('capacidad_carga', null, array('class' => 'form-control input-xs', 'id' => 'capacidad_carga')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('horas_min', 'Horas mínimas:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::number('horas_min', null, array('class' => 'form-control input-xs', 'id' => 'horas_min')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('precio', 'Precio Alquiler:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::number('precio', null, array('class' => 'form-control input-xs', 'id' => 'precio')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('moneda', 'Moneda:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('moneda',[ '#' => 'Selecione moneda'  , 0 => 'Soles S/' , 1 => 'Dolar $' ] , null, array('class' => 'form-control input-xs', 'id' => 'moneda')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('unidad', 'Unidad:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('unidad_id',[ '#' => 'Selecione unidad'  , 6 => 'Horas' , 7 => 'Días' ] , null, array('class' => 'form-control input-xs', 'id' => 'unidad_id')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('marca_id', 'Marca:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('marca_id', $cboMarca, null, array('class' => 'form-control input-xs', 'id' => 'marca_id')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('contratista_id', 'Subcontratista:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('contratista_id', $cboContratista, null, array('class' => 'form-control input-xs', 'id' => 'contratista_id')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('area_id', 'Area:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
			<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('area_id', $cboArea, null, array('class' => 'form-control input-xs', 'id' => 'area_id')) !!}
		</div>
	</div>

	<div class="form-group col-md-6 ">
		{!! Form::label('horometraje_id', 'Regla:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('horometraje_id', $cboHorometraje ,null, array('class' => 'form-control input-xs', 'id' => 'horometraje_id')) !!}
		</div>
	</div>
	<div class="form-group col-md-6">
		{!! Form::label('uatrabajo_id', 'Ua Trabajo:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			<div class="u-ua-style js-ua-trabajo-desc">
				<?php if($equipo && $equipo->uatrabajo_id) echo $equipo ->uatrabajo->descripcion; ?>
			</div>
			<input type="text" name="uatrabajo_id" id="uatrabajo_id" class="form-control js-ua-trabajo-id input-xs" 
			value="@if($equipo && $equipo->uatrabajo_id ){{$equipo->uatrabajo->codigo}}@endif">
			<small id="autoComplete_listTrabajo" class="text-danger"></small>
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('horometroinicial', 'Horometro Ini.:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('horometroinicial', null, array('class' => 'form-control input-xs', 'id' => 'horometroinicial')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('mantenimiento', 'Mantenimiento:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
			<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('mantenimiento', $cboMantenimiento, null, array('class' => 'form-control input-xs', 'id' => 'mantenimiento')) !!}
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
		{!! Form::label('saltarhoro', 'Saltar Horómetro:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
			<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('saltarhoro', $cboSaltarhoro, null, array('class' => 'form-control input-xs', 'id' => 'saltarhoro')) !!}
		</div>
	</div>
	<div class="form-group col-md-6 ">
		{!! Form::label('decimales', 'Decimales CheckList:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::number('decimales', null, array('class' => 'form-control input-xs', 'id' => 'decimales')) !!}
		</div>
	</div>
</div>
<div class="form-group ">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>

{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('600');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		doSearchUA();
		doSearchUATrabajo();
	}); 
</script>

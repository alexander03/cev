<?php 
$icono = '';
if ($tipohora !== NULL) {
	$icono = $tipohora->icono;
}
?>

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($tipohora, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="form-group">
	{!! Form::label('codigo', 'Código:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::text('codigo', null, array('class' => 'form-control input-xs', 'id' => 'codigo')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('descripcion', 'Descripcion:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::text('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('tipo', 'Tipo:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::select('tipo', $cboTipo, null, array('class' => 'form-control input-xs', 'id' => 'descripcion')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('prioridad', 'Prioridad:', array('class' => 'col-lg-6 col-md-6 col-sm-6 control-label')) !!}
		<input type="checkbox" name="prioridad" id="prioridad" class="input-xs"  @if($tipohora) checked @endif >
		
	
</div>
<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('350');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	}); 
</script>
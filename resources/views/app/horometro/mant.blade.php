<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($horometro, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="form-group">
	{!! Form::label('grifo_id', 'Grifo:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::select('grifo_id', $cboGrifo, null, array('class' => 'form-control input-xs', 'id' => 'grifo_id')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('fecha', 'Fecha:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::date('fecha', null, array('class' => 'form-control input-xs', 'id' => 'fecha')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('inicio', 'Inicio:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::number('inicio', null, array('class' => 'form-control input-xs', 'id' => 'inicio')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('fin', 'Fin:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::number('fin', null, array('class' => 'form-control input-xs', 'id' => 'fin')) !!}
	</div>
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
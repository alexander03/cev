<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($kilometraje, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="form-group">
	{!! Form::label('descripcion', 'Descripcion:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::text('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('limite_inf', 'Límite inferior:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::number('limite_inf', null, array('class' => 'form-control input-xs', 'id' => 'limite_inf')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('limite_sup', 'Límite superior:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::text('limite_sup', null, array('class' => 'form-control input-xs', 'id' => 'limite_sup')) !!}
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
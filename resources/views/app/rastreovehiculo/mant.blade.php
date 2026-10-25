<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($document, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
{!! Form::hidden('tipo2', $tipo2, array('id' => 'tipo2')) !!}

<div class="form-group">
	{!! Form::label('placa', 'Placa:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::text('placa', $placa, array('class' => 'form-control input-xs', 'id' => 'placa', 'disabled'=>'true')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('comentario', 'Comentario:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::textarea('comentario', null, array('class' => 'form-control input-xs', 'id' => 'comentario','rows'=>'5')) !!}
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
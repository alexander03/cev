<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($configuracion, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="form-group">
	{!! Form::label('nombre', 'Nombre:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12" style="padding-top: 10px;">
		{!! Form::text('nombre', null, array('class' => 'form-control input-xs', 'id' => 'nombre', 'readonly' => 'true')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('descripcion', 'Descripcion:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12" style="padding-top: 10px;">
		{!! Form::text('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion', 'readonly' => 'true')) !!}
	</div>
</div>
@if($configuracion->id < 7)
	<div class="form-group">
		{!! Form::label('valor', 'Valor:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::number('valor', null, array('class' => 'form-control input-xs', 'id' => 'valor', 'min' => '0')) !!}
		</div>
	</div>
@else
	<div class="form-group">
		{!! Form::label('valor', 'Valor:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::textarea('valor', null, array('class' => 'form-control input-xs', 'id' => 'valor', 'rows' => '5')) !!}
		</div>
	</div>
@endif
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
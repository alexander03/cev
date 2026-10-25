<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($piepagina, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="form-group">
	{!! Form::label('orden', 'Orden:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::number('orden', null, array('class' => 'form-control input-xs', 'id' => 'orden', 'maxlength' => '100')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('nombre', 'Nombre:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::text('nombre', null, array('class' => 'form-control input-xs', 'id' => 'nombre', 'maxlength' => '100')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('cargo', 'Cargo:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::text('cargo', null, array('class' => 'form-control input-xs', 'id' => 'cargo', 'maxlength' => '100')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('formato', 'Formato:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::select('formato', $cboFormato, null, array('class' => 'form-control input-xs', 'id' => 'formato')) !!}
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
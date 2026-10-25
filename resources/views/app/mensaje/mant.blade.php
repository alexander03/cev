<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($mensaje, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="form-group">
	{!! Form::label('fechainicio', 'Fecha Inicio:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::date('fechainicio', null, array('class' => 'form-control input-xs', 'id' => 'fechainicio')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('fechafin', 'Fecha Fin:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::date('fechafin', null, array('class' => 'form-control input-xs', 'id' => 'fechafin')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('descripcion', 'Descripcion:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::textarea('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion', 'rows' => '5')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('tipouser_id', 'Tipo Usuario:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::select('tipouser_id', $cboTipouser, null, array('class' => 'form-control input-xs', 'id' => 'tipouser_id')) !!}
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
		configurarAnchoModal('400');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	}); 
</script>
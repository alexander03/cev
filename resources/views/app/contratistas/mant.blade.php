<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($contratista, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="form-group">
	{!! Form::label('razonsocial', 'Razon Social:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::text('razonsocial', null, array('class' => 'form-control input-xs', 'id' => 'razonsocial', 'maxlength' => '150')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('ruc', 'RUC:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::text('ruc', null, array('class' => 'form-control input-xs', 'id' => 'ruc', 'maxlength' => '11')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('propietario', 'Contacto1:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::text('propietario', null, array('class' => 'form-control input-xs', 'id' => 'propietario')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('email', 'E-Mail1:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::email('email', null, array('class' => 'form-control input-xs', 'id' => 'email')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('telefono', 'Teléfono1:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::text('telefono', null, array('class' => 'form-control input-xs', 'id' => 'telefono', 'maxlength' => '30')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('propietario2', 'Abreviatura:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::text('propietario2', null, array('class' => 'form-control input-xs', 'id' => 'propietario2')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('email2', 'E-Mail2:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::email('email2', null, array('class' => 'form-control input-xs', 'id' => 'email2')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('telefono2', 'Teléfono2:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::text('telefono2', null, array('class' => 'form-control input-xs', 'id' => 'telefono2', 'maxlength' => '30')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('propietario3', 'Contacto3:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::text('propietario3', null, array('class' => 'form-control input-xs', 'id' => 'propietario3')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('email3', 'E-Mail3:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::email('email3', null, array('class' => 'form-control input-xs', 'id' => 'email3')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('telefono3', 'Teléfono3:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::text('telefono3', null, array('class' => 'form-control input-xs', 'id' => 'telefono3', 'maxlength' => '30')) !!}
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
		const inputCode = document.getElementById('ruc');
		inputCode.addEventListener('keydown', e => {
			if (e.keyCode < 48|| (e.keyCode > 57&&e.keyCode < 96)
			|| (e.keyCode > 105)){
				if(e.keyCode!=8&&e.keyCode!=13&&e.keyCode!=37&&e.keyCode!=39)
    			e.preventDefault();
  			}
  		})
	}); 
</script>
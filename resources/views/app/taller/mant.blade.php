<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($taller, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<section class="form-row">
	<div class="form-group col-md-12 p-3">
		<label for="ruc" class="pl-3">RUC</label>
		{!! Form::text('ruc', null, array('class' => 'form-control input-xs', 'id' => 'ruc', 'maxlength' => '11')) !!}
	</div>
	<div class="form-group col-md-12 p-3">
		<label for="razonsocial" class="pl-3">Razon Social</label>
		{!! Form::text('razonsocial', null, array('class' => 'form-control input-xs', 'id' => 'razonsocial')) !!}
	</div>
	<div class="form-group col-md-12 p-3">
		<label for="contacto" class="pl-3">Contacto</label>
		{!! Form::text('contacto', null, array('class' => 'form-control input-xs', 'id' => 'contacto')) !!}
	</div>
	<div class="form-group w-100">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
</section>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('450');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	}); 
</script>
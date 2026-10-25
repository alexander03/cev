<?php 
$icono = '';
if ($unidad !== NULL) {
	$icono = $unidad->icono;
}
?>

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($unidad, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<section class="form-row">
	<div class="form-group col-md-12 p-3">
		<label for="descripcion" class="pl-3">Descripción</label>
		<textarea name="descripcion" id="descripcion" class="form-control" rows="3">@php if($unidad) echo $unidad->descripcion @endphp</textarea>
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
		configurarAnchoModal('350');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	}); 
</script>
@if(!isset($childs))
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($modelo, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
{{-- comment --}}
{{-- {!! $mensaje or '<blockquote><p class="text-danger">¿Esta seguro de eliminar el registro?</p></blockquote>' !!} --}}

@if ($mensaje) <blockquote><p class="text-danger">¿Esta seguro de eliminar el registro?</p></blockquote> @endif
<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal((contadorModal - 1));')) !!}
	</div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		configurarAnchoModal('350');
	}); 
</script>
@else
<section>
	<h3 class="text-danger text-center">No puedes eliminar este registro</h3>
	<p class="text-center text-secondary">
		Existen registros en {{ $entidadChild }} que dependenden de este. 
	</p>
	<div class="d-flex justify-content-center">
		<button class="btn btn-success" onclick="getModal()">Aceptar</button>
	</div>
</section>
<script>
	const getModal = ()=>{
		const idModal = document.querySelector('.bootbox.modal').id;
		$(`#${idModal}`).modal('hide');
	};
</script>
@endif
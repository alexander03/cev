<?php 
$icono = '';
if ($tipouser !== NULL) {
	$icono = $tipouser->icono;
}
?>

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($tipouser, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="form-group">
	{!! Form::label('descripcion', 'Descripcion:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::text('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion')) !!}
	</div>
</div>
@foreach($Gruposmenu as $grupomenu)
<br>
<p class="h6 text-secondary ml-3 ">{{$grupomenu->descripcion}}</p>
	@foreach($grupomenu->opcionesmenu()->get() as $opcionmenu)
		<div class="form-check">
			{!! Form::label( $opcionmenu->id , $opcionmenu->descripcion , array('class' => 'col-lg-9 col-md-9 col-sm-9 form-check-label')) !!}
			
			<input type="checkbox"   name="opcionmenu[]" id="{{$opcionmenu->id}}" value="{{$opcionmenu->id}}"
				@if($tipouser &&  

						$tipouser->whereHas('accesos', function($query) use($opcionmenu){
							$query->where('opcionmenu_id',$opcionmenu->id);
						})->where('id',$tipouser->id)->count() > 0

				)
					checked
				@endif
			>
			
		</div>
	@endforeach
@endforeach
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
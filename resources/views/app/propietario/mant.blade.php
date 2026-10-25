<?php 
$icono = '';
if ($propietario !== NULL) {
	$icono = $propietario->icono;
}
?>

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($propietario, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<section class="form-row">
	<div class="form-group col-md-6 p-3">
		<label for="descripcion" class="pl-3">Descripción</label>
		<textarea name="descripcion" id="descripcion" class="form-control" rows="1">@php if($propietario) echo $propietario->descripcion @endphp</textarea>
	</div>
	<div class="form-group col-md-6 p-3 u-search-ua">
		<label for="id-ua" class="pl-3">Código Ua</label>
		<div class="u-ua-style js-ua-desc">
			<?php if($propietario) if($propietario -> ua -> id) echo $propietario -> ua -> descripcion;?>
		</div>
		<input type="text" 
			tabindex="1"
			name="ua_id"
			id="id-ua"
			class="form-control js-ua-id" 
			value="<?php if($propietario) if($propietario -> ua -> id) echo $propietario -> ua -> codigo;?>">
		<small id="autoComplete_list1" class="text-danger"></small>
	</div>
	<div class="form-group col-md-6 p-3">
	  <label for="id-date-begin" class="pl-3">Fecha de llegada</label>
	  <input type="date" name="fecha_llegada" id="id-date-begin" class="form-control" value="<?php if($propietario) echo $propietario->fecha_llegada ?>">
	</div>
	<div class="form-group col-md-6 p-3">
	  <label for="id-date-end" class="pl-3">Fecha de salida</label>
	  <input type="date" name="fecha_salida" id="id-date-end" class="form-control" value="<?php if($propietario) echo $propietario->fecha_salida ?>">
	</div>
	<div class="form-group col-md-6 p-3">
	  <label for="id-date-cont" class="pl-3">Fecha de contrato</label>
	  <input type="date" name="fecha_contrato" id="id-date-cont" class="form-control" value="<?php if($propietario) echo $propietario->fecha_contrato ?>">
	</div>
	<div class="form-group col-md-6 p-3">
	  <label for="id-status" class="pl-3">Status</label>
	  <input type="text" name="status" id="id-status" class="form-control" value="<?php if($propietario) echo $propietario->status ?>">
	</div>
	<div class="form-group col-md-6 p-3">
	  <label for="id-hra" class="pl-3">Hra</label>
	  <input type="text" name="hra" id="id-hra" class="form-control" value="<?php if($propietario) echo $propietario->hra ?>">
	</div>
	<div class="form-group col-md-6 p-3">
	  <label for="id-hrb" class="pl-3">Hrb</label>
	  <input type="text" name="hrb" id="id-hrb" class="form-control" value="<?php if($propietario) echo $propietario->hrb ?>">
	</div>
	<div class="form-group col-md-6 p-3">
	  <label for="id-hrc" class="pl-3">Hrc</label>
	  <input type="text" name="hrc" id="id-hrc" class="form-control" value="<?php if($propietario) echo $propietario->hrc ?>">
	</div>
	<div class="form-group col-md-6 p-3">
	  <label for="id-km" class="pl-3">Km</label>
	  <input type="number" name="km" id="id-km" class="form-control" value="<?php if($propietario) echo $propietario->km ?>">
	</div>
	<div class="form-group col-md-6 p-3">
	  <label for="id-obs" class="pl-3">Observación</label>
	  <textarea name="observacion" id="id-obs" class="form-control" rows="1">@php if($propietario) echo $propietario->observacion @endphp</textarea>
	</div>
	<div class="form-group col-md-6 p-3">
		<label for="id-location" class="pl-3">Ubicación</label>
		<input type="text" name="ubicacion" id="id-location" class="form-control" value="<?php if($propietario) echo $propietario->ubicacion ?>">
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
		configurarAnchoModal('800');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		doSearchUA();
	}); 
</script>
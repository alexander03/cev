<?php 
$icono = '';
if ($abastecimiento !== NULL) {
	$icono = $abastecimiento->icono;
}
$datemin = date("Y-m-d",strtotime("-".MIN_DIAS." days",strtotime("now")));
?>

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($abastecimiento, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<section class="form-row">
	<div class="form-group col-12 col-md-6 p-3">
		{!! Form::label('user_id', 'Conductor/Operador:', array('class' => 'pl-3')) !!}
		<input type="text" class="form-control" style="display: none">
		<section class="d-flex mt-1"> 
			{!! Form::select('user_id', $cboUsuario, null, array('class' => 'form-control', 'id' => 'user_id')) !!}
		</section>
	</div>
	<!--div class="form-group col-12 col-md-6 p-3">
		<label for="id-usuario" class="pl-3">Usuario</label>
		<input type="text"  onblur="obtenerHora();"
			name="usuario" 
			id="id-usuario" 
			class="form-control" 
			value="<?php if($abastecimiento) echo ''?>">
	</div>
	<div class="form-group col-12 col-md-6 p-3">
		<label for="id-password" class="pl-3">Password</label>
		<input type="password" onblur="obtenerHora();"
			name="password" 
			id="id-password" 
			class="form-control" 
			value="<?php if($abastecimiento) echo ''?>">
	</div-->
	<div class="form-group col-12 col-md-6 p-3">
		<label for="id-fa" class="pl-3">Fecha de abastecimiento</label>
		<input type="date" 
			name="fecha_abastecimiento" 
			id="id-fa" 
			class="form-control" 
			value="<?php if($abastecimiento) echo $abastecimiento -> fecha_abastecimiento; else echo date('Y-m-d')?>" max="{{date('Y-m-d')}}" min="{{$datemin}}" onkeydown="return false" >
	</div>
	<div class="form-group col-12 col-md-6 p-3">
		<label for="id-grifo" class="pl-3">Grifo</label>
		<input type="text" 
			name="grifo_id" 
			id="id-grifo" 
			class="form-control js-grifo-id" 
			value="<?php if($abastecimiento) echo $abastecimiento -> grifo -> descripcion ?>">
		<small id="autoComplete_list2" class="text-danger"></small>
	</div>
	<div class="form-group col-md-6 p-3">
		<label for="id-tipo-combustible" class="pl-3">Tipo de combustible</label>
		<input type="text" class="form-control" style="display: none">
		<section class="d-flex mt-1"> 
			<select class="form-control" name="tipocombustible_id" id="id-tipo-combustible">
				<option value="">Seleccionar tipo de combustible</option>
				@foreach ($lstTipoCombustibles as $tipC)
					<option value="{{ $tipC -> id }}" <?php if($abastecimiento) if($abastecimiento -> tipocombustible_id == $tipC -> id) echo 'selected' ?>>
						{{ $tipC -> descripcion }}
					</option>
				@endforeach
			</select>
		</section>	
	</div>
	<div class="form-group col-12 col-md-6 p-3">
		<label for="id-equipo" class="pl-3">UA / Desc. (Equipo - Vehículo)</label>
		{{-- <div class="u-ua-style js-equipo-desc"> --}}
			<?php 
				//if($abastecimiento) 
				//	if(isset($abastecimiento -> equipo)) echo $abastecimiento -> equipo -> descripcion;
				//	else if(isset($abastecimiento -> vehiculo)) echo $abastecimiento -> vehiculo -> modelo 
			?>
		{{-- </div> --}}
		<input type="text" 
			id="id-equipo" 
			class="form-control js-equipo-id" 
			value="<?php if($abastecimiento) if(isset($abastecimiento -> equipo)) echo $abastecimiento -> equipo -> descripcion; else if(isset($abastecimiento -> vehiculo)) echo $abastecimiento -> vehiculo -> modelo;?>">
		<input type="hidden" 
			name="equipo_tipo" 
			class="js-equipo-hidden"
			value="<?php if($abastecimiento) if(isset($abastecimiento -> equipo)) echo 'e'; else if(isset($abastecimiento -> vehiculo)) echo 'v';?>">
		<input type="hidden"
			name="equipo_id"
			class="js-equipo-id-hidden"
			value="<?php if($abastecimiento) if(isset($abastecimiento -> equipo)) echo $abastecimiento -> equipo -> id; else if(isset($abastecimiento -> vehiculo)) echo $abastecimiento -> vehiculo -> id;?>">
		<small id="autoComplete_list4" class="text-danger"></small>
	</div>
	<div class="form-group col-12 col-md-6 p-3 u-search-ua">
		<label for="id-ua" class="pl-3">PLANO UAs</label>
		<div class="u-ua-style js-ua-desc">
			<?php if($abastecimiento) if(isset($abastecimiento -> ua)) echo $abastecimiento -> ua -> descripcion; else echo 'Sin ua';?>
		</div>
		<input type="text" 
			name="ua_id" 
			id="id-ua" 
			class="form-control js-ua-id" 
			value="<?php if($abastecimiento) echo $abastecimiento -> ua -> codigo?>">
		<small id="autoComplete_list1" class="text-danger"></small>
	</div>
	<div class="form-group col-12 col-md-6 p-3">
		<label for="id-qtdgl" class="pl-3">QTD(GL)</label>
		<input type="text" 
			name="qtdgl" 
			id="id-qtdgl" 
			class="form-control js-qtd-gl" 
			value="<?php if($abastecimiento) echo $abastecimiento -> qtdgl?>">
	</div>
	<div class="form-group col-12 col-md-6 p-3">
		<label for="id-qtdl" class="pl-3">QTD(L)</label>
		<input type="text" 
			name="qtdl" 
			id="id-qtdl" 
			class="form-control js-qtd-l"
			readonly="readonly" 
			value="<?php if($abastecimiento) echo $abastecimiento -> qtdl?>">
	</div>
	<div class="form-group col-12 col-md-6 p-3">
		<label for="id-km" class="pl-3">Km/Horometro</label>
		<input type="text" 
			name="km" 
			id="id-km" 
			class="form-control" 
			value="<?php if($abastecimiento) echo $abastecimiento -> km?>">
	</div>
	<div class="form-group col-12 col-md-6 p-3" style="display:none;">
		<label for="id-abast-dia" class="pl-3">Abastecimiento por día</label>
		<input type="text" 
			name="abastecimiento_dia" 
			id="id-abast-dia" 
			class="form-control" 
			value="<?php if($abastecimiento) echo $abastecimiento -> abastecimiento_dia;else echo "0";?>">
	</div>
	<div class="form-group col-md-6 p-3">
		<label for="id-lugar" class="pl-3">Lugar de abastecimiento</label>
		<input type="text" class="form-control" style="display: none">
		<section class="d-flex mt-1"> 
			<select class="form-control" name="abastecimiento_id" id="id-lugar">
				<option value="">Seleccionar lugar de abastecimiento</option>
				@foreach ($lstAbastecimientos as $abast)
					<option value="{{ $abast -> id }}" <?php if($abastecimiento) if($abastecimiento -> abastecimiento_id == $abast -> id) echo 'selected' ?>>
						{{ $abast -> descripcion }}
					</option>
				@endforeach
			</select>
		</section>	
	</div>
	<div class="form-group col-12 col-md-6 p-3">
		<label for="id-motivo" class="pl-3">Motivo</label>
		<input type="text" 
			name="motivo" 
			id="id-motivo" 
			class="form-control" 
			value="<?php if($abastecimiento) echo $abastecimiento -> motivo?>">
	</div>
	<div class="form-group col-md-6 p-3">
		<label class="pl-3">Comprobante</label>
		<input type="text" class="form-control" style="display: none">
		<section class="d-flex mt-1"> 
			<select class="form-control" name="comprobante">
				<option value="">Seleccionar comprobante</option>
				<option value="BOLETA" <?php if($abastecimiento) if($abastecimiento -> comprobante) echo 'selected' ?>>Boleta</option>
				<option value="FACTURA" <?php if($abastecimiento) if($abastecimiento -> comprobante) echo 'selected' ?>>Factura</option>
				<option value="OTROS" <?php if($abastecimiento) if($abastecimiento -> comprobante) echo 'selected' ?>>Otros</option>
			</select>
		</section>	
	</div>
	<div class="form-group col-12 col-md-6 p-3">
		<label for="id-comprobante" class="pl-3">Numero de comprobante</label>
		<input type="text" 
			name="numero_comprobante" 
			id="id-comprobante" 
			class="form-control" 
			value="<?php if($abastecimiento) echo $abastecimiento -> numero_comprobante?>">
	</div>
	<div class="form-group col-md-6 p-3" style="display:none">
		<label for="id-f-init" class="pl-3">Hora de inicio</label>
		<input type="time" name="hora_inicio" 
			id="id-f-init" class="form-control" 
			value="<?php if($abastecimiento) echo $abastecimiento->hora_inicio; else echo ''; ?>">
	</div>
	<div class="form-group col-md-6 p-3" style="display:none">
		<label for="id-f-fin" class="pl-3">Hora de fin</label>
		<input type="time" name="hora_fin" 
			id="id-f-fin" class="form-control" 
			value="<?php if($abastecimiento) echo $abastecimiento->hora_fin; ?>">
	</div>
	<div class="form-group col-md-6 p-3">
	    <input type="checkbox" for="contratista_id" onclick="if(this.checked){$('#divContratista').css('display','');}else{$('#divContratista').css('display','none');}" />
		{!! Form::label('contratista_id', 'Subcontratista:', array('class' => 'pl-3')) !!}
		<section class="mt-1" id="divContratista" style="display:none !important;"> 
			{!! Form::select('contratista_id', $cboContratista, null, array('class' => 'form-control', 'id' => 'contratista_id')) !!}
		</section>
	</div>
	<div class="form-group col-md-6">
		<label for="id-descuento" class="pl-3">Descuento</label>
		<section class="mt-1" style="margin-left: 18px;"> 
			{!! Form::select('descuento', ["N"=>"No","S"=>"SI"], null, array('class' => 'form-control', 'id' => 'descuento')) !!}
		</section>
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
		doSearchGrifo();
		// doSearchConductor();
		doSearchEquipo();
		convertGLtoL();
	}); 

function obtenerHora(){
    if($("#id-password").val().length>0 && $("#id-usuario").val().length>0){
        var fecha = new Date();
        var hora = fecha.getHours();console.log(fecha.toLocaleTimeString());
        var hora2 = fecha.toLocaleTimeString().substring(0,5);
        $("#id-f-init").val(hora2);
    }
}
</script>
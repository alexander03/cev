@php
$datemin = date("Y-m-d",strtotime("-".MIN_DIAS." days",strtotime("now")));
@endphp
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($obj, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<section class="form-row" id="inicioregrepveh">
	<div class="form-group col-lg-12 col-md-12 col-sm-12">
		<h4 name="idconc" id="oConcesionaria">Concesionaria: {{ $oConcesionaria }}</h4>
		<input type="hidden" name="concesionaria_id" id="concesionaria_id" value="{{ $idconc }}">
		<input type="hidden" name="tipo" id="tipo" value="{{ $tipo }}">
	</div>
	<div class="form-group col-lg-7 col-md-7 col-sm-7">
		{!! Form::label('client', 'Taller:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12" style="margin-top: 15px">
			{!! Form::text('cliente', null, array('class' => 'form-control input-xs', 'id' => 'cliente', 'maxlength' => '100', 'readonly'=>'true')) !!}
		</div>
	</div>
	<div class="form-group col-lg-5 col-md-5 col-sm-5">
		{!! Form::label('ordencompra', 'DOCMAT:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12" style="margin-top: 15px">
			{!! Form::text('ordencompra', null, array('class' => 'form-control input-xs', 'id' => 'ordencompra', 'maxlength' => '100', 'readonly'=>'true')) !!}
		</div>
	</div>
	<div class="mt-8 mb-4 ml-6  col-lg-12 col-md-12 col-sm-12">
		<p class="text-warning"  style="margin-bottom: 0;">Datos Generales</p> 
	</div>
	<div class="form-group col-4 col-md-4 p-3 u-search-ua">
		<label for="ua" class="pl-3">C&oacute;digo Ua</label>
		<div class="u-ua-style js-vehiculo-desc">
			{{$placa}}
		</div>
		<input class="js-vehiculo-hiddenid" id="vehiculo_id" name="vehiculo_id" type="hidden" value="{{$vehiculo_id}}">
		<input class="js-equipo-hiddenid" id="equipo_id" name="equipo_id" type="hidden" value="{{$equipo_id}}">
		<input class="form-control input-xs js-vehiculo-id" id="uades" name="uades" type="text" value="{{$uades}}" readonly="">
		
		<small id="autoComplete_listve" class="text-danger"></small>
	</div>
	
	<div class="form-group col-lg-4 col-md-4 col-sm-4">
		{!! Form::label('fin', 'Fecha DOCMAT:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12" style="margin-top: 15px">
			{!! Form::date('fechaentrada', null, array('class' => 'form-control input-xs', 'id' => 'fechaentrada', 'maxlength' => '100','max' =>date('Y-m-d'), 'min'=>$datemin,'onkeydown'=>'return false','readonly'=>'true' )) !!}
		</div>
	</div>
	<div class="form-group col-lg-12 col-md-12 col-sm-12">
		{!! Form::label('checklistvehicular_id', 'CHECKLIST:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label','style'=>'font-weight:bold;color:blue;')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12" id="divChecklist">
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
</section>
{!! Form::close() !!}

<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('900');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		const inputCode = document.getElementById('codigo');
		inputCode.addEventListener('change', e => {
			if(isNaN(e.target.value)) e.target.value = '';
		})

		inputCode.addEventListener('keydown', e => {
			//TODO: que acepte las teclas: flecha left y right
			if(e.target.value.length > 6 && e.keyCode != 8) e.preventDefault();
			if(e.keyCode < 8 || (e.keyCode >9 && e.keyCode< 48) || (e.keyCode >57 && e.keyCode< 67) || (e.keyCode >67 && e.keyCode< 86) || (e.keyCode >86 && e.keyCode< 96) || e.keyCode> 105) e.preventDefault();
		})
	}); 	

	function generarCombo(vid){
	    $.ajax({
	        type: "POST",
	        url: "regrepveh/comboChecklist",
	        data: "vehiculo_id="+$("#vehiculo_id").val()+"&equipo_id="+$("#equipo_id").val()+"&_token="+$(':input[name="_token"]').val(),
	        success: function(a) {
	            $('#divChecklist').html(a);
	            if(vid!="0"){
	            	$("#checklistvehicular_id").val(vid);
	            }
		    }
	    });
	}
	@if(!is_null($obj))
		generarCombo({{$obj->checklistvehicular_id}});
	@endif
</script>
@php
$datemin = date("Y-m-d",strtotime("-".MIN_DIAS." days",strtotime("now")));
@endphp
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($diagnostico, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<section class="form-row">
	<div class="form-group col-lg-12 col-md-12 col-sm-12">
		<h4 name="idconc" id="oConcesionaria">Concesionaria: {{ $oConcesionaria }}</h4>
		<input type="hidden" name="concesionaria_id" id="concesionaria_id" value="{{ $idconc }}">
	</div>
	<div class="form-group col-lg-3 col-md-3 col-sm-3">
		{!! Form::label('numero', 'Numero:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12" style="margin-top: 15px">
			{!! Form::text('numero', $numero, array('class' => 'form-control input-xs', 'id' => 'numero', 'readonly'=>'true')) !!}
		</div>
	</div>
	<div class="form-group col-lg-3 col-md-3 col-sm-3">
		{!! Form::label('fecha', 'Fecha Mant.:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12" style="margin-top: 15px">
			{!! Form::date('fecha', null, array('class' => 'form-control input-xs', 'id' => 'fecha')) !!}
		</div>
	</div>
	<div class="form-group col-lg-3 col-md-3 col-sm-3" style="margin-top: -15px">
		{!! Form::label('tipo', 'Tipo de Mantenimiento:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label','style'=>'margin-top:5px')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('tipomantenimiento',array(''=>'Seleccione Tipo','1'=>'Preventivo','2'=>'Correctivo'), null, array('class' => 'form-control input-xs', 'id' => 'tipomantenimiento')) !!}
		</div>
	</div>
	<div class="form-group col-3 col-md-3 p-3 u-search-ua">
		<label for="ua" class="pl-3">UA / Placa</label>
		<div class="u-ua-style js-vehiculo-desc">
			{{$placa}}
		</div>
		<input class="js-vehiculo-hiddenid" id="vehiculo_id" name="vehiculo_id" type="hidden" value="{{$vehiculo_id}}">
		<input class="js-equipo-hiddenid" id="equipo_id" name="equipo_id" type="hidden" value="{{$equipo_id}}">
		<input class="form-control input-xs js-vehiculo-id" id="uades" name="uades" type="text" value="{{$uades}}">
		
		<small id="autoComplete_listve" class="text-danger"></small>
	</div>
	<div class="form-group col-lg-3 col-md-3 col-sm-3">
		{!! Form::label('docmat', 'DOCMAT:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12" style="margin-top: 15px">
			{!! Form::text('docmat', null, array('class' => 'form-control input-xs', 'id' => 'docmat')) !!}
		</div>
	</div>
	<div class="mt-8 mb-4 ml-6  col-lg-12 col-md-12 col-sm-12">
		<p class="text-warning"  style="margin-bottom: 0;">Datos Generales</p> 
	</div>
	<div class="form-group col-lg-5 col-md-5 col-sm-5">
		{!! Form::label('taller_id', 'Taller:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('cliente', null, array('class' => 'form-control input-xs', 'id' => 'cliente', 'maxlength' => '100','style'=>'display:none')) !!}
			{!! Form::select('taller_id',$cboTaller, null, array('class' => 'form-control input-xs', 'id' => 'taller_id')) !!}
		</div>
	</div>
	<div class="form-group col-lg-7 col-md-7 col-sm-7">
		{!! Form::label('recorrido', 'Recorrido:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label','style'=>'margin-bottom:15px;')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12" style="margin-top: 5px">
			{!! Form::text('recorrido', null, array('class' => 'form-control input-xs', 'id' => 'recorrido','readonly'=>'true')) !!}
		</div>
	</div>
	<div class="form-group col-lg-12 col-md-12 col-sm-12" style="display: none;">
		{!! Form::label('regrepveh_id', 'Seleccione Registro Mantenimiento con Repuesto Vehicular:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label','style'=>'font-weight:bold;color:green;')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12" id="divRepVeh">
		</div>
	</div>
	<div class="form-group col-lg-12 col-md-12 col-sm-12">
		{!! Form::label('checklistvehicular_id', 'CHECKLIST:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label','style'=>'font-weight:bold;color:blue;')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12" id="divChecklist">
		</div>
	</div>
	<div class="form-group col-lg-6 col-md-6 col-sm-6">
		{!! Form::label('revision', 'Revision:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12" >
			{!! Form::textarea('revision', null, array('class' => 'form-control input-xs', 'id' => 'revision','rows'=>'5')) !!}
		</div>
	</div>
	<div class="form-group col-lg-6 col-md-6 col-sm-6" >
		{!! Form::label('diagnostico', 'Diagnostico:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12" >
			{!! Form::textarea('diagnostico', null, array('class' => 'form-control input-xs', 'id' => 'diagnostico','rows'=>'5')) !!}
		</div>
	</div>
	<div class="form-group col-lg-6 col-md-6 col-sm-6">
		{!! Form::label('recomendacion', 'Recomendacion:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12" >
			{!! Form::textarea('recomendacion', null, array('class' => 'form-control input-xs', 'id' => 'recomendacion','rows'=>'5')) !!}
		</div>
	</div>
	<div class="form-group col-lg-6 col-md-6 col-sm-6" >
		{!! Form::label('cierre', 'Cierre:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12" >
			{!! Form::textarea('cierre', null, array('class' => 'form-control input-xs', 'id' => 'cierre','rows'=>'5')) !!}
		</div>
	</div>
	<div class="form-group col-12 col-md-3 p-3" style="margin-top:-25px ">
		{!! Form::label('situacion', 'Situacion:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('situacion',array('F'=>'Finalizado','P'=>'Pendiente'), null, array('class' => 'form-control input-xs', 'id' => 'situacion')) !!}
		</div>
	</div>
	<div class="form-group col-lg-5 col-md-5 col-sm-5">
	</div>
	<div class="form-group col-lg-4 col-md-4 col-sm-4">
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
		doSearchVehiculo();		
	}); 	
	function generarCombo(vid){
	    $.ajax({
	        type: "POST",
	        url: "regmanveh/comboChecklist",
	        data: "vehiculo_id="+$("#vehiculo_id").val()+"&equipo_id="+$("#equipo_id").val()+"&_token="+$(':input[name="_token"]').val(),
	        success: function(a) {
	            $('#divChecklist').html(a);
	            if(vid!="0"){
	            	$("#checklistvehicular_id").val(vid);
	            }
		    }
	    });
	}

	function generarCombo2(vid){
	    $.ajax({
	        type: "POST",
	        url: "regmanveh/comboRegRepVeh",
	        data: "vehiculo_id="+$("#vehiculo_id").val()+"&equipo_id="+$("#equipo_id").val()+"&_token="+$(':input[name="_token"]').val(),
	        success: function(a) {
	            $('#divRepVeh').html(a);
	            if(vid!="0"){
	            	$("#regrepveh_id").val(vid);
	            }else{
	            	generarRecorrido();
	            }
		    }
	    });
	}

	function generarRecorrido(){
	    $.ajax({
	        type: "POST",
	        url: "diagnostico/recorrido",
	        data: "vehiculo_id="+$("#vehiculo_id").val()+"&equipo_id="+$("#equipo_id").val()+"&_token="+$(':input[name="_token"]').val(),
	        success: function(a) {
	            $('#recorrido').val(a);
		    }
	    });
	}
	@if(!is_null($diagnostico))
		generarCombo({{$diagnostico->checklistvehicular_id}});
	@endif
</script>
@php
$datemin = date("Y-m-d",strtotime("-".MIN_DIAS." days",strtotime("now")));
@endphp
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($regrepveh, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<section class="form-row" id="inicioregrepveh">
	<div class="form-group col-lg-12 col-md-12 col-sm-12">
		<h4 name="idconc" id="oConcesionaria">Concesionaria: {{ $oConcesionaria }}</h4>
		<input type="hidden" name="concesionaria_id" id="concesionaria_id" value="{{ $idconc }}">
	</div>
	<div class="form-group col-lg-12 col-md-12 col-sm-12">
		{!! Form::label('titulo', 'Descripcion:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('titulo', null, array('class' => 'form-control input-xs', 'id' => 'titulo')) !!}
		</div>
	</div>
	<div class="form-group col-lg-7 col-md-7 col-sm-7">
		{!! Form::label('taller_id', 'Taller:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('cliente', null, array('class' => 'form-control input-xs', 'id' => 'cliente', 'maxlength' => '100','style'=>'display:none')) !!}
			{!! Form::select('taller_id',$cboTaller, null, array('class' => 'form-control input-xs', 'id' => 'taller_id')) !!}
		</div>
	</div>
	<div class="form-group col-lg-4 col-md-4 col-sm-4" style="margin-top: 15px">
		{!! Form::label('fechamantenimiento', 'Fecha Mant.:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::date('fechamantenimiento', null, array('class' => 'form-control input-xs', 'id' => 'fechamantenimiento')) !!}
		</div>
	</div>
	<div class="mt-8 mb-4 ml-6  col-lg-12 col-md-12 col-sm-12">
		<p class="text-warning"  style="margin-bottom: 0;">Datos Generales</p> 
	</div>
	<div class="form-group col-4 col-md-4 p-3 u-search-ua">
		<label for="ua" class="pl-3">Código Ua</label>
		<div class="u-ua-style js-vehiculo-desc">{{$placa}}</div>
		<input class="js-vehiculo-hiddenid" id="vehiculo_id" name="vehiculo_id" type="hidden" value="{{$vehiculo_id}}">
		<input class="js-equipo-hiddenid" id="equipo_id" name="equipo_id" type="hidden" value="{{$equipo_id}}">
		<input class="form-control input-xs js-vehiculo-id" id="uades" name="uades" type="text" value="{{$uades}}">
		
		<small id="autoComplete_listve" class="text-danger"></small>
	</div>
	
	<div class="form-group col-lg-4 col-md-4 col-sm-4">
		{!! Form::label('fin', 'Fecha DOCMAT:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12" style="margin-top: 15px">
			{!! Form::date('fechaentrada', null, array('class' => 'form-control input-xs', 'id' => 'fechaentrada', 'maxlength' => '100','max' =>date('Y-m-d'), 'min'=>$datemin,'onkeydown'=>'return false' )) !!}
		</div>
	</div>
	<div class="form-group col-lg-4 col-md-4 col-sm-4">
		{!! Form::label('fin', 'Fecha Cotizacion/Salida:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12" style="margin-top: 15px">
			{!! Form::date('fechasalida', null, array('class' => 'form-control input-xs', 'id' => 'fechasalida', 'maxlength' => '100')) !!}
		</div>
	</div>
	<div class="form-group col-lg-4 col-md-4 col-sm-4">
		{!! Form::label('kmmant', 'Km de mantenimiento:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('kmman', null, array('class' => 'form-control input-xs', 'id' => 'kmman', 'maxlength' => '100')) !!}
		</div>
	</div>
	<div class="form-group col-lg-4 col-md-4 col-sm-4">
		{!! Form::label('kmin', 'Km Inicial:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('kminicial', null, array('class' => 'form-control input-xs', 'id' => 'kminicial', 'maxlength' => '100')) !!}
		</div>
	</div>
	<div class="form-group col-lg-4 col-md-4 col-sm-4">
		{!! Form::label('kmfin', 'Km Final:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('kmfinal', null, array('class' => 'form-control input-xs', 'id' => 'kmfinal', 'maxlength' => '100')) !!}
		</div>
	</div>
	<div class="form-group col-lg-4 col-md-4 col-sm-4">
		{!! Form::label('tipo', 'Tipo de Mantenimiento:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('tipomantenimiento',array(''=>'Seleccione Tipo','1'=>'Preventivo','2'=>'Correctivo'), null, array('class' => 'form-control input-xs', 'id' => 'tipomantenimiento', 'maxlength' => '100')) !!}
		</div>
	</div>
	<div class="form-group col-lg-4 col-md-4 col-sm-4" style="margin-top: 21px">
		{!! Form::label('telefono', 'Nro. DOCMAT:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12" style="margin-top: 20px">
			{!! Form::text('telefono', null, array('class' => 'form-control input-xs', 'id' => 'telefono', 'maxlength' => '100')) !!}
		</div>
	</div>
	<div class="form-group col-lg-4 col-md-4 col-sm-4" style="margin-top: 21px">
		{!! Form::label('otros', 'Otros:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12" style="margin-top: 20px">
			{!! Form::number('otros', null, array('class' => 'form-control input-xs', 'id' => 'otros', 'maxlength' => '100')) !!}
		</div>
	</div>
	<div class="form-group col-lg-12 col-md-12 col-sm-12" style="display: none;">
		{!! Form::label('checklistvehicular_id', 'CHECKLIST:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label','style'=>'font-weight:bold;color:blue;')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12" id="divChecklist">
		</div>
	</div>
	<div class="form-group col-lg-4 col-md-4 col-sm-4 bmd-form-group" hidden>
		{!! Form::label('diagnostico_id', 'Diagnostico:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('diagnostico_id',$cboDiagnostico, null, array('class' => 'form-control input-xs', 'id' => 'diagnostico_id')) !!}
		</div>
	</div>
	<div class="col-lg-1 col-sm-1 col-md-1 pl-1" hidden>
        <span class="input-group-append">
            {!! Form::button('<i class="fa fa-search "></i>', array('class' => 'btn btn-primary', 'onclick' => 'if($(\'.js-vehiculo-desc\').html()!=\'\'){modal (\''.URL::route('regmanveh.diagnostico', array('listar'=>'SI','page'=>'1','filas'=>'1000')).'\', \'Buscar\', this);}else{alert(\'Debe seleccionar un vehiculo\');}', 'title' => 'Buscar WP')) !!}
        </span>
    </div>
    <div class="form-group col-lg-3 col-md-3 col-sm-3">
		{!! Form::label('docmat_id', 'DOCMAT:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::hidden('ordencompra', null, array('id' => 'ordencompra')) !!}
			{!! Form::select('docmat_id', $cboDocmat, null, array('class' => 'form-control input-xs', 'id' => 'docmat_id')) !!}
		</div>
	</div>
	<div class="col-lg-1 col-sm-1 col-md-1 pl-1">
        {!! Form::button('<i class="fa fa-search "></i>', array('class' => 'btn-xs btn-primary', 'onclick' => 'if($(\'.js-vehiculo-desc\').html()!=\'\'){modal (\''.URL::route('regmanveh.docmat', array('listar'=>'SI','page'=>'1','filas'=>'1000','tipo'=>'')).'\', \'Buscar\', this);}else{alert(\'Debe seleccionar un vehiculo\');}', 'title' => 'Buscar WP')) !!}
    </div>
    <div class="form-group col-lg-3 col-md-3 col-sm-3">
		{!! Form::label('docmat_id2', 'DOCMAT:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('docmat_id2', $cboDocmat2, null, array('class' => 'form-control input-xs', 'id' => 'docmat_id2')) !!}
		</div>
	</div>
	<div class="col-lg-1 col-sm-1 col-md-1 pl-1">
        {!! Form::button('<i class="fa fa-search "></i>', array('class' => 'btn-xs btn-primary', 'onclick' => 'if($(\'.js-vehiculo-desc\').html()!=\'\'){modal (\''.URL::route('regmanveh.docmat', array('listar'=>'SI','page'=>'1','filas'=>'1000','tipo'=>'2')).'\', \'Buscar\', this);}else{alert(\'Debe seleccionar un vehiculo\');}', 'title' => 'Buscar WP')) !!}
    </div>
    <div class="form-group col-lg-3 col-md-3 col-sm-3">
		{!! Form::label('docmat_id3', 'DOCMAT:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('docmat_id3', $cboDocmat3, null, array('class' => 'form-control input-xs', 'id' => 'docmat_id3')) !!}
		</div>
	</div>
	<div class="col-lg-1 col-sm-1 col-md-1 pl-1">
        {!! Form::button('<i class="fa fa-search "></i>', array('class' => 'btn-xs btn-primary', 'onclick' => 'if($(\'.js-vehiculo-desc\').html()!=\'\'){modal (\''.URL::route('regmanveh.docmat', array('listar'=>'SI','page'=>'1','filas'=>'1000','tipo'=>'3')).'\', \'Buscar\', this);}else{alert(\'Debe seleccionar un vehiculo\');}', 'title' => 'Buscar WP')) !!}
    </div>
    <div class="form-group col-lg-3 col-md-3 col-sm-3">
		{!! Form::label('docmat_id4', 'DOCMAT:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('docmat_id4', $cboDocmat4, null, array('class' => 'form-control input-xs', 'id' => 'docmat_id4')) !!}
		</div>
	</div>
	<div class="col-lg-1 col-sm-1 col-md-1 pl-1">
        {!! Form::button('<i class="fa fa-search "></i>', array('class' => 'btn-xs btn-primary', 'onclick' => 'if($(\'.js-vehiculo-desc\').html()!=\'\'){modal (\''.URL::route('regmanveh.docmat', array('listar'=>'SI','page'=>'1','filas'=>'1000','tipo'=>'4')).'\', \'Buscar\', this);}else{alert(\'Debe seleccionar un vehiculo\');}', 'title' => 'Buscar WP')) !!}
    </div>
	<div class="mt-4 mb-2 ml-3  col-lg-10 col-md-10 col-sm-10">
		<p class="text-warning ">Observaciones </p>
	</div>
	<div class="form-group col-12 col-md-6 p-3">
		<label for="id-repuesto" class="pl-3">Repuesto</label>
		<input type="hidden" 
			name="repuesto_hiddenid" 
			id="hiddenid-repuesto" 
			class="form-control js-repuesto-hiddenid" 
			value="">
			<input type="hidden" 
			name="repuesto_hiddenunidad" 
			id="hiddenunidad-repuesto" 
			class="form-control js-repuesto-unidad" 
			value="">
			<input type="hidden" 
			name="hiddendescripcion" 
			id="hiddendescripcion-repuesto" 
			class="form-control js-repuesto-hiddendescripcion" 
			value="">
		<div class="u-ua-style js-repuesto-desc" id="descripciongeneral"></div>
		<input type="text" 
			name="repuesto_id" 
			id="id-repuesto" 
			class="form-control js-repuesto-id" 
			value="">
		<small id="autoComplete_list3" class="text-danger"></small>
	</div>{!! Form::button('<i class="fa fa-plus fa-lg table-add"></i>', array('class' => 'btn btn-success btn-sm', 'id' => 'btnAgregar', 'onclick' => 'agregarfila();')) !!}
	<div class="form-group col-12 col-md-3 p-3" style="margin-top:-25px ">
		{!! Form::label('situacion', 'Situacion:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('situacion',array('F'=>'Finalizado','P'=>'Pendiente'), null, array('class' => 'form-control input-xs', 'id' => 'situacion')) !!}
		</div>
	</div>
	<div class="table-responsive">
		<table id="example1" class="table table-bordered table-striped table-condensed table-hover">
			<thead>
				<tr>
					<th colspan="1"  class="text-center" style="width:40px;">#</th>
					<th colspan="1"  class="text-center" style="width:80px;">Cantidad</th>
					<th colspan="1"  class="text-center" style="width:100px;">Unidad</th>
					<th colspan="1"  class="text-center" style="width:100px;">Código</th>
					<th colspan="1"  class="text-center">Descripción</th>
					<th colspan="1"  class="text-center" style="width:80px;">Monto</th>
					<th colspan="1"  class="text-center" style="width:80px;">Subtotal</th>
					<th colspan="1"  class="text-center" style="width:60px;">Elim.</th>
				</tr>
			</thead>
			<tbody id="tbody">
				<?php
				$contador = 1;
				?>
				@foreach ($oObservaciones as $key => $value)
				@if($value->id>=0)
				<tr>
					
					<td name="nnn" style="text-align:center;">{{ $contador }}</td>
					<td><input name="cantidad[]" onkeyup="evaluartotal()" class='form-control' type="number" value="{{ $value->cantidad }}" style="text-align:right;width:100px;"></td>
					<td><input disabled name="unidad[]" class="form-control" type="text" value="{{ $value->unidad }}" style="width:100px;"></td>
					<td><input disabled name="codigo[]" class="form-control" type="text" value="{{ $value->codigo }}" style="width:100px;"></td>
					<td><input disabled name="descripcion[]" class="form-control" type="text" value="{{ $value->descripcion }}"></td>
					<td><input  name="monto[]" onkeyup="evaluartotal()" class='form-control' type="text" value="{{ $value->monto }}" style="text-align:right;width:100px;"></td>
					<td><input disabled name="monto2[]" class='form-control' type="number" value="{{ $value->monto*$value->cantidad }}" style="text-align:right;width:100px;"></td>
					<td onclick="if(confirm('¿Desea Eliminar la Observación?')){borrarfila({{ $value->id }});deleteRow(this);}" style="color:#ff0000;text-align:center;width:50px;"><i class="material-icons text-center">close</i></td>
					<p name	="datos{{$value->id}}" type="hidden"><input type="hidden" name="idobservacion[]" id="ididid" value="{{ $value->id }}">
					<input type="hidden" name="repuestoid[]" id="rep" value="{{ $value->repuesto_id }}">
					<input type="hidden" name="unidad[]" id="uni" value="{{ $value->unidad }}"></p>
				</tr>
				<?php
				$contador = $contador + 1;
				?>
				@else
				<?php
				if(count($oObservaciones)<=1)$oObservaciones=array();?>
				@endif
				@endforeach
			</tbody>
		</table>
		<input  name="montofinal" disabled id="totalponer" class='form-control' type="number" value="" style="float:right; text-align:right;width:100px;">
		<div  class='control-label' type="label" style="float:right; text-align:right;width:50px;padding:10px">Total</div>
		<?php $items=''; if(count($oObservaciones)>=1){ $items='1';}?>
		<input type="hidden" name="cuantasdescripciones" id="cuantasdescripciones" value="{{ $items }}"></p>
	</div>
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'if(contarEnvio()){guardar2(\''.$entidad.'\', this);}')) !!}
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
</section>
{!! Form::close() !!}

<script type="text/javascript">
	var a = <?php echo $contador?> -1;
	$(document).ready(function() {
		configurarAnchoModal('900');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		doSearchRepuesto();
		doSearchVehiculo();
		evaluartotal();
		/*const inputCode = document.getElementById('codigo');
		inputCode.addEventListener('change', e => {
			if(isNaN(e.target.value)) e.target.value = '';
		})

		inputCode.addEventListener('keydown', e => {
			//TODO: que acepte las teclas: flecha left y right
			if(e.target.value.length > 6 && e.keyCode != 8) e.preventDefault();
			if(e.keyCode < 8 || (e.keyCode >9 && e.keyCode< 48) || (e.keyCode >57 && e.keyCode< 67) || (e.keyCode >67 && e.keyCode< 86) || (e.keyCode >86 && e.keyCode< 96) || e.keyCode> 105) e.preventDefault();
		})*/
	}); 	



	function evaluarcantidad(){
		var fff=document.getElementsByName('nnn');
		for (var i = 0; i < fff.length; i++) {

			fff[i].innerHTML=(1+i);
			fff[i].className="probarcambiodeclase";

		}

	}

	var contadorEnvio=0;
	function contarEnvio(){
		if(contadorEnvio>0){
			alert('Estamos guardando la información, espere por favor...');
			return false;
		}else{
			contadorEnvio=1;
			return true;
		}
	}

	function guardar2 (entidad, idboton, entidad2) {
		var idformulario = IDFORMMANTENIMIENTO + entidad;
		var data         = submitForm(idformulario);
		var respuesta    = '';
		var listar       = 'NO';
		if ($(idformulario + ' :input[id = "listar"]').length) {
			var listar = $(idformulario + ' :input[id = "listar"]').val()
		};
		var btn = $(idboton);
		btn.button('loading');
		data.done(function(msg) {
			respuesta = msg;
		}).fail(function(xhr, textStatus, errorThrown) {
			respuesta = 'ERROR';
		}).always(function() {
			btn.button('reset');
			if(respuesta === 'ERROR'){
				contadorEnvio=0;
			}else{
				if (respuesta === 'OK') {
					md.showNotification('top','right','Guardado correctamente','info');
					cerrarModal();
					if (listar === 'SI') {
						if(typeof entidad2 != 'undefined' && entidad2 !== ''){
							entidad = entidad2;
						}
						buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
					}        
				} else {
					mostrarErrores(respuesta, idformulario, entidad);
					contadorEnvio=0;
				}
			}
		});
	}

	function evaluartotal(){
		var monto=document.getElementsByName('monto[]');
		var subtotal=document.getElementsByName('monto2[]');
		var cantidad=document.getElementsByName('cantidad[]');
		var total=0;
		for (var i = 0; i < monto.length; i++) {
			if(monto[i].value.length>0 && cantidad[i].value.length>0){
				subtotal[i].value=monto[i].value*cantidad[i].value;
			total+=parseFloat(monto[i].value*cantidad[i].value);}
			else{
				subtotal[i].value="";
			}
		}
		//alert(total);
		document.getElementById('totalponer').value=total;
	}

	

	function agregarfila(){
		if(document.getElementById('descripciongeneral').innerHTML.length>0){
		a++;
		document.getElementById('cuantasdescripciones').value='1';
		var idrepuesto=document.getElementById("hiddenid-repuesto").value;
		var unidad=document.getElementById("hiddenunidad-repuesto").value;
		var codigo=document.getElementById("id-repuesto").value;
		var descripcion=document.getElementById("hiddendescripcion-repuesto").value;
		var tr = document.createElement("tr");
		tr.innerHTML = `
			<tr>
				<td name="nnn" style="text-align:center;">${a}</td>
				<td><input onkeyup="evaluartotal();" name="cantidad[]" class='form-control' type="number" value="" style="text-align:right;width:100px;"></td>
				<td><input disabled name="unidad[]" class="form-control" type="text" value="${unidad}" style="width:100px;"></td>
				<td ><input disabled name="codigo[]" class="form-control" type="text" value="${codigo}" style="width:100px;"></td>
				<td><input disabled name="descripcion[]" class="form-control" type="text" value="${descripcion}"></td>
				<td><input  onkeyup="evaluartotal()" name="monto[]"class='form-control' type="text" value="" style="text-align:right;width:100px;"></td>
				<td><input disabled name="monto2[]"class='form-control' type="number" value="" style="text-align:right;width:100px;"></td>
				<td onclick="if(confirm('¿Desea Eliminar la Observación?')){deleteRow(this);}" style="color:#ff0000;text-align:center;width:50px;"><i class="material-icons text-center">close</i></td>
				<p><input type="hidden" name="idobservacion[]" id="ididid" value="-1"></p>
				<p><input type="hidden" name="repuestoid[]" id="rep" value="${idrepuesto}"></p>
				<p><input type="hidden" name="unidad[]" id="uni" value="${unidad}"></p>
			</tr>`;
		document.getElementById("tbody").appendChild(tr);

		}else{
			alert('no se puede garegar vacío, seleccione Repuesto');
		}

   }

	   function deleteRow(btn) {
	   		a--;
	   		if (document.getElementsByName('monto[]').length>1) {
	   			document.getElementById('cuantasdescripciones').value='1';
	   		}else{
	   			document.getElementById('cuantasdescripciones').value='';
	   		}
			var row = btn.parentNode;
		  	row.parentNode.removeChild(row);
		  	evaluarcantidad();
		  	evaluartotal();
		  
	}

	function borrarfila(iddd){

   		var input = document.createElement("input");

		input.setAttribute("type", "hidden");

		input.setAttribute("name", "idseliminados[]");

		input.setAttribute("value", iddd);

		document.getElementById("tbody").appendChild(input);


		var fff=document.getElementsByName('datos'+iddd);
		for (var i = 0; i < fff.length; i++) {
			fff[i].innerHTML='';
		}

   	}

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

	function generarCombo2(vid){
	    $.ajax({
	        type: "POST",
	        url: "regmanveh/comboRegRepVeh",
	        data: "vehiculo_id="+$("#vehiculo_id").val()+"&equipo_id="+$("#equipo_id").val()+"&_token="+$(':input[name="_token"]').val(),
	        success: function(a) {
	            $('#divRepVeh').html(a);
	            if(vid!="0"){
	            	$("#regrepveh_id").val(vid);
	            }
		    }
	    });
	}
	function seleccionarDiagnostico (iddiagnostico,numero){
	    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="diagnostico_id"]').html("<option value='"+iddiagnostico+"'>"+numero+"</option>");
	    cerrarModal();
	    $(".modal"+(contadorModal-1)).css("overflow-y",'auto');
	}
	function seleccionarDocmat (iddocmat,numero,fecha,taller_id,tipomantenimiento,km,tipo){
		if(tipo==""){
			if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="docmat_id2"]').val()==iddocmat){
				alert("DOCMAT ya seleccionado");
				return false;
			}
			if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="docmat_id3"]').val()==iddocmat){
				alert("DOCMAT ya seleccionado");
				return false;
			}
			if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="docmat_id4"]').val()==iddocmat){
				alert("DOCMAT ya seleccionado");
				return false;
			}
		}else if(tipo=="2"){
			if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="docmat_id"]').val()==iddocmat){
				alert("DOCMAT ya seleccionado");
				return false;
			}
			if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="docmat_id3"]').val()==iddocmat){
				alert("DOCMAT ya seleccionado");
				return false;
			}
			if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="docmat_id4"]').val()==iddocmat){
				alert("DOCMAT ya seleccionado");
				return false;
			}
		}else if(tipo=="3"){
			if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="docmat_id"]').val()==iddocmat){
				alert("DOCMAT ya seleccionado");
				return false;
			}
			if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="docmat_id2"]').val()==iddocmat){
				alert("DOCMAT ya seleccionado");
				return false;
			}
			if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="docmat_id4"]').val()==iddocmat){
				alert("DOCMAT ya seleccionado");
				return false;
			}
		}else if(tipo=="4"){
			if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="docmat_id"]').val()==iddocmat){
				alert("DOCMAT ya seleccionado");
				return false;
			}
			if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="docmat_id2"]').val()==iddocmat){
				alert("DOCMAT ya seleccionado");
				return false;
			}
			if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="docmat_id3"]').val()==iddocmat){
				alert("DOCMAT ya seleccionado");
				return false;
			}
		}
		if(tipomantenimiento=="Preventivo"){
			$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipomantenimiento"]')	.val(1);
		}else{
			$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="tipomantenimiento"]')	.val(2);
		}
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="kmman"]').val(km);
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="fechaentrada"]').val(fecha);
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="taller_id"]').val(taller_id);
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="ordencompra"]').val(numero);
	    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="docmat_id'+tipo+'"]').html("<option value='"+iddocmat+"'>"+numero+"</option>");
	    cerrarModal();
	    $(".modal"+(contadorModal-1)).css("overflow-y",'auto');
	}
	@if(!is_null($regrepveh))
		generarCombo({{$regrepveh->checklistvehicular_id}});
	@endif
</script>
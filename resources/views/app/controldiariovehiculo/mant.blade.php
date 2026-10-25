<?php 
$icono = '';
if ($controldiario !== NULL) {
	$icono = $controldiario->icono;
}
$datemin = date("Y-m-d",strtotime("-".MIN_DIAS." days",strtotime("now")));
?>

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($controldiario, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

<div class="row">
    @if(!is_null($controldiario))
    <div class="form-group col-lg-4 col-md-4 col-sm-4">
    	{!! Form::label('fecha', 'Fecha :', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
    	<div class="col-lg-12 col-md-12 col-sm-12">
    		
    		<input type="date" name="fecha" id='fecha' class="form-control input-xs" min="{{$datemin}}" onkeydown="return false"
    		@if($controldiario)
    			value="{{$controldiario->fecha}}"
    		@else
    			value="{{ date('Y-m-d')}}"
    		@endif
    		>
    	</div>
    </div>
    @endif
	<div class="form-group col-lg-4 col-md-4 col-sm-4">
		{!! Form::label('horometro_inicial', 'Horometro/Km inicial :', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('horometro_inicial', null, array('class' => 'form-control input-xs', 'id' => 'horometro_inicial')) !!}
		</div>
	</div>
	<div class="form-group col-lg-4 col-md-4 col-sm-4">
		{!! Form::label('horometro_final', 'Horometro/Km final :', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('horometro_final', null, array('class' => 'form-control input-xs', 'id' => 'horometro_final')) !!}
		</div>
	</div>
</div>
<div class="form-group">
	{!! Form::label('equipo_id', 'Ua vehiculo:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="u-ua-style js-vehiculo-desc"></div>
		<input type="text" name="vehiculo_id" id="vehiculo_id" class="form-control js-vehiculo-id input-xs" 
		value="@if($controldiario){{$controldiario->vehiculo->ua->codigo }}@endif">
		<small id="autoComplete_list4" class="text-danger"></small>
	</div>
</div>
<input type="hidden" name="idVehiculo" id="idVehiculo" value="@if($controldiario){{$controldiario->vehiculo->id }}@endif">
<div class="@if($controldiario) d-none @endif">
	<p class="h6 text-secondary ml-3" style="display: inline;">DETALLE DE DIAS TRABAJADOS</p> 
	<?php /*
	{!! Form::button('<i class="fa fa-plus fa-lg"></i>', array('class' => 'btn btn-success btn-sm', 'id' => 'btnAgregarHora', 'onclick' => 'nuevaFila(this);' , 'data-filas' => '1')) !!}
	{!! Form::button('<i class="fa fa-minus fa-lg"></i>', array('class' => 'btn btn-success btn-sm', 'id' => 'btnAgregarHora', 'onclick' => 'quitarFila(this);' , 'data-filas' => '1')) !!}
	*/ ?>
</div>
<div id='filas-horarios'>
	<div class="item mt-3 col-lg-12 col-md-12 col-sm-12">
		<div class="form-group ">		
			{!! Form::label('ua_id', 'UA TRABAJO:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
			<div class="col-lg-12 col-md-12 col-sm-12">
				<div class="u-ua-style js-ua-desc">
					<?php if($controldiario && $controldiario->ua_id) echo $controldiario ->ua->descripcion; ?>
				</div>
				<input type="text" name="ua_id[]" id="ua_id" class="form-control js-ua-id input-xs" 
				value="@if($controldiario && $controldiario->ua_id ){{$controldiario->ua->codigo}}@endif">
				<small id="autoComplete_list1" class="text-danger"></small>
			</div>
		</div>
		<div class="form-row">
			<div class="form-group col-md-2 mt-4 col-sm-6 col-6" hidden="">
				{!! Form::label('hora_inicio', 'hora inicio:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					<input type="time" class="form-control input-xs" value="@if($controldiario){{substr($controldiario->hora_inicio,0,5)}}@endif" name="hora_inicio[]" id='hora_inicio'>
				</div>
			</div>
			<div class="form-group col-md-2 mt-4 col-sm-6 col-6" hidden="">
				{!! Form::label('hora_fin', 'hora Fin:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					<input type="time" class="form-control input-xs" value="@if($controldiario){{substr($controldiario->hora_fin,0,5)}}@endif" name="hora_fin[]" id='hora_fin'>
				</div>
			</div>
			@if(is_null($controldiario))
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
            	{!! Form::label('fechainicial1', 'Fecha Inicial:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
            	<div class="col-lg-12 col-md-12 col-sm-12">
            		<input type="date" name="fechaInicial1" id='fechaInicial1' class="form-control input-xs" min="{{$datemin}}" onkeydown="return false"
            			value="{{ date('Y-m-d')}}" onchange="calcularDias(this.value,$('#fechaFinal1').val(),'acumulado1');">
            	</div>
            </div>
            <div class="form-group col-md-4 mt-4 col-sm-4 col-4">
            	{!! Form::label('fechafinal1', 'Fecha Final:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
            	<div class="col-lg-12 col-md-12 col-sm-12">
            		<input type="date" name="fechaFinal1" id='fechaFinal1' class="form-control input-xs" 
            			value="{{ date('Y-m-d')}}" onchange="calcularDias($('#fechaInicial1').val(),this.value,'acumulado1');">
            	</div>
            </div>
            <div class="form-group col-md-4 mt-4 col-sm-4 col-4">
            	{!! Form::label('acumulado1', 'Dias Trabajados:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
            	<div class="col-lg-12 col-md-12 col-sm-12">
            		<input type="number" name="acumulado1" id='acumulado1' class="form-control input-xs" disabled=""
            			value="0">
            	</div>
            </div>
            @endif
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4" hidden="">
				{!! Form::label('hora_total', 'DIAS TRABAJADAS:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					{!! Form::number('hora_total[]', 1, array('class' => 'form-control input-xs', 'id' => 'hora_total')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4" @if(is_null($controldiario)) hidden="" @endif>
				{!! Form::label('tipohora_id', 'Motivo Parada:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12">
					{!! Form::select('tipohora_id[]',$cboThoras, null, array('class' => 'form-control input-xs', 'id' => 'tipohora_id')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4" hidden="">
				{!! Form::label('hora_parada', 'HORAS PARADAS:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					{!! Form::number('hora_parada[]', 0, array('class' => 'form-control input-xs', 'id' => 'hora_parada')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
				{!! Form::label('observaciones', 'Observaciones:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12">
					{!! Form::text('observaciones[]', null, array('class' => 'form-control input-xs', 'id' => 'observaciones')) !!}
				</div>
			</div>
		</div>
		<hr>
	</div>

</div>
@if(is_null($controldiario))
<div class="@if($controldiario) d-none @endif">
	<p class="h6 text-secondary ml-3" style="display: inline;">DETALLE DE DIAS PARADOS</p> 
	<?php /*
	{!! Form::button('<i class="fa fa-plus fa-lg"></i>', array('class' => 'btn btn-success btn-sm', 'id' => 'btnAgregarHora2', 'onclick' => 'nuevaFila2(this);' , 'data-filas2' => '1')) !!}
	{!! Form::button('<i class="fa fa-minus fa-lg"></i>', array('class' => 'btn btn-success btn-sm', 'id' => 'btnAgregarHora2', 'onclick' => 'quitarFila2(this);' , 'data-filas2' => '1')) !!}
	*/ ?>
</div>
<div id='filas-horarios2'>
	<div class="item mt-3 col-lg-12 col-md-12 col-sm-12">
		<div class="form-group " hidden="">		
			{!! Form::label('ua_id', 'Ua :', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
			<div class="col-lg-12 col-md-12 col-sm-12">
				<div class="u-ua-style js-ua-desc-0">
					<?php if($controldiario && $controldiario->ua_id) echo $controldiario ->ua->descripcion; ?>
				</div>
				<input type="text" name="ua_id[]" id="ua_id" class="form-control js-ua-id-0 input-xs" 
				value="@if($controldiario && $controldiario->ua_id ){{$controldiario->ua->codigo}}@endif">
				<small id="autoComplete_list2-0" class="text-danger"></small>
			</div>
		</div>
		<div class="form-row">
			<div class="form-group col-md-2 mt-4 col-sm-6 col-6" hidden="">
				{!! Form::label('hora_inicio', 'hora inicio:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					<input type="time" class="form-control input-xs" value="@if($controldiario){{substr($controldiario->hora_inicio,0,5)}}@endif" name="hora_inicio[]" id='hora_inicio'>
				</div>
			</div>
			<div class="form-group col-md-2 mt-4 col-sm-6 col-6" hidden="">
				{!! Form::label('hora_fin', 'hora Fin:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					<input type="time" class="form-control input-xs" value="@if($controldiario){{substr($controldiario->hora_fin,0,5)}}@endif" name="hora_fin[]" id='hora_fin'>
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4" hidden="">
				{!! Form::label('hora_total', 'HORAS TRABAJADAS:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					{!! Form::number('hora_total[]', 0, array('class' => 'form-control input-xs', 'id' => 'hora_total')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
            	{!! Form::label('fechainicial2', 'Fecha Inicial:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
            	<div class="col-lg-12 col-md-12 col-sm-12">
            		<input type="date" name="fechaInicial2" id='fechaInicial2' class="form-control input-xs" min="{{$datemin}}" onkeydown="return false" 
            			value="{{ date('Y-m-d')}}" onchange="calcularDias(this.value,$('#fechaFinal2').val(),'acumulado2');">
            	</div>
            </div>
            <div class="form-group col-md-4 mt-4 col-sm-4 col-4">
            	{!! Form::label('fechafinal2', 'Fecha Final:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
            	<div class="col-lg-12 col-md-12 col-sm-12">
            		<input type="date" name="fechaFinal2" id='fechaFinal2' class="form-control input-xs" 
            			value="{{ date('Y-m-d')}}" onchange="calcularDias($('#fechaInicial2').val(),this.value,'acumulado2');">
            	</div>
            </div>
            <div class="form-group col-md-3 mt-3 col-sm-3 col-3">
            	{!! Form::label('acumulado2', 'Dias Parados:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
            	<div class="col-lg-12 col-md-12 col-sm-12">
            		<input type="number" name="acumulado2" id='acumulado2' class="form-control input-xs" disabled=""
            			value="0" min="0">
            	</div>
            </div>
            <div class="form-group col-md-1 mt-1 col-sm-1 col-1">
            	<label for="chkCero">Dia Cero</label>
            	<input type="checkbox" name="chkCero" id="chkCero" onclick="diaCero(this.checked);">
            </div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4" style="margin-top: 5px !important;">
				{!! Form::label('tipohora_id', 'Tipo de Dias Parados:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12">
					{!! Form::select('tipohora_id[]',$cboThoras, null, array('class' => 'form-control input-xs', 'id' => 'tipohora_id')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4" hidden="">
				{!! Form::label('hora_parada', 'DIAS PARADOS:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					{!! Form::number('hora_parada[]', 1, array('class' => 'form-control input-xs', 'id' => 'hora_parada')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
				{!! Form::label('observaciones', 'Observaciones:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12">
					{!! Form::text('observaciones[]', null, array('class' => 'form-control input-xs', 'id' => 'observaciones')) !!}
				</div>
			</div>
		</div>
		<hr>
	</div>

</div>
@endif
<div class="volquete d-none">
	<div class="form-group">
		{!! Form::label('inicio', 'Inicio:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('inicio', null, array('class' => 'form-control input-xs', 'id' => 'inicio')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('acceso', 'Acceso:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('acceso', null, array('class' => 'form-control input-xs', 'id' => 'acceso')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('uaorigen', 'Ua origen:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('uaorigen', null, array('class' => 'form-control input-xs', 'id' => 'uaorigen')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('destino', 'Destino:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('destino', null, array('class' => 'form-control input-xs', 'id' => 'destino')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('uadestino', 'Ua destino:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('uadestino', null, array('class' => 'form-control input-xs', 'id' => 'uadestino')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('distancia', 'Distancia recorrida:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('distancia', null, array('class' => 'form-control input-xs', 'id' => 'distancia')) !!}
		</div>
	</div>
	<div class="form-group">
		{!! Form::label('material', 'Tipo de material:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('material', null, array('class' => 'form-control input-xs', 'id' => 'material')) !!}
		</div>
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
		configurarAnchoModal('850');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		doSearchUA();
		doSearchVehiculoPrim();

		nuevaFila = (boton) =>{
			let Num = Number(boton.getAttribute('data-filas'));
			Num = Num +1;

			const newFila = document.createElement('div');
			newFila.setAttribute('class' ,'item mt-3 col-lg-12 col-md-12 col-sm-12') ;

			newFila.innerHTML = ` 
					<div class="form-group ">	
						<label For="ua_id_${Num}" class="col-lg-12 col-md-12 col-sm-12 control-label">Ua :</label>
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="u-ua-style js-ua-desc-${Num}">
							</div>
							<input type="text" name="ua_id[]" id="ua_id_${Num}" class="form-control js-ua-id-${Num} input-xs" >
							<small id="autoComplete_list2-${Num}" class="text-danger"></small>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-3 mt-4" hidden="">
							<label For="hora_inicio_${Num}" class="col-lg-12 col-md-12 col-sm-12 control-label">Hora inicio : </label>
							<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
								<input type="time" class="form-control input-xs" name="hora_inicio[]" id='hora_inicio_${Num}'>
							</div>
						</div>
						<div class="form-group col-md-3 mt-4" hidden="">
							<label For="hora_fin_${Num}" class="col-lg-12 col-md-12 col-sm-12 control-label">Hora fin : </label>
							<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
								<input type="time" class="form-control input-xs" name="hora_fin[]" id='hora_fin_${Num}'>
							</div>
						</div>
						<div class="form-group col-md-4 mt-4">
							<label For="hora_total_${Num}" class="col-lg-12 col-md-12 col-sm-12 control-label">HORAS TRABAJADAS : </label>
							<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
								<input type="number" class="form-control input-xs" name="hora_total[]" id='hora_total_${Num}'>
							</div>
						</div>
						<div class="form-group col-md-4 mt-4" hidden="">
							<label for="tipohora_id_${Num}" class="col-lg-12 col-md-12 col-sm-12 control-label">Tipo de horas paradas : </label>
							<div class="col-lg-12 col-md-12 col-sm-12">
							</div>
						</div>
						<div class="form-group col-md-4 mt-4" hidden="">
							<label For="hora_parada_${Num}" class="col-lg-12 col-md-12 col-sm-12 control-label">HORAS PARADAS : </label>
							<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
								<input type="number" class="form-control input-xs" name="hora_parada[]" id='hora_parada_${Num}'>
							</div>
						</div>
            			<div class="form-group col-md-4 mt-4">
							<label For="observaciones_${Num}" class="col-lg-12 col-md-12 col-sm-12 control-label">Observaciones : </label>
							<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
								<input type="text" class="form-control input-xs" name="observaciones[]" id='observaciones_${Num}'>
							</div>
						</div>
					</div>
					<hr>
			`;

			const selector = document.getElementById('tipohora_id').cloneNode(true);
			selector.setAttribute('id',`tipohora_id_${Num}`);
			console.log(newFila.querySelector(`label[for="tipohora_id_${Num}"]`));
			newFila.querySelector(`label[For="tipohora_id_${Num}"]`).nextElementSibling.appendChild(selector);

			document.getElementById('filas-horarios').appendChild(newFila);
			boton.setAttribute('data-filas',Num);
			boton.nextElementSibling.setAttribute('data-filas',Num);
			doSearchUA('.js-ua-id-'+Num,'.js-ua-desc-'+Num,'#autoComplete_list2-'+Num);

		}

		quitarFila = (boton) =>{
			let Num = Number(boton.getAttribute('data-filas'));
			if(Num > 1){
				Num = Num -1;
				document.getElementById('filas-horarios').lastChild.remove();
				boton.setAttribute('data-filas',Num);
				boton.previousElementSibling.setAttribute('data-filas',Num);
			}
		}
		
		nuevaFila2 = (boton) =>{
			let Num = Number(boton.getAttribute('data-filas2'));
			Num = Num +1;

			const newFila = document.createElement('div');
			newFila.setAttribute('class' ,'item mt-3 col-lg-12 col-md-12 col-sm-12') ;

			newFila.innerHTML = ` 
					<div class="form-group ">	
						<label For="ua_id_${Num}" class="col-lg-12 col-md-12 col-sm-12 control-label">Ua :</label>
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="u-ua-style js-ua-desc-${Num}">
							</div>
							<input type="text" name="ua_id[]" id="ua_id_${Num}" class="form-control js-ua-id-${Num} input-xs" >
							<small id="autoComplete_list1-${Num}" class="text-danger"></small>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-3 mt-4" hidden="">
							<label For="hora_inicio_${Num}" class="col-lg-12 col-md-12 col-sm-12 control-label">Hora inicio : </label>
							<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
								<input type="time" class="form-control input-xs" name="hora_inicio[]" id='hora_inicio_${Num}'>
							</div>
						</div>
						<div class="form-group col-md-3 mt-4" hidden="">
							<label For="hora_fin_${Num}" class="col-lg-12 col-md-12 col-sm-12 control-label">Hora fin : </label>
							<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
								<input type="time" class="form-control input-xs" name="hora_fin[]" id='hora_fin_${Num}'>
							</div>
						</div>
						<div class="form-group col-md-4 mt-4" hidden="">
							<label For="hora_total_${Num}" class="col-lg-12 col-md-12 col-sm-12 control-label">HORAS TRABAJADAS : </label>
							<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
								<input type="number" class="form-control input-xs" name="hora_total[]" id='hora_total_${Num}'>
							</div>
						</div>
						<div class="form-group col-md-4 mt-4">
							<label for="tipohora_id_${Num}" class="col-lg-12 col-md-12 col-sm-12 control-label">Tipo de horas paradas : </label>
							<div class="col-lg-12 col-md-12 col-sm-12">
							</div>
						</div>
						<div class="form-group col-md-4 mt-4">
							<label For="hora_parada_${Num}" class="col-lg-12 col-md-12 col-sm-12 control-label">HORAS PARADAS : </label>
							<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
								<input type="number" class="form-control input-xs" name="hora_parada[]" id='hora_parada_${Num}'>
							</div>
						</div>
            			<div class="form-group col-md-4 mt-4">
							<label For="observaciones_${Num}" class="col-lg-12 col-md-12 col-sm-12 control-label">Observaciones : </label>
							<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
								<input type="text" class="form-control input-xs" name="observaciones[]" id='observaciones_${Num}'>
							</div>
						</div>
					</div>
					<hr>
			`;

			const selector = document.getElementById('tipohora_id').cloneNode(true);
			selector.setAttribute('id',`tipohora_id_${Num}`);
			console.log(newFila.querySelector(`label[for="tipohora_id_${Num}"]`));
			newFila.querySelector(`label[For="tipohora_id_${Num}"]`).nextElementSibling.appendChild(selector);

			document.getElementById('filas-horarios2').appendChild(newFila);
			boton.setAttribute('data-filas2',Num);
			boton.nextElementSibling.setAttribute('data-filas2',Num);
			doSearchUA('.js-ua-id-'+Num,'.js-ua-desc-'+Num,'#autoComplete_list1-'+Num);

		}

		quitarFila2 = (boton) =>{
			let Num = Number(boton.getAttribute('data-filas2'));
			if(Num > 1){
				Num = Num -1;
				document.getElementById('filas-horarios2').lastChild.remove();
				boton.setAttribute('data-filas2',Num);
				boton.previousElementSibling.setAttribute('data-filas2',Num);
			}
		}
		doSearchUA('.js-ua-id-0','.js-ua-desc-0','#autoComplete_list2-0');
	}); 
	
	function calcularDias(f1,f2,vtext){
	    var fechaInicio = new Date(f1).getTime();
        var fechaFin    = new Date(f2).getTime();
        var diff = fechaFin - fechaInicio;
        var dias = diff/(1000*60*60*24)+1;
        if($("#chkCero").is(":checked")==true && vtext=="acumulado2"){
        	$("#"+vtext).val(0);
        }else{
        	$("#"+vtext).val(dias);
        }
	}

	function diaCero(vcheck){
		if(vcheck){
			var vdias = 0;
			$("#acumulado2").val(vdias);
			console.log(vdias);
		}else{
			calcularDias($('#fechaInicial2').val(),$('#fechaFinal2').val(),'acumulado2');
		}
	}
</script>
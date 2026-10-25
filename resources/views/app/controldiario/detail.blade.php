<?php 
$icono = '';
if ($controldiario !== NULL) {
	$icono = $controldiario->icono;
}
$turno=0;
?>

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($controldiario, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
{!! Form::hidden('id', null, array('id' => 'id')) !!}

<div class="row">
    <div class="form-group col-lg-4 col-md-4 col-sm-4">
    	{!! Form::label('fecha', 'Fecha :', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
    	<div class="col-lg-12 col-md-12 col-sm-12">
    		
    		<input type="date" name="fecha" id='fecha' class="form-control input-xs" 
    		@if($controldiario)
    			value="{{$controldiario->fecha}}"
    		@else
    			value="{{ date('Y-m-d')}}"
    		@endif
    		>
    	</div>
    </div>
	<div class="form-group col-lg-4 col-md-4 col-sm-4">
		{!! Form::label('horometro_inicial', 'Horometro inicial :', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('horometro_inicial', null, array('class' => 'form-control input-xs', 'id' => 'horometro_inicial')) !!}
		</div>
	</div>
	<div class="form-group col-lg-4 col-md-4 col-sm-4">
		{!! Form::label('horometro_final', 'Horometro final :', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('horometro_final', null, array('class' => 'form-control input-xs', 'id' => 'horometro_final')) !!}
		</div>
	</div>
</div>
<div class="form-group">
	{!! Form::label('equipo_id', 'Ua equipo:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="u-ua-style js-equipo-desc"></div>
		<input type="text" name="equipo_id" id="equipo_id" class="form-control js-equipo-id input-xs" 
		value="@if($controldiario){{$controldiario->equipo->ua->codigo }}@endif">
		<small id="autoComplete_list4" class="text-danger"></small>
	</div>
</div>
<input type="hidden" name="idEquipo" id="idEquipo" value="@if($controldiario){{$controldiario->equipo->id }}@endif">
<div class="row">
<div class="col-lg-6 col-md-6 col-sm-6">
    
<div class="form-group">
	<p class="h6 text-secondary ml-3" style="display: inline;">DETALLE DE HORAS TRABAJADAS</p> 
</div>
<div id='filas-horarios'>
<?php foreach($detail as $controldiario1){ 
    if($controldiario1->hora_total>0){
        $turno = $turno+$controldiario1->hora_total;
?>
	<div class="item mt-3 col-lg-12 col-md-12 col-sm-12">
		<div class="form-group ">		
			{!! Form::label('ua_id', 'Ua :', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
			<div class="col-lg-12 col-md-12 col-sm-12">
				<div class="u-ua-style js-ua-desc">
					<?php if($controldiario1 && $controldiario1->ua_id) echo $controldiario1 ->ua->descripcion; ?>
				</div>
				<input type="text" name="ua_id[]" id="ua_id" class="form-control js-ua-id input-xs" 
				value="@if($controldiario1 && $controldiario1->ua_id ){{$controldiario1->ua->codigo}}@endif">
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
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
				{!! Form::label('hora_total', 'HORAS TRABAJADAS:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					{!! Form::text('hora_total[]', $controldiario1->hora_total, array('class' => 'form-control input-xs', 'id' => 'hora_total',  'onchange' =>'calcularTotal();')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4" hidden="">
				{!! Form::label('tipohora_id', 'Tipo de Horas Paradas:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12">
					{!! Form::select('tipohora_id[]',$cboThoras, null, array('class' => 'form-control input-xs', 'id' => 'tipohora_id')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4" hidden="">
				{!! Form::label('hora_parada', 'HORAS PARADAS:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					{!! Form::number('hora_parada[]', null, array('class' => 'form-control input-xs', 'id' => 'hora_paradax',  'onchange' =>'calcularTotal();')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
				{!! Form::label('viajes', 'Nro. Viajes:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					{!! Form::number('viajes[]', $controldiario1->viajes, array('class' => 'form-control input-xs', 'id' => 'viajes')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
				{!! Form::label('km_inicial', 'Km. Inicial:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					{!! Form::text('km_inicial[]', $controldiario1->km_inicial, array('class' => 'form-control input-xs', 'id' => 'km_inicial')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
        		{!! Form::label('uaorigen', 'Ua origen:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
        		<div class="col-lg-12 col-md-12 col-sm-12">
        		    <div class="u-ua-style js-ua-desc-origen">
    					<?php if($controldiario1 && $controldiario1->ua_origen) echo $controldiario1 ->ua_origen->descripcion; ?>
    				</div>
        			{!! Form::text('uaorigen[]', ($controldiario1 && $controldiario1->ua_origen)?$controldiario1->ua_origen->codigo:'', array('class' => 'form-control input-xs js-ua-origen', 'id' => 'uaorigen')) !!}
        			<small id="autoComplete_listOrigen" class="text-danger"></small>
        		</div>
        	</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
				{!! Form::label('acceso_origen', 'Acceso Origen:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					{!! Form::text('acceso_origen[]', $controldiario1->acceso_origen, array('class' => 'form-control input-xs', 'id' => 'acceso_origen')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
				{!! Form::label('km_destino', 'Km. Destino:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					{!! Form::text('km_destino[]', $controldiario1->km_destino, array('class' => 'form-control input-xs', 'id' => 'km_destino')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
        		{!! Form::label('uadestino', 'Ua Destino:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
        		<div class="col-lg-12 col-md-12 col-sm-12">
        		    <div class="u-ua-style js-ua-desc-destino">
    					<?php if($controldiario1 && $controldiario1->ua_destino) echo $controldiario1 ->ua_destino->descripcion; ?>
    				</div>
        			{!! Form::text('uadestino[]', ($controldiario1 && $controldiario1->ua_destino)?$controldiario1->ua_destino->codigo:'', array('class' => 'form-control input-xs js-ua-destino', 'id' => 'uadestino')) !!}
        			<small id="autoComplete_listDestino" class="text-danger"></small>
        		</div>
        	</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
				{!! Form::label('acceso_destino', 'Acceso Destino:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					{!! Form::text('acceso_destino[]', $controldiario1->acceso_destino, array('class' => 'form-control input-xs', 'id' => 'acceso_destino')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
				{!! Form::label('tipo_material', 'Tipo Material:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					{!! Form::text('tipo_material[]', $controldiario1->tipo_material, array('class' => 'form-control input-xs', 'id' => 'tipo_material')) !!}
				</div>
			</div>
			<div class="form-group col-md-12">
				{!! Form::label('observaciones', 'Observaciones:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12">
					{!! Form::text('observaciones[]', $controldiario1->observaciones, array('class' => 'form-control input-xs', 'id' => 'observaciones')) !!}
				</div>
			</div>
		</div>
		<hr style="background:black;">
	</div>

<?php }
    }
?>
</div>
</div>
<div class="col-lg-6 col-md-6 col-sm-6">


<div class="form-group">
	<p class="h6 text-secondary ml-3" style="display: inline;">DETALLE DE HORAS PARADAS</p> 
</div>
<div id='filas-horarios2'>
<?php foreach($detail as $controldiario1){ 
    if($controldiario1->hora_parada>0){
        $turno = $turno+$controldiario1->hora_parada;
?>  
	<div class="item mt-3 col-lg-12 col-md-12 col-sm-12">
		<div class="form-group ">		
			{!! Form::label('ua_id', 'Ua :', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
			<div class="col-lg-12 col-md-12 col-sm-12">
				<div class="u-ua-style js-ua-desc-0">
					<?php if($controldiario1 && $controldiario1->ua_id) echo $controldiario1 ->ua->descripcion; ?>
				</div>
				<input type="text" name="ua_id[]" id="ua_id" class="form-control js-ua-id-0 input-xs" 
				value="@if($controldiario && $controldiario->ua_id ){{$controldiario1->ua->codigo}}@endif">
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
					{!! Form::number('hora_total[]', null, array('class' => 'form-control input-xs', 'id' => 'hora_totalp')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
				{!! Form::label('hora_parada', 'HORAS PARADAS:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					{!! Form::text('hora_parada[]', $controldiario1->hora_parada, array('class' => 'form-control input-xs', 'id' => 'hora_paradap',  'onchange' =>'calcularTotal();')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4" style="margin-top: -3px !important;">
				{!! Form::label('tipohora_id', 'Tipo de Horas:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12">
					{!! Form::select('tipohora_id[]',$cboThoras, $controldiario1->tipohora_id, array('class' => 'form-control input-xs', 'id' => 'tipohora_id')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
				{!! Form::label('viajes', 'Nro. Viajes:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					{!! Form::number('viajes[]', $controldiario1->viajes, array('class' => 'form-control input-xs', 'id' => 'viajes')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
				{!! Form::label('km_inicial', 'Km. Inicial:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					{!! Form::text('km_inicial[]', $controldiario1->km_inicial, array('class' => 'form-control input-xs', 'id' => 'km_inicial')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
				{!! Form::label('acceso_origen', 'Acceso Origen:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					{!! Form::text('acceso_origen[]', $controldiario1->acceso_origen, array('class' => 'form-control input-xs', 'id' => 'acceso_origen')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
				{!! Form::label('km_destino', 'Km. Destino:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					{!! Form::text('km_destino[]', $controldiario1->km_destino, array('class' => 'form-control input-xs', 'id' => 'km_destino')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
				{!! Form::label('acceso_destino', 'Acceso Destino:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					{!! Form::text('acceso_destino[]', $controldiario1->acceso_destino, array('class' => 'form-control input-xs', 'id' => 'acceso_destino')) !!}
				</div>
			</div>
			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
				{!! Form::label('tipo_material', 'Tipo Material:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
					{!! Form::text('tipo_material[]', $controldiario1->tipo_material, array('class' => 'form-control input-xs', 'id' => 'tipo_material')) !!}
				</div>
			</div>
			<div class="form-group col-md-12">
				{!! Form::label('observaciones', 'Observaciones:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12">
					{!! Form::text('observaciones[]', $controldiario1->observaciones, array('class' => 'form-control input-xs', 'id' => 'observaciones')) !!}
				</div>
			</div>
		</div>
		<hr style="background:black;">
	</div>
<?php }
    }
?>
</div>

</div>
</div>
<div class="row">
    <div class="col-lg-4 col-md-4 col-sm-4">
    	{!! Form::label('turno', 'Turno:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
    	<div class="col-lg-12 col-md-12 col-sm-12">
    		{!! Form::select('turno', $cboTurnos ,null, array('class' => 'form-control input-xs', 'id' => 'turno')) !!}
    	</div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4">
    	{!! Form::label('totalturno', 'Total Turno:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
    	<div class="col-lg-12 col-md-12 col-sm-12">
    		{!! Form::text('totalturno' ,$turno, array('class' => 'form-control input-xs', 'id' => 'totalturno', 'readonly' => 'true')) !!}
    	</div>
    </div>
</div>
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
		{!! Form::label('destino', 'Destino:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('destino', null, array('class' => 'form-control input-xs', 'id' => 'destino')) !!}
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
		configurarAnchoModal('1000');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		doSearchUA();
		doSearchEquipoPrim();

		nuevaFila = (boton) =>{
			let Num = Number(boton.getAttribute('data-filas'));
			Num = Num +1;

			const newFila = document.createElement('div');
			newFila.setAttribute('class' ,'item mt-3 col-lg-12 col-md-12 col-sm-12 item-'+Num) ;

			newFila.innerHTML = ` 
					<div class="form-group ">	
						<label For="ua_id_${Num}" class="col-lg-12 col-md-12 col-sm-12 control-label">Ua :</label>
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="u-ua-style js-ua-desc-${Num}">`
							+$(".js-ua-desc").html()+`
							</div>
							<input type="text" name="ua_id[]" id="ua_id_${Num}" class="form-control js-ua-id-${Num} input-xs" value="`+$(".js-ua-id").val()+`">
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
								<input type="text" class="form-control input-xs" name="hora_total[]" id='hora_total_${Num}' onchange='calcularTotal();'>
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
								<input type="number" class="form-control input-xs" name="hora_parada[]" id='hora_parada_${Num}' onchange='calcularTotal();'>
							</div>
						</div>
						<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
            				{!! Form::label('viajes_${Num}', 'Nro. Viajes:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
            				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
            					{!! Form::number('viajes[]', null, array('class' => 'form-control input-xs', 'id' => 'viajes_${Num}')) !!}
            				</div>
            			</div>
            			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
            				{!! Form::label('km_inicial_${Num}', 'Km. Inicial:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
            				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
            					{!! Form::text('km_inicial[]', null, array('class' => 'form-control input-xs', 'id' => 'km_inicial_${Num}')) !!}
            				</div>
            			</div>
            			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
                    		{!! Form::label('uaorigen-${Num}', 'Ua origen:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
                    		<div class="col-lg-12 col-md-12 col-sm-12">
                    		    <div class="u-ua-style js-ua-desc-origen-${Num}">
                				</div>
                    			{!! Form::text('uaorigen[]', null, array('class' => 'form-control input-xs js-ua-origen-${Num}', 'id' => 'uaorigen-${Num}')) !!}
                    			<small id="autoComplete_listOrigen-${Num}" class="text-danger"></small>
                    		</div>
                    	</div>
            			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
            				{!! Form::label('acceso_origen_${Num}', 'Acceso Origen:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
            				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
            					{!! Form::text('acceso_origen[]', null, array('class' => 'form-control input-xs', 'id' => 'acceso_origen_${Num}')) !!}
            				</div>
            			</div>
            			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
            				{!! Form::label('km_destino_${Num}', 'Km. Destino:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
            				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
            					{!! Form::text('km_destino[]', null, array('class' => 'form-control input-xs', 'id' => 'km_destino_${Num}')) !!}
            				</div>
            			</div>
            			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
                    		{!! Form::label('uadestino-${Num}', 'Ua Destino:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
                    		<div class="col-lg-12 col-md-12 col-sm-12">
                    		    <div class="u-ua-style js-ua-desc-destino-${Num}">
                				</div>
                    			{!! Form::text('uadestino[]', null, array('class' => 'form-control input-xs js-ua-destino-${Num}', 'id' => 'uadestino-${Num}')) !!}
                    			<small id="autoComplete_listDestino-${Num}" class="text-danger"></small>
                    		</div>
                    	</div>
            			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
            				{!! Form::label('acceso_destino_${Num}', 'Acceso Destino:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
            				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
            					{!! Form::text('acceso_destino[]', null, array('class' => 'form-control input-xs', 'id' => 'acceso_destino_${Num}')) !!}
            				</div>
            			</div>
            			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
            				{!! Form::label('tipo_material_${Num}', 'Tipo Material:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
            				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
            					{!! Form::text('tipo_material[]', null, array('class' => 'form-control input-xs', 'id' => 'tipo_material_${Num}')) !!}
            				</div>
            			</div>
            			<div class="form-group col-md-12 mt-12">
							<label For="observaciones_${Num}" class="col-lg-12 col-md-12 col-sm-12 control-label">Observaciones : </label>
							<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
								<input type="text" class="form-control input-xs" name="observaciones[]" id='observaciones_${Num}'>
							</div>
						</div>
					</div>
					<hr style="background:black;">
			`;

			const selector = document.getElementById('tipohora_id').cloneNode(true);
			selector.setAttribute('id',`tipohora_id_${Num}`);
			//console.log(newFila.querySelector(`label[for="tipohora_id_${Num}"]`));
			newFila.querySelector(`label[For="tipohora_id_${Num}"]`).nextElementSibling.appendChild(selector);

			document.getElementById('filas-horarios').appendChild(newFila);
			/*var list = document.getElementById("filas-horarios");
            list.insertBefore(newFila, list.childNodes[0]);*/
            
			boton.setAttribute('data-filas',Num);
			boton.nextElementSibling.setAttribute('data-filas',Num);
			doSearchUA('.js-ua-id-'+Num,'.js-ua-desc-'+Num,'#autoComplete_list2-'+Num);
            doSearchUA('.js-ua-origen-'+Num,'.js-ua-desc-origen-'+Num,'#autoComplete_listOrigen-'+Num);
		    doSearchUA('.js-ua-destino-'+Num,'.js-ua-desc-destino-'+Num,'#autoComplete_listDestino-'+Num);
		}

		quitarFila = (boton) =>{
			let Num = Number(boton.getAttribute('data-filas'));
			if(Num > 1){
				Num = Num -1;
				document.getElementById('filas-horarios').lastChild.remove();
				boton.setAttribute('data-filas',Num);
				boton.previousElementSibling.setAttribute('data-filas',Num);
			}
			calcularTotal();
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
							<div class="u-ua-style js-ua-desc-${Num}">`
							+$(".js-ua-desc-0").html()+`
							</div>
							<input type="text" name="ua_id[]" id="ua_id_${Num}" class="form-control js-ua-id-${Num} input-xs" value="`+$(".js-ua-id-0").val()+`">
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
								<input type="text" class="form-control input-xs" name="hora_total[]" id='hora_totalp_${Num}'>
							</div>
						</div>
						<div class="form-group col-md-4 mt-4">
							<label For="hora_parada_${Num}" class="col-lg-12 col-md-12 col-sm-12 control-label">HORAS PARADAS : </label>
							<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
								<input type="number" class="form-control input-xs" name="hora_parada[]" id='hora_paradap_${Num}'>
							</div>
						</div>
						<div class="form-group col-md-4 mt-4" style="margin-top: -3px !important;">
							<label for="tipohora_id_${Num}" class="col-lg-12 col-md-12 col-sm-12 control-label">Tipo de horas: </label>
							<div class="col-lg-12 col-md-12 col-sm-12">
							</div>
						</div>
						<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
            				{!! Form::label('viajes_${Num}', 'Nro. Viajes:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
            				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
            					{!! Form::number('viajes[]', null, array('class' => 'form-control input-xs', 'id' => 'viajes_${Num}')) !!}
            				</div>
            			</div>
            			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
            				{!! Form::label('km_inicial_${Num}', 'Km. Inicial:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
            				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
            					{!! Form::text('km_inicial[]', null, array('class' => 'form-control input-xs', 'id' => 'km_inicial_${Num}')) !!}
            				</div>
            			</div>
            			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
            				{!! Form::label('acceso_origen_${Num}', 'Acceso Origen:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
            				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
            					{!! Form::text('acceso_origen[]', null, array('class' => 'form-control input-xs', 'id' => 'acceso_origen_${Num}')) !!}
            				</div>
            			</div>
            			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
            				{!! Form::label('km_destino_${Num}', 'Km. Destino:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
            				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
            					{!! Form::text('km_destino[]', null, array('class' => 'form-control input-xs', 'id' => 'km_destino_${Num}')) !!}
            				</div>
            			</div>
            			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
            				{!! Form::label('acceso_destino_${Num}', 'Acceso Destino:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
            				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
            					{!! Form::text('acceso_destino[]', null, array('class' => 'form-control input-xs', 'id' => 'acceso_destino_${Num}')) !!}
            				</div>
            			</div>
            			<div class="form-group col-md-4 mt-4 col-sm-4 col-4">
            				{!! Form::label('tipo_material_${Num}', 'Tipo Material:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
            				<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
            					{!! Form::text('tipo_material[]', null, array('class' => 'form-control input-xs', 'id' => 'tipo_material_${Num}')) !!}
            				</div>
            			</div>
            			<div class="form-group col-md-12 mt-12">
							<label For="observaciones_${Num}" class="col-lg-12 col-md-12 col-sm-12 control-label">Observaciones : </label>
							<div class="col-lg-12 col-md-12 col-sm-12 mt-2">
								<input type="text" class="form-control input-xs" name="observaciones[]" id='observaciones_${Num}'>
							</div>
						</div>
					</div>
					<hr style="background:black;">
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
			calcularTotal();
		}
		@if(is_null($controldiario))
		    doSearchUA('.js-ua-id-0','.js-ua-desc-0','#autoComplete_list2-0');
		@endif
		doSearchUA('.js-ua-origen','.js-ua-desc-origen','#autoComplete_listOrigen');
		doSearchUA('.js-ua-destino','.js-ua-desc-destino','#autoComplete_listDestino');
	}); 
	
	function calcularTotal(){
	    var trabajadas = Number(document.getElementById("btnAgregarHora").getAttribute('data-filas'));
	    var paradas = Number(document.getElementById("btnAgregarHora2").getAttribute('data-filas2'));
	    var total=0;
	    if($("#hora_total").val()!=""){
	        total = total + Number($("#hora_total").val());
	    }
	    if($("#hora_paradap").val()!=""){
	        total = total + Number($("#hora_paradap").val());
	    }
	    if(paradas>1){
	        for(var x=2;x<=paradas;x++){
    	        if($("#hora_paradap_"+x).val()!=""){
    	            total = total + Number($("#hora_paradap_"+x).val());
    	        }
    	    }
	    }
	    if(trabajadas>1){
    	    for(var x=2;x<=trabajadas;x++){
    	        if($("#hora_total_"+x).val()!=""){
    	            total = total + Number($("#hora_total_"+x).val());
    	        }
    	    }
	    }
	    $("#totalturno").val(total);
	}
	
</script>
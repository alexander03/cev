@php
	$modalNuevo = false;
	$horainicio="";
	$horafin="";
	if($checklistvehicular == null) {//Nuevo
		$modalNuevo = true;
		$sistema_electrico = [
			['orden' => 1, 'id' => 'freno_emergencia', 'titulo' => 'Freno de emergencia', 'estado' => true],
			['orden' => 2, 'id' => 'funcionamiento_tablero', 'titulo' => 'Funcionamiento de tablero','estado' => true],
			['orden' => 3, 'id' => 'estado_bateria_funcionamiento', 'titulo' => 'Estado de batería y funcionamiento', 'estado' => true],
			['orden' => 4, 'id' => 'funcionamiento_claxon', 'titulo' => 'Funcionamiento de claxon', 'estado' => true],
			['orden' => 5, 'id' => 'luces_retroceso_pirata', 'titulo' => 'Luces de retroceso pirata','estado' => true],
			['orden' => 6, 'id' => 'luces_direccional', 'titulo' => 'Luces direccional','estado' => true],
			['orden' => 7, 'id' => 'faros_neblineros', 'titulo' => 'Faros neblineros','estado' => true],
			['orden' => 8, 'id' => 'faros_delanteros', 'titulo' => 'Faros delanteros','estado' => true],
			['orden' => 9, 'id' => 'faros_posteriores', 'titulo' => 'Faros posteriores','estado' => true],
			['orden' => 10,'id' => 'alarma_retroceso', 'titulo' => 'Alarma de retroceso','estado' => true],
		];
		$sistema_mecanico = [
			['orden' => 1, 'id' => 'nivel_liquido_freno', 'titulo' => 'Nivel liquido de freno', 'estado' => true],
			['orden' => 2, 'id' => 'sistema_direccion', 'titulo' => 'Sistema de dirección', 'estado' => true],
			['orden' => 3, 'id' => 'palancas_cambios', 'titulo' => 'Palancas de cambios', 'estado' => true],
			['orden' => 4, 'id' => 'estado_neumaticos', 'titulo' => 'Estado de neumáticos', 'estado' => true],
			['orden' => 5, 'id' => 'llantas_repuesto', 'titulo' => 'Llantas de repuesto', 'estado' => true],
			['orden' => 6, 'id' => 'ajustes_tuercas', 'titulo' => 'Ajustes de tuercas', 'estado' => true],
			['orden' => 7, 'id' => 'presion_llantas_libras', 'titulo' => 'Presion de llantas en libras', 'estado' => true],
			['orden' => 8, 'id' => 'cinturon_seguridad_conductor', 'titulo' => 'Cinturon de seguridad conductor', 'estado' => true],
			['orden' => 9, 'id' => 'cinturon_seguridad_pasajeros', 'titulo' => 'Cinturon de seguridad pasajeros', 'estado' => true],
			['orden' => 10, 'id' => 'suspension', 'titulo' => 'Suspensión', 'estado' => true],

			['orden' => 11, 'id' => 'sistema_freno', 'titulo' => 'Sistema de freno', 'estado' => true],
			['orden' => 12, 'id' => 'pernos_neumaticos', 'titulo' => 'Pernos de neumáticos', 'estado' => true],
			['orden' => 13, 'id' => 'nivel_aceite', 'titulo' => 'Nivel de aceite', 'estado' => true],
			['orden' => 14, 'id' => 'espejos_int_ext', 'titulo' => 'Espejos int y ext', 'estado' => true],
			['orden' => 15, 'id' => 'parachoques', 'titulo' => 'Parachoques', 'estado' => true],
			['orden' => 16, 'id' => 'parabrisas_ventanas', 'titulo' => 'Parabrisas y ventanas', 'estado' => true],
			['orden' => 17, 'id' => 'puertas_cabina', 'titulo' => 'Puertas de cabina', 'estado' => true],
			['orden' => 18, 'id' => 'puertas_tolva', 'titulo' => 'Puertas de tolva', 'estado' => true],
			['orden' => 19, 'id' => 'plumillas', 'titulo' => 'Plumillas', 'estado' => true],
			['orden' => 20, 'id' => 'estado_carroceria', 'titulo' => 'Estado de carrocería', 'estado' => true],
		];
		$accesorios = [
			['orden' => 1, 'id' => 'estuche_herramientas', 'titulo' => 'Estuche de herramientas', 'estado' => true],
			['orden' => 2, 'id' => 'estado_carga_extintor', 'titulo' => 'Estado y carga de extintor', 'estado' => true],
			['orden' => 3, 'id' => 'botiquin', 'titulo' => 'Botiquín', 'estado' => true],
			['orden' => 4, 'id' => 'cable_remolque', 'titulo' => 'Cable de remolque', 'estado' => true],
			['orden' => 5, 'id' => 'tacos_seguridad_cuna_2', 'titulo' => 'Tacos de seguridad cu&ntilde;a(2)', 'estado' => true],
			['orden' => 6, 'id' => 'llave_ruedas', 'titulo' => 'Llave de ruedas', 'estado' => true],
			['orden' => 7, 'id' => 'kit_antiderrames', 'titulo' => 'Kit antiderrames', 'estado' => true],
			['orden' => 8, 'id' => 'limpieza_unidad', 'titulo' => 'Limpieza de la unidad', 'estado' => true],
		];
		$documentos = [
			['orden' => 1, 'id' => 'tarjeta_propiedad', 'titulo' => 'Tarjeta de propiedad', 'estado' => true],
			['orden' => 2, 'id' => 'soat', 'titulo' => 'SOAT', 'estado' => true],
			['orden' => 3, 'id' => 'licencia_conducir', 'titulo' => 'Licencia de conducir', 'estado' => true],
			['orden' => 4, 'id' => 'revision_tecnica', 'titulo' => 'Revisión técnica', 'estado' => true],
		];

	}else{
		$horainicio = substr($checklistvehicular->hora_inicio,0,5);
		$horafin = substr($checklistvehicular->hora_fin,0,5);
	}
	$datemin = date("Y-m-d",strtotime("-".MIN_DIAS." days",strtotime("now")));
@endphp

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($checklistvehicular, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="form-row">
	<div class="form-group col-lg-4 col-sm-12">
		{!! Form::button('<i class="material-icons">edit</i> Editar', array('class' => 'btn btn-warning btn-sm mr-5', 'id' => 'btn-editar', 'hidden' => $modalNuevo)) !!}
	</div>
</div>
<div class="form-row">
	<div class="form-group col-lg-4 col-sm-12">
		{!! Form::label('fecha_registro', 'F. Registro:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::date('fecha_registro', null, array('class' => 'form-control input-xs solo-lectura', 'id' => 'fecha_registro_modal','max' =>date('Y-m-d'), 'min'=>$datemin,'onkeydown'=>'return false' , 'readonly' => !$modalNuevo )) !!}
		</div>
	</div>
	<div class="form-group col-lg-4 col-sm-12">
		{!! Form::label('hora_inicio', 'Hora Inicio:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::time('hora_inicio', $horainicio, array('class' => 'form-control input-xs solo-lectura', 'id' => 'hora_inicio','onkeydown'=>'return false', 'readonly' => !$modalNuevo )) !!}
		</div>
	</div>
	<div class="form-group col-lg-4 col-sm-12">
		{!! Form::label('hora_fin', 'Hora Fin:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::time('hora_fin', $horafin, array('class' => 'form-control input-xs solo-lectura', 'id' => 'hora_fin','onkeydown'=>'return false', 'readonly' => !$modalNuevo )) !!}
		</div>
	</div>


	<div class="col-12 p-0 m-0">
		<div class="col-12 p-0 m-0 input-group">
			<div class="form-group col-lg-3 col-sm-12">
				{!! Form::label('unidad_placa', 'Unidad placa:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12">
					{!! Form::text('unidad_placa', $unidad_placa, array('class' => 'form-control input-xs solo-lectura', 'id' => 'unidad_placa', 'readonly' => !$modalNuevo)) !!}
				</div>
			</div>
			<div class="form-group col-lg-7 col-sm-12">
				{!! Form::label('unidad_descripcion', 'Unidad descripción:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
				<div class="col-lg-12 col-md-12 col-sm-12">
					{!! Form::text('unidad_descripcion', $unidad_descripcion, array('class' => 'form-control input-xs', 'id' => 'unidad_descripcion', 'disabled' => true)) !!}
					{{-- <input name="unidad_id" id="unidad_id" type="text" class="hidden" value=""> --}}
				</div>
			</div>
			<div class="form-group col-2">
				<i id="unidad_loading" class="fa fa-spinner fa-lg fa-spin text-info" aria-hidden="true" hidden></i>
			</div>
		</div>
		<div id="unidad_data_table" class="data-table">
			<table class="table table-bordered table-sm table-condensed">
				<thead class="dt-row-bordered">
					<tr>
						<th class="table-primary font-weight-bold pt-0 pb-0">PLACA</th>
						<th class="table-primary font-weight-bold pt-0 pb-0">DESCRIPCION</th>
						<th class="table-primary font-weight-bold pt-0 pb-0">UA</th>
					</tr>
				</thead>
				<tbody id="unidad_data_table_rows">
				</tbody>
			</table>
		</div>
	</div>

	
</div>
<div class="form-row">
	<div class="form-group col-lg-3 col-sm-12">
		{!! Form::label('k_inicial', 'Kilometraje inicial:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::number('k_inicial', null, array('class' => 'form-control input-xs solo-lectura', 'id' => 'k_inicial', 'readonly' => !$modalNuevo, 'oninput'=>'this.value=(parseInt(this.value)||0)','onkeyup'=>'calcularRecorrido();','onblur'=>'calcularRecorrido()')) !!}
		</div>
	</div>
	<div class="form-group col-lg-3 col-sm-12">
		{!! Form::label('k_final', 'Kilometraje final:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::number('k_final', null, array('class' => 'form-control input-xs solo-lectura', 'id' => 'k_final', 'readonly' => !$modalNuevo, 'oninput'=>'this.value=(parseInt(this.value)||0)','onkeyup'=>'calcularRecorrido();','onblur'=>'calcularRecorrido()')) !!}
		</div>
	</div>
	<div class="form-group col-lg-3 col-sm-12">
		{!! Form::label('recorrido', 'Recorrido(km):', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::number('recorrido', null, array('class' => 'form-control input-xs','style'=>'background-color: transparent;cursor: not-allowed;', 'id' => 'recorrido', 'disabled' => true)) !!}
		</div>
	</div>
</div>
<div class="form-row">
	<div class="form-group col-lg-6">
		{!! Form::label('conductor_id', 'Conductor:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-12 input-group p-0 m-0">
			<div class="col-lg-4 col-md-4 col-sm-4">
				{!! Form::hidden('conductor_id', $conductor['id'], array('id' => 'conductor_id')) !!}
				{!! Form::text('conductor_dni', $conductor['dni'], array('class' => 'form-control input-xs solo-lectura', 'id' => 'conductor_dni', 'readonly' => !$modalNuevo)) !!}
			</div>
			<div class="col-lg-8 col-md-8 col-sm-8">
				{!! Form::text('conductor_nombres', $conductor['nombres'], array('class' => 'form-control input-xs solo-lectura', 'id' => 'conductor_nombres', 'disabled' => true)) !!}
			</div>
		</div>
		<div id="conductor_data_table" class="data-table">
			<table class="table table-condensed table-bordered">
				<thead class="dt-row-bordered">
					<tr>
						<th class="table-primary font-weight-bold pt-0 pb-0">DNI</th>
						<th class="table-primary font-weight-bold pt-0 pb-0">NOMBRE COMPLETO</th>
						<th class="table-primary font-weight-bold pt-0 pb-0">LIDER</th>
					</tr>
				</thead>
				<tbody id="conductor_data_table_rows">
				</tbody>
			</table>
		</div>
	</div>
	<div class="form-group col-lg-4 col-sm-12">
		{!! Form::label('lider_area', 'Lider del área:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('lider_area', $conductor['lider'], array('class' => 'form-control input-xs solo-lectura', 'id' => 'lider_area', 'readonly' => 'true')) !!}
		</div>
	</div>
</div>
<div class="form-row">
	<div class="form-group col-12 col-md-12 p-3">
		<label for="id-ua" class="pl-3">UA Vehiculo</label>
		<div class="u-ua-style js-ua-desc" style="width:100% !important">
			<?php if($checklistvehicular) if(isset($checklistvehicular -> ua)) echo $checklistvehicular -> ua -> descripcion; else echo 'Sin UA';?>
		</div>
		<input type="text" 
			name="ua_id" 
			id="id-ua" 
			class="form-control js-ua-id" 
			value="<?php if($checklistvehicular) if(isset($checklistvehicular -> ua)) echo $checklistvehicular -> ua -> codigo?>">
		<small id="autoComplete_list1" class="text-danger"></small>
	</div>
	<div class="form-group col-12 col-md-12 p-3">
		<label for="id-ua-trabajo" class="pl-3">UA Trabajo</label>
		<div class="u-ua-style js-ua-trabajo-desc"  style="width:100% !important">
			<?php if($checklistvehicular) if(isset($checklistvehicular -> uatrabajo)) echo $checklistvehicular -> uatrabajo -> descripcion; else echo 'Sin UA';?>
		</div>
		<input type="text" 
			name="ua_id_trabajo" 
			id="id-ua-trabajo" 
			class="form-control js-ua-trabajo-id" 
			value="<?php if($checklistvehicular) if(isset($checklistvehicular -> uatrabajo)) echo $checklistvehicular -> uatrabajo -> codigo?>">
		<small id="autoComplete_listTrabajo" class="text-danger"></small>
	</div>
	<div class="form-group col-12 col-md-12 p-3" hidden>
		<label for="Concesionaria-ua" class="pl-3">Concesionaria UA</label>
		<input type="text" name="Concesionaria-ua" id="Concesionaria-ua" value="<?php if($checklistvehicular) if(isset($checklistvehicular -> uatrabajo)) echo $checklistvehicular -> uatrabajo -> concesionaria->abreviatura?>" readonly='' class="form-control">
	</div>
</div>
<h5>Agregar varias UAs</h5>
<div class="form-row">
	<div class="col-lg-2 col-sm-12">
		{!! Form::label('km_inicial', 'KM Inicial:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('km_inicial', null, array('class' => 'form-control input-xs', 'id' => 'km_inicial')) !!}
		</div>
	</div>
	<div class="col-lg-2 col-sm-12">
		{!! Form::label('km_final', 'KM Final:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('km_final', null, array('class' => 'form-control input-xs', 'id' => 'km_final')) !!}
		</div>
	</div>
	<div class="col-lg-2 col-sm-12">
		{!! Form::label('detalle_hora_inicio', 'Hora Ini.:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::time('detalle_hora_inicio', null, array('class' => 'form-control input-xs', 'id' => 'detalle_hora_inicio')) !!}
		</div>
	</div>
	<div class="col-lg-2 col-sm-12">
		{!! Form::label('detalle_hora_fin', 'Hora Fin:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::time('detalle_hora_fin', null, array('class' => 'form-control input-xs', 'id' => 'detalle_hora_fin')) !!}
		</div>
	</div>
</div>
<div class="form-row">
	<div class="col-lg-11 col-sm-11">
		<label for="id-ua-carro" class="pl-3">UA</label>
		<div class="u-ua-style js-ua-carro-desc"  style="width:100% !important;top:0rem;">
		</div>
		<input type="text" 
			name="ua_id_carro" 
			id="id-ua-carro" 
			class="form-control js-ua-carro-id" 
			value="" style="margin-left: 10px;">
		<small id="autoComplete_listCarro" class="text-danger"></small>
	</div>
	{!! Form::button('<i class="fa fa-plus fa-lg table-add"></i>', array('class' => 'btn btn-success btn-sm', 'id' => 'btnAgregar', 'onclick' => 'agregarfila();','style'=>'height:40px;')) !!}
</div>
<br />
<div class="table-responsive col-lg-12" style="max-height: 200px;overflow-y: auto;">
	<table id="example1" class="table table-bordered table-striped table-condensed table-hover">
		<thead>
			<tr>
				<th colspan="1"  class="text-center" >#</th>
				<th colspan="1"  class="text-center" >Km Inicio</th>
				<th colspan="1"  class="text-center" >Km Fin</th>
				<th colspan="1"  class="text-center" >Hora Ini</th>
				<th colspan="1"  class="text-center" >Hora Fin</th>
				<th colspan="1"  class="text-center" >UA</th>
				<th colspan="1"  class="text-center" >Elim.</th>
			</tr>
		</thead>
		<tbody id="tbody">
			<?php
			$contador = 1;
			?>
			@foreach ($detalle as $key => $value)
				<tr>
					<td name="nnn" style="text-align:center;">{{ $contador }}</td>
					<td>{{ $value->km_inicial }}</td>
					<td>{{ $value->km_final }}</td>
					<td>{{ $value->hora_inicio }}</td>
					<td>{{ $value->hora_fin }}</td>
					<td>{{ $value->ua->codigo }}</td>
					<!--td onclick="editarfila({{ $value->id }});" style="color:blue;text-align:center;width:50px;cursor: pointer;"><i class="material-icons text-center" style="margin-bottom:20px;">edit</i></td-->
					<td onclick="if(confirm('¿Desea Eliminar el item?')){borrarfila({{ $value->id }});deleteRow(this);}" style="color:#ff0000;text-align:center;width:50px;cursor: pointer;"><i class="material-icons text-center">close</i>
					<p><input type="hidden" name="KmInicialList[]" id="KmInicialList{{$value->id}}" value="{{ $value->km_inicial }}"></p>
					<p><input type="hidden" name="KmFinalList[]" id="KmFinalList{{$value->id}}" value="{{ $value->km_final }}"></p>
					<p><input type="hidden" name="HoraInicioList[]" id="HoraInicioList{{$value->id}}" value="{{ $value->hora_inicio }}"></p>
					<p><input type="hidden" name="HoraFinList[]" id="HoraFinList{{$value->id}}" value="{{ $value->hora_fin }}"></p>
					<p><input type="hidden" name="uaList[]" id="uaList{{$value->id}}" value="{{ $value->ua_id }}"></p>
					<input type="hidden" name="iddetalle[]" value="{{ $value->id }}">
					</td>
				</tr>
				<?php
				$contador = $contador + 1;
				?>
			@endforeach
		</tbody>
	</table>
</div>
<div class="form-row">
	<div class="container">
		<h5>Revisión de accesorios</h5>
		<div class="row">
			<div class="col-lg-4 col-sm-12 d-flex justify-content-center">
				<table class="table-sm">
					<thead>
						<th class="">SISTEMA ELECTRICO</th>
						<th class="text-center">SI</th>
						<th class="text-center">NO</th>
					</thead>
					<tbody class="table__personal-body">
						@foreach ($sistema_electrico as $item)
							<tr>
								<td>{{ $item['titulo'] }}</td>
								<td class="text-center">
									{!! Form::radio( $item['id'], 'si', $item['estado'] ? true : false, ['data-type' => 'sistema_electrico', 'data-titulo' => $item['titulo'], 'data-orden' => $item['orden'], 'disabled' => !$modalNuevo ]) !!}
								</td>
								<td class="text-center">
									{!! Form::radio( $item['id'], 'no', $item['estado'] !== null && !$item['estado'] ? true : false, ['data-type' => 'sistema_electrico', 'data-titulo' => $item['titulo'], 'data-orden' => $item['orden'], 'disabled' => !$modalNuevo ]) !!}
								</td>
							</tr>
						@endforeach
						<input name="sistema_electrico" id="sistema_electrico" type="text" class="hidden" value="{{ json_encode($sistema_electrico) }}">
					</tbody>
				</table>
			</div>
			<div class="col-lg-4 col-sm-12 d-flex justify-content-center">
				<table class="table-sm">
					<thead>
						<th class="text-uppercase">Sistema Mecanico</th>
						<th class="text-uppercase text-center">Si</th>
						<th class="text-uppercase text-center">No</th>
					</thead>
					<tbody class="table__personal-body">
						@for ($i = 0; $i < 10; $i++)
							<tr>
								<td>{{ $sistema_mecanico[$i]['titulo'] }}</td>
								<td class="text-center">
									{!! Form::radio( $sistema_mecanico[$i]['id'], 'si', $sistema_mecanico[$i]['estado'] ? true : false, ['data-type' => 'sistema_mecanico', 'data-titulo' => $sistema_mecanico[$i]['titulo'], 'data-orden' => $sistema_mecanico[$i]['orden'], 'disabled' => !$modalNuevo ]) !!}
								</td>
								<td class="text-center">
									{!! Form::radio( $sistema_mecanico[$i]['id'], 'no', $sistema_mecanico[$i]['estado'] !== null && !$sistema_mecanico[$i]['estado'] ? true : false, ['data-type' => 'sistema_mecanico', 'data-titulo' => $sistema_mecanico[$i]['titulo'], 'data-orden' => $sistema_mecanico[$i]['orden'], 'disabled' => !$modalNuevo]) !!}
								</td>
							</tr>
						@endfor
					</tbody>
				</table>
			</div>
			<div class="col-lg-4 col-sm-12 d-flex justify-content-center">
				<table class="table-sm">
					<thead>
						<th class="text-uppercase">Sistema Mecanico</th>
						<th class="text-uppercase text-center">Si</th>
						<th class="text-uppercase text-center">No</th>
					</thead>
					<tbody class="table__personal-body">
						@for ($i = 10; $i < count($sistema_mecanico); $i++)
							<tr>
								<td>{{ $sistema_mecanico[$i]['titulo'] }}</td>
								<td class="text-center">
									{!! Form::radio( $sistema_mecanico[$i]['id'], 'si', $sistema_mecanico[$i]['estado'] ? true : false, ['data-type' => 'sistema_mecanico', 'data-titulo' => $sistema_mecanico[$i]['titulo'], 'data-orden' => $sistema_mecanico[$i]['orden'], 'disabled' => !$modalNuevo]) !!}
								</td>
								<td class="text-center">
									{!! Form::radio( $sistema_mecanico[$i]['id'], 'no', $sistema_mecanico[$i]['estado'] !== null && !$sistema_mecanico[$i]['estado'] ? true : false, ['data-type' => 'sistema_mecanico', 'data-titulo' => $sistema_mecanico[$i]['titulo'], 'data-orden' => $sistema_mecanico[$i]['orden'], 'disabled' => !$modalNuevo]) !!}
								</td>
							</tr>
						@endfor
						<input name="sistema_mecanico" id="sistema_mecanico" type="text" class="hidden" value="{{ json_encode($sistema_mecanico) }}">
					</tbody>
				</table>
			</div>
			<div class="col-lg-4 col-sm-12 d-flex justify-content-center">
				<table class="table-sm">
					<thead>
						<th class="text-uppercase">Accesorios</th>
						<th class="text-uppercase text-center">Si</th>
						<th class="text-uppercase text-center">No</th>
					</thead>
					<tbody class="table__personal-body">
						@foreach ($accesorios as $item)
							<tr>
								<td>{!! $item['titulo'] !!}</td>
								<td class="text-center">
									{!! Form::radio( $item['id'], 'si', $item['estado'] ? true : false, ['data-type' => 'accesorios', 'data-titulo' => $item['titulo'], 'data-orden' => $item['orden'], 'disabled' => !$modalNuevo]) !!}
								</td>
								<td class="text-center">
									{!! Form::radio( $item['id'], 'no', $item['estado'] !== null && !$item['estado'] ? true : false, ['data-type' => 'accesorios', 'data-titulo' => $item['titulo'], 'data-orden' => $item['orden'], 'disabled' => !$modalNuevo]) !!}
								</td>
							</tr>
						@endforeach
						<input name="accesorios" id="accesorios" type="text" class="hidden" value="{{ json_encode($accesorios) }}">
					</tbody>
				</table>
			</div>
			<div class="col-lg-4 col-sm-12 d-flex justify-content-center">
				<table class="table-sm">
					<thead>
						<th class="text-uppercase">Documentos</th>
						<th class="text-uppercase text-center">Si</th>
						<th class="text-uppercase text-center">No</th>
					</thead>
					<tbody class="table__personal-body">
						@foreach ($documentos as $item)
							<tr>
								<td>{{ $item['titulo'] }}</td>
								<td class="text-center">
									{!! Form::radio( $item['id'], 'si', $item['estado'] ? true : false, ['data-type' => 'documentos', 'data-titulo' => $item['titulo'], 'data-orden' => $item['orden'], 'disabled' => !$modalNuevo]) !!}
								</td>
								<td class="text-center">
									{!! Form::radio( $item['id'], 'no', $item['estado'] !== null && !$item['estado'] ? true : false, ['data-type' => 'documentos', 'data-titulo' => $item['titulo'], 'data-orden' => $item['orden'], 'disabled' => !$modalNuevo]) !!}
								</td>
							</tr>
						@endforeach
						<input name="documentos" id="documentos" type="text" class="hidden" value="{{ json_encode($documentos) }}">
					</tbody>
				</table>
			</div>
			<div class="col-lg-4 col-sm-12">
				<h5 class="mb-0">Observaciones:</h5>
				{!! Form::textarea('observaciones', null, ['class' => 'form-control solo-lectura', 'rows' => '5', 'cols' => '26', 'readonly' => !$modalNuevo]) !!}
				<h5 class="mb-0 mt-4">Incidentes:</h5>
				{!! Form::textarea('incidentes', null, ['class' => 'form-control solo-lectura', 'rows' => '5', 'cols' => '26', 'readonly' => !$modalNuevo]) !!}
			</div>
		</div>
	</div>
</div>

<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardarChecklist(\''.$entidad.'\', this)', 'hidden' => !$modalNuevo)) !!}
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}

<style>
	.solo-lectura:read-only {
		background-color: transparent;
		cursor: not-allowed;
	}
	.hidden {
		display: none;
	}
	
	.dt-row-bordered-selectable{
		border: 2px solid blueviolet !important;
		cursor: pointer;
	}
	.dt-row-bordered{
		border: 2px solid blueviolet !important;
	}
	.showTable{
		display: block !important;
	}
	.data-table{
		float: left;
		position: absolute;
		display: none;
		z-index: 1000;
	}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('900');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');

		doSearchUA();
		doSearchUATrabajo();
		doSearchUACarro();

		const btnEditar = document.getElementById('btn-editar');

		//TODO: Revisarlo al final
		const getDateCurrent = () => {
			const fecha = new Date();
			let mes = fecha.getMonth()+1;
			let dia = fecha.getDate();
			const ano = fecha.getFullYear();
			if(dia<10) dia = '0' + dia;
			if(mes<10) mes = '0' + mes;

			const inputFechaRegistro = document.getElementById('fecha_registro_modal');
			// console.log('antes: ', inputFechaRegistro.value);
			inputFechaRegistro.value = ano+"-"+mes+"-"+dia;
			//inputFechaRegistro.setAttribute('min', ano+"-"+mes+"-"+dia);
			// console.log('despues: ', inputFechaRegistro.value);
			// console.log(ano+"-"+mes+"-"+dia);
		}
		@if($checklistvehicular == null)
		    getDateCurrent();
        @endif

		// TABLA SELECCIONADORA DE CONDUCTORES
		let listaConductores = [];
		const conductorId = document.getElementById('conductor_id');
		const conductorDNI = document.getElementById('conductor_dni');
		const conductorNombres = document.getElementById('conductor_nombres');
		const liderArea = document.getElementById('lider_area');
		const conductorDataTable = document.getElementById('conductor_data_table');
		const conductorDataTableRows = document.getElementById('conductor_data_table_rows');

		const searchConductor = async (filter = '') => {
			listaConductores = await querySearchConductor(filter);

			if ( listaConductores.length == 0 ) {
				updateConductor(conductorDNI.value);
				conductorDataTable.classList.remove('showTable');
				return;
			}
			if ( listaConductores.length == 1 && updateConductor(conductorDNI.value) ) {
				conductorDataTable.classList.remove('showTable');
				return;
			};
			
			conductorDataTableRows.innerHTML = '';
			listaConductores.forEach(conductor => {
				conductorDataTableRows.innerHTML += `
					<tr name="conductor_row" data-conductor_dni="${conductor.dni}" class="dt-row-bordered-selectable table-hover">
						<td class="table-secondary pt-0 pb-0">${conductor.dni}</td>
						<td class="table-secondary pt-0 pb-0">${conductor.nombres}</td>
						<td class="table-secondary pt-0 pb-0">${conductor.lider}</td>
					</tr>
				`;
			});

			const conductor_rows = Array.from(document.getElementsByName('conductor_row'));
			conductor_rows.forEach(row => {
				row.addEventListener('click', e => {
					const trow = e.srcElement.parentElement;
					const dni = trow.dataset.conductor_dni;
					selectConductor(dni);
				});
			});
			
			conductorDataTable.classList.add('showTable');
			updateConductor(conductorDNI.value);
		}
		const querySearchConductor = async (filter) => {
			if ( !filter.length ) return [];

			return list = fetch(`./mantcorrprev/searchConductor?filter=${filter}`)
			.then(response => response.status === 200 ? response.json() : console.log('Error on searchConductor method'))
			.then(response => response.list)
			.catch(error => console.log(`Error on searchConductor method: ${error}`));
		}
		const selectConductor = (dni) => {
			conductorDataTable.classList.remove('showTable');
			updateConductor(dni);	
		}
		const updateConductor = (dni) => {
			let found = false;
			for (let i = 0; i < listaConductores.length; i++) {
				if ( listaConductores[i].dni == dni ) {
					conductorId.value = listaConductores[i].id;
					conductorDNI.value = listaConductores[i].dni;
					conductorNombres.value = listaConductores[i].nombres;
					liderArea.value = listaConductores[i].lider;
					found = true; break;
				}
			}
			if ( !found ) {
				conductorId.value = null;
				conductorNombres.value = '';
			}

			return found;
		}
		conductorDNI.addEventListener('keyup', async (e) => {
			const filter = e.target.value;
			
			if ( filter.length > 0 ) {
				searchConductor(filter);
			} else {
				updateConductor(conductorDNI.value);
				conductorDataTable.classList.remove('showTable');
			}
		});


		// TABLA SELECCIONADORA DE UNIDADES
		let listaUnidades = [];
		const unidadPlaca = document.getElementById('unidad_placa');
		const unidadDescripcion = document.getElementById('unidad_descripcion');
		const unidadUa = document.getElementById('id-ua');
		const unidadDescUa = $('.js-ua-desc');
		const unidadLoading = document.getElementById('unidad_loading');
		const unidadDataTable = document.getElementById('unidad_data_table');
		const unidadDataTableRows = document.getElementById('unidad_data_table_rows');

		const searchUnidad = async (filter = '') => {
			listaUnidades = await querySearchUnidad(filter);
			console.log(listaUnidades);
			if ( listaUnidades.length == 0 ) {
				updateUnidad(unidadPlaca.value);
				unidadDataTable.classList.remove('showTable'); 
				unidadLoading.setAttribute('hidden', true); return;
			}
			if ( listaUnidades.length == 1 && updateUnidad(unidadPlaca.value) ) {
				unidadDataTable.classList.remove('showTable');
				unidadLoading.setAttribute('hidden', true); return;
			}

			unidadDataTableRows.innerHTML = '';
			listaUnidades.forEach(unidad => {
				unidadDataTableRows.innerHTML += `
					<tr name="unidad_row" data-unidad_placa="${unidad.placa}" class="dt-row-bordered-selectable table-hover">
						<td class="table-secondary pt-0 pb-0">${unidad.placa}</td>
						<td class="table-secondary pt-0 pb-0">${unidad.descripcion}</td>
						<td class="table-secondary pt-0 pb-0">${unidad.codigoua}</td>
					</tr>
				`;
			});

			unidad_rows = Array.from(document.getElementsByName('unidad_row'));
			unidad_rows.forEach(unidad => {
				unidad.addEventListener('click', e => {
					let trow = e.srcElement.parentElement;
					let placa = trow.dataset.unidad_placa;
					selectUnidad(placa);
				});
			});

			unidadDataTable.classList.add('showTable');
			unidadLoading.setAttribute('hidden', true);
			updateUnidad(unidadPlaca.value);
		}
		const querySearchUnidad = async (filter) => {
			if ( !filter.length ) return [];

			return fetch(`./mantcorrprev/searchUnidad?filter=${filter}`)
			.then(response => response.status === 200 ? response.json() : console.error(`Error on query into querySearchUnidad method: ${response.status}`))
			.then(response => response.equipos.concat(response.vehiculos))
			.catch(error => console.error('Error in querySearchUnidad method'));
		}
		const selectUnidad = (placa) => {
			unidadDataTable.classList.remove('showTable');
			updateUnidad(placa);
		}
		const updateUnidad = (placa) => {
			let found = false;

			for (let i = 0; i < listaUnidades.length; i++) {
				if ( listaUnidades[i].placa == placa ) {
					unidadPlaca.value = listaUnidades[i].placa;
					unidadDescripcion.value = listaUnidades[i].descripcion;
					unidadUa.value = listaUnidades[i].codigoua;
					unidadDescUa.html(listaUnidades[i].ua);
					$('#id-ua-trabajo').val(listaUnidades[i].codigouatrabajo);
					$('.js-ua-trabajo-desc').html(listaUnidades[i].uatrabajo);
					if(listaUnidades[i].decimales!="0"){
						$("#k_inicial").attr("oninput","");
						$("#k_final").attr("oninput","");
					}else{
						$("#k_inicial").attr("oninput","this.value=(parseInt(this.value)||0)");
						$("#k_final").attr("oninput","this.value=(parseInt(this.value)||0)");
					}
					found = true; return;
				}
			}

			if ( !found ) {
				unidadDescripcion.value = '';
			}
			
			return found;
		}
		unidadPlaca.addEventListener('keyup', async (e) => {
			const filter = e.target.value;
			
			if ( filter.length > 0 ){
				unidadLoading.removeAttribute('hidden');
				searchUnidad(filter);
			} else {
				unidadLoading.setAttribute('hidden', true);
				updateUnidad(unidadPlaca.value);
				unidadDataTable.classList.remove('showTable');
			}
		});

		//SISTEMA ELECTRICO
		$("input[data-type='sistema_electrico']").change(function(){
			const arrObjects = [];
			const el = $(this)[0];
			const brothers = document.querySelectorAll(`input[data-type='sistema_electrico'][name=${el.name}]`);
			Array.from(brothers).forEach(element => {
				element.removeAttribute('checked');
			});
			el.setAttribute('checked', 'checked');

			const listSistemaElectricoChecked = document.querySelectorAll("input[data-type='sistema_electrico'][checked='checked']");
			Array.from(listSistemaElectricoChecked).forEach(el => {
				myObject = {
					orden: el.dataset.orden,
					id: el.name,
					titulo: el.dataset.titulo,
					estado: el.value == 'si' ? true: false
				};
				arrObjects.push(myObject);
				// console.log(arrObjects)
			});

			const listSistemaElectrico = document.querySelectorAll("input[data-type='sistema_electrico']");
			const filtradoSistemaElectrico = Array.from(listSistemaElectrico).filter((el, index) => index % 2 == 0);
			filtradoSistemaElectrico.forEach(el => {
				const brothers = document.querySelectorAll(`input[data-type='sistema_electrico'][name=${el.name}]`);
				const arrBrothers = Array.from(brothers);

				let haveChecked = false;
				for (const item of arrBrothers) {
					if(item.getAttribute('checked') != null) haveChecked = true;
				}
				if(haveChecked == false ) {
					myObject = {
						orden: el.dataset.orden,
						id: el.name,
						titulo: el.dataset.titulo,
						estado: null
					};
					arrObjects.push(myObject);
				}
			});
			// console.log(arrObjects)
			document.getElementById('sistema_electrico').value = JSON.stringify(arrObjects);
		});

		//SISTEMA MECANICO
		$("input[data-type='sistema_mecanico']").change(function(){
			const arrObjects = [];
			const el = $(this)[0];
			const brothers = document.querySelectorAll(`input[data-type='sistema_mecanico'][name=${el.name}]`);
			Array.from(brothers).forEach(element => {
				element.removeAttribute('checked');
			});
			el.setAttribute('checked', 'checked');

			const listSistemaMecanicoChecked = document.querySelectorAll("input[data-type='sistema_mecanico'][checked='checked']");
			Array.from(listSistemaMecanicoChecked).forEach(el => {
				myObject = {
					orden: el.dataset.orden,
					id: el.name,
					titulo: el.dataset.titulo,
					estado: el.value == 'si' ? true: false
				};
				arrObjects.push(myObject);
				// console.log(arrObjects)
			});

			const listSistemaMecanico = document.querySelectorAll("input[data-type='sistema_mecanico']");
			const filtradoSistemaMecanico = Array.from(listSistemaMecanico).filter((el, index) => index % 2 == 0);
			filtradoSistemaMecanico.forEach(el => {
				const brothers = document.querySelectorAll(`input[data-type='sistema_mecanico'][name=${el.name}]`);
				const arrBrothers = Array.from(brothers);

				let haveChecked = false;
				for (const item of arrBrothers) {
					if(item.getAttribute('checked') != null) haveChecked = true;
				}
				if(haveChecked == false ) {
					myObject = {
						orden: el.dataset.orden,
						id: el.name,
						titulo: el.dataset.titulo,
						estado: null
					};
					arrObjects.push(myObject);
				}
			});
			//console.log(arrObjects)
			document.getElementById('sistema_mecanico').value = JSON.stringify(arrObjects);
		});
		
		//ACCESORIOS
		$("input[data-type='accesorios']").change(function(){
			const arrObjects = [];
			const el = $(this)[0];
			const brothers = document.querySelectorAll(`input[data-type='accesorios'][name=${el.name}]`);
			Array.from(brothers).forEach(element => {
				element.removeAttribute('checked');
			});
			el.setAttribute('checked', 'checked');

			const listAccesoriosChecked = document.querySelectorAll("input[data-type='accesorios'][checked='checked']");
			Array.from(listAccesoriosChecked).forEach(el => {
				myObject = {
					orden: el.dataset.orden,
					id: el.name,
					titulo: el.dataset.titulo,
					estado: el.value == 'si' ? true: false
				};
				arrObjects.push(myObject);
				// console.log(arrObjects)
			});

			const listAccesorios = document.querySelectorAll("input[data-type='accesorios']");
			const filtradoAccesorios = Array.from(listAccesorios).filter((el, index) => index % 2 == 0);
			filtradoAccesorios.forEach(el => {
				const brothers = document.querySelectorAll(`input[data-type='accesorios'][name=${el.name}]`);
				const arrBrothers = Array.from(brothers);

				let haveChecked = false;
				for (const item of arrBrothers) {
					if(item.getAttribute('checked') != null) haveChecked = true;
				}
				if(haveChecked == false ) {
					myObject = {
						orden: el.dataset.orden,
						id: el.name,
						titulo: el.dataset.titulo,
						estado: null
					};
					arrObjects.push(myObject);
				}
			});
			// console.log(arrObjects)
			document.getElementById('accesorios').value = JSON.stringify(arrObjects);
		});

		//DOCUMENTOS
		$("input[data-type='documentos']").change(function(){
			const arrObjects = [];
			const el = $(this)[0];
			const brothers = document.querySelectorAll(`input[data-type='documentos'][name=${el.name}]`);
			Array.from(brothers).forEach(element => {
				element.removeAttribute('checked');
			});
			el.setAttribute('checked', 'checked');

			const listDocumentosChecked = document.querySelectorAll("input[data-type='documentos'][checked='checked']");
			Array.from(listDocumentosChecked).forEach(el => {
				myObject = {
					orden: el.dataset.orden,
					id: el.name,
					titulo: el.dataset.titulo,
					estado: el.value == 'si' ? true: false
				};
				arrObjects.push(myObject);
				// console.log(arrObjects)
			});

			const listDocumentos = document.querySelectorAll("input[data-type='documentos']");
			const filtradoDocumentos = Array.from(listDocumentos).filter((el, index) => index % 2 == 0);
			filtradoDocumentos.forEach(el => {
				const brothers = document.querySelectorAll(`input[data-type='documentos'][name=${el.name}]`);
				const arrBrothers = Array.from(brothers);

				let haveChecked = false;
				for (const item of arrBrothers) {
					if(item.getAttribute('checked') != null) haveChecked = true;
				}
				if(haveChecked == false ) {
					myObject = {
						orden: el.dataset.orden,
						id: el.name,
						titulo: el.dataset.titulo,
						estado: null
					};
					arrObjects.push(myObject);
				}
			});
			//console.log(arrObjects)
			document.getElementById('documentos').value = JSON.stringify(arrObjects);
		});

		btnEditar.addEventListener('click', (e) => {
			const inputsReadonly = document.querySelectorAll("[readonly]");
			Array.from(inputsReadonly).map(el => {
				el.removeAttribute('readonly');
			});
			const inputsDisabled = document.querySelectorAll("[disabled]");
			 Array.from(inputsDisabled).map(el => {
			 	el.removeAttribute('disabled');
			 });

			const btnGuardar = document.getElementById('btnGuardar');
			btnGuardar.removeAttribute('hidden');

			e.srcElement.setAttribute('hidden', true);

			$("#recorrido").attr("readonly","true");
		});
	});

	var contadorEnvio=0;
	function guardarChecklist (entidad, idboton, entidad2) {
		if(contadorEnvio>0){
			alert('Estamos guardando la información, espere por favor...');
			return false;
		}
		contadorEnvio=1;
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
			}else{
				if (respuesta === 'OK') {
					md.showNotification('top','right','Guardado correctamente','info');
					contadorEnvio=0;
					cerrarModal();
					if (listar === 'SI') {
						if(typeof entidad2 != 'undefined' && entidad2 !== ''){
							entidad = entidad2;
						}
						buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
					}        
				} else {
					contadorEnvio=0;
					mostrarErrores(respuesta, idformulario, entidad);
				}
			}
		});
	}
var a = <?php echo $contador?> -1;
function agregarfila(){
	if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="id-ua-carro"]').val()==""){
		alert("Debe ingresar una ua");
		return false;
	}
	if($("#km_inicial").val()==""){
		alert("Debe ingresar un km inicial");
		return false;
	}
	if($("#km_final").val()==""){
		alert("Debe ingresar un km final");
		return false;
	}
	if(parseFloat($("#km_inicial").val())>parseFloat($("#km_final").val())){
		alert("Km Inicial no puede ser mayor al Km Final");
		return false;
	}
	if($("#detalle_hora_inicio").val()==""){
		alert("Debe ingresar una hora inicio");
		return false;
	}
	if($("#detalle_hora_fin").val()==""){
		alert("Debe ingresar una hora fin");
		return false;
	}
	if($("#detalle_hora_fin").val() == $("#detalle_hora_inicio").val()){
		alert("Hora Inicio y Hora Fin no pueden ser los mismos");
		return false;
	}
	if($("#km_final").val() == $("#km_inicial").val()){
		alert("Km Inicio y Km Fin no pueden ser los mismos");
		return false;
	}
	a++;
	var ua_id=document.getElementById("id-ua-carro").value;
	var km_inicial=$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="km_inicial"]').val();
	var km_final=$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="km_final"]').val();
	var detalle_hora_inicio=$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="detalle_hora_inicio"]').val();
	var detalle_hora_fin=document.getElementById("detalle_hora_fin").value;
	var tr = document.createElement("tr");
	tr.innerHTML = `
		<tr>
			<td name="nnn" style="text-align:center;">${a}</td>
			<td>${km_inicial}</td>
			<td>${km_final}</td>
			<td>${detalle_hora_inicio}</td>
			<td>${detalle_hora_fin}</td>
			<td>${ua_id}</td>
			<td onclick="if(confirm('¿Desea Eliminar el item?')){deleteRow(this);}" style="color:#ff0000;text-align:center;width:50px;cursor: pointer;"><i class="material-icons text-center">close</i>
			<p><input type="hidden" name="KmInicialList[]" value="${km_inicial}"></p>
			<p><input type="hidden" name="KmFinalList[]" value="${km_final}"></p>
			<p><input type="hidden" name="HoraInicioList[]" value="${detalle_hora_inicio}"></p>
			<p><input type="hidden" name="HoraFinList[]" value="${detalle_hora_fin}"></p>
			<p><input type="hidden" name="uaList[]" value="${ua_id}"></p>
			<p><input type="hidden" name="iddetalle[]" id="ididid" value="-1"></p>
			</td>
		</tr>`;
	document.getElementById("tbody").appendChild(tr);
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="id-ua-carro"]').val("");
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="km_inicial"]').val("");
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="km_final"]').val("");
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="detalle_hora_inicio"]').val("0");
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="detalle_hora_fin"]').val("0");
	$(".js-ua-carro-desc").html("");
}

function deleteRow(btn) {
	a--;
	var row = btn.parentNode;
  	row.parentNode.removeChild(row);
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

function calcularRecorrido(){
	var km_inicial = $("#k_inicial").val();
	var km_final = $("#k_final").val();
	if(km_inicial==""){
		km_inicial=0;
	}
	if(km_final==""){
		km_final=0;
	}
	if(parseFloat(km_final)>0){
		var recorrido = parseFloat(km_final) - parseFloat(km_inicial);
		$("#recorrido").val(recorrido.toFixed(0));
	}
}

	@if(!is_null($checklistvehicular) && ($unidad_placa=="F0Y-907" || $unidad_placa=="BPE-708" || $unidad_placa=="310SL"))
		$("#k_inicial").attr("oninput","");
		$("#k_final").attr("oninput","");
	@else
		@if($conductor["nombres"]!="")
			$("#conductor_dni").attr("readonly",'true');
		@endif
	@endif
	calcularRecorrido();
</script>
<style>
	.table__personal-body {
		font-size: .85em; !important
	}
</style>
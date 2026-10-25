<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-info">
            <h4 class="card-title ">{{ $title }}</h4>
          </div>
          <div class="card-body">
						{!! Form::open(['route' => $ruta["search"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'row', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
						{!! Form::hidden('page', 1, array('id' => 'page')) !!}
						{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}

						<div class="col-lg-4 col-md-12 p-0 form-group input-group">
							{!! Form::label(null, 'Buscar por rango de Fecha de Registro inicial y final', array('class' => 'mb-0')) !!}
							<div class="col-12 p-0 form-group input-group">
								<div class="col-lg-6 col-md-6 col-sm-6 col-12 p-0 input-group">
									{!! Form::label('fecha_registro_inicial', 'Inicio', array('class' => 'col-3 p-0 mt-2')) !!}
									{!! Form::date('fecha_registro_inicial', date('Y-m-01'), array('class' => 'col-7 form-control input-xs', 'id' => 'fecha_registro_inicial')) !!}
									<div class="col-1 p-0">
										<button class="btn btn-warning p-1" id="btn_remove_fecha_registro_inicial">
											<span class="material-icons outlined">close</span>
										</button>
									</div>
								</div>
								<div class="col-lg-6 col-md-6 col-sm-6 col-12 p-0 input-group">
									{!! Form::label('fecha_registro_final', 'Final', array('class' => 'col-3 p-0 mt-2')) !!}
									{!! Form::date('fecha_registro_final', date("Y-m-d"), array('class' => 'col-7 form-control input-xs', 'id' => 'fecha_registro_final')) !!}
									<div class="col-1 p-0">
										<button class="btn btn-warning p-1" id="btn_remove_fecha_registro_final">
											<span class="material-icons">close</span>
										</button>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-4 col-md-12 p-0 form-group input-group">
						    <div class="col-lg-6 col-md-6 col-sm-6 col-6 p-0">
    							{!! Form::label('ua', 'UA', array('class' => 'col-5 p-0 mt-2')) !!}
    							{!! Form::text('ua', null, array('class' => 'col-11 form-control input-xs', 'id' => 'ua')) !!}
    						</div>
    						<div class="col-lg-6 col-md-6 col-sm-6 col-6 p-0">
    							{!! Form::label('modelo', 'Modelo/Placa', array('class' => 'col-5 p-0 mt-2')) !!}
    							{!! Form::text('modelo', null, array('class' => 'col-11 form-control input-xs', 'id' => 'modelo')) !!}
    						</div>
    					</div>
    					<div class="col-lg-4 col-md-12 p-0 form-group input-group">
    						<div class="col-lg-6 col-md-6 col-sm-6 col-6 p-0" <?php if($user->tipouser->id==7){ echo "style='display:none'";}?>>
    							{!! Form::label('conductor', 'Conductor', array('class' => 'col-5 p-0 mt-2')) !!}
    							{!! Form::text('conductor', ($user->tipouser->id==7?$user->name:null), array('class' => 'col-11 form-control input-xs', 'id' => 'conductor')) !!}
    						</div>
    						<div class="col-lg-6 col-md-6 col-sm-6 col-6 p-0" >
    							{!! Form::label('txtLider', 'Lider', array('class' => 'col-5 p-0 mt-2')) !!}
    							{!! Form::text('txtLider', null, array('class' => 'col-11 form-control input-xs', 'id' => 'txtLider')) !!}
    						</div>
    					</div>
    					<div class="col-lg-3 col-md-12 form-group input-group">
						    <div class="col-lg-12 col-md-12 col-sm-12 col-12 p-0">
    							{!! Form::label('accesorio', 'Revision', array('class' => 'col-5 p-0 mt-2')) !!}
    							<select id="accesorio" name="accesorio" class="col-12 form-control input-xs">
    								<option value="">NINGUNO</option>
    								@php
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
										['orden' => 5, 'id' => 'tacos_seguridad_cuña_2', 'titulo' => 'Tacos de seguridad cuña(2)', 'estado' => true],
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
    								@endphp
    								<optgroup label="SISTEMA ELECTRICO">
    								@foreach($sistema_electrico as $k)
    									<option value="{{$k["id"]}}">{{$k["titulo"]}}</option>
    								@endforeach
    								</optgroup>
    								<optgroup label="SISTEMA MECANICO">
    								@foreach($sistema_mecanico as $k)
    									<option value="{{$k["id"]}}">{{$k["titulo"]}}</option>
    								@endforeach
    								</optgroup>
    								<optgroup label="ACCESORIOS">
    								@foreach($accesorios as $k)
    									<option value="{{$k["id"]}}">{{$k["titulo"]}}</option>
    								@endforeach
    								</optgroup>
    								<optgroup label="DOCUMENTOS">
    								@foreach($documentos as $k)
    									<option value="{{$k["id"]}}">{{$k["titulo"]}}</option>
    								@endforeach
    								</optgroup>
    							</select>
    						</div>
    					</div>
    					<div class="col-lg-9 col-md-12 form-group input-group">
    					    <div class="col-lg-1 col-md-1 col-sm-2 col-2 p-0">
								{!! Form::label('filas', 'Filas')!!}
								{!! Form::selectRange('filas', 1, 30, 20, array('class' => 'col-12 form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
							</div>
							<div class="form-group">
								{!! Form::button('<i class="material-icons">search</i>Buscar', array('class' => 'btn btn-success p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
								@if($user->tipouser_id!="10")
									{!! Form::button('<i class="material-icons">add</i>Registrar', array('class' => 'btn btn-info p-2 pl-1 pr-1', 'id' => 'btnNuevo', 'onclick' => 'modal (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$tituloCheckListVehicular.'\', this);')) !!}
								@endif
								@if($user->tipouser_id!="7" && $user->tipouser_id!="10" && $user->tipouser_id!="11")
									{!! Form::button('<i class="material-icons">backup</i> Km Resumen', array('class' => 'btn btn-primary p-2 pl-1 pr-1', 'id' => 'btnReporte', 'onclick' => 'reporte ()')) !!}
									{!! Form::button('<i class="material-icons">backup</i> Seg. Km', array('class' => 'btn btn-info p-2 pl-1 pr-1', 'id' => 'btnReporte2', 'onclick' => 'reporte2 ()','style'=>'background:blue')) !!}
									{!! Form::button('<i class="material-icons">backup</i> Seg. Km Detallado', array('class' => 'btn btn-info p-2 pl-1 pr-1', 'id' => 'btnReporte3', 'onclick' => 'reporte3 ()','style'=>'background:blue')) !!}
									{!! Form::button('<i class="material-icons">backup</i> Seg. Km Mes', array('class' => 'btn btn-info p-2 pl-1 pr-1', 'id' => 'btnReporte3', 'onclick' => 'reporteMes ()','style'=>'background:blue')) !!}
								@endif
								@if($user->tipouser_id!="7")
									<a href="#" onclick="excel();" class="btn btn-sm btn-warning p-2 pl-1 pr-1">
	    								<i class="material-icons">cloud_download</i> Exportar Lista
	    							</a>
	    							<a href="#" onclick="excelRevision();" class="btn btn-sm btn-warning p-2 pl-1 pr-1">
	    								<i class="material-icons">cloud_download</i> Exportar Revision
	    							</a>
	    						@endif
    							@if($user->tipouser_id!="7" && $user->tipouser_id!="10" && $user->tipouser_id!="11")
	    							<a href="#" onclick="excelNoRegistrado();" class="btn btn-sm btn-danger p-2 pl-1 pr-1">
	    								<i class="material-icons">cloud_download</i> No Registrado
	    							</a>
    							@endif
							</div>
						</div>

						{!! Form::close() !!}
						<div class="table-responsive" id="listado{{ $entidad }}">
						</div>
					</div>
				</div>
			</div>

<script>
$(document).ready(function () {
	buscar('{{ $entidad }}');
	init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
	$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="filter"]').keyup(function (e) {
		var key = window.event ? e.keyCode : e.which;
		if (key == '13') {
			buscar('{{ $entidad }}');
		}
	});

	document.getElementById('btn_remove_fecha_registro_inicial').addEventListener('click', e => {
		const fecha_registro_inicial = document.getElementById('fecha_registro_inicial');
		fecha_registro_inicial.value = '';
	});
	document.getElementById('btn_remove_fecha_registro_final').addEventListener('click', e => {
		const fecha_registro_final = document.getElementById('fecha_registro_final');
		fecha_registro_final.value = '';
	});

});
function reporte(){
    window.open('mantcorrprev/recorridoexcel?fechainicial='+$("#fecha_registro_inicial").val()+"&fechafinal="+$("#fecha_registro_final").val(),'_blank');
}
function reporte2(){
    window.open('mantcorrprev/recorridoResumenexcel?fechainicial='+$("#fecha_registro_inicial").val()+"&fechafinal="+$("#fecha_registro_final").val(),'_blank');
}
function reporte3(){
    window.open('mantcorrprev/recorridoResumenDetalladoexcel?fechainicial='+$("#fecha_registro_inicial").val()+"&fechafinal="+$("#fecha_registro_final").val(),'_blank');
}
function reporteMes(){
    window.open('mantcorrprev/recorridoResumenMesexcel?fechainicial='+$("#fecha_registro_inicial").val()+"&fechafinal="+$("#fecha_registro_final").val(),'_blank');
}
function excel(){
    window.open('mantcorrprev/exportar?fecha_registro_inicial='+$("#fecha_registro_inicial").val()+"&fecha_registro_final="+$("#fecha_registro_final").val()+"&modelo="+$("#modelo").val()+"&conductor="+$("#conductor").val()+"&ua="+$("#ua").val()+"&lider="+$("#txtLider").val(),'_blank');
}
function excelRevision(){
	if($("#accesorio").val()==""){
		alert("Debe seleccionar Revision");
		return false;
	}
    window.open('mantcorrprev/exportarRevision?fecha_registro_inicial='+$("#fecha_registro_inicial").val()+"&fecha_registro_final="+$("#fecha_registro_final").val()+"&modelo="+$("#modelo").val()+"&conductor="+$("#conductor").val()+"&ua="+$("#ua").val()+"&lider="+$("#txtLider").val()+"&accesorio="+$("#accesorio").val(),'_blank');
}
function excelNoRegistrado(){
    window.open('mantcorrprev/exportarNoRegistrado?fecha_registro_inicial='+$("#fecha_registro_inicial").val()+"&fecha_registro_final="+$("#fecha_registro_final").val(),'_blank');
}
</script>
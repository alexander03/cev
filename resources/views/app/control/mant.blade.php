<?php
if(is_null($control)){
	$fecha = date("Y-m-d");
	$hora = "";
	$origen = "";
	$destino = "";
}else{
	$fecha = date("Y-m-d",strtotime($control->fecha));
	if(!is_null($control->origen2)){
		$origen = $control->origen2->nombre;
	}else{
		$origen = $control->origen;
	}
	if(!is_null($control->destino2)){
		$destino = $control->destino2->nombre;
	}else{
		$destino = $control->destino;
	}
	$hora = date("H:i",strtotime($control->fecha));
}
?>

<div id="divMensajeError{!! $entidad !!}"></div>
<div id="my-div-errors" class="hidden"><h5 class="text-center p-0 m-0"><span class="badge badge-danger"></span></h5></div>
{!! Form::model($control, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="row">
	<div class="col-lg-4">
		{!! Form::label('fecha', 'Fecha:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::date('fecha', $fecha, array('class' => 'form-control input-xs', 'id' => 'fecha')) !!}
		</div>
	</div>
	<div class="col-lg-4">
		{!! Form::label('hora', 'Hora:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::time('hora', $hora, array('class' => 'form-control input-xs', 'id' => 'hora')) !!}
		</div>
	</div>
	<div class="col-lg-4">
		{!! Form::label('tipo', 'Tipo:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('tipo',$cboTipo, null, array('class' => 'form-control input-xs', 'id' => 'tipo')) !!}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-4">
		{!! Form::label('turno_id', 'Turno:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('turno_id',$cboTurno, null, array('class' => 'form-control input-xs', 'id' => 'turno_id')) !!}
		</div>
	</div>
	<div class="col-lg-4">
		{!! Form::label('garita_id', 'Garita:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('garita_id',$cboGarita, null, array('class' => 'form-control input-xs', 'id' => 'garita_id')) !!}
		</div>
	</div>
	<div class="col-lg-4">
		{!! Form::label('agente_id', 'Agente:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('agente_id',$cboAgente, null, array('class' => 'form-control input-xs', 'id' => 'agente_id')) !!}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-3">
		{!! Form::label('origen', 'Origen:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('origen', $origen, array('class' => 'form-control input-xs js-origen', 'id' => 'origen')) !!}
			<small id="autoComplete_listDestino" class="text-danger"></small>
			{!! Form::hidden('origen_id', null, array('id' => 'origen_id')) !!}
		</div>
	</div>
	<div class="col-lg-3">
		{!! Form::label('destino', 'Destino:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('destino', $destino, array('class' => 'form-control input-xs js-destino', 'id' => 'destino')) !!}
			<small id="autoComplete_listOrigen" class="text-danger"></small>
			{!! Form::hidden('destino_id', null, array('id' => 'destino_id')) !!}
		</div>
	</div>
	<div class="col-lg-6">
		{!! Form::label('observacion', 'Observacion:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::textarea('observacion', null, array('class' => 'form-control input-xs', 'id' => 'observacion','rows'=>'4')) !!}
		</div>
	</div>
	<div class="col-lg-6" hidden=''>
		{!! Form::label('conductor', 'Conductor:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('conductor', null, array('class' => 'form-control input-xs js-conductor', 'id' => 'conductor')) !!}
			<small id="autoComplete_listConductor" class="text-danger"></small>
			{!! Form::hidden('conductor_id', null, array('id' => 'conductor_id')) !!}
			{!! Form::hidden('personaconductor_id', null, array('id' => 'personaconductor_id')) !!}
		</div>
	</div>
</div>
<br /> 
<div class="row">
	<div class="col-1">
		{!! Form::label('mostrardni', 'DNI:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label','style'=>'margin-bottom:0px;')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('mostrardni',["SI"=>"SI","NO"=>"NO"], null, array('class' => 'form-control input-xs', 'id' => 'mostrardni','onchange'=>'mostrarDNI();')) !!}
		</div>
	</div>
	<div class="col-lg-2" id="divDoc">
		{!! Form::label('dni', 'Ingresar:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('dni', null, array('class' => 'form-control input-xs js-dni', 'id' => 'dni')) !!}
			<small id="autoComplete_listDni" class="text-danger"></small>
		</div>
	</div>
	<div class="col-1 justify-content-around" id="divDoc2">
		{!! Form::button('<span class="material-icons">cached</span>', array('class' => 'btn btn-info p-2 pl-1 pr-1', 'id' => 'btn-consult')) !!}
		{!! Form::button('<span class="material-icons">delete</span>', array('class' => 'btn btn-danger p-2 pl-1 pr-1', 'id' => 'btn-clear')) !!}
	</div>
	<div class="col-lg-5">
		{!! Form::label('persona', 'Persona:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('persona', null, array('class' => 'form-control input-xs js-persona', 'id' => 'persona')) !!}
			<small id="autoComplete_listPersona" class="text-danger"></small>
			{!! Form::hidden('persona_id', 0, array('id' => 'persona_id')) !!}
			{!! Form::hidden('personawp_id', 0, array('id' => 'personawp_id')) !!}
		</div>
	</div>
	<div class="col-lg-1 d-none justify-content-around">
		{!! Form::button('<i class="fa fa-search fa-lg table-add"></i>  H2O / CTO', array('class' => 'btn btn-info btn-sm', 'id' => 'btnBuscar', 'onclick' => 'modal (\''.URL::route('control.wp', array('listar'=>'SI','page'=>'1','filas'=>'1000')).'\', \'Buscar WP\', this);','title'=>'Buscar WP')) !!}
	</div>
	<div class="col-lg-1">
		{!! Form::label('cboConductor', 'Conductor:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label','style'=>'margin-bottom:0px;')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			<select id="cboConductor" class = 'form-control input-xs' name="cboConductor">
				<option value="S">Si</option>
				<option value="N">No</option>
			</select>
		</div>
	</div>
	<div class="col-lg-2">
		{!! Form::label('cboCaminando', 'Vehiculo:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label','style'=>'margin-bottom:0px;')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			<select id="cboCaminando" class = 'form-control input-xs' name="cboCaminando">
				<option value="N">Si</option>
				<option value="S">No</option>
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-3 col-sm-12">
		{!! Form::label('unidad_placa', 'Unidad placa:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('unidad_placa', '', array('class' => 'form-control input-xs solo-lectura', 'id' => 'unidad_placa')) !!}
		</div>
	</div>
	<div class="col-lg-7 col-sm-12">
		{!! Form::label('unidad_descripcion', 'Unidad descripción:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('unidad_descripcion', '', array('class' => 'form-control input-xs', 'id' => 'unidad_descripcion')) !!}
			<input name="vehiculo_id" id="vehiculo_id" type="hidden" value="">
			<input name="equipo_id" id="equipo_id" type="hidden" value="">
		</div>
	</div>
	<div class="col-lg-2 col-sm-12">
		{!! Form::label('km', 'KM:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('km', null, array('class' => 'form-control input-xs', 'id' => 'km')) !!}
		</div>
	</div>
	<div class="col-2">
		<i id="unidad_loading" class="fa fa-spinner fa-lg fa-spin text-info" aria-hidden="true" hidden></i>
	</div>
	<div class="col-12">
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
					<?php
					$contador = 1;
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-11">
		{!! Form::label('contratista', 'Empresa:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('contratista', null, array('class' => 'form-control input-xs js-contratista', 'id' => 'contratista')) !!}
			<small id="autoComplete_list2" class="text-danger"></small>
			{!! Form::hidden('contratista_id', null, array('id' => 'contratista_id')) !!}
			{!! Form::hidden('empresa_id', null, array('id' => 'empresa_id')) !!}
		</div>
	</div>
	{!! Form::button('<i class="fa fa-plus fa-lg table-add"></i>', array('class' => 'btn btn-success btn-sm', 'id' => 'btnAgregar', 'onclick' => 'agregarfila();')) !!}
</div>
<br />
<div class="table-responsive col-lg-12" style="max-height: 200px;overflow-y: auto;">
	<table id="example1" class="table table-bordered table-striped table-condensed table-hover">
		<thead>
			<tr>
				<th colspan="1"  class="text-center" >#</th>
				<th colspan="1"  class="text-center" >DNI</th>
				<th colspan="1"  class="text-center" >Persona</th>
				<th colspan="1"  class="text-center" >Conductor</th>
				<th colspan="1"  class="text-center" >Vehiculo</th>
				<th colspan="1"  class="text-center" >Empresa</th>
				<th colspan="1"  class="text-center" >KM</th>
				<th colspan="1"  class="text-center" >Edit.</th>
				<th colspan="1"  class="text-center" >Elim.</th>
			</tr>
		</thead>
		<tbody id="tbody">
			<?php
			if(count($detalle)<=1)$oObservaciones=array();
			?>
			@foreach ($detalle as $key => $value)
				<tr>
					<td name="nnn" style="text-align:center;">{{ $contador }}</td>
					<td>{{ $value->dni }}</td>
					<td style="@if($value->persona_id==0 && $value->personawp_id==0) color:red; @endif">{{ $value->persona }}</td>
					<td> {{ $value->conductor }} </td>
					<td>{{ $value->vehiculo }}</td>
					@if(!is_null($value->contratista))
					<td>{{ $value->contratista->razonsocial }}</td>
					@elseif(!is_null($value->empresa2))
					<td>{{ $value->empresa2->razonsocial }}</td>
					@else
					<td> {{ $value->empresa }} </td>
					@endif
					<td>{{ $value->km }}</td>
					<td onclick="editarfila({{ $value->id }});" style="color:blue;text-align:center;width:50px;cursor: pointer;"><i class="material-icons text-center" style="margin-bottom:20px;">edit</i></td>
					<td onclick="if(confirm('¿Desea Eliminar el item?')){borrarfila({{ $value->id }});deleteRow(this);}" style="color:#ff0000;text-align:center;width:50px;cursor: pointer;"><i class="material-icons text-center">close</i>
					<p><input type="hidden" name="dniList[]" id="dniList{{$value->id}}" value="{{ $value->dni }}"></p>
					<p><input type="hidden" name="personaList[]" id="personaList{{$value->id}}" value="{{ $value->persona }}"></p>
					<p><input type="hidden" name="contratistaList[]" id="contratistaList{{$value->id}}" value="{{ is_null($value->contratista)?$value->empresa:$value->contratista->razonsocial }}"></p>
					<p><input type="hidden" name="conductorList[]" id="conductorList{{$value->id}}" value="{{ $value->conductor }}"></p>
					<p><input type="hidden" name="vehiculoList[]" id="vehiculoList{{$value->id}}" value="{{ $value->vehiculo }}"></p>
					<p><input type="hidden" name="kmList[]" id="kmList{{$value->id}}" value="{{ $value->km }}"></p>
					<p><input type="hidden" name="personaId[]" id="personaId{{$value->id}}" value="{{ $value->persona_id }}"></p>
					<p><input type="hidden" name="personaWPId[]" id="personaWPId{{$value->id}}" value="{{ $value->personawp_id }}"></p>
					<p><input type="hidden" name="contratistaId[]" id="contratistaId{{$value->id}}" value="{{ $value->contratista_id }}"></p>
					<p><input type="hidden" name="empresaId[]" id="empresaId{{$value->id}}" value="{{ $value->empresa_id }}"></p>
					<p><input type="hidden" name="vehiculoId[]" id="vehiculoId{{$value->id}}" value="{{ $value->vehiculo_id }}"></p>
					<p><input type="hidden" name="equipoId[]" id="equipoId{{$value->id}}" value="{{ $value->equipo_id }}"></p>
					<input type="hidden" name="iddetalle[]" value="{{ $value->id }}">
					</td>
				</tr>
				<?php
				$contador = $contador + 1;
				?>
			@endforeach
		</tbody>
	</table>
	<?php $items=''; if(count($detalle)>=1){ $items='1';}?>
	<input type="hidden" name="cuantasdescripciones" id="cuantasdescripciones" value="{{ $items }}"></p>
</div>

<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
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
	var a = <?php echo $contador?> -1;
	$(document).ready(function() {
		configurarAnchoModal('1100');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	
		// TABLA SELECCIONADORA DE UNIDADES
		let listaUnidades = [];
		const unidadPlaca = document.getElementById('unidad_placa');
		const unidadDescripcion = document.getElementById('unidad_descripcion');
		const unidadLoading = document.getElementById('unidad_loading');
		const vehiculoId = document.getElementById('vehiculo_id');
		const equipoId = document.getElementById('equipo_id');
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

			return fetch(`./control/searchUnidad?filter=${filter}`)
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
					vehiculoId.value = listaUnidades[i].vehiculo_id;
					equipoId.value = listaUnidades[i].equipo_id;
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

		const doSearchContratista = () => {

		    var autoCompletejsContratista = new autoComplete({
		        data: {
		            src: async () => {
		                // Loading placeholder text
		                document
		                    .querySelector(".js-contratista")
		                    .setAttribute("placeholder", "Loading...");
		       
		                // Fetch External Data Source
		                const headers = new Headers();
		                headers.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
		                const config = {
		                    headers,
		                    method:'GET'
		                };
		                  //Query
		                const query = document.querySelector('.js-contratista').value;
		                const source = await fetch(
		                    `control/search/contratista/${query}` , config
		                );
		                let data = await source.json();
		                // Post loading placeholder text
		                // Returns Fetched data
		                return data;
		            },
		            key: ["descripcion"],
		            cache: false
		        },
		        sort: (a, b) => {
		            if (a.match < b.match) return -1;
		            if (a.match > b.match) return 1;
		            return 0;
		        },
		        selector: ".js-contratista",
		        threshold: 1,
		        debounce: 0,
		        searchEngine: "strict",
		        highlight: true,
		        maxResults: 5,
		        resultsList: {
		            render: true,
		            container: source => {
		                source.setAttribute("id", "autoComplete_list");
		                source.setAttribute("class", "u-search-ua__result");
		            },
		            destination: document.querySelector(".js-contratista"),
		            position: "afterend",
		            element: "ul"
		        },
		        resultItem: {
		            content: (data, source) => {
		                source.innerHTML = data.match;
		            },
		            element: "li"
		        },
		        noResults: () => {
		            document.querySelector("#autoComplete_list2").innerText = 'Sin resultados';
		            $('#contratista_id').val("0");
		            $('#empresa_id').val("0");
		        },
		        onSelection: feedback => { //VA NUESTRA LOGICA
		            document.querySelector("#autoComplete_list2").innerText = '';
		            const selection = feedback.selection.value;
		            document.querySelector(".js-contratista").value = selection.razonsocial;
		            document.querySelector("#contratista_id").value = selection.contratista_id;
		            document.querySelector("#empresa_id").value = selection.empresa_id;
		            // console.log(feedback);
		        }
		    });
		    
		    // Toggle event for search input
		    // showing & hidding results list onfocus / blur
		    ["focus", "blur"].forEach(function(eventType) {
		      const resultsList = document.querySelector("#autoComplete_list2");
		    
		      document.querySelector(".js-contratista").addEventListener(eventType, function() {
		        // Hide results list & show other elemennts
		        if (eventType === "blur") {
		          resultsList.style.display = "none";
		        } else if (eventType === "focus") {
		          // Show results list & hide other elemennts
		          resultsList.style.display = "block";
		        }
		      });
		    });
		};

		const doSearchPersona = () => {

		    var autoCompletejsPersona = new autoComplete({
		        data: {
		            src: async () => {
		                // Loading placeholder text
		                document
		                    .querySelector(".js-persona")
		                    .setAttribute("placeholder", "Loading...");
		       
		                // Fetch External Data Source
		                const headers = new Headers();
		                headers.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
		                const config = {
		                    headers,
		                    method:'GET'
		                };
		                  //Query
		                const query = document.querySelector('.js-persona').value;
		                const source = await fetch(
		                    `control/search/persona/${query}` , config
		                );
		                let data = await source.json();
		                // Post loading placeholder text
		                // Returns Fetched data
		                return data;
		            },
		            key: ["descripcion"],
		            cache: false
		        },
		        sort: (a, b) => {
		            if (a.match < b.match) return -1;
		            if (a.match > b.match) return 1;
		            return 0;
		        },
		        selector: ".js-persona",
		        threshold: 1,
		        debounce: 0,
		        searchEngine: "strict",
		        highlight: true,
		        maxResults: 5,
		        resultsList: {
		            render: true,
		            container: source => {
		                source.setAttribute("id", "autoComplete_list");
		                source.setAttribute("class", "u-search-ua__result");
		            },
		            destination: document.querySelector(".js-persona"),
		            position: "afterend",
		            element: "ul"
		        },
		        resultItem: {
		            content: (data, source) => {
		                source.innerHTML = data.match;
		            },
		            element: "li"
		        },
		        noResults: () => {
		            document.querySelector("#autoComplete_listPersona").innerText = 'Sin resultados';
		            $('#persona_id').val("0");
		            $('#contratista_id').val("0");
		            $('#contratista').val("");
		        },
		        onSelection: feedback => { //VA NUESTRA LOGICA
		            document.querySelector("#autoComplete_listPersona").innerText = '';
		            const selection = feedback.selection.value;
		            document.querySelector(".js-persona").value = selection.persona;
		            document.querySelector("#dni").value = selection.dni;
		            document.querySelector("#persona_id").value = selection.persona_id;
		            document.querySelector("#contratista_id").value = selection.contratista_id;
		            document.querySelector("#contratista").value = selection.contratista;
		            document.querySelector("#personawp_id").value = selection.personawp_id;
		            // console.log(feedback);
		        }
		    });
		    
		    // Toggle event for search input
		    // showing & hidding results list onfocus / blur
		    ["focus", "blur"].forEach(function(eventType) {
		      const resultsList = document.querySelector("#autoComplete_listPersona");
		    
		      document.querySelector(".js-contratista").addEventListener(eventType, function() {
		        // Hide results list & show other elemennts
		        if (eventType === "blur") {
		          resultsList.style.display = "none";
		        } else if (eventType === "focus") {
		          // Show results list & hide other elemennts
		          resultsList.style.display = "block";
		        }
		      });
		    });
		};

		const doSearchConductor = () => {

		    var autoCompletejsConductor = new autoComplete({
		        data: {
		            src: async () => {
		                // Loading placeholder text
		                document
		                    .querySelector(".js-conductor")
		                    .setAttribute("placeholder", "Loading...");
		       
		                // Fetch External Data Source
		                const headers = new Headers();
		                headers.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
		                const config = {
		                    headers,
		                    method:'GET'
		                };
		                  //Query
		                const query = document.querySelector('.js-conductor').value;
		                const source = await fetch(
		                    `control/search/conductor/${query}` , config
		                );
		                let data = await source.json();
		                // Post loading placeholder text
		                // Returns Fetched data
		                return data;
		            },
		            key: ["descripcion"],
		            cache: false
		        },
		        sort: (a, b) => {
		            if (a.match < b.match) return -1;
		            if (a.match > b.match) return 1;
		            return 0;
		        },
		        selector: ".js-conductor",
		        threshold: 1,
		        debounce: 0,
		        searchEngine: "strict",
		        highlight: true,
		        maxResults: 5,
		        resultsList: {
		            render: true,
		            container: source => {
		                source.setAttribute("id", "autoComplete_list");
		                source.setAttribute("class", "u-search-ua__result");
		            },
		            destination: document.querySelector(".js-conductor"),
		            position: "afterend",
		            element: "ul"
		        },
		        resultItem: {
		            content: (data, source) => {
		                source.innerHTML = data.match;
		            },
		            element: "li"
		        },
		        noResults: () => {
		            document.querySelector("#autoComplete_listConductor").innerText = 'Sin resultados';
		            $('#conductor_id').val("0");
		            $('#personaconductor_id').val("0");
		        },
		        onSelection: feedback => { //VA NUESTRA LOGICA
		            document.querySelector("#autoComplete_listConductor").innerText = '';
		            const selection = feedback.selection.value;
		            document.querySelector(".js-conductor").value = selection.persona;
		            document.querySelector("#conductor_id").value = selection.conductor_id;
		            document.querySelector("#personaconductor_id").value = selection.personaconductor_id;
		            // console.log(feedback);
		        }
		    });
		    
		    // Toggle event for search input
		    // showing & hidding results list onfocus / blur
		    ["focus", "blur"].forEach(function(eventType) {
		      const resultsList = document.querySelector("#autoComplete_listConductor");
		    
		      document.querySelector(".js-contratista").addEventListener(eventType, function() {
		        // Hide results list & show other elemennts
		        if (eventType === "blur") {
		          resultsList.style.display = "none";
		        } else if (eventType === "focus") {
		          // Show results list & hide other elemennts
		          resultsList.style.display = "block";
		        }
		      });
		    });
		};

		const doSearchDNI = () => {

		    var autoCompletejsPersona = new autoComplete({
		        data: {
		            src: async () => {
		                // Loading placeholder text
		                document
		                    .querySelector(".js-dni")
		                    .setAttribute("placeholder", "Loading...");
		       
		                // Fetch External Data Source
		                const headers = new Headers();
		                headers.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
		                const config = {
		                    headers,
		                    method:'GET'
		                };
		                  //Query
		                const query = document.querySelector('.js-dni').value;
		                const source = await fetch(
		                    `control/search/persona/${query}` , config
		                );
		                let data = await source.json();
		                // Post loading placeholder text
		                // Returns Fetched data
		                return data;
		            },
		            key: ["descripcion"],
		            cache: false
		        },
		        sort: (a, b) => {
		            if (a.match < b.match) return -1;
		            if (a.match > b.match) return 1;
		            return 0;
		        },
		        selector: ".js-dni",
		        threshold: 1,
		        debounce: 0,
		        searchEngine: "strict",
		        highlight: true,
		        maxResults: 5,
		        resultsList: {
		            render: true,
		            container: source => {
		                source.setAttribute("id", "autoComplete_list");
		                source.setAttribute("class", "u-search-ua__result");
		            },
		            destination: document.querySelector(".js-dni"),
		            position: "afterend",
		            element: "ul"
		        },
		        resultItem: {
		            content: (data, source) => {
		                source.innerHTML = data.match;
		            },
		            element: "li"
		        },
		        noResults: () => {
		            document.querySelector("#autoComplete_listDni").innerText = 'Sin resultados';
		            $('#persona_id').val("0");
		            $('#contratista_id').val("0");
		            $('#contratista').val("");
		        },
		        onSelection: feedback => { //VA NUESTRA LOGICA
		            document.querySelector("#autoComplete_listDni").innerText = '';
		            const selection = feedback.selection.value;
		            document.querySelector(".js-persona").value = selection.persona;
		            document.querySelector("#dni").value = selection.dni;
		            document.querySelector("#persona_id").value = selection.persona_id;
		            document.querySelector("#contratista_id").value = selection.contratista_id;
		            document.querySelector("#contratista").value = selection.contratista;
		            document.querySelector("#personawp_id").value = selection.personawp_id;
		            // console.log(feedback);
		        }
		    });
		    
		    // Toggle event for search input
		    // showing & hidding results list onfocus / blur
		    ["focus", "blur"].forEach(function(eventType) {
		      const resultsList = document.querySelector("#autoComplete_listDni");
		    
		      document.querySelector(".js-contratista").addEventListener(eventType, function() {
		        // Hide results list & show other elemennts
		        if (eventType === "blur") {
		          resultsList.style.display = "none";
		        } else if (eventType === "focus") {
		          // Show results list & hide other elemennts
		          resultsList.style.display = "block";
		        }
		      });
		    });
		};

		const doSearchOrigen = () => {

		    var autoCompletejsOrigen = new autoComplete({
		        data: {
		            src: async () => {
		                // Loading placeholder text
		                document
		                    .querySelector(".js-origen")
		                    .setAttribute("placeholder", "Loading...");
		       
		                // Fetch External Data Source
		                const headers = new Headers();
		                headers.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
		                const config = {
		                    headers,
		                    method:'GET'
		                };
		                  //Query
		                const query = document.querySelector('.js-origen').value;
		                const source = await fetch(
		                    `control/search/lugar/${query}` , config
		                );
		                let data = await source.json();
		                // Post loading placeholder text
		                // Returns Fetched data
		                return data;
		            },
		            key: ["descripcion"],
		            cache: false
		        },
		        sort: (a, b) => {
		            if (a.match < b.match) return -1;
		            if (a.match > b.match) return 1;
		            return 0;
		        },
		        selector: ".js-origen",
		        threshold: 1,
		        debounce: 0,
		        searchEngine: "strict",
		        highlight: true,
		        maxResults: 5,
		        resultsList: {
		            render: true,
		            container: source => {
		                source.setAttribute("id", "autoComplete_list");
		                source.setAttribute("class", "u-search-ua__result");
		            },
		            destination: document.querySelector(".js-origen"),
		            position: "afterend",
		            element: "ul"
		        },
		        resultItem: {
		            content: (data, source) => {
		                source.innerHTML = data.match;
		            },
		            element: "li"
		        },
		        noResults: () => {
		            document.querySelector("#autoComplete_listOrigen").innerText = 'Sin resultados';
		            $('#origen_id').val("0");
		        },
		        onSelection: feedback => { //VA NUESTRA LOGICA
		            document.querySelector("#autoComplete_listOrigen").innerText = '';
		            const selection = feedback.selection.value;
		            document.querySelector(".js-origen").value = selection.descripcion;
		            document.querySelector("#origen_id").value = selection.id;
		            // console.log(feedback);
		        }
		    });
		    
		    // Toggle event for search input
		    // showing & hidding results list onfocus / blur
		    ["focus", "blur"].forEach(function(eventType) {
		      const resultsList = document.querySelector("#autoComplete_listOrigen");
		    
		      document.querySelector(".js-origen").addEventListener(eventType, function() {
		        // Hide results list & show other elemennts
		        if (eventType === "blur") {
		          resultsList.style.display = "none";
		        } else if (eventType === "focus") {
		          // Show results list & hide other elemennts
		          resultsList.style.display = "block";
		        }
		      });
		    });
		};

		const doSearchDestino = () => {

		    var autoCompletejsDestino = new autoComplete({
		        data: {
		            src: async () => {
		                // Loading placeholder text
		                document
		                    .querySelector(".js-destino")
		                    .setAttribute("placeholder", "Loading...");
		       
		                // Fetch External Data Source
		                const headers = new Headers();
		                headers.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
		                const config = {
		                    headers,
		                    method:'GET'
		                };
		                  //Query
		                const query = document.querySelector('.js-destino').value;
		                const source = await fetch(
		                    `control/search/lugar/${query}` , config
		                );
		                let data = await source.json();
		                // Post loading placeholder text
		                // Returns Fetched data
		                return data;
		            },
		            key: ["descripcion"],
		            cache: false
		        },
		        sort: (a, b) => {
		            if (a.match < b.match) return -1;
		            if (a.match > b.match) return 1;
		            return 0;
		        },
		        selector: ".js-destino",
		        threshold: 1,
		        debounce: 0,
		        searchEngine: "strict",
		        highlight: true,
		        maxResults: 5,
		        resultsList: {
		            render: true,
		            container: source => {
		                source.setAttribute("id", "autoComplete_list");
		                source.setAttribute("class", "u-search-ua__result");
		            },
		            destination: document.querySelector(".js-destino"),
		            position: "afterend",
		            element: "ul"
		        },
		        resultItem: {
		            content: (data, source) => {
		                source.innerHTML = data.match;
		            },
		            element: "li"
		        },
		        noResults: () => {
		            document.querySelector("#autoComplete_listDestino").innerText = 'Sin resultados';
		            $('#destino_id').val("0");
		        },
		        onSelection: feedback => { //VA NUESTRA LOGICA
		            document.querySelector("#autoComplete_listDestino").innerText = '';
		            const selection = feedback.selection.value;
		            document.querySelector(".js-destino").value = selection.descripcion;
		            document.querySelector("#destino_id").value = selection.id;
		            // console.log(feedback);
		        }
		    });
		    
		    // Toggle event for search input
		    // showing & hidding results list onfocus / blur
		    ["focus", "blur"].forEach(function(eventType) {
		      const resultsList = document.querySelector("#autoComplete_listDestino");
		    
		      document.querySelector(".js-destino").addEventListener(eventType, function() {
		        // Hide results list & show other elemennts
		        if (eventType === "blur") {
		          resultsList.style.display = "none";
		        } else if (eventType === "focus") {
		          // Show results list & hide other elemennts
		          resultsList.style.display = "block";
		        }
		      });
		    });
		};

		doSearchContratista();
		doSearchPersona();
		doSearchConductor();
		doSearchDNI();
		doSearchOrigen();
		doSearchDestino();

		const myDivErrors = document.getElementById('my-div-errors');
		const btnConsultar = document.getElementById('btn-consult');
		const btnClear = document.getElementById('btn-clear');

		const inputDni = document.getElementById('dni');

		inputDni.addEventListener('change', e => {
			if(isNaN(e.target.value)) e.target.value = '';
		})

		inputDni.addEventListener('keydown', e => {
			// console.log(e.keyCode)
			if(e.target.value.length > 8 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 39) e.preventDefault();
			if(e.keyCode < 8 || (e.keyCode >9 && e.keyCode< 37) || (e.keyCode >37 && e.keyCode< 39) || 
					(e.keyCode >39 && e.keyCode< 48) || (e.keyCode >57 && e.keyCode< 67) || (e.keyCode >67 && e.keyCode< 86) || (e.keyCode >86 && e.keyCode< 96) || e.keyCode> 105) e.preventDefault();
		})


		btnConsultar.addEventListener('click', async (e) => {
			const dni = document.getElementById('dni').value.trim();
			const iconLoader = e.target.children[0];
			//iconLoader.classList.add('fa-spin');
			try {
				if(dni.length == 8) {
					myDivErrors.classList.add('hidden');
					let person = await consultReniec(dni);
					
					if(person) {
						$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="persona"]').val(person.nombres + " " + person.apepat+" "+person.apemat);
						inputDni.setAttribute('readonly', true);
					}else {
						showErrors('No existe este DNI');
						inputDni.removeAttribute('readonly');
						inputDni.focus();
					}
				}
			} catch (error) {
				console.log(error)
			}
			//iconLoader.classList.remove('fa-spin');
		});

		btnClear.addEventListener('click', () => {
			inputDni.value = '';
			$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="persona"]').val('');
			inputDni.removeAttribute('readonly');
			inputDni.focus();
			myDivErrors.classList.add('hidden');
		})

		const consultReniec = (dni) => {
			// const uriConsult = 'http://127.0.0.1:80/Reniec/consulta_reniec.php';
			//const uriConsult = './Reniec/consulta_reniec.php';
			const uriConsult = './Reniec/consultaReniec.php';
			return fetch(uriConsult,{
				method: 'POST',
				body: 'dni=' + dni,
				headers: {
					'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8;'
				}
			})
			.then(res => res.status === 200 ? res.json() : console.error(`Error al cosultar DNI en la db: ${res.status}`));
		}
	}); 


function seleccionarWP(idusuario,personal,usuario,email,contratista_id,dni){
	if(idusuario==""){
		alert("Falta crear usuario en WorkPeople");
		return false;
	}
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="persona"]').val(personal);
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni"]').val(dni);
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="personawp_id"]').val(idusuario);
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="persona_id"]').val(0);
    $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="contratista_id"]').val(contratista_id);
    if(contratista_id=="12"){
    	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="contratista"]').val("H2OLMOS S.A.");
    }else{
    	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="contratista"]').val("CONCESIONARIA TRASVASE OLMOS S.A.");
    }
    $(".modal0").css('overflow-y','auto');
    cerrarModal();
}

function onlyAllowAlphanumeric() {
	    this.value = this.value.replace(/[^a-zA-Z0-9 _]/g, '');
	};

$('input[id="origen"]').keyup(onlyAllowAlphanumeric);
$('input[id="destino"]').keyup(onlyAllowAlphanumeric);

function agregarfila(){
	if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="persona"]').val()==""){
		alert("Debe ingresar una persona");
		return false;
	}
	if($("#cboCaminando").val()=="N"){
		/*if($("#conductor").val()==""){
			alert("Debe ingresar un conductor");
			return false;
		}*/
		if($("#unidad_placa").val()==""){
			alert("Debe ingresar una placa");
			return false;
		}
		if($("#unidad_descripcion").val()==""){
			alert("Debe ingresar una descripcion del vehiculo");
			return false;
		}
		if($("#km").val()==""){
			alert("Debe ingresar un Kilometraje");
			return false;
		}
		if($(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="contratista"]').val()=="" || $("#contratista_id").val()==""){
			alert("Debe seleccionar una empresa");
			return false;
		}
	}
	var vband = true;
	$("input[name='personaList[]']").each(function(indice, elemento) {
		if($(elemento).val()==$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="persona"]').val()){
			alert("No se puede agregar a la misma persona");
			vband = false;
		}

	});
	$("input[name='conductorList[]']").each(function(indice, elemento) {
		if($(elemento).val()=="S" && $('#cboConductor').val()=="S"){
			alert("No se puede agregar otro conductor");
			vband = false;
		}
	});
	if(vband==false){
		return false;
	}
	a++;
	document.getElementById('cuantasdescripciones').value='1';
	var persona_id=document.getElementById("persona_id").value;
	var personawp_id=document.getElementById("personawp_id").value;
	var persona=$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="persona"]').val();
	var conductor=$('#cboConductor').val();
	var dni=$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni"]').val();
	var contratista=$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="contratista"]').val();
	var contratista_id=document.getElementById("contratista_id").value;
	var empresa_id=document.getElementById("empresa_id").value;
	var vehiculo_id=document.getElementById("vehiculo_id").value;
	var equipo_id=document.getElementById("equipo_id").value;
	var vehiculo=document.getElementById("unidad_placa").value + " " + $("#unidad_descripcion").val();
	var km=document.getElementById("km").value;
	var tr = document.createElement("tr");
	tr.innerHTML = `
		<tr>
			<td name="nnn" style="text-align:center;">${a}</td>
			<td>${dni}</td>
			<td>${persona}</td>
			<td>${conductor}</td>
			<td>${vehiculo}</td>
			<td>${contratista}</td>
			<td>${km}</td>
			<td> - </td>
			<td onclick="if(confirm('¿Desea Eliminar el item?')){deleteRow(this);}" style="color:#ff0000;text-align:center;width:50px;cursor: pointer;"><i class="material-icons text-center">close</i>
			<p><input type="hidden" name="dniList[]" value="${dni}"></p>
			<p><input type="hidden" name="personaList[]" value="${persona}"></p>
			<p><input type="hidden" name="conductorList[]" value="${conductor}"></p>
			<p><input type="hidden" name="vehiculoList[]" value="${vehiculo}"></p>
			<p><input type="hidden" name="kmList[]" value="${km}"></p>
			<p><input type="hidden" name="personaId[]" value="${persona_id}"></p>
			<p><input type="hidden" name="personaWPId[]" value="${personawp_id}"></p>
			<p><input type="hidden" name="contratistaList[]" value="${contratista}"></p>
			<p><input type="hidden" name="contratistaId[]" value="${contratista_id}"></p>
			<p><input type="hidden" name="empresaId[]" value="${empresa_id}"></p>
			<p><input type="hidden" name="vehiculoId[]" value="${vehiculo_id}"></p>
			<p><input type="hidden" name="equipoId[]" value="${equipo_id}"></p>
			<p><input type="hidden" name="iddetalle[]" id="ididid" value="-1"></p>
			</td>
		</tr>`;
	document.getElementById("tbody").appendChild(tr);
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni"]').val("");
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="persona"]').val("");
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="persona_id"]').val("0");
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="personawp_id"]').val("0");
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="contratista_id"]').val("0");
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="empresa_id"]').val("0");
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="contratista"]').val("");
}

function deleteRow(btn) {
	a--;
	if (document.getElementsByName('personaId[]').length>1) {
		document.getElementById('cuantasdescripciones').value='1';
	}else{
		document.getElementById('cuantasdescripciones').value='';
	}
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

function editarfila(vid){
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="persona"]').val($("#personaList"+vid).val());
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni"]').val($("#dniList"+vid).val());
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="persona_id"]').val($("#personaId"+vid).val());
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="personawp_id"]').val($("#personaWPId"+vid).val());
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="contratista_id"]').val($("#contratistaId"+vid).val());
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="empresa_id"]').val($("#empresaId"+vid).val());
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="contratista"]').val($("#contratistaList"+vid).val());
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="km"]').val($("#kmList"+vid).val());
	$('#cboConductor').val($("#conductorList"+vid).val());
	var veh = $("#vehiculoList"+vid).val();
	veh = veh.split(" ");
	var veh2 = "";
	for(var xv=1;xv<veh.length;xv++){
		veh2 += veh[xv] + " ";
	}
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="unidad_placa"]').val(veh[0]);
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="unidad_descripcion"]').val(veh2.trim());
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="vehiculo_id"]').val($("#vehiculoId"+vid).val());
	$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="equipo_id"]').val($("#equipoId"+vid).val());
}
function mostrarDNI(){
	if($("#mostrardni").val()=="SI"){
		$("#divDoc").removeClass("hidden");
		$("#divDoc2").removeClass("hidden");
	}else{
		$("#divDoc").addClass("hidden");
		$("#divDoc2").addClass("hidden");
	}
}
</script>
<?php
if(is_null($pasaje)){
	$horaorigen = "";
	$origen = "";
	$destino = "";
	$horadestino = "";
	$empresa= "";
}else{
	$origen = $pasaje->origen->nombre;
	$destino = $pasaje->destino->nombre;
	$horaorigen = date("H:i",strtotime($pasaje->horaorigen));
	$horadestino = date("H:i",strtotime($pasaje->horadestino));
	$empresa = $pasaje->empresa;
}
?>

<div id="divMensajeError{!! $entidad !!}"></div>
<div id="my-div-errors" class="hidden"><h5 class="text-center p-0 m-0"><span class="badge badge-danger"></span></h5></div>
{!! Form::model($pasaje, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="row">
	<div class="col-lg-3">
		{!! Form::label('tipo', 'Tipo:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::select('tipo', $cboTipo, null, array('class' => 'form-control input-xs', 'id' => 'tipo')) !!}
		</div>
	</div>
	<div class="col-lg-3">
		{!! Form::label('fechaorigen', 'Fecha:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::date('fechaorigen', null, array('class' => 'form-control input-xs', 'id' => 'fechaorigen')) !!}
		</div>
	</div>
	<div class="col-lg-3">
		{!! Form::label('horaorigen', 'Hora Origen:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::time('horaorigen', $horaorigen, array('class' => 'form-control input-xs', 'id' => 'horaorigen')) !!}
		</div>
	</div>
	<div class="col-lg-3">
		{!! Form::label('origen', 'Origen:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('origen', $origen, array('class' => 'form-control input-xs js-origen', 'id' => 'origen')) !!}
			<small id="autoComplete_listOrigen" class="text-danger"></small>
			{!! Form::hidden('origen_id', null, array('id' => 'origen_id')) !!}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-4" hidden>
		{!! Form::label('fechadestino', 'Fecha Destino:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::date('fechadestino', null, array('class' => 'form-control input-xs', 'id' => 'fechadestino')) !!}
		</div>
	</div>
	<div class="col-lg-4">
		{!! Form::label('horadestino', 'Hora Destino:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::time('horadestino', $horadestino, array('class' => 'form-control input-xs', 'id' => 'horadestino')) !!}
		</div>
	</div>
	<div class="col-lg-4">
		{!! Form::label('destino', 'Destino:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('destino', $destino, array('class' => 'form-control input-xs js-destino', 'id' => 'destino')) !!}
			<small id="autoComplete_listDestino" class="text-danger"></small>
			{!! Form::hidden('destino_id', null, array('id' => 'destino_id')) !!}
		</div>
	</div>
	<div class="col-4 col-md-4">
		<label for="id-ua-trabajo" class="pl-3">UA</label>
		<div class="u-ua-style js-ua-trabajo-desc"  style="margin-top:15px;">
			<?php if($pasaje) if(isset($pasaje -> ua)) echo $pasaje -> ua -> descripcion; else echo 'Sin UA';?>
		</div>
		<input type="text" 
			name="ua_id_trabajo" 
			id="id-ua-trabajo" 
			class="form-control js-ua-trabajo-id" 
			value="<?php if($pasaje) if(isset($pasaje -> ua)) echo $pasaje -> ua -> codigo?>">
		<small id="autoComplete_listTrabajo" class="text-danger"></small>
	</div>
</div>
<div class="row">
	<div class="col-lg-4">
		{!! Form::label('numvuelo', 'Nro Vuelo:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('numvuelo', null, array('class' => 'form-control input-xs', 'id' => 'numvuelo')) !!}
		</div>
	</div>
	<div class="col-lg-4">
		{!! Form::label('reserva', 'Cod. Reserva:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('reserva', null, array('class' => 'form-control input-xs', 'id' => 'reserva')) !!}
		</div>
	</div>
	<div class="col-lg-4">
		{!! Form::label('aerolinea', 'Aerolinea:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('aerolinea', null, array('class' => 'form-control input-xs', 'id' => 'aerolinea')) !!}
		</div>
	</div>
</div>
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
</div>
<div class="row">
	<div class="col-lg-11">
		{!! Form::label('contratista', 'Empresa:', array('class' => 'col-lg-12 col-md-12 col-sm-12 control-label')) !!}
		<div class="col-lg-12 col-md-12 col-sm-12">
			{!! Form::text('contratista', $empresa, array('class' => 'form-control input-xs js-contratista', 'id' => 'contratista')) !!}
			<small id="autoComplete_list2" class="text-danger"></small>
			{!! Form::hidden('contratista_id', null, array('id' => 'contratista_id')) !!}
			{!! Form::hidden('empresa_id', null, array('id' => 'empresa_id')) !!}
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
		configurarAnchoModal('1100');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');

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
		                    `pasaje/search/lugar/${query}` , config
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
		                    `pasaje/search/lugar/${query}` , config
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
		doSearchDNI();
		doSearchOrigen();
		doSearchDestino();
		doSearchUATodos();

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
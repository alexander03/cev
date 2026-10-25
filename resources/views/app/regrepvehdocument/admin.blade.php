
<div>			
	{!! Form::open(['route' => $ruta["search"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'row', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
		{!! Form::hidden('page', 1, array('id' => 'page')) !!}
		{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
		<div class="row container">
			<div class="col-8 col-sm-4">
				{!! Form::label('tipo', 'Tipo:') !!}
				{!! Form::select('tipo',['all' => 'Todos','Docs'=>'Docs'] ,'', array('class' => 'form-control', 'id' => 'tipo')) !!}
				{!! Form::hidden('regrepveh_id',  $regrepveh_id, array('class' => 'form-control', 'id' => 'regrepveh_id')) !!}
			</div>
			<div class="col-4 col-sm-3">
				{!! Form::label('filas', 'Filas:')!!}
				{!! Form::selectRange('filas', 1, 30, 5, array('class' => 'form-control', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
			</div>
			<div class="col-12 col-sm-5 d-flex justify-content-around align-items-center mt-2 mt-sm-0">
				{!! Form::button('<i class="material-icons">search</i>Buscar', array('class' => 'btn btn-success p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
				<button class="btn btn-info p-2 pl-1 pr-1" id='document-nuevo'><i class="material-icons">add</i>Nuevo</button>
			</div>
		</div>
	{!! Form::close() !!}

	<div class="mt-2 p-2 rounded border border-info d-none" id='content-form-document'>
		<div id='divMensajeError{{ $entidad }}'></div>
		{!! Form::open([ 'route' => 'regrepvehdocument.store' ,'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'form-document', 'enctype' => 'multipart/form-data']) !!}
		<div class="row container mb-3">
			{!! Form::label('tipo', 'Tipo:', ['class' => 'col-4 justify-content-start']) !!}
			{!! Form::select('tipo',['' => 'Seleccione', 'Docs'=>'Docs'] ,'', array('class' => 'form-control col-8', 'id' => 'tipo')) !!}
			{!! Form::label('archivo', 'Archivo:', ['class' => 'col-4 justify-content-start']) !!}
			{!! Form::file('archivo', ['class' => 'form-control input-xs mt-2', 'id' => 'archivo']) !!}

			{!! Form::hidden('regrepveh_id',  $regrepveh_id, array('class' => 'form-control', 'id' => 'regrepveh_id')) !!}
			<input type="hidden" name="regrepvehdocument_id" id='regrepvehdocument_id'>
		</div>
		<div>
			<button class="btn btn-info btn-sm" id='document-enviar'>Enviar</button>
			<button class="btn btn-warning btn-sm" id='document-cancelar'> Cancelar</button>
		</div>
		{!! Form::close() !!}
	</div>

	<div class="card">
		<div class="table-responsive" id="listado{{ $entidad }}"></div>
	</div>
	{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
</div>


<script>
	$(document).ready(function () {
		buscar('{{ $entidad }}');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="vehiculo_id"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
	});

	document.getElementById('document-nuevo').addEventListener('click',function(e){
		e.preventDefault();
		document.getElementById('form-document').querySelector('#tipo').value = document.getElementById('formBusqueda{{ $entidad }}').querySelector('#tipo').value;
		document.getElementById('form-document').setAttribute('action','{{ URL::route("regrepvehdocument.store") }}');
		//document.getElementById('form-document').setAttribute('method','POST');
		document.getElementById('content-form-document').classList.remove('d-none');
	});
	document.getElementById('document-cancelar').addEventListener('click',function(e){
		e.preventDefault();
		document.getElementById('content-form-document').classList.add('d-none');
		let formulario = document.getElementById('form-document')
		formulario.reset();
		formulario.getElementById('regrepveh_id').value ='';
		formulario.getElementById('regrepvehdocument_id').value ='';
	});
	function editar_document(document_id,btn){
		const formulario = document.getElementById('form-document');
		formulario.setAttribute('action','{{ URL::route("regrepvehdocument.store") }}' +'/'+ document_id);

		const fila = btn.parentElement.parentElement;
		formulario.querySelector('#tipo').value = fila.children[2].dataset.tipo;
		formulario.querySelector('#regrepvehdocument_id').value = Number(document_id);
		document.getElementById('content-form-document').classList.remove('d-none');
	}

	document.getElementById('document-enviar').addEventListener('click', function(e){
		e.preventDefault();
		const formulario = document.getElementById('form-document');
		let formData = new FormData(formulario);
		let data = $.ajax({	
			headers: {
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
				},
			url : formulario.getAttribute('action'),
			type: formulario.getAttribute('method'),
			data: formData,
			processData: false,
			contentType: false,
		});
		data.done(function(msg) {
			respuesta = msg;
		}).fail(function(xhr, textStatus, errorThrown) {
			respuesta = 'ERROR';
		}).always(function() {
			if(respuesta === 'ERROR'){
			}else{
				if (respuesta === 'OK') {
					document.getElementById('content-form-document').classList.add('d-none');
					md.showNotification('top','right','Guardado correctamente','info');
					buscarCompaginado('', 'Accion realizada correctamente', '{{ $entidad }}', 'OK');
					formulario.reset();
					formulario.getElementById('regrepveh_id').value ='';
					formulario.getElementById('regrepvehdocument_id').value ='';
				} else {
					mostrarErrores(respuesta, '#form-document', '{{ $entidad }}');

				}
			}
		});

	});





</script>
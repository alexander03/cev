
          <div>			
			{!! Form::open(['route' => $ruta["search"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'row', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
			{!! Form::hidden('page', 1, array('id' => 'page')) !!}
			{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
			<div class="col-3 col-sm-3 col-md-3 col-lg-3">
				{!! Form::label('tipo', 'Tipo:') !!}
				{!! Form::select('tipo',['SOAT'=>'SOAT', 'GPS' => 'GPS' , 'RTV' => 'RTV', 'POLIZA'=>'POLIZA','TARJ. PROP.'=>'TARJ. PROP.'] ,'', array('class' => 'form-control', 'id' => 'tipo')) !!}
				{!! Form::hidden('vehiculo_id',  $vehiculo_id, array('class' => 'form-control', 'id' => 'vehiculo_id')) !!}
			</div>
			<div class="col-4 col-sm-4 col-md-4 col-lg-4">
				{!! Form::label('filas', 'Filas a mostrar:')!!}
				{!! Form::selectRange('filas', 1, 30, 5, array('class' => 'form-control', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
			</div>
			<div class="form-group">
				{!! Form::button('<i class="material-icons">search</i>Buscar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
				<button class="btn btn-info btn-sm" id='document-nuevo'><i class="material-icons">add</i>Nuevo</button>
			</div>
			{!! Form::close() !!}

			<div class="mt-2 p-2 rounded border border-info d-none" id='content-form-document'>
				<div id='divMensajeError{{ $entidad }}'></div>
				{!! Form::open([ 'route' => 'vehiculodocument.store' ,'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'form-document', 'enctype' => 'multipart/form-data']) !!}
				<div class="form-row col-12">
					<label class="my-1 mr-2" for="fecha">Fecha</label>
					<input type="date" name="fecha" class="form-control input-xs my-1 mr-sm-2" id='fecha'>
					<input type="hidden" name="_method" id='methodOculto'>
					{!! Form::label('tipo', 'Tipo:') !!}
					{!! Form::select('tipo',['SOAT'=>'SOAT', 'GPS' => 'GPS' , 'RTV' => 'RTV', 'POLIZA'=>'POLIZA','TARJ. PROP.'=>'TARJ. PROP.'] ,'', array('class' => 'form-control mr-4', 'id' => 'tipo')) !!}
					<input type="file" name="archivo" class="form-control input-xs my-1 mr-sm-2" id='archivo'>

					{!! Form::hidden('vehiculo_id',  $vehiculo_id, array('class' => 'form-control', 'id' => 'vehiculo_id')) !!}
					<input type="hidden" name="vehiculodocumentid" id='vehiculodocumentid'>
				</div>
				<div class="form-row col-12">
					{!! Form::label('precio', 'Precio:') !!}
					<input type="number" name="precio" class="form-control mr-4" id='precio'>
				</div>
				<div>
					<button class="btn btn-info btn-sm" id='document-enviar'>Enviar</button>
					<button class="btn btn-warning btn-sm" id='document-cancelar'> Cancelar</button>
				</div>
				{!! Form::close() !!}
			</div>

			<div class="card">
	            <div class="table-responsive" id="listado{{ $entidad }}">
				</div>
			</div>
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		 </div>


<script>
	$(document).ready(function () {
		configurarAnchoModal('600');
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
		document.getElementById('form-document').setAttribute('action','{{ URL::route("vehiculodocument.store") }}');
		//document.getElementById('form-document').setAttribute('method','POST');
		document.getElementById('methodOculto').value = 'POST';
		document.getElementById('content-form-document').classList.remove('d-none');
	});
	document.getElementById('document-cancelar').addEventListener('click',function(e){
		e.preventDefault();
		document.getElementById('content-form-document').classList.add('d-none');
		let formulario = document.getElementById('form-document')
		formulario.reset();
		formulario.getElementById('vehiculo_id').value ='';
		formulario.getElementById('vehiculodocumentid').value ='';
	});
	function editar_document(document_id,btn){
		const formulario = document.getElementById('form-document');
		formulario.setAttribute('action','{{ URL::route("vehiculodocument.store") }}' +'/'+ document_id);
		//formulario.setAttribute('method','PUT');
		document.getElementById('methodOculto').value = 'PUT';
		const fila = btn.parentElement.parentElement;
		formulario.querySelector('#fecha').value = fila.children[1].textContent;
		formulario.querySelector('#tipo').value = fila.children[2].textContent;
		formulario.querySelector('#precio').value = fila.children[3].textContent;
		formulario.querySelector('#vehiculodocumentid').value = Number(document_id);
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
					formulario.getElementById('vehiculo_id').value ='';
					formulario.getElementById('vehiculodocumentid').value ='';
				} else {
					mostrarErrores(respuesta, '#form-document', '{{ $entidad }}');

				}
			}
		});

	});





</script>
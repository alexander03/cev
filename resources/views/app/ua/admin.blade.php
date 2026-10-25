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

				<div class="col-2 col-sm-2 col-md-2 col-lg-2">
					{!! Form::label('descripcion', 'Descripción:') !!}
					{!! Form::text('descripcion', '', array('class' => 'form-control input-xs mr-2', 'id' => 'descripcion')) !!}
				</div>
				<div class="col-2 col-sm-2 col-md-2 col-lg-2">
					{!! Form::label('codigo', 'Código:') !!}
					{!! Form::text('codigo', '', array('class' => 'form-control input-xs mr-2', 'id' => 'codigo')) !!}
				</div>
				<div class="col-2 col-sm-2 col-md-2 col-lg-2">
					<label for="id-is-father">Es padre</label>
					<select id="id-is-father" name="is_father" class="form-control input-xs mr-2">
						<option value="">Seleccionar opción</option>
						<option value="SI">SI</option>
						<option value="NO">NO</option> 
					</select>
				</div>
				<div class="col-2 col-sm-2 col-md-2 col-lg-2">
					{!! Form::label('filas', 'Filas a mostrar:')!!}
					{!! Form::selectRange('filas', 1, 30, 20, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
				</div>
				<div class="form-group">
					{!! Form::button('<i class="material-icons">search</i>Buscar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
					{!! Form::button('<i class="material-icons">add</i>Nuevo', array('class' => 'btn btn-info btn-sm', 'id' => 'btnNuevo', 'onclick' => 'modal (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$titulo_registrar.'\', this);')) !!}
				</div>
				<section class="col-12 d-flex justify-content-center">
					<div class="form-group">
					    <a target="_blank" href="{{asset('files/formato-ua.xlsx')}}" class="ml-1 btn btn-sm btn-warning">
					        <i class="material-icons">cloud_download</i> Formato
					   </a>
					    
						<a href="#" class="ml-1 btn btn-sm btn-primary js-import-excel">
							<i class="material-icons">cloud_upload</i> Importar
						</a>
						<input class="js-import-excel-file" type="file" accept=".xls, .xlsx">
						<a href="{{ route('ua.excel.export') }}" class="btn btn-sm btn-info">
							<i class="material-icons">cloud_download</i> Exportar
						</a>
						<a href="{{ route('ua.excel.export2') }}" class="btn btn-sm btn-danger">
							<i class="material-icons">cloud_download</i> Exportar jerarquia
						</a>
					</div>
				</section>
				
				{!! Form::close() !!}
            	<div class="table-responsive" id="listado{{ $entidad }}">
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalError" tabindex="-1" role="dialog"aria-hidden="true">
	<div class="modal-dialog" role="document">
	  <div class="modal-content">
		<div class="modal-header">
		  <h4 class="modal-title">Error</h4>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		  </button>
		</div>
		<div class="modal-body">
			<section>
				<h3 class="text-danger text-center">Error al importar</h3>
				<p class="text-center text-secondary">
					No se pudo importar, el formato no coincide, 
					existen datos repetidos, códigos no existentes o 
					alguna ua padre no pertenece a esta concesionaria.
				</p>
				<p class="text-center text-secondary" id='MensajeError'></p>
				<div class="d-flex justify-content-center">
					<button class="btn btn-success" data-dismiss="modal">Aceptar</button>
				</div>
			</section>
		</div>
	  </div>
	</div>
</div>

<script>
	$(document).ready(function () {
		buscar('{{ $entidad }}');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="descripcion"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
		doImportExcel();
	});
</script>

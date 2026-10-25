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
					{!! Form::label('fechainicio', 'Fecha Inicio:') !!}
					{!! Form::date('fechainicio', date("Y-m-")."01", array('class' => 'form-control input-xs mr-2', 'id' => 'fechainicio')) !!}
				</div>
				<div class="col-2 col-sm-2 col-md-2 col-lg-2">
					{!! Form::label('fechafin', 'Fecha Fin:') !!}
					{!! Form::date('fechafin', date("Y-m-d"), array('class' => 'form-control input-xs mr-2', 'id' => 'fechafin')) !!}
				</div>
				<div class="col-2 col-sm-2 col-md-2 col-lg-2">
					{!! Form::label('placa', 'Placa:') !!}
					{!! Form::text('placa', '', array('class' => 'form-control input-xs mr-2', 'id' => 'placa')) !!}
				</div>
				<!--div class="col-2 col-sm-2 col-md-2 col-lg-2">
					{!! Form::label('localizacion', 'Localizacion:') !!}
					{!! Form::text('localizacion', '', array('class' => 'form-control input-xs mr-2', 'id' => 'localizacion')) !!}
				</div-->
				<div class="col-3 col-sm-3 col-md-3 col-lg-3">
					{!! Form::label('zona', 'Zona:') !!}
					{!! Form::select('zona', $cboZona, '', array('class' => 'form-control input-xs mr-2', 'id' => 'zona')) !!}
				</div>
				<div class="col-2 col-sm-2 col-md-2 col-lg-2">
					{!! Form::label('filas', 'Filas a mostrar:')!!}
					{!! Form::selectRange('filas', 1, 30, 20, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
				</div>
				<div class="form-group">
					{!! Form::button('<i class="material-icons">search</i>Buscar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
					<a href="#" class="ml-1 btn btn-sm btn-primary js-import-excel">
						<i class="material-icons">cloud_upload</i> Importar
					</a>
					<input class="js-import-excel-file" type="file" accept=".xls, .xlsx">
					{!! Form::button('<i class="material-icons">cloud_download</i> Exportar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnBuscar', 'onclick' => 'exportar(\''.$entidad.'\')','style'=>'display:none')) !!}
					{!! Form::button('<i class="material-icons">cloud_download</i> KM>90', array('class' => 'btn btn-success btn-sm', 'id' => 'btnBuscar', 'onclick' => 'exportar2(\''.$entidad.'\')','style'=>'display:none')) !!}
					{!! Form::button('<i class="material-icons">cloud_download</i> Detallado KM>90', array('class' => 'btn btn-success btn-sm', 'id' => 'btnBuscar', 'onclick' => 'exportar3(\''.$entidad.'\')','style'=>'display:none')) !!}
					{!! Form::button('<i class="material-icons">cloud_download</i> Exportar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnBuscar', 'onclick' => 'exportar4(\''.$entidad.'\')')) !!}
				</div>
				<section class="col-12 d-flex justify-content-center">
					<div class="form-group">
					    
						<!--a href="{{ route('ua.excel.export') }}" class="btn btn-sm btn-info">
							<i class="material-icons">cloud_download</i> Exportar
						</a-->
						
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
					No se pudo importar, el formato no coincide.
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
		doImportVelocidadExcel();
	});
	function exportar(entidad){
		window.open('velocidadvehiculo/exportar?fechainicio='+$("#fechainicio").val()+"&fechafin="+$("#fechafin").val()+"&localizacion="+$("#localizacion").val()+"&zona="+$("#zona").val(),'_blank');
	}
	function exportar2(entidad){
		window.open('velocidadvehiculo/exportar2?fechainicio='+$("#fechainicio").val()+"&fechafin="+$("#fechafin").val()+"&localizacion="+$("#localizacion").val()+"&zona="+$("#zona").val(),'_blank');
	}
	function exportar3(entidad){
		window.open('velocidadvehiculo/exportar3?fechainicio='+$("#fechainicio").val()+"&fechafin="+$("#fechafin").val()+"&localizacion="+$("#localizacion").val()+"&zona="+$("#zona").val(),'_blank');
	}
	function exportar4(entidad){
		window.open('velocidadvehiculo/exportar4?fechainicio='+$("#fechainicio").val()+"&fechafin="+$("#fechafin").val()+"&localizacion="+$("#localizacion").val()+"&zona="+$("#zona").val(),'_blank');
	}
</script>

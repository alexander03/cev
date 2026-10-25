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
				{!! Form::date('fechainicio', date("Y-m-d"), array('class' => 'form-control', 'id' => 'fechainicio')) !!}
			</div>
			<div class="col-2 col-sm-2 col-md-2 col-lg-2">
				{!! Form::label('fechafin', 'Fecha Fin:') !!}
				{!! Form::date('fechafin', date("Y-m-d"), array('class' => 'form-control', 'id' => 'fechafin')) !!}
			</div>
			<div class="col-2 col-sm-2 col-md-2 col-lg-2">
				{!! Form::label('placa', 'Placa:') !!}
				{!! Form::text('placa', '', array('class' => 'form-control', 'id' => 'placa')) !!}
			</div>
			<div class="col-2 col-sm-2 col-md-2 col-lg-2" hidden>
				{!! Form::label('filas', 'Filas a mostrar:')!!}
				{!! Form::selectRange('filas', 1, 30, 200000000, array('class' => 'form-control', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
			</div>
			<div class="form-group">
				{!! Form::button('<i class="material-icons">search</i>Buscar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
				<a href="#" onclick="window.open('{{ route('rastreovehiculo.exportar') }}?placa='+$('#placa').val()+'&fechainicio='+$('#fechainicio').val()+'&fechafin='+$('#fechafin').val(),'_blank');" class="btn btn-sm btn-info">
					<i class="material-icons">cloud_download</i> Exportar
				</a>
			</div>
			{!! Form::close() !!}
            <div class="table-responsive" id="listado{{ $entidad }}">
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function () {
		//buscar('{{ $entidad }}');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="descripcion"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
	});
</script>
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
				{!! Form::label('codigo', 'Código:') !!}
				{!! Form::text('codigo', '', array('class' => 'form-control', 'id' => 'codigo')) !!}
			</div>
			<div class="col-2 col-sm-2 col-md-2 col-lg-2">
				{!! Form::label('descripcion', 'Descripción:') !!}
				{!! Form::text('descripcion', '', array('class' => 'form-control', 'id' => 'descripcion')) !!}
			</div>
			<div class="col-2 col-sm-2 col-md-2 col-lg-2">
				{!! Form::label('placa', 'Placa:') !!}
				{!! Form::text('placa', '', array('class' => 'form-control', 'id' => 'placa')) !!}
			</div>
			<div class="col-2 col-sm-2 col-md-2 col-lg-2">
				{!! Form::label('contratista', 'Subcontratista:') !!}
				{!! Form::text('contratista', '', array('class' => 'form-control', 'id' => 'contratista')) !!}
			</div>
			<div class="col-2 col-sm-2 col-md-2 col-lg-2">
				{!! Form::label('estado', 'Estado:')!!}
				{!! Form::select('estado', $cboEstado, null, array('class' => 'form-control', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
			</div>
			<div class="col-2 col-sm-2 col-md-2 col-lg-2">
				{!! Form::label('filas', 'Filas a mostrar:')!!}
				{!! Form::selectRange('filas', 1, 30, 20, array('class' => 'form-control', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
			</div>
			<div class="col-2 col-sm-2 col-md-2 col-lg-2">
				{!! Form::label('tipo', 'Tipo Doc:') !!}
				{!! Form::select('tipo',['SOAT'=>'SOAT', 'GPS' => 'GPS' , 'RTV' => 'RTV', 'POLIZA'=>'POLIZA'] ,'', array('class' => 'form-control', 'id' => 'tipo')) !!}
			</div>
			<div class="form-group">
				{!! Form::button('<i class="material-icons">search</i>Buscar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
				@if($user->tipouser_id==1)
				{!! Form::button('<i class="material-icons">add</i>Nuevo', array('class' => 'btn btn-info btn-sm', 'id' => 'btnNuevo', 'onclick' => 'modal (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$titulo_registrar.'\', this);')) !!}
				@endif
				<a href="#" onclick="window.open('{{ route('equipo.exportar') }}','_blank');" class="btn btn-sm btn-primary p-2 pl-1 pr-1">
                    <i class="material-icons">cloud_download</i> Exportar
                </a>
                <a href="#" onclick="window.open('{{ route('equipo.exportarDoc') }}','_blank');" class="btn btn-sm btn-primary p-2 pl-1 pr-1">
                    <i class="material-icons">cloud_download</i> Exportar Docs.
                </a>
                <a href="#" onclick="window.open('{{ route('equipo.downloadMultiple') }}?tipo='+$('#tipo').val(),'_blank');" class="btn btn-sm btn-primary p-2 pl-1 pr-1">
                    <i class="material-icons">cloud_download</i> Descargar Docs.
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
		buscar('{{ $entidad }}');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="descripcion"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
	});
</script>
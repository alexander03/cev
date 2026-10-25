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
				{!! Form::label('fechainicial', 'Fecha Inicial:') !!}
				{!! Form::date('fechainicial', date("Y-m-d"), array('class' => 'form-control', 'id' => 'fechainicial')) !!}
			</div>
			<div class="col-2 col-sm-2 col-md-2 col-lg-2">
				{!! Form::label('fechafinal', 'Fecha Final:') !!}
				{!! Form::date('fechafinal', date("Y-m-d"), array('class' => 'form-control', 'id' => 'fechafinal')) !!}
			</div>
			<div class="col-2 col-sm-2 col-md-2 col-lg-2">
				{!! Form::label('accion', 'Accion:') !!}
				{!! Form::select('accion', $cboAccion, null, array('class' => 'form-control', 'id' => 'accion')) !!}
			</div>
			<div class="col-2 col-sm-2 col-md-2 col-lg-2">
				{!! Form::label('persona', 'Usuario:') !!}
				{!! Form::text('persona', '', array('class' => 'form-control', 'id' => 'persona')) !!}
			</div>
			<div class="col-2 col-sm-2 col-md-2 col-lg-2">
				{!! Form::label('tabla', 'Tabla:') !!}
				{!! Form::text('tabla', '', array('class' => 'form-control', 'id' => 'tabla')) !!}
			</div>
			<div class="col-2 col-sm-2 col-md-2 col-lg-2">
				{!! Form::label('filas', 'Filas a mostrar:')!!}
				{!! Form::selectRange('filas', 1, 30, 20, array('class' => 'form-control', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
			</div>
			<div class="form-group">
				{!! Form::button('<i class="material-icons">search</i>Buscar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
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
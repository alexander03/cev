{{-- TODO: CORREGIR los estilos del nav de la paginacion --}}
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
						<div class="col-8 col-sm-7 col-md-3 col-lg-3">
							{!! Form::label('filter', 'Nombre/Apellido:') !!}
							{!! Form::text('filter', '', array('class' => 'form-control input-xs', 'id' => 'filter')) !!}
						</div>
						<div class="col-8 col-sm-7 col-md-2 col-lg-2">
							{!! Form::label('dni', 'DNI:') !!}
							{!! Form::text('dni', '', array('class' => 'form-control input-xs', 'id' => 'dni')) !!}
						</div>
						<div class="col-8 col-sm-7 col-md-3 col-lg-3">
							{!! Form::label('empresa', 'Empresa:') !!}
							{!! Form::text('empresa', '', array('class' => 'form-control input-xs', 'id' => 'empresa')) !!}
						</div>
						<div class="col-8 col-sm-7 col-md-2 col-lg-2">
							{!! Form::label('ua', 'UA:') !!}
							{!! Form::text('ua', '', array('class' => 'form-control input-xs', 'id' => 'ua')) !!}
						</div>
						<div class="col-3 col-sm-2 col-md-1 col-lg-1">
							{!! Form::label('filas', 'Filas')!!}
							{!! Form::selectRange('filas', 1, 30, 20, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
						</div>
						<div class="col-12 col-sm-4 col-md-2 col-lg-2 d-flex justify-content-around align-items-center">
							{!! Form::button('<i class="material-icons">search</i>Buscar', array('class' => 'btn btn-success p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
							@if($user->tipouser_id==1)
							{!! Form::button('<i class="material-icons">add</i>Nuevo', array('class' => 'btn btn-info p-2 pl-1 pr-1', 'id' => 'btnNuevo', 'onclick' => 'modal (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$titulo_registrar.'\', this);')) !!}
							@endif
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
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="dni"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="empresa"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="ua"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
	});
</script>
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
			<div class="col-8 col-sm-9 col-md-3 col-lg-3">
				{!! Form::label('filter', 'Buscar por...') !!}
				{!! Form::text('filter', '', array('class' => 'form-control input-xs', 'id' => 'filter')) !!}
			</div>
			<div class="col-8 col-sm-9 col-md-2 col-lg-2">
				{!! Form::label('tipofecha', 'Tipo Fecha') !!}
				{!! Form::select('tipofecha',['fecharegistro'=>'Fecha Registro','fecha'=>'Fecha Diag.'], '', array('class' => 'form-control input-xs', 'id' => 'tipofecha')) !!}
			</div>
			<div colspan='2' class="col-8 col-sm-9 col-md-3 col-lg-3">
				<tr>{!! Form::label('filter', 'Fecha Inicio') !!}
					<button style="float:right;" class="btn btn-dark p-1" onclick="document.getElementById('fechainicio').value=''" id="btn_remove_fecha_registro_inicial">
						<span class="material-icons outlined">close</span>
					</button>
				</tr>
				<input class="form-control input-xs" id="fechainicio" name="fechainicio" type="date" value="<?php  echo date('Y');echo'-';echo date('m');echo'-';echo'01'?>">
			</div>
			<div colspan='2' class="col-8 col-sm-9 col-md-3 col-lg-3">
				<tr>{!! Form::label('filter', 'Fecha Fin') !!}
					<button style="float:right;" class="btn btn-dark p-1" onclick="document.getElementById('fechafin').value=''" id="btn_remove_fecha_registro_inicial">
						<span class="material-icons outlined">close</span>
					</button>
				</tr>
				<input class="form-control input-xs" id="fechafin" name="fechafin" type="date" value="<?php echo date("Y-m-d");?>">
			</div>
			<div class="col-2 col-sm-2 col-md-2 col-lg-2">
				{!! Form::label('docmat', 'DOCMAT') !!}
				{!! Form::text('docmat', '', array('class' => 'form-control input-xs', 'id' => 'docmat')) !!}
			</div>
			<div class="col-3 col-sm-2 col-md-2 col-lg-1">
				{!! Form::label('filas', 'Filas')!!}
				{!! Form::selectRange('filas', 1, 30, 20, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
			</div>
			<div class="form-group">
				{!! Form::button('<i class="material-icons">search</i>Buscar', array('class' => 'btn btn-success p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
				{!! Form::button('<i class="material-icons">add</i>Registrar', array('class' => 'btn btn-info p-2 pl-1 pr-1', 'id' => 'btnNuevo', 'onclick' => 'modal (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$tituloRegistrar.'\', this);')) !!}
				{!! Form::button('<i class="material-icons">cloud_download</i> Exportar ', array('class' => 'btn btn-warning p-2 pl-1 pr-1', 'id' => 'btnReporte2', 'onclick' => 'excel()')) !!}
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
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="docmat"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
	});
	function excel(){
		window.open('{{ route('diagnostico.excel')  }}?fechainicio='+$('#fechainicio').val()+'&fechafin='+$('#fechafin').val()+"&tipofecha="+$("#tipofecha").val(),'_blank');
	}
	function excelExportarTodos(){
		window.open('{{ route('regmanveh.excelTodos.export')  }}?fechainicio='+$('#fechainicio').val()+'&fechafin='+$('#fechafin').val(),'_blank');
	}
</script>
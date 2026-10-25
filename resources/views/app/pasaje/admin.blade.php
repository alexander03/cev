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
						<div class="col-2 col-sm-2 col-md-2 col-lg-2" hidden>
							{!! Form::label('tipofecha', 'Tipo Fecha:') !!}
							{!! Form::select('tipofecha', $cboTipoFecha,null, array('class' => 'form-control input-xs', 'id' => 'tipofecha')) !!}
						</div>
						<div class="col-2 col-sm-2 col-md-2 col-lg-2">
							{!! Form::label('fechainicio', 'Fecha Inicio:') !!}
							{!! Form::date('fechainicio', date("Y-m-")."01", array('class' => 'form-control input-xs', 'id' => 'fechainicio')) !!}
						</div>
						<div class="col-2 col-sm-2 col-md-2 col-lg-2">
							{!! Form::label('fechafin', 'Fecha Fin:') !!}
							{!! Form::date('fechafin', date("Y-m-d",strtotime("-1 day",strtotime("+1 month",strtotime(date("Y-m-")."01")))), array('class' => 'form-control input-xs', 'id' => 'fechafin')) !!}
						</div>
						<div class="col-2 col-sm-2 col-md-2 col-lg-2">
							{!! Form::label('reserva', 'Cod. Reserva:') !!}
							{!! Form::text('reserva', null, array('class' => 'form-control input-xs', 'id' => 'reserva')) !!}
						</div>
						<div class="col-2 col-sm-2 col-md-2 col-lg-2">
							{!! Form::label('origen', 'Origen:') !!}
							{!! Form::text('origen',null, array('class' => 'form-control input-xs', 'id' => 'origen')) !!}
						</div>
						<div class="col-2 col-sm-2 col-md-2 col-lg-2">
							{!! Form::label('destino', 'Destino:') !!}
							{!! Form::text('destino',null, array('class' => 'form-control input-xs', 'id' => 'destino')) !!}
						</div>
						<div class="col-2 col-sm-2 col-md-2 col-lg-2">
							{!! Form::label('personal', 'Persona:') !!}
							{!! Form::text('personal',null, array('class' => 'form-control input-xs', 'id' => 'personal')) !!}
						</div>
						<div class="col-2 col-sm-2 col-md-2 col-lg-2">
							{!! Form::label('empresa', 'Empresa:') !!}
							{!! Form::text('empresa',null, array('class' => 'form-control input-xs', 'id' => 'empresa')) !!}
						</div>
						<div class="col-2 col-sm-2 col-md-2 col-lg-2">
							{!! Form::label('tipo', 'Tipo:') !!}
							{!! Form::select('tipo',$cboTipo,null, array('class' => 'form-control input-xs', 'id' => 'tipo', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
						</div>
						<div class="col-2 col-sm-2 col-md-2 col-lg-2">
							{!! Form::label('concesionaria', 'Concesionaria:') !!}
							{!! Form::select('concesionaria',$cboConcesionaria,null, array('class' => 'form-control input-xs', 'id' => 'concesionaria', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
						</div>
						<div class="col-1 col-sm-1 col-md-1 col-lg-1">
							{!! Form::label('filas', 'Filas')!!}
							{!! Form::selectRange('filas', 1, 30, 20, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
						</div>
						<div class="col-8 col-sm-4 col-md-4 col-lg-4 d-flex justify-content-around align-items-center">
							{!! Form::button('<i class="material-icons">search</i>Buscar', array('class' => 'btn btn-success p-2 pl-1 pr-1', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
							{!! Form::button('<i class="material-icons">add</i>Nuevo', array('class' => 'btn btn-info p-2 pl-1 pr-1', 'id' => 'btnNuevo', 'onclick' => 'modal (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$titulo_registrar.'\', this);')) !!}
							<a href="#" onclick="excel();" class="btn btn-sm btn-warning p-2 pl-1 pr-1">
								<i class="material-icons">cloud_download</i> Exportar Lista
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
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="nombre"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
	});
	function excel(){
    window.open('pasaje/exportar?fechainicio='+$("#fechainicio").val()+"&fechafin="+$("#fechafin").val()+"&tipofecha="+$("#tipofecha").val()+"&reserva="+$("#reserva").val()+"&origen="+$("#origen").val()+"&destino="+$("#destino").val()+"&personal="+$("#personal").val()+"&empresa="+$(IDFORMBUSQUEDA+'{{ $entidad }} #empresa').val()+"&concesionaria="+$(IDFORMBUSQUEDA+'{{ $entidad }} #concesionaria').val(),'_blank');
	}
	function copyToClipboard(vtexto) {

      // Create a "hidden" input
      var aux = document.createElement("input");

      // Assign it the value of the specified element
      aux.setAttribute("value", vtexto);

      // Append it to the body
      document.body.appendChild(aux);

      // Highlight its content
      aux.select();

      // Copy the highlighted text
      document.execCommand("copy");

      // Remove it from the body
      document.body.removeChild(aux);

  }
</script>
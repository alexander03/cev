@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
<table id="example10" class="table table-sm text-center table-striped  table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
			<th class="text-nowrap" @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif class="text-center">{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
			@if($value->situacion=="P")
		<tr style="color:red">		
			@else
		<tr>
			@endif
			<td>{{ $contador }}</td>
			<td>{{ $value->numero }}</td>
			<td>{{ date("d/m/Y",strtotime($value->fecharegistro)) }}</td>
			@if($value->fecha!="")
				<td>{{ date("d/m/Y",strtotime($value->fecha)) }}</td>
			@else
				<td> - </td>
			@endif
			@if($value->taller_id>0)
				<td>{{ $value->taller->razonsocial }}</td>
			@else
				<td> - </td>
			@endif
			@if($value->vehiculo_id>0)
				<td>{{ $value->vehiculo->modelo }}</td>
			@else
				<td>{{ $value->equipo->modelo }}</td>
			@endif
			<td>{{ (!is_null($value->vehiculo)?$value->vehiculo->placa:($value->equipo->descripcion.' '.$value->equipo->placa)) }}</td>
			@php
			$check="";
			if(!is_null($value->checklistvehicular)){
				$check = date("d/m/Y",strtotime($value->checklistvehicular->fecha_registro))." ".substr($value->checklistvehicular->hora_inicio,0,5)." - ".substr($value->checklistvehicular->hora_fin,0,5)." | ".$value->checklistvehicular->conductor->nombres." ".$value->checklistvehicular->conductor->apellidos." | ".$value->checklistvehicular->observaciones." ".$value->checklistvehicular->incidentes;
			}
			@endphp
			<td>{{ $check }}</td>
			<td>{{ $value->recorrido }}</td>
			<td>{{ $value->revision }}</td>
			<td>{{ $value->diagnostico }}</td>
			<td>{{ $value->recomendacion }}</td>
			<td>{{ $value->cierre }}</td>
			<td>{{ $value->tipomantenimiento=="1"?"Preventivo":"Correctivo" }}</td>
			<td>{!! Form::button('<div class="fa fa-check"></div>', array('onclick' => 'seleccionarDiagnostico (\''.$value->id.'\',\''.$value->numero.'\');', 'class' => 'btn btn-sm btn-outline-success')) !!}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script>
    configurarAnchoModal('1600');
    $("#example10").DataTable({"search": {
    		"search": $(".js-vehiculo-desc").html(),

    	}
  	});
  	$(".dataTables_filter, .dataTables_info").css("display","none");
</script>
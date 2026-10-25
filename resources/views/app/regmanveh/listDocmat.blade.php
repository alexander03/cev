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
		<tr>
			<td>{{ $contador }}</td>
			<td>{{ $value->numero.'('.($value->motivoveh!=""?$value->motivoveh:'-').')' }}</td>
			<td>{{ date("d/m/Y",strtotime($value->fecha)) }}</td>
			@if($value->fechanecesidad!="")
				<td>{{ date("d/m/Y",strtotime($value->fechanecesidad)) }}</td>
			@else
				<td> - </td>
			@endif
			<td>{{ $value->codigoua }}</td>
			<td>{{ $value->placa }}</td>
			<td>{{ $value->tipomantenimiento }}</td>
			<td>{{ $value->km }}</td>
			<td>{!! Form::button('<div class="fa fa-check"></div>', array('onclick' => 'seleccionarDocmat (\''.$value->id.'\',\''.$value->numero.'('.($value->motivoveh!=""?$value->motivoveh:'-').')\',\''.$value->fecha.'\','.$value->taller_id.',\''.$value->tipomantenimiento.'\',\''.$value->km.'\',\''.$tipo.'\');', 'class' => 'btn btn-sm btn-outline-success')) !!}</td>
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
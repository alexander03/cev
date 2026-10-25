<?php
use App\Checklistvehicular;
?>
@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion !!}
<table id="example1" class="table table-striped table-hover mt-2">

	<thead>
		<tr class="text-center ">
			@foreach($cabecera as $key => $value)
				<th @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody class="text-center ">
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		@php
		$check = Checklistvehicular::where('vehiculo_id','=',$value->vehiculo_id)
                            ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_fin))'),'>=',DB::raw('UNIX_TIMESTAMP(\''.$value->fecha.'\')'))
                            ->where(DB::raw('UNIX_TIMESTAMP(concat(fecha_registro,\' \',hora_inicio))'),'<=',DB::raw('UNIX_TIMESTAMP(\''.$value->fecha.'\')'))
                            ->first();
        if(!is_null($check)){
            $conductor = $check->conductor->nombres." ".$check->conductor->apellidos;
        }else{
            $conductor = '-';
        }
		@endphp
		<tr class="">
			<td>{{ $contador }}</td>
			<td>{{ date("d/m/Y H:i:s",strtotime($value-> fecha)) }}</td>
			<td>{{ $value->	vehiculo->placa }}</td>
			<td>{{ $value->	velocidad }}</td>
			<td>{{ $value->	rumbo }}</td>
			<td>{{ $value->	latitud }}</td>
			<td>{{ $value->	longitud }}</td>
			<!--td>{{ $value->	localizacion }}</td-->
			<td>{{ $conductor }}</td>
			<td>{{ $value->	zona->nombre }}</td>
			<td>{{ $value->	calle }}</td>
			<td><a class='btn btn-success btn-link btn-sm' onclick="window.open('{{$value->mapa}}','_blank')" style="cursor: pointer;"><i class="material-icons">map</i></a></td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif
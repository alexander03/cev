<?php
use App\Detallecontrol;
use App\Conductor;
use App\Persona;
?>
@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif class="text-center">{!! $value['valor'] !!}</th>
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
			<td>{{ $value->tipo }}</td>
			<td>{{ $value->persona }}</td>
			<td>{{ $value->dni }}</td>
			<td>{{ !is_null($value->ua)?($value->ua->codigo.' '.$value->ua->descripcion):'-' }}</td>
			<td>{{ $value->origen->nombre }}</td>
			<td>{{ date("d/m/Y",strtotime($value->fechaorigen)) }}</td>
			<td>{{ date("H:i",strtotime($value->horaorigen)) }}</td>
			<td>{{ $value->destino->nombre }}</td>
			<!--td>{{ date("d/m/Y",strtotime($value->fechadestino)) }}</td-->
			<td>{{ date("H:i",strtotime($value->horadestino)) }}</td>
			<td>{{ $value->numvuelo }}</td>
			<td>{{ $value->reserva }}</td>
			<td>{{ $value->aerolinea }}</td>
			<td>{!! $value->empresa !!}</td>
			<td>{!! $value->concesionaria->abreviatura !!}</td>
			<td>{!! Form::button('<i class="material-icons">edit</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-primary btn-link btn-sm','rel'=>'tooltip','title'=>'Editar')) !!}</td>
			<td>{!! Form::button('<i class="material-icons">close</i>', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-danger btn-link btn-sm','rel'=>'tooltip','title'=>'Eliminar')) !!}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
{!! $paginacion !!}
@endif
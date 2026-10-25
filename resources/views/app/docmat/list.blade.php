<?php
use App\Detallecontrol;
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
			<td>{{ $value->numero }}</td>
			<td>{{ date("d/m/Y",strtotime($value->fecha)) }}</td>
			@if($value->fechanecesidad!="")
				<td>{{ date("d/m/Y",strtotime($value->fechanecesidad)) }}</td>
			@else
				<td> - </td>
			@endif
			<td>{{ $value->codigo }}</td>
			@if(!is_null($value->vehiculo))
				<td>{{ $value->vehiculo->ua->codigo }}</td>
				<td>{{ $value->vehiculo->placa }}</td>
			@else
				<td>{{ $value->equipo->ua->codigo }}</td>
				<td>{{ $value->equipo->placa }}</td>
			@endif
			<td>{{ $value->km }}</td>
			<td>{{ $value->tipomantenimiento }}</td>
			<td>{{ $value->taller->razonsocial }}</td>
			<td>{!! Form::button('<i class="material-icons">edit</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-primary btn-link btn-sm','rel'=>'tooltip','title'=>'Editar')) !!}</td>
			<td>
				<a href="{{ route('docmat.pdf.export') . '?id=' . $value->id }}" target="_blank" class="btn btn-sm btn-secondary" title="Exportar">
						<i class="material-icons">cloud_download</i>
				</a>
			</td>
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
@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th class="text-center" @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
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
			<td>{{ date("d/m/Y",strtotime($value->fecha)) }}</td>
			<td>{{ $value->equipo->ua->codigo }}</td>
			<td>{{ $value->equipo->descripcion }}</td>
			<td>{{ $value->equipo->contratista->razonsocial }}</td>
			<td>
				@if($value->ua && $value->hora_total>0)
					{{ $value->ua->codigo.' '.$value->ua->descripcion }}
				@endif
			</td>
			<td>
		{{--	{{round((strtotime($value->hora_fin) - strtotime($value->hora_inicio))/3600,2)}} --}}	
				{{ $value->hora_total }}
			</td>
			<td>
				@if($value->tipohora_id)
					{{ $value->tipohora->codigo.' '.$value->tipohora->descripcion }}
				@else
				    {{ ($value->hora_total>0?'HORA TRABAJADA':'-') }}
				@endif
			</td>
			<td>
			    {{ $value->hora_parada }}
			</td>
			<td>
				@if($value->turno)
					Diurno
				@else
					Nocturno
				@endif

			</td>
			<td>{{ $value->horometro_inicial }}</td>
			<td>{{ $value->horometro_final }}</td>
			<td>{{ $value->observaciones }}</td>
			<td>{!! Form::button('<i class="material-icons">list</i>', array('onclick' => 'modal (\''.URL::route($ruta["detail"], array('id'=>$value->id, 'listar'=>'SI')).'\', \'Detalle\', this);', 'class' => 'btn btn-info btn-link btn-sm','rel'=>'tooltip','title'=>'Detalle')) !!}</td>
			<td>{!! Form::button('<i class="material-icons">edit</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-primary btn-link btn-sm','rel'=>'tooltip','title'=>'Editar')) !!}</td>
			<td>{!! Form::button('<i class="material-icons">close</i>', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-danger btn-link btn-sm','rel'=>'tooltip','title'=>'Eliminar')) !!}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif

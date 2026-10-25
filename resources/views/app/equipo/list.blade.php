@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th class="text-nowrap" @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		@if($value->estado=="Desmovilizado")
		<tr style="color:red">
		@else
		<tr>
		@endif
			<td>{{ $contador }}</td>
			<td>{!! Form::button('<i class="material-icons">info</i>', array('onclick' => 'modal (\''.URL::route('equipodocument.index', array('equipo_id' =>$value->id, 'listar'=>'NO')).'\',  \'Registros: Documentos de equipo\' , this);', 'class' => 'btn btn-info btn-link btn-sm','rel'=>'tooltip','title'=>'Registros')) !!}</td>
			@if(auth()->user()->tipouser_id!="19")
			<td>{!! Form::button('<i class="material-icons">edit</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-primary btn-link btn-sm','rel'=>'tooltip','title'=>'Editar')) !!}</td>
			@endif
			<td>{{ $value->ua->codigo }}</td>
			<td>{{ $value->descripcion }}</td>
			<td>{{ $value->modelo }}</td>
			<td>
				@if($value->marca)
					{{ $value->marca->descripcion }}
				@endif
			</td>
			<td>{{ $value->anio }}</td>
			<td>{{ $value->placa }}</td>
			<td>{{ $value->capacidad_carga }}</td>
			<td>
				@if($value->contratista)
					{{ $value->contratista->razonsocial }}
				@endif
			</td>
			<td>
				@if($value->area)
					{{ $value->area->descripcion }}
				@endif
			</td>
			<td>{{ $value->horas_min }}</td>
			<td>{{ $value->precio }}</td>
			<td>
				@if(!$value->moneda)
					Sol S/
				@else
					Dolar $
				@endif
			</td>
			<td>{{ $value->unidad->descripcion }}</td>
			<td>{{ $value->estado }}</td>
			@if(auth()->user()->tipouser_id!="19")
			<td>{!! Form::button('<i class="material-icons">close</i>', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-danger btn-link btn-sm','rel'=>'tooltip','title'=>'Eliminar')) !!}</td>
			@endif
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif
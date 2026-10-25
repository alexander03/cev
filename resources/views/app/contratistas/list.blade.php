@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion !!}
<table id="example1" class="table table-striped table-hover mt-2">

	<thead>
		<tr class="text-center contratista-table__row">
			@foreach($cabecera as $key => $value)
				<th class="text-nowrap" @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif class="text-center">{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody class="text-center ua-table__body">
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<tr class="contratista-table__row">
			<td>{{ $contador }}</td>
			<td>{!! Form::button('<i class="material-icons">edit</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-primary btn-link btn-sm','rel'=>'tooltip','title'=>'Editar')) !!}
			{!! Form::button('<i class="material-icons">close</i>', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-danger btn-link btn-sm','rel'=>'tooltip','title'=>'Eliminar')) !!}</td>
			<td>{{ $value->ruc }}</td>
			<td>{{ $value->razonsocial }}</td>
			<td>{{ $value->propietario }}</td>
			<td>{{ $value->email }}</td>
			<td>{{ $value->telefono }}</td>
			<td>{{ $value->propietario2 }}</td>
			<td>{{ $value->email2 }}</td>
			<td>{{ $value->telefono2 }}</td>
			<td>{{ $value->propietario3 }}</td>
			<td>{{ $value->email3 }}</td>
			<td>{{ $value->telefono3 }}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif
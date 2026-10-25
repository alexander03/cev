@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion !!}
<table id="example1" class="table table-striped table-hover mt-2">

	<thead>
		<tr class="text-center">
			@foreach($cabecera as $key => $value)
				<th class="text-nowrap" @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody class="text-center">
		<?php
		$contador = $inicio + 1;
        ?>
		@foreach ($lista as $key => $value)
		<tr>
		    <td class="text-nowrap">{{ $contador }}</td>
		    @if($usuario->tipouser_id==1 || $usuario->tipouser_id==13)
		    	<td class="text-nowrap">{!! Form::button('<i class="material-icons">edit</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-primary btn-link btn-sm','rel'=>'tooltip','title'=>'Editar')) !!}</td>
				<td class="text-nowrap">{!! Form::button('<i class="material-icons">close</i>', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-danger btn-link btn-sm','rel'=>'tooltip','title'=>'Eliminar')) !!}</td>
			@else
				<td> - </td>
				<td> - </td>
			@endif
			<td class="text-nowrap">{{ date("d/m/Y",strtotime($value -> fecha_abastecimiento)) }}</td>
			<td class="text-nowrap">{{ $value -> grifo -> descripcion }}</td>
			<td class="text-nowrap">{{ isset($value -> tipoCombustible) ? $value -> tipoCombustible -> descripcion:'-' }}</td> 
			<td class="text-nowrap">{{ isset($value -> usuario) ? $value -> usuario -> name : '' }}</td>
            <td class="text-nowrap">{{ isset($value -> usuario) ? (isset($value -> usuario -> conductor) ? $value -> usuario -> conductor -> dni : 'SIN DNI') : '-'}}</td>
            <td class="text-nowrap">{{ $value -> ua -> codigo }}</td>
			<td class="text-nowrap">{{ $value -> ua -> descripcion }}</td>
			<td class="text-nowrap">@if(!($value -> equipo == null && $value -> vehiculo == null)) {{ isset($value -> equipo) ? $value -> equipo -> descripcion : 'VEHÍCULO' }}@else - @endif</td>
			<td class="text-nowrap">@if(!($value -> equipo == null && $value -> vehiculo == null)) {{ isset($value -> equipo) ? $value -> equipo -> marca -> descripcion : $value -> vehiculo -> marca -> descripcion }}@else - @endif</td>
			<td class="text-nowrap">@if(!($value -> equipo == null && $value -> vehiculo == null)) {{ isset($value -> equipo) ? ( isset($value -> equipo -> modelo) ? $value -> equipo -> modelo : 'No Asignado' ) : ( isset($value -> vehiculo -> modelo) ? $value -> vehiculo -> modelo : 'No Asignado' ) }}@else - @endif</td>
			<td class="text-nowrap">@if(!($value -> equipo == null && $value -> vehiculo == null)) {{ isset($value -> equipo) ? ( isset($value -> equipo -> placa) ? $value -> equipo -> placa : 'No Asignado' ) : ( isset($value -> vehiculo -> placa) ? $value -> vehiculo -> placa : 'No Asignado' ) }}@else - @endif</td>
			<td class="text-nowrap">@if(!($value -> equipo == null && $value -> vehiculo == null)) {{ isset($value -> equipo) ? $value -> equipo -> contratista -> razonsocial : $value -> vehiculo -> contratista -> razonsocial }}@else {{ (!is_null($value->contratista)?$value->contratista->razonsocial:'-') }} @endif</td>
			<td class="text-nowrap">{{ $value -> qtdgl }}</td>
			<td class="text-nowrap">{{ $value -> qtdl }}</td>
			<td class="text-nowrap">{{ $value -> km }}</td>
			<!--td class="text-nowrap">{{ $value -> abastecimiento_dia }}</td-->
			<td class="text-nowrap">{{ $value -> motivo }}</td>
			<td class="text-nowrap">{{ $value -> comprobante }}</td>
			<td class="text-nowrap">{{ $value -> numero_comprobante }}</td>
			<td class="text-nowrap">{{ $value -> hora_inicio }}</td>
			<td class="text-nowrap">{{ $value -> hora_fin }}</td>
			<td class="text-nowrap">{{ $value -> abastecimiento -> descripcion }}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif
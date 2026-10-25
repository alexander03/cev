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
			<td>{{ $value->apellidos }} {{$value->nombres}}</td>
			<td>{{$value->dni}}</td>
			<td>{{$value->categoria==""?'-':$value->categoria}}</td>
			<td>{{$value->licencia==""?'-':$value->licencia}}</td>
			@if(strtotime($value->fechavencimiento)<strtotime('now') && $value->tipo=="Conductor")
				<td style="color:red;font-weight: bold;">{{ $value->fechavencimiento=="0000-00-00"?'-':date('d/m/Y', strtotime($value->fechavencimiento))}}</td>
			@else
				<td>{{ $value->fechavencimiento=="0000-00-00"?'-':date('d/m/Y', strtotime($value->fechavencimiento)) }}</td>
			@endif
			<td>{{$value->contratista_razonsocial}}</td>
			<td>{{$value->tipo}}</td>
			<td>{!! Form::button('<i class="material-icons">edit</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-primary btn-link btn-sm','rel'=>'tooltip','title'=>'Editar')) !!}</td>

			<td>{!! Form::button('<i class="material-icons">assignment</i>', array('onclick' => 'modal (\''.URL::route($ruta["documentos_index"], array('conductor_id' => $value->id, 'listar'=>'SI')).'\', \''.$titulo_documentos.'\', this);', 'class' => 'btn btn-primary btn-link btn-sm','rel'=>'tooltip','title'=>'Documentos')) !!}</td>

			@if ($value->conductor_estado == true)
			<td>{!! Form::button('<i class="material-icons">close</i>', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-danger btn-link btn-sm','rel'=>'tooltip','title'=>'Eliminar')) !!}</td>
			@else
			<td>{!! Form::button('<i class="material-icons">done</i>', array('onclick' => 'modal (\''.URL::route($ruta["activar"], array($value->id, 'SI')).'\', \''.$titulo_activar.'\', this);', 'class' => 'btn btn-success btn-link btn-sm','rel'=>'tooltip','title'=>'Activar')) !!}</td>
			@endif

		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif
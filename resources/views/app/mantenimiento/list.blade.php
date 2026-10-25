@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif class="text-center" style="@if($key>4) color:blue;font-weight:bold @endif">{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
		$contador = $inicio + 1;$color="";
		?>
		@foreach ($lista as $key => $value)
		<tr style="">
			<td>{{ $contador }}</td>
			<td>{{ $value->tipo }}</td>
			<td>{{ date('d/m/Y', strtotime($value->fechaentrada)) }}</td>
			@if ($value->equipo_descripcion == null)
				<td>{{ $value->vehiculo_modelo." / ".$value->vehiculo_placa }}</td>
			@else
				<td>{{ $value->equipo_descripcion." / ".$value->equipo_placa }}</td>
			@endif
			<td>{{ $value->ordencompra }}</td>
			@if($value->fechachecklist!="")
				<td style="color: blue">{{ date('d/m/Y', strtotime($value->fechachecklist)) }}</td>
			@else
				<td style="color: blue"> - </td>
			@endif
			<td style="color: blue">{{ $value->observaciones }}</td>
			<td style="color: blue">{{ $value->incidentes }}</td>
			@if(auth()->user()->tipouser_id!="19")
			<td style="color: blue">{!! Form::button('<i class="material-icons">edit</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI','tipo'=>$value->tipo)).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-primary btn-link btn-sm','rel'=>'tooltip','title'=>'Editar')) !!}</td>
			@endif
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif
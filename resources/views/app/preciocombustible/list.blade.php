@php
use App\Detallepreciocombustible;
@endphp
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
		$c=0;
		?>
		@foreach ($lista as $key => $value)
		@php
			$detalle = "";
			$data = Detallepreciocombustible::where('preciocombustible_id','=',$value->id)->get();
			foreach($data as $k => $v){
				$detalle .= $v->tipocombustible->descripcion." => ".$v->precio.'<br />';
			}
		@endphp
		<tr>
			<td>{{ $contador }}</td>
			<td>{{ $value->grifo->descripcion }}</td>
			<td>{{ date("d/m/Y",strtotime($value->fechainicio)) }}</td>
			<td>{{ date("d/m/Y",strtotime($value->fechafin)) }}</td>
			<td>@php echo $detalle; @endphp</td>
			<td>{!! Form::button('<i class="material-icons">edit</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-primary btn-link btn-sm','rel'=>'tooltip','title'=>'Editar')) !!}</td>
			<td>{!! Form::button('<i class="material-icons">close</i>', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-danger btn-link btn-sm','rel'=>'tooltip','title'=>'Eliminar')) !!}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		$c++;
		?>
		@endforeach
	</tbody>
</table>
@endif
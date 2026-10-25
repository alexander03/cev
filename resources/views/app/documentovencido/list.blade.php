<?php
use App\Vehiculo;
use App\Equipo;
use App\Vehiculodocument;
use App\Equipodocument;
?>
@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		    <?php
		    if($value->tipo2=="vehiculo"){
		    	$vehiculo = Vehiculo::find($value->vehiculo_id);
		    	$carroceria = $vehiculo->carroceria->descripcion;
		    	$document = Vehiculodocument::where('fecha','=',$value->fecha)->where('tipo','like',$value->tipo)->where('vehiculo_id','=',$value->vehiculo_id)->first();
		    }else{
		    	$vehiculo = Equipo::find($value->vehiculo_id);
		    	$carroceria = $vehiculo->descripcion;
		    	$document = Equipodocument::where('fecha','=',$value->fecha)->where('tipo','like',$value->tipo)->where('equipo_id','=',$value->vehiculo_id)->first();
		    }
		    ?>
		<tr>
			<td>{{ $contador }}</td>
			<td>{{ $vehiculo->placa }}</td>
			<td>{{ $vehiculo->modelo }}</td>
			<td>{{ $carroceria }}</td>
			<td>{{ date("d/m/Y",strtotime($value->fecha)) }}</td>
			<td>{{ $value->tipo }}</td>
			<td>{{ $value->concesionaria }}</td>
			<td>{{ $document->comentario.(!is_null($document->user)?(" (".$document->user->name.")"):'') }}</td>
			<td>{!! Form::button('<i class="material-icons">edit</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($document->id, 'listar'=>'SI','tipo'=>$value->tipo2)).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-info btn-link btn-sm','rel'=>'tooltip','title'=>'Comentario')) !!}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif
<?php
use App\Checklistvehicular;
use App\AbastecimientoCombustible;
use App\RegManVeh;
use App\RegRepVeh;
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
		    $value->horometro_rec = ($value->horometroactual - $value->horometroinicial);
		    $value->save();
		    $recorrido = $value->horometro_rec;
		    if( $recorrido < $value->horometraje2->limite_inf ){
		        $color="";
		        $background="";
		    }elseif( $recorrido < $value->horometraje2->limite_sup ){
		        $color="";
		        $background="color:blue";
		    }else{
		        $background="color:red";
		        $color="";
		    }
		    
		    ?>
		<tr class="{{ $color }}" style="{{ $background }}">
			<td>{{ $contador }}</td>
			<td>{{ $value->horometraje2->descripcion }}</td>
			<td>{{ $value->descripcion }}</td>
			<td>{{ $value->modelo }}</td>
			<td>{{ $value->placa }}</td>
			<td>{{ $value->horometroinicial }}</td>
			<td>{{ round($recorrido,2) }}</td>
			<td>{{ number_format($value->horometroactual,2,'.','') }}</td>
			<td>{{ number_format($value->horometraje2->limite_sup -  $recorrido,2,'.','') }}</td>
			@if( $recorrido < $value->horometraje2->limite_inf )
				<td > Normal </td>
			@elseif( $recorrido < $value->horometraje2->limite_sup )
				<td class=""> Observación </td>
			@else
				<td class=""> Desgastado </td>
				<?php 
				if($value->estadorecorrido==''){
					$value->estadorecorrido='NO EJECUTADO';
					$value->save();
				}
				?>
			@endif
			<td>{{ $value->comentario }}</td>
			@if($value->estadorecorrido=="EJECUTADO")
			<td style="color:green;font-weight: bold;">{{ $value->estadorecorrido }}</td>
			@else
			<td>{{ $value->estadorecorrido }}</td>
			<td>{{ $value->concesionaria->abreviatura }}</td>
			@endif
			@if(auth()->user()->tipouser_id!="19")
			<td>{!! Form::button('<i class="material-icons">edit</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit3"], array($value->id, 'listar'=>'SI')).'\', \'Comentario\', this);', 'class' => 'btn btn-info btn-link btn-sm','rel'=>'tooltip','title'=>'Comentario')) !!}</td>
			<td>{!! Form::button('<i class="material-icons">backup</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit4"], array($value->id, 'listar'=>'SI')).'\', \'Estado Mant.\', this);', 'class' => 'btn btn-danger btn-link btn-sm','rel'=>'tooltip','title'=>'Estado Mant.')) !!}</td>
			<td>{!! Form::button('<i class="material-icons">settings</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-primary btn-link btn-sm','rel'=>'tooltip','title'=>'Editar')) !!}</td>
			<td>{!! Form::button('<i class="material-icons">speed</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit2"], array($value->id, 'listar'=>'SI')).'\', \'Km Actual\', this);', 'class' => 'btn btn-success btn-link btn-sm','rel'=>'tooltip','title'=>'Editar Horometro Actual')) !!}</td>
			@endif
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif
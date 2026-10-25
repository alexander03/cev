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
		    $value->kilometraje_rec = $value->kilometraje_act - $value->kilometraje_inicial;
		    $value->save();
		    if( $value->kilometraje_rec < $value->kilometraje2->limite_inf ){
		        $color="";
		        $background="";
		    }elseif( $value->kilometraje_rec < $value->kilometraje2->limite_sup ){
		        $color="";
		        $background="color:blue";
		    }else{
		        $background="color:red";
		        $color="";
		    }
		    //ultimos mantenimientos especiales
		    $id = $value->id;
		    $UltimosMant = collect([]);
	        $UltimoChecklist = Checklistvehicular::where('especial',true)
	        					->where('vehiculo_id',$id)->select('id','fecha_registro as fecha', 'vehiculo_id')
	        					->orderBy('fecha_registro','desc')
	        					->get()->first();
	        if($UltimoChecklist){
	        	$UltimosMant->offsetSet(1,$UltimoChecklist);
	        }
	        $UltimoControlCombustible = AbastecimientoCombustible::where('especial',true)->where('vehiculo_id',$id)
	        								->select('id','fecha_abastecimiento as fecha', 'vehiculo_id')
	        								->orderBy('fecha_abastecimiento','desc')
	        								->get()->first();
	        if($UltimoControlCombustible){
	        	$UltimosMant->offsetSet(2,$UltimoControlCombustible);
	        }
	        $UltimoRegRepVeh = RegRepVeh::where('especial',true)->where('ua_id',$id)
	        						->select('id','fechasalida as fecha','ua_id as vehiculo_id')
	        						->orderBy('fechasalida','desc')
	        						->get()->first();
	        if($UltimoRegRepVeh){
	        	$UltimosMant->offsetSet(3,$UltimoRegRepVeh);
	        }
	        $UltimoRegManVeh = RegManVeh::where('especial',true)->where('ua_id',$id)
	        						->select('id','fechasalida as fecha','ua_id as vehiculo_id')
	        						->orderBy('fechasalida','desc')
	        						->get()->first();
	        if($UltimoRegManVeh){
	        	$UltimosMant->offsetSet(4,$UltimoRegManVeh);
	        }
	        
	        
	        $FechaUltimoMantEspecial = null;

	        //Verifica si ya existió antes un mant especial
	        if($UltimosMant->first()){
		        $UltimosMant->sortByDesc('fecha');

		        $FechaUltimoMantEspecial = date("d/m/Y",strtotime($UltimosMant->first()->fecha));

	        }
		    ?>
		<tr class="{{ $color }}" style="{{ $background }}">
			<td>{{ $contador }}</td>
			<td>{{ $value->kilometraje->descripcion }}</td>
			<td>{{ $value->modelo }}</td>
			<td>{{ $value->placa }}</td>
			<!--td>{{ $FechaUltimoMantEspecial }}</td-->
			<td>{{ $value->kilometraje_inicial }}</td>
			<td>{{ $value->kilometraje_rec }}</td>
			<td>{{ number_format($value->kilometraje_act,2,'.','') }}</td>
			<td>{{ number_format($value->kilometraje2->limite_sup -  $value->kilometraje_rec,2,'.','') }}</td>
			@if( $value->kilometraje_rec < $value->kilometraje2->limite_inf )
				<td > Normal </td>
			@elseif( $value->kilometraje_rec < $value->kilometraje2->limite_sup )
				<td class=""> Observación </td>
			@else
				<td class=""> Desgastado </td>
				<?php 
				if($value->estadorecorrido==''){
					$value->estadorecorrido='NO EJECUTADO';
					$value->save;
				}
				?>
			@endif
			<td>{{ $value->estado }}</td>
			<td>{{ $value->comentario.(!is_null($value->usercomentario)?(" (".$value->usercomentario->name.")"):'') }}</td>
			@if($value->estadorecorrido=="EJECUTADO")
			<td style="color:green;font-weight: bold;">{{ $value->estadorecorrido }}</td>
			@else
			<td>{{ $value->estadorecorrido }}</td>
			@endif
			<td>{{ $value->concesionaria->abreviatura }}</td>
			<td>{!! Form::button('<i class="material-icons">edit</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit3"], array($value->id, 'listar'=>'SI')).'\', \'Comentario\', this);', 'class' => 'btn btn-info btn-link btn-sm','rel'=>'tooltip','title'=>'Comentario')) !!}</td>
			<td>{!! Form::button('<i class="material-icons">backup</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit4"], array($value->id, 'listar'=>'SI')).'\', \'Estado Mant.\', this);', 'class' => 'btn btn-danger btn-link btn-sm','rel'=>'tooltip','title'=>'Estado Mant.')) !!}</td>
			<td>{!! Form::button('<i class="material-icons">settings</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-primary btn-link btn-sm','rel'=>'tooltip','title'=>'Editar')) !!}</td>
			@if($user->id=1 || $user->id==261)
			<td>{!! Form::button('<i class="material-icons">speed</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit2"], array($value->id, 'listar'=>'SI')).'\', \'Km Actual\', this);', 'class' => 'btn btn-success btn-link btn-sm','rel'=>'tooltip','title'=>'Editar KM Actual')) !!}</td>
			@endif
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif
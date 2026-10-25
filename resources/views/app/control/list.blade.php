<?php
use App\Detallecontrol;
use App\Conductor;
use App\Persona;
?>
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
			<td>{{ str_pad($value->id,5,"0",STR_PAD_LEFT)."-".date("Y",strtotime($value->created_at)) }}</td>
			<td>{{ $value->garita->nombre }}</td>
			<td>{{ date("d/m/Y",strtotime($value->fecha)) }}</td>
			<td>{{ date("H:i",strtotime($value->fecha)) }}</td>
			<td>{{ $value->tipo }}</td>
			<td>{{ $value->turno->nombre }}</td>
			<td><label style="@if($value->origen_id==0) color:red @else color:black @endif">{{ ($value->origen_id>0)?$value->origen2->nombre:$value->origen }}</label></td>
			<td><label style="@if($value->destino_id==0) color:red @else color:black @endif">{{ $value->destino_id>0?$value->destino2->nombre:$value->destino }}</label></td>
			<td>{{ $value->agente->apellidos." ".$value->agente->nombres }}</td>
			<?php 
			$detalle = Detallecontrol::where('control_id','=',$value->id)->get();
			$personal = "";$km="";$vehiculo="";$personal2="";$empresa="";$conductor="";$colorconductor="";
			foreach ($detalle as $k => $v) {
				if($v->conductor=="N"){
					if($v->persona_id=="0" && $v->personawp_id=="0"){
						$color = "color:red";
					}else{
						$color = "color:black";
					}
					if($v->contratista_id>0){
						$personal2.=utf8_encode(str_replace("'", "", $v->persona))."(".$v->contratista->propietario2.") ";
					}else{
						$personal2.=utf8_encode(str_replace("'", "", $v->persona))."(".$v->empresa.") ";
					}
					$personal .= " - <label style='".$color."'>".($v->persona)."</label><br />";
					$km = $v->km;
					$vehiculo = $v->vehiculo;
					$empresa .= " - <label>".$v->empresa."</label><br />";
				}else{
					if($v->persona_id=="0" && $v->personawp_id=="0"){
						$colorconductor = "color:red";
					}else{
						$colorconductor = "color:black";
					}
					if($v->contratista_id>0){
						$conductor=utf8_encode(str_replace("'", "", $v->persona))."(".$v->contratista->propietario2.") ";
					}else{
						$conductor=utf8_encode(str_replace("'", "", $v->persona))."(".$v->empresa.") ";
					}
					$km = $v->km;
					$vehiculo = $v->vehiculo;
				}
			}
			if($vehiculo=="" && $km==""){
				$copy = "*".$value->garita->nombre."* | ".date("d/m/Y",strtotime($value->fecha))." | ".date("H:i",strtotime($value->fecha))." | *".$value->tipo."* | *Turno:* ".$value->turno->nombre." | *Origen:* ".($value->origen_id>0?$value->origen2->nombre:$value->origen)." | *Destino:* ".($value->destino_id>0?$value->destino2->nombre:$value->destino)." | *Agente:* ".$value->agente->apellidos." ".$value->agente->nombres." | SIN VEHICULO | *Conductor:* ".$conductor."| *Pasajeros:* ".$personal2." | *Observ:* ".$value->observacion;
			}else{
				$copy = "*".$value->garita->nombre."* | ".date("d/m/Y",strtotime($value->fecha))." | ".date("H:i",strtotime($value->fecha))." | *".$value->tipo."* | *Turno:* ".$value->turno->nombre." | *Origen:* ".($value->origen_id>0?$value->origen2->nombre:$value->origen)." | *Destino:* ".($value->destino_id>0?$value->destino2->nombre:$value->destino)." | *Agente:* ".$value->agente->apellidos." ".$value->agente->nombres." | *Vehiculo:* ".$vehiculo." | *Km:* ".$km." | *Conductor:* ".$conductor." | *Pasajeros:* ".$personal2." | *Observ:* ".$value->observacion;
			}
			?>
			<td>{!! $vehiculo !!}</td>
			<td>{!! $km !!}</td>
			<td><label style="{{$colorconductor}}">{{ $conductor }}</label></td>
			<td>{!! $personal !!}</td>
			<td>{!! $empresa !!}</td>
			<td>{!! $value->observacion !!}</td>
			<td>{!! date("d/m/Y H:i",strtotime($v->created_at)) !!}</td>
			@if(auth()->user()->tipouser_id!="19")
				<td>{!! Form::button('<i class="material-icons">add_ic_call</i>', array('onclick' => 'copyToClipboard(\''.$copy.'\')', 'class' => 'btn btn-info btn-link btn-sm','rel'=>'tooltip','title'=>'Copiar')) !!}</td>
				<td>{!! Form::button('<i class="material-icons">edit</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-primary btn-link btn-sm','rel'=>'tooltip','title'=>'Editar')) !!}</td>
				<td>{!! Form::button('<i class="material-icons">close</i>', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-danger btn-link btn-sm','rel'=>'tooltip','title'=>'Eliminar')) !!}</td>
			@endif
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
{!! $paginacion !!}
@endif
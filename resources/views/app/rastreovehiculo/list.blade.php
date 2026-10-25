<?php
use App\Vehiculo;
use App\Equipo;
use App\Vehiculodocument;
use App\Equipodocument;
use App\RegManVeh;
use App\RegRepVeh;
use App\Control;
use App\Checklistvehicular;
?>
@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
<br />
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
		    $fechainicio = $fecha;
		    while(strtotime($fechainicio)<=strtotime($fechafin)){
		    	$regmanveh = RegManVeh::where('ua_id','=',$value->id)->where('fechamantenimiento','=',$fechainicio)->first();
		    	if(!is_null($regmanveh)){
		    		$kmman = $regmanveh->kmman;
		    	}else{
		    		$kmman = '-';
		    	}
		    	$regrepveh = RegRepVeh::where('ua_id','=',$value->id)->where('fechamantenimiento','=',$fechainicio)->first();
		    	if(!is_null($regrepveh)){
		    		$kmrep = $regrepveh->kmman;
		    	}else{
		    		$kmrep = '-';
		    	}
		    	$control = Control::join('detallecontrol','detallecontrol.control_id','=','control.id')
		    					->join('garita','garita.id','=','control.garita_id')
		    					->where(DB::raw('cast(control.fecha as date)'),'=',$fechainicio)
		    					->where('detallecontrol.vehiculo_id','=',$value->id)
		    					->where('control.tipo','like','Entrada')
		    					->whereNull('detallecontrol.deleted_at')
		    					->select(DB::raw('distinct(detallecontrol.km) as km'),'control.fecha','garita.nombre as garita')
		    					->orderBy('fecha','asc')
		    					->get();
		    	$entrada = "";
		    	if(count($control)>0){
		    		foreach ($control as $k => $v) {
		    			$entrada .= "[".substr($v->fecha,11,5)."]: ".$v->km." km (".$v->garita.")<br />";
		    		}
		    		$entrada = substr($entrada,0,strlen($entrada)-6);
		    	}else{
		    		$entrada = '-';
		    	}
		    	$control = Control::join('detallecontrol','detallecontrol.control_id','=','control.id')
		    					->join('garita','garita.id','=','control.garita_id')
		    					->where(DB::raw('cast(control.fecha as date)'),'=',$fechainicio)
		    					->where('detallecontrol.vehiculo_id','=',$value->id)
		    					->where('control.tipo','like','Salida')
		    					->whereNull('detallecontrol.deleted_at')
		    					->select(DB::raw('distinct(detallecontrol.km) as km'),'control.fecha','garita.nombre as garita')
		    					->orderBy('fecha','asc')
		    					->get();
		    	$salida = "";
		    	if(count($control)>0){
		    		foreach ($control as $k => $v) {
		    			$salida .= "[".substr($v->fecha,11,5)."]: ".$v->km." km (".$v->garita.")<br />";
		    		}
		    		$salida = substr($salida,0,strlen($salida)-6);
		    	}else{
		    		$salida = '-';
		    	}
		    	$checklist = Checklistvehicular::where('vehiculo_id','=',$value->id)
		    					->where('fecha_registro','like',$fechainicio)
		    					->orderBy('hora_inicio','asc')
		    					->get();
		    	$check = '';
		    	if(count($checklist)>0){
		    		foreach ($checklist as $k => $v) {
		    			$check = "[".substr($v->hora_inicio,0,5)." - ".substr($v->hora_fin,0,5)."] ".$v->k_inicial." - ".$v->k_final." km (".$v->conductor->nombres." ".$v->conductor->apellidos.")<br />";
		    		}
		    		$check = substr($check,0,strlen($check)-6);
		    	}else{
		    		$check = '-';
		    	}
		    ?>
			<tr>
				<td>{{ $contador }}</td>
				<td>{{ date("d/m/Y",strtotime($fechainicio)) }}</td>
				<td>{{ $value->placa }}</td>
				<!--td>{{ $kmman }}</td>
				<td>{{ $kmrep }}</td-->
				<td width="300px">{!! $entrada !!}</td>
				<td width="300px">{!! $salida !!}</td>
				<td>{!! $check !!}</td>
			</tr>
		<?php
				$contador = $contador + 1;
				$fechainicio = date("Y-m-d",strtotime("+1 day",strtotime($fechainicio)));
			}
		?>
		@endforeach
	</tbody>
</table>
@endif
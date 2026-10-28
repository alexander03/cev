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
		$contador = $inicio + 1;$color="";
		?>
		@foreach ($lista as $key => $value)
			@php
			if($value->k_final=="" || $value->k_inicial==$value->k_final){
				$color = "background:#ff000061";
			}else{
				$color = "";
			}
			@endphp
		<tr style="<?php echo $color; ?>">
			<td>{{ $contador }}</td>
			@if($user->tipouser_id!=10)
				@if(App\Checklistvehicular::conductorPuedeEditar($user, $value->conductor_user_id, $value->id))
				<td>{!! Form::button('<i class="material-icons">edit</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-primary btn-link btn-sm','rel'=>'tooltip','title'=>'Ver')) !!}</td>
				@else
				<td></td>
				@endif
			@endif
			<td>
				<a href="{{ route('mantcorrprev.pdf.export') . '?checklistvehicular_id=' . $value->id }}" target="_blank" class="btn btn-sm btn-secondary" title="Exportar">
						<i class="material-icons">cloud_download</i>
				</a>
			</td>
			<td>{{ date('d/m/Y', strtotime($value->fecha_registro)) }}</td>
			<td>{{ $value->hora_inicio }}</td>
			<td>{{ $value->hora_fin }}</td>
			@if ($value->equipo_descripcion == null)
			    <td>{{ $value->vehiculo_ua }}</td>
			    <td>{{ $value->vehiculo_uatrabajo }}</td>
			    @if($value->saltarkm=="S")
				<td><label style="color:green;font-weight: bold">{{ $value->vehiculo_modelo." / ".$value->vehiculo_placa }}</label></td>
				@else
				<td>{{ $value->vehiculo_modelo." / ".$value->vehiculo_placa }}</td>
				@endif
			@else
			    <td>{{ $value->vehiculo_ua }}</td>
			    <td>{{ $value->vehiculo_uatrabajo }}</td>
				<td>{{ $value->equipo_descripcion." / ".$value->equipo_placa }}</td>
			@endif
			<td>{{ $value->k_inicial }}</td>
			<td>{{ $value->k_final }}</td>
			@if($value->k_final=="" || $value->k_inicial==$value->k_final)			
			<td></td>
			@else
			<td>{{ number_format($value->k_final - $value->k_inicial,2,'.','') }}</td>
			@endif
			<td>{{ $value->lider_area }}</td>
			<td>{{ $value->conductor_nombres . ' ' . $value->conductor_apellidos }}</td>
			<td>{{ $value->observaciones }}</td>
			<td>{{ $value->incidentes }}</td>
			@if($user->tipouser_id==1 || $user->tipouser_id==6 || $user->tipouser_id==12)
				<td>{{ $value->justificacion }}</td>
			@endif
			<td>{{ $value->usuario }}</td>
			<td>{{ $value->created_at ? date('d/m/Y H:i:s', strtotime($value->created_at)) : '' }}</td>
			<td>{{ $value->updated_at ? date('d/m/Y H:i:s', strtotime($value->updated_at)) : '' }}</td>
			@if($user->tipouser_id==1 || $user->tipouser_id==6 || $user->tipouser_id==12)
			<td>{!! Form::button('<i class="material-icons">visibility</i>', array('onclick' => 'modal (\''.URL::route($ruta["justificar"], array($value->id, 'SI')).'\', \'Ingrese Información\', this);', 'class' => 'btn btn-warning btn-link btn-sm','rel'=>'tooltip','title'=>'Justificar')) !!}</td>
			@endif
			@if($user->tipouser_id!=7)
			<td>{!! Form::button('<i class="material-icons">close</i>', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-danger btn-link btn-sm','rel'=>'tooltip','title'=>'Eliminar')) !!}</td>
			@endif
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif
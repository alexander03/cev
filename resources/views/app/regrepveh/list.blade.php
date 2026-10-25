@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
	<th class="text-nowrap" @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif class="text-center">{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
			@if($value->situacion=="P")
		<tr style="color:red">		
			@else
		<tr>
			@endif
			<td>{{ $contador }}</td>
			<td>{{ $value->numero }}</td>
			@if($value->fechamantenimiento!="")
				<td>{{ date("d/m/Y",strtotime($value->fechamantenimiento)) }}</td>
			@else
				<td> - </td>
			@endif
			@if($value->taller_id>0)
				<td>{{ $value->taller->razonsocial }}</td>
			@else
				<td> - </td>
			@endif
			<?php 
            if(!is_null($value->docmat_id) && ($value->docmat_id+0)>0){
            	$docmat = DB::connection('mysql3')->select('select docmat.*
            		from docmat where id='.$value->docmat_id);
            	echo "<td> - </td>";
            	echo "<td> ".$docmat[0]->numero."</td>";
            }else{
            	echo "<td>".(is_null($value->ordencompra)?$value->telefono:$value->ordencompra)."</td>";
            	echo "<td> - </td>";
            }
			?>
			<td>{{ (!is_null($value->vehiculo)?$value->vehiculo->placa:(is_null($value->equipo)?'-':$value->equipo->placa)) }}</td>
			<td>{{ $value->kmman }}</td>
			<td>{{ $value->kminicial }}</td>
			<td>{{ $value->kmfinal }}</td>
			<td>{{ date("d/m/Y",strtotime($value->fechaentrada)) }}</td>
			<td>{{ date("d/m/Y",strtotime($value->fechasalida)) }}</td>
			@if ($value->tipomantenimiento==1)
			<td>Preventivo</td>
			@elseif ($value->tipomantenimiento==2)
			<td>Correctivo </td>
			@else
			<td>Indefinido </td>
			@endif
			<td>{{ (!is_null($value->user($value->id))?$value->user($value->id)->name:'-') }}</td>
			<td>{{ date("d/m/Y",strtotime($value->fecharegistro)) }}</td>
			<td>{{ $value->documentos>0?'SI':'NO' }}</td>
			@if(auth()->user()->tipouser_id!="19")
			<td>{!! Form::button('<i class="material-icons">description</i>', array('onclick' => 'modal (\''.URL::route('regrepvehdocument.index', array('regrepveh_id' =>$value->id, 'listar'=>'NO')).'\',  \'Registros de Documentos\' , this);', 'class' => 'btn btn-info btn-link btn-sm','rel'=>'tooltip','title'=>'Registros')) !!}</td>
			<td>{!! Form::button('<i class="material-icons">edit</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-primary btn-link btn-sm','rel'=>'tooltip','title'=>'Editar')) !!}</td>
			<td>
				<a href="{{ route('regrepveh.pdf.export') . '?id=' . $value->id }}" target="_blank" class="btn btn-sm btn-secondary" title="Exportar">
						<i class="material-icons">cloud_download</i>
				</a>
			</td>
			
			@if (!$value->deleted_at)
			<td>{!! Form::button('<i class="material-icons">close</i>', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-danger btn-link btn-sm','rel'=>'tooltip','title'=>'Eliminar')) !!}</td>
			@else
			<td>{!! Form::button('<i class="material-icons">done</i>', array('onclick' => 'modal (\''.URL::route($ruta["activar"], array($value->id, 'SI')).'\', \''.$titulo_activar.'\', this);', 'class' => 'btn btn-success btn-link btn-sm','rel'=>'tooltip','title'=>'Activar')) !!}</td>
			@endif
			@endif
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif
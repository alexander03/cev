<?php 
use Illuminate\Support\Facades\DB;
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
		<tr>
			<td>{{ $contador }}</td>
			<td>{{ date("d/m/Y H:i:s",strtotime($value->created_at)) }}</td>
			<td>{{ $value->tabla }}</td>
			<td>{{ $value->accion }}</td>
			<?php if($value->accion=="Editar"){ ?>
			    <td><?php
			    	$data=json_decode($value->valor_anterior); 
			    	echo "<table>
			    				<tr>
			    					<th>COLUMNA</th>
			    					<th>VALOR ANT.</th>
			    				</tr>";
			    	$data2=json_decode($value->valor_posterior); 
			    	if($value->valor_posterior!="" && $value->valor_anterior!="" && $value->valor_anterior!="[]"){
					    foreach ($data as $k => $v) {
					    	eval("\$comparar=\$data2->".$k.";");
					    	if($k!="updated_at" && ($v!=$comparar || $k=="id")){
						    	echo "<tr>";
						    	echo "<th>".$k."</th>";
						    	if(is_array($v)){
						    		echo "<td>".print_r($v)."</td>";
						    	}else{
						    		echo "<td>".$v."</td>";
						    	}
						    	echo "</tr>";
						    }
					    }
					}
				    echo "</table>";
				  ?>
				</td>
			    <td><?php
			    	$data2=json_decode($value->valor_posterior); 
			    	$data=json_decode($value->valor_anterior);
			    	echo "<table>
			    				<tr>
			    					<th>COLUMNA</th>
			    					<th>VALOR NUEVO</th>
			    				</tr>";
			    	if($value->valor_posterior!="" && $value->valor_anterior!="" && $value->valor_anterior!="[]"){
					    foreach ($data2 as $k => $v) {
					    	eval("\$comparar=\$data->".$k.";");
					    	if($k!="updated_at" && ($v!=$comparar || $k=="id")){
						    	echo "<tr>";
						    	echo "<th>".$k."</th>";
						    	if(is_array($v)){
						    		echo "<td>".print_r($v)."</td>";
						    	}else{
						    		echo "<td>".$v."</td>";
						    	}
						    	echo "</tr>";
						    }
					    }
					}
				    echo "</table>";
				  ?></td>
			<?php }elseif($value->accion=="eliminar"){
			    $registro=DB::table($value->tabla)->find($value->registro_id);
			    if(is_null($registro)){
			        $registro="";
			    }
			?>
			    <td>{{ $value->registro_id }}</td>
			    <td>{{ json_encode($registro) }}</td>
			<?php }else{
			    $registro=DB::table($value->tabla)->find($value->registro_id);
			    if(is_null($registro)){
			        $registro="";
			    }
			?>
			    <td>{{ $value->registro_id }}</td>
			    <td>{{ json_encode($registro) }}</td>
			<?php } ?>
			<td>{{ $value->persona }}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif
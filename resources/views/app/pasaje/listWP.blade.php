@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
<table id="example10" class="table table-sm text-center table-striped  table-hover">

	<thead>
		<tr>
		    <th>Area</th>
			<th>Personal</th>
			<th>DNI</th>
			<th>Seleccionar</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<?php
    		$len = strlen($value->nivel);
            $sangria = "";
    
            for ($j = 0; $j < $len; $j++) {
    
                if ($j == ($len - 1))
                    $sangria .='- ';
                else
                    $sangria .='-';
            }
        ?>
		<tr>
			<td>{{ $value->area }}</td>
            <td>{{ (($value->personal)) }}</td>
            <td>{{ (($value->docid)) }}</td>
			<td>{!! Form::button('<div class="fa fa-check"></div>', array('onclick' => 'seleccionarWP (\''.$value->codusuario.'\',\''.$value->personal.'\',\''.$value->usuario.'\',\''.$value->email.'\','.$value->contratista_id.',\''.$value->docid.'\');', 'class' => 'btn btn-sm btn-outline-success')) !!}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
@endif
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script>
    configurarAnchoModal('1200');
    $("#example10").DataTable({
    	"paging": false,
    });
</script>
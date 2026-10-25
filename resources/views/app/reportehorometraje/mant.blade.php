<table class="table table-bordered table-striped table-condensed table-hover">
	<tr>
	    <th>Nro</th>
	    <th>Fecha</th>
		<th>Mantenimiento</th>
		<th>Horometro actual contr/comb</th>
		<th>Horometro inicial de control</th>
		<th>Horometro recorrido</th>
		<th>Opciones</th>
	</tr>
	<?php $c=0; 
	?>
	@foreach($ListaFinal as $value)
	    <?php 
	    $c=$c+1;
	    ?>
		<tr>
		    <td>{{ $c }}</td>
		    <td>
				{{ date("d/m/Y",strtotime($value->fecha)) }}
			</td>
			<td>
				@if( $value->tipo == 1)
					Control Equipo
				@elseif( $value->tipo == 2)
					Abastecimiento de Combustible
				@elseif( $value->tipo == 3)
					Checklist
				@elseif( $value->tipo == 4)
					Mant. repuesto vehicular {{ $value->tipo==1?'Preventivo':'Correctivo' }}
				@else( $value->tipo == 5)
					Mant. vehicular {{ $value->tipo==1?'Preventivo':'Correctivo' }}
				@endif
			</td>
			<td>
				{{ number_format($value->kmfinal,2,'.',',') }}
			</td>
			<td>
				{{ number_format($equipo->horometrajefinal,2,'.',',') }}
			</td>
			<td>
				{{ number_format($value->kmfinal - $equipo->horometrajefinal,2,'.',',') }}
			</td>
			<td>
				<button class="btn btn-success btn-sm"  
					onclick="actualizarMantenimiento({{$value->id}},{{$equipo->id}},{{$value->tipo }});">
					<i class="fa fa-check fa-lg"></i>Seleccionar
				</button>
			</td>
		</tr>
	@endforeach
    @if($c==0)
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>{{ $equipo->horometrajefinal }}</th>
        </tr>
    @endif
</table>



<button onclick='cerrarModal();' class="btn btn-warning btn-sm"> 
	<i class="fa fa-exclamation fa-lg"></i> Cancelar
</button>

<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('800');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	}); 




	function actualizarMantenimiento(mante_id,equipo_id,tipo){

		let formData = new FormData();

		formData.append('tipo',tipo);
		formData.append('Mant_id',mante_id);
		formData.append('equipo_id',equipo_id);
		formData.append('_method','PUT');

		let data = $.ajax({	
							headers: {
							    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
							  },
							url : 'reportehorometraje/update',
							type: 'POST',
							data: formData,
							processData: false,
							contentType: false,
						});
		data.done(function(msg) {
			respuesta = msg;
		}).fail(function(xhr, textStatus, errorThrown) {
			respuesta = 'ERROR';
		}).always(function() {
			if(respuesta === 'ERROR'){
			}else{
				if (respuesta === 'OK') {
					md.showNotification('top','right','Guardado correctamente','info');
					buscarCompaginado('', 'Accion realizada correctamente', '{{ $entidad }}', 'OK');
				} else {
					mostrarErrores(respuesta, '#form-document', '{{ $entidad }}');

				}
			}
		});

	    
	}

</script>
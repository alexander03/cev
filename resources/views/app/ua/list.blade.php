@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
<section class="d-flex flex-column flex-md-row align-items-center justify-content-between">
	{!! $paginacion !!}
	<article class="d-flex">
		<button class="btn btn-sm btn-outline-danger"
			onclick="deleteSelected()">
			Borrar seleccionados
		</button>
		<button class="btn btn-sm btn-danger"
			onclick="deleteAll()">
			Borrar todos
		</button>
	</article>
</section>

<table id="example1" class="table table-striped table-hover mt-2">

	<thead>
		<tr class="text-center ">
			@foreach($cabecera as $key => $value)
				<th class="text-nowrap" @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody class="text-center ">
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<tr class="">
			<td>{{ $contador }}</td>
			<td>{!! Form::button('<i class="material-icons">edit</i>', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-primary btn-link btn-sm','rel'=>'tooltip','title'=>'Editar')) !!}</td>
			<td>{!! Form::button('<i class="material-icons">close</i>', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-danger btn-link btn-sm','rel'=>'tooltip','title'=>'Eliminar')) !!}</td>
			<td>
				<div class="custom-control custom-checkbox" style="left:35%">
					<input type="checkbox" class="custom-control-input" 
						id="id-{{ $value -> id }}" name="wants_delete" 
						value="{{ $value -> id }}">
					<label class="custom-control-label" for="id-{{ $value -> id }}"></label>
				</div>
			</td>
			<td>{{ $value->	codigo }}</td>
			<td class='text-left'>{{ $value->	descripcion }}</td>
			<td class='text-left'>
				@if($value -> ua_padre_id)
					<?php echo '<strong>'.$value -> uaPadre($value -> ua_padre_id)[0] -> codigo.'</strong> '.$value -> uaPadre($value -> ua_padre_id)[0] -> descripcion; ?>
				@else
					Sin padre
				@endif
			</td>
			<td>
				<?php // {{ $value -> es_padre }} ?>
				{{ $value -> padre }}
			</td>
			<td>{{ ($value-> habilitada) ? 'HABILITADO' : 'DESHABILITADO' }}</td>
			<td>{{ date("d/m/Y",strtotime($value-> fecha_inicio)) }}</td>
			<td>{{ ($value-> fecha_fin) ? date("d/m/Y",strtotime($value -> fecha_fin)) : 'ILIMITADO' }}</td>
			<td>
				@if(!is_null($value->responsable_id) && $value -> responsable_id>0)
				{{ $value -> responsable -> nombre }}
				@else
					Sin responsable
				@endif
			</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif

<div class="modal" id="id-modal-ua" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog">
	  <div class="modal-content">
		<div class="modal-header">
		  <h5 class="modal-title text-dark">Eliminar ua</h5>
		</div>
		<div class="modal-body">
			<h3 class="text-center text-danger js-modal-title">
				No has seleccionado ningún registro
			</h3>
			<p class="text-center text-secondary js-modal-desc">
				Error debe seleccionar por lo menos un registro
			</p>
			<section class="d-flex justify-content-center">
				<button type="button" class="btn btn-success js-btn-ua-accept" data-dismiss="modal">
					Aceptar
				</button>
			</section>
		</div>
	  </div>
	</div>
</div>

<div class="modal" id="id-modal-confirm" tabindex="-1" data-backdrop="static">
	<div class="modal-dialog">
	  <div class="modal-content m-auto" style="max-width: 350px">
		<div class="modal-header">
		  <h5 class="modal-title text-dark">Eliminar ua</h5>
		</div>
		<div class="modal-body">
			<p class="text-danger">¿Esta seguro de eliminar los registros?</p>
		
			<div class="col-lg-12 col-md-12 col-sm-12 text-right">
				<button class="btn btn-success btn-sm js-btn-delete" type="button">
					<i class="fa fa-check fa-lg"></i> 
					Eliminar
				</button>
				<button class="btn btn-warning btn-sm js-btn-cancel" type="button">
					<i class="fa fa-exclamation fa-lg"></i> 
					Cancelar
				</button>
			</div>
		
		</div>
	  </div>
	</div>
</div>
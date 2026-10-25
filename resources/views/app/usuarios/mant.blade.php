<?php
$id= 0;
if(!is_null($user)){
	$id = $user->id;
}
?>
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($user, $formData) !!}	
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="form-group">
	{!! Form::label('tipouser_id', 'Tipo de usuario:', array('class' => 'col-sm-12 control-label')) !!}
	<div class="col-sm-12">
		{!! Form::select('tipouser_id', $cboTipousers, null, array('class' => 'form-control input-xs', 'id' => 'tipouser_id')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('name', 'Nombre de la persona:', array('class' => 'col-sm-12 control-label')) !!}
	<div class="col-sm-12">
		{!! Form::text('name', null, array('class' => 'form-control input-xs', 'id' => 'name')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('username', 'Usuario:', array('class' => 'col-sm-12 control-label')) !!}
	<div class="col-sm-12">
		{!! Form::text('username', null, array('class' => 'form-control input-xs', 'id' => 'username')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('correo', 'Correo:', array('class' => 'col-sm-12 control-label')) !!}
	<div class="col-sm-12">
		{!! Form::email('correo', null, array('class' => 'form-control input-xs', 'id' => 'correo')) !!}
	</div>
</div>
<div class="form-group">
	{!! Form::label('password', 'Contraseña:', array('class' => 'col-sm-12 control-label')) !!}
	<div class="col-sm-12">
		{!! Form::password('password', null, array('class' => 'form-control input-xs', 'id' => 'password')) !!}
	</div>
</div>
<div class="form-group" @if(!is_null($user) && $user->tipouser_id==16) hidden='' @endif>
	{!! Form::label('concesionaria_id', 'Concesionaria:', array('class' => 'col-sm-12 control-label')) !!}
	<div class="col-sm-12 d-flex justify-content-around">
		@foreach ($cboConcesionaria as $item)
			<div>
				{!! Form::label($item['id'], $item['abreviatura'], []) !!}
				@if ($item['estado'])
					<input type="checkbox" name="{{$item['id']}}" value="{{$item['id']}}" checked="true" class="arr-concesionarias" data-estado="true">
				@else
					<input type="checkbox" name="{{$item['id']}}" value="{{$item['id']}}" class="arr-concesionarias" data-estado="false">
				@endif
				{{-- {!! Form::checkbox($item['ruc'], $item['id'], $item['estado'], ['class' => 'arr-concesionarias', 'id' => $item['ruc'], 'data-estado' => $item['estado']]) !!} --}}
			</div>
		@endforeach
		{!! Form::text('las-concesionarias', json_encode($cboConcesionaria), ['id' => 'input-concesionarias', 'hidden' => true]) !!}
	</div>
</div>
@if (!is_null($user)) 
<div class="form-group">
	<div class="col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-reply-all"></i> Credenciales', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar2', 'onclick' => 'guardar2(\''.$entidad.'\', this)')) !!}
	</div>
</div>
@endif
<div class="form-group">
	<div class="col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('350');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');

		const inputConcesionarias = document.getElementById('input-concesionarias');
		const arrConcesionarias = Array.from(document.getElementsByClassName('arr-concesionarias')).map(el => {
			return {
				'id'     : el.value,
				'estado' : el.dataset.estado == 'true' ? true : false
			};
		});

		console.log(arrConcesionarias);

		$('.arr-concesionarias').change(function() {
			if ($(this).is(':checked') ) {
				changeEstadoConcesionaria($(this).val(),true);
			} else {
				changeEstadoConcesionaria($(this).val(),false);
			}
			inputConcesionarias.value = JSON.stringify(arrConcesionarias);
		});

		const changeEstadoConcesionaria = (idElement,estado) => {
			arrConcesionarias.map((el) => {
				if(el.id == idElement) {
					el.estado = estado;
				}
			});
			console.log(arrConcesionarias)
		};
	}); 

	function guardar2 (entidad, idboton) {
	    var password = $(IDFORMMANTENIMIENTO + '{{ $entidad }}' + " :input[id='password']").val();
	    var correo = $(IDFORMMANTENIMIENTO + '{{ $entidad }}' + " :input[id='correo']").val();
	    if(password==""){
	    	alert("Debe ingresar password");
	    	return false;
	    }
	    if(correo==""){
	    	alert("Debe ingresar correo");
	    	return false;
	    }
        var ruta = 'user/enviarCorreo?password='+password+"&correo="+correo+"&id={{$id}}";
        var respuesta = '';
        var data = sendRuta(ruta);
        data.done(function(msg) {
            respuesta = msg;
        }).fail(function(xhr, textStatus, errorThrown) {
            
        }).always(function() {
            md.showNotification('top','right','Enviado correctamente','info');
        });
	}
</script>